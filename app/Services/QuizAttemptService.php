<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\QuizAttemptRepository;
use App\Repositories\QuestionRepository;
use App\Repositories\QuizRepository;
use Core\Database;

final class QuizAttemptService
{
    private QuizAttemptRepository $attemptRepository;
    private QuestionRepository $questionRepository;
    private QuizRepository $quizRepository;
    private AnswerValidationService $validationService;

    public function __construct()
    {
        $this->attemptRepository = new QuizAttemptRepository();
        $this->questionRepository = new QuestionRepository();
        $this->quizRepository = new QuizRepository();
        $this->validationService = new AnswerValidationService();
    }

    public function getAttemptById(int $attemptId): ?array
    {
        return $this->attemptRepository->findAttempt($attemptId);
    }

    public function getAttemptAnswers(int $attemptId): array
    {
        return $this->attemptRepository->getAttemptAnswers($attemptId);
    }

    /**
     * Start a new attempt or resume an active one.
     *
     * @param int $quizId
     * @param int $userId
     * @return int Attempt ID
     */
    public function startAttempt(int $quizId, int $userId): int
    {
        $quiz = $this->quizRepository->find($quizId);
        if (!$quiz) {
            throw new \RuntimeException("Quiz not found.");
        }

        // Verify status and dates
        if ($quiz['status'] !== 'published') {
            throw new \RuntimeException("Quiz is not published.");
        }
        
        $now = time();
        if ($quiz['start_at'] && strtotime($quiz['start_at']) > $now) {
            throw new \RuntimeException("Quiz has not started yet.");
        }
        if ($quiz['end_at'] && strtotime($quiz['end_at']) < $now) {
            throw new \RuntimeException("Quiz has ended.");
        }

        // Resume check
        if (filter_var($quiz['allow_resume'] ?? true, FILTER_VALIDATE_BOOL)) {
            $active = $this->attemptRepository->findActiveAttempt($quizId, $userId);
            if ($active) {
                // If duration is set, check if the active attempt has expired
                if ($quiz['duration_minutes']) {
                    $started = strtotime($active['started_at']);
                    $expiry = $started + ((int) $quiz['duration_minutes'] * 60);
                    if ($now > $expiry) {
                        // Mark as expired and proceed to create a new one
                        $db = Database::connection();
                        $db->beginTransaction();
                        try {
                            $this->attemptRepository->submitAttempt((int) $active['id'], [
                                'status' => 'expired',
                                'score' => 0.0,
                                'total_score' => 0.0,
                                'percentage' => 0.0,
                                'passed' => false,
                            ]);
                            $db->commit();
                        } catch (\Throwable $e) {
                            $db->rollBack();
                        }
                    } else {
                        return (int) $active['id'];
                    }
                } else {
                    return (int) $active['id'];
                }
            }
        }

        return $this->attemptRepository->createAttempt($quizId, $userId);
    }

    /**
     * Save a single answer dynamically during the quiz.
     *
     * @param int $attemptId
     * @param int $userId
     * @param array $payload Must contain 'question_id' and either 'selected_option_ids' or 'answer_text'
     * @return array Contains success and validation result details
     */
    public function saveAnswer(int $attemptId, int $userId, array $payload): array
    {
        $attempt = $this->attemptRepository->findAttempt($attemptId);
        if (!$attempt) {
            throw new \RuntimeException("Quiz attempt not found.");
        }

        if (isset($attempt['user_id']) && (int) $attempt['user_id'] !== $userId) {
            throw new \RuntimeException("Unauthorized: This attempt does not belong to you.");
        }

        if ($attempt['status'] !== 'in_progress') {
            throw new \RuntimeException("Cannot save answers for a submitted or expired attempt.");
        }

        // Check time limit
        if ($attempt['duration_minutes']) {
            $started = strtotime($attempt['started_at']);
            $expiry = $started + ((int) $attempt['duration_minutes'] * 60);
            if (time() > $expiry) {
                throw new \RuntimeException("This attempt has expired due to time limit.");
            }
        }

        $questionId = (int) ($payload['question_id'] ?? 0);
        $question = $this->questionRepository->find($questionId);
        if (!$question || (int) $question['quiz_id'] !== (int) $attempt['quiz_id']) {
            throw new \RuntimeException("Invalid question.");
        }

        $db = Database::connection();
        $db->beginTransaction();

        try {
            $optionsOrAcceptedAnswers = [];
            $type = $question['type'] ?? '';

            if (in_array($type, ['single_choice', 'multiple_choice', 'true_false'], true)) {
                $optionsOrAcceptedAnswers = $this->questionRepository->getOptionsForQuestion($questionId);
            } elseif ($type === 'open_text') {
                $optionsOrAcceptedAnswers = $this->questionRepository->getAcceptedAnswersForQuestion($questionId);
            }

            // Perform server-side validation of the answer
            $validation = $this->validationService->validate($question, $optionsOrAcceptedAnswers, $payload);
            
            $answerText = $payload['answer_text'] ?? null;
            $selectedOptionIds = $payload['selected_option_ids'] ?? [];
            if (!is_array($selectedOptionIds)) {
                $selectedOptionIds = [$selectedOptionIds];
            }
            $selectedOptionIds = array_map('intval', $selectedOptionIds);

            // Save attempt answer record
            $attemptAnswerId = $this->attemptRepository->saveAttemptAnswer(
                $attemptId,
                $questionId,
                $answerText,
                $validation['is_correct'],
                $validation['awarded_score']
            );

            // Save selected options if choice-based
            if (in_array($type, ['single_choice', 'multiple_choice', 'true_false'], true)) {
                $optionCustomTexts = $payload['option_custom_texts'] ?? [];
                if (!is_array($optionCustomTexts)) {
                    $optionCustomTexts = [];
                }
                $this->attemptRepository->saveAttemptAnswerOptions($attemptAnswerId, $selectedOptionIds, $optionCustomTexts);
            }

            $db->commit();
            return [
                'success' => true,
                'is_correct' => $validation['is_correct'],
                'awarded_score' => $validation['awarded_score'],
            ];
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Submit and finalize a quiz attempt, calculating final scores.
     *
     * @param int $attemptId
     * @param int $userId
     * @return array Results summary
     */
    public function submitAttempt(int $attemptId, int $userId): array
    {
        $attempt = $this->attemptRepository->findAttempt($attemptId);
        if (!$attempt) {
            throw new \RuntimeException("Quiz attempt not found.");
        }

        if (isset($attempt['user_id']) && (int) $attempt['user_id'] !== $userId) {
            throw new \RuntimeException("Unauthorized: This attempt does not belong to you.");
        }

        if ($attempt['status'] !== 'in_progress') {
            throw new \RuntimeException("Attempt has already been submitted or expired.");
        }

        $db = Database::connection();
        $db->beginTransaction();

        try {
            $questions = $this->questionRepository->getQuestionsForQuiz((int) $attempt['quiz_id']);
            $savedAnswers = $this->attemptRepository->getAttemptAnswers($attemptId);
            
            $answersMap = [];
            foreach ($savedAnswers as $ans) {
                $answersMap[(int) $ans['question_id']] = $ans;
            }

            // Build trigger options map
            $triggerOptionsMap = [];
            foreach ($questions as $q) {
                $options = $this->questionRepository->getOptionsForQuestion((int) $q['id']);
                foreach ($options as $opt) {
                    $rqIds = $opt['related_question_ids'] ?? [];
                    foreach ($rqIds as $rqId) {
                        $triggerOptionsMap[(int) $rqId][] = (int) $opt['id'];
                    }
                }
            }

            // Determine visible questions
            $visibleQuestionIds = [];
            foreach ($questions as $q) {
                $qId = (int) $q['id'];
                if (!isset($triggerOptionsMap[$qId])) {
                    $visibleQuestionIds[] = $qId;
                } else {
                    $isVisible = false;
                    foreach ($triggerOptionsMap[$qId] as $triggerOptId) {
                        foreach ($savedAnswers as $ans) {
                            if (in_array($triggerOptId, $ans['selected_option_ids'])) {
                                $isVisible = true;
                                break 2;
                            }
                        }
                    }
                    if ($isVisible) {
                        $visibleQuestionIds[] = $qId;
                    }
                }
            }

            $totalScore = 0.0;
            $awardedScore = 0.0;
            $correctCount = 0;
            $incorrectCount = 0;

            foreach ($questions as $question) {
                $qId = (int) $question['id'];
                
                // If the question is not visible, skip it completely
                if (!in_array($qId, $visibleQuestionIds, true)) {
                    continue;
                }

                $qScore = (float) ($question['score'] ?? 1.0);
                $totalScore += $qScore;

                if (isset($answersMap[$qId])) {
                    $ans = $answersMap[$qId];
                    if ($ans['is_correct']) {
                        $awardedScore += (float) $ans['awarded_score'];
                        $correctCount++;
                    } else {
                        $incorrectCount++;
                    }
                } else {
                    // Unanswered question -> save empty answer
                    $this->attemptRepository->saveAttemptAnswer($attemptId, $qId, null, false, 0.0);
                    $incorrectCount++;
                }
            }

            // Calculate percentage
            $percentage = 0.0;
            if ($totalScore > 0.0) {
                $percentage = ($awardedScore / $totalScore) * 100.0;
            }

            // Determine passed
            $passed = true;
            if ($attempt['pass_score'] !== null) {
                $passed = ($percentage >= (float) $attempt['pass_score']);
            }

            // Update status (also handle time-limit overflow status setting)
            $status = 'submitted';
            if ($attempt['duration_minutes']) {
                $started = strtotime($attempt['started_at']);
                if (time() > ($started + ((int) $attempt['duration_minutes'] * 60) + 10)) { // 10s buffer
                    $status = 'expired';
                    $passed = false;
                }
            }

            $this->attemptRepository->submitAttempt($attemptId, [
                'status' => $status,
                'score' => $awardedScore,
                'total_score' => $totalScore,
                'percentage' => $percentage,
                'passed' => $passed,
            ]);

            $db->commit();

            return [
                'attempt_id' => $attemptId,
                'status' => $status,
                'score' => $awardedScore,
                'total_score' => $totalScore,
                'percentage' => $percentage,
                'passed' => $passed,
                'correct_count' => $correctCount,
                'incorrect_count' => $incorrectCount,
            ];
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
