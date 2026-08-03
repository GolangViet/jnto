<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\QuizAttemptService;
use App\Services\QuizService;
use App\Services\QuestionService;
use Core\Controller;
use Core\Csrf;

final class QuizAttemptController extends Controller
{
    private QuizAttemptService $attemptService;
    private QuizService $quizService;
    private QuestionService $questionService;

    public function __construct()
    {
        $this->attemptService = new QuizAttemptService();
        $this->quizService = new QuizService();
        $this->questionService = new QuestionService();
    }

    /**
     * Web Page: List all published quizzes.
     */
    public function index(): string
    {
        $quizzes = $this->quizService->getPublishedQuizzes();
        return $this->view('quiz/list', ['quizzes' => $quizzes]);
    }

    /**
     * Web Page: Quiz details and start page.
     */
    public function show(string $id): string
    {
        $quizId = (int) $id;
        $quiz = $this->quizService->getQuizById($quizId);
        
        if (!$quiz || $quiz['status'] !== 'published') {
            http_response_code(404);
            return 'Quiz not found or not published.';
        }

        $questions = $this->questionService->getQuestionsForQuiz($quizId);

        return $this->view('quiz/show', [
            'quiz' => $quiz,
            'questionsCount' => count($questions),
        ]);
    }

    /**
     * Web Page Action: Start a quiz attempt and redirect.
     */
    public function start(string $id): never
    {
        Csrf::verify(app()->request());
        
        $quizId = (int) $id;
        $user = app()->session()->get('user');
        if (!$user) {
            $this->redirect('/login');
        }

        try {
            $attemptId = $this->attemptService->startAttempt($quizId, (int) $user['id']);
            $this->redirect("/quiz-attempts/{$attemptId}");
        } catch (\Throwable $e) {
            app()->session()->flash('error', $e->getMessage());
            $this->redirect("/quizzes/{$quizId}");
        }
    }

    /**
     * Web Page: Interactive quiz taking page.
     */
    public function attempt(string $attemptId): string
    {
        $id = (int) $attemptId;
        $user = app()->session()->get('user');
        if (!$user) {
            $this->redirect('/login');
        }

        $attempt = $this->attemptService->getAttemptById($id);
        if (!$attempt) {
            http_response_code(404);
            return 'Attempt not found.';
        }

        if (isset($attempt['user_id']) && (int) $attempt['user_id'] !== (int) $user['id']) {
            http_response_code(403);
            return 'Unauthorized to access this attempt.';
        }

        if ($attempt['status'] !== 'in_progress') {
            $this->redirect("/quiz-attempts/{$id}/result");
        }

        // Check expiry
        if ($attempt['duration_minutes']) {
            $started = strtotime($attempt['started_at']);
            $expiry = $started + ((int) $attempt['duration_minutes'] * 60);
            if (time() > $expiry) {
                // Auto-submit expired attempt on page load
                try {
                    $this->attemptService->submitAttempt($id, (int) $user['id']);
                } catch (\Throwable $e) {
                    // Ignore already submitted errors
                }
                $this->redirect("/quiz-attempts/{$id}/result");
            }
        }

        $questions = $this->questionService->getQuestionsForQuiz((int) $attempt['quiz_id']);
        foreach ($questions as &$question) {
            $details = $this->questionService->getQuestionById((int) $question['id']);
            // STRIP is_correct to prevent inspect element cheating!
            $options = $details['options'] ?? [];
            foreach ($options as &$opt) {
                unset($opt['is_correct']);
            }
            $question['options'] = $options;
            $question['accepted_answers'] = []; // Never expose accepted answers
        }

        $savedAnswers = $this->attemptService->getAttemptAnswers($id);

        return $this->view('quiz/attempt', [
            'attempt' => $attempt,
            'questions' => $questions,
            'savedAnswers' => $savedAnswers,
        ]);
    }

    /**
     * Web Page: Quiz attempt result details.
     */
    public function result(string $attemptId): string
    {
        $id = (int) $attemptId;
        $user = app()->session()->get('user');
        if (!$user) {
            $this->redirect('/login');
        }

        $attempt = $this->attemptService->getAttemptById($id);
        if (!$attempt) {
            http_response_code(404);
            return 'Attempt not found.';
        }

        $mainSurveyId = setting('main_survey_quiz_id');
        if ($mainSurveyId && (int)$attempt['quiz_id'] === (int)$mainSurveyId) {
            $this->redirect('/thank-you');
        }

        if (isset($attempt['user_id']) && (int) $attempt['user_id'] !== (int) $user['id']) {
            http_response_code(403);
            return 'Unauthorized to access this result.';
        }

        if ($attempt['status'] === 'in_progress') {
            // Check expiry first
            if ($attempt['duration_minutes']) {
                $started = strtotime($attempt['started_at']);
                $expiry = $started + ((int) $attempt['duration_minutes'] * 60);
                if (time() > $expiry) {
                    $this->attemptService->submitAttempt($id, (int) $user['id']);
                    // Refresh record
                    $attempt = $this->attemptService->getAttemptById($id);
                } else {
                    $this->redirect("/quiz-attempts/{$id}");
                }
            } else {
                $this->redirect("/quiz-attempts/{$id}");
            }
        }

        $questions = [];
        $savedAnswers = [];

        if (filter_var($attempt['show_result'] ?? true, FILTER_VALIDATE_BOOL)) {
            $questions = $this->questionService->getQuestionsForQuiz((int) $attempt['quiz_id']);
            $savedAnswers = $this->attemptService->getAttemptAnswers($id);
            
            // Map saved answers by question id
            $answersMap = [];
            foreach ($savedAnswers as $ans) {
                $answersMap[(int) $ans['question_id']] = $ans;
            }

            $questionsWithDetails = [];
            $triggerOptionsMap = [];

            foreach ($questions as $question) {
                $details = $this->questionService->getQuestionById((int) $question['id']);
                
                $options = $details['options'] ?? [];
                foreach ($options as $opt) {
                    $rqIds = $opt['related_question_ids'] ?? [];
                    foreach ($rqIds as $rqId) {
                        $triggerOptionsMap[(int) $rqId][] = (int) $opt['id'];
                    }
                }

                if (!filter_var($attempt['show_correct_answer'] ?? true, FILTER_VALIDATE_BOOL)) {
                    foreach ($options as &$opt) {
                        unset($opt['is_correct']);
                    }
                }
                
                $question['options'] = $options;
                $question['accepted_answers'] = filter_var($attempt['show_correct_answer'] ?? true, FILTER_VALIDATE_BOOL) 
                    ? ($details['accepted_answers'] ?? []) 
                    : [];

                $question['user_answer'] = $answersMap[(int) $question['id']] ?? null;
                $questionsWithDetails[] = $question;
            }

            $visibleQuestions = [];
            foreach ($questionsWithDetails as $q) {
                $qId = (int) $q['id'];
                $isVisible = false;
                if (!isset($triggerOptionsMap[$qId])) {
                    $isVisible = true;
                } else {
                    foreach ($triggerOptionsMap[$qId] as $triggerOptId) {
                        foreach ($savedAnswers as $ans) {
                            if (in_array($triggerOptId, $ans['selected_option_ids'])) {
                                $isVisible = true;
                                break 2;
                            }
                        }
                    }
                }
                if ($isVisible) {
                    $visibleQuestions[] = $q;
                }
            }
            $questions = $visibleQuestions;
        }

        return $this->view('quiz/result', [
            'attempt' => $attempt,
            'questions' => $questions,
        ]);
    }

    // --- JSON API Endpoints ---

    public function apiIndex(): never
    {
        $quizzes = $this->quizService->getPublishedQuizzes();
        app()->response()->json($quizzes);
    }

    public function apiShow(string $id): never
    {
        $quiz = $this->quizService->getQuizById((int) $id);
        if (!$quiz || $quiz['status'] !== 'published') {
            app()->response()->json(['message' => 'Quiz not found.'], 404);
        }
        
        // Exclude internal dates or status
        app()->response()->json([
            'id' => $quiz['id'],
            'title' => $quiz['title'],
            'description' => $quiz['description'],
            'duration_minutes' => $quiz['duration_minutes'],
            'pass_score' => $quiz['pass_score'],
        ]);
    }

    public function apiStart(string $id): never
    {
        $user = app()->session()->get('user');
        if (!$user) {
            app()->response()->json(['message' => 'Unauthenticated.'], 401);
        }

        try {
            $attemptId = $this->attemptService->startAttempt((int) $id, (int) $user['id']);
            $attempt = $this->attemptService->getAttemptById($attemptId);
            app()->response()->json($attempt);
        } catch (\Throwable $e) {
            app()->response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function apiGetAttempt(string $attemptId): never
    {
        $user = app()->session()->get('user');
        if (!$user) {
            app()->response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $id = (int) $attemptId;
        $attempt = $this->attemptService->getAttemptById($id);
        if (!$attempt) {
            app()->response()->json(['message' => 'Attempt not found.'], 404);
        }

        if (isset($attempt['user_id']) && (int) $attempt['user_id'] !== (int) $user['id']) {
            app()->response()->json(['message' => 'Unauthorized.'], 403);
        }

        $questions = $this->questionService->getQuestionsForQuiz((int) $attempt['quiz_id']);
        foreach ($questions as &$question) {
            $details = $this->questionService->getQuestionById((int) $question['id']);
            
            // STRIP is_correct to prevent cheating!
            $options = $details['options'] ?? [];
            foreach ($options as &$opt) {
                unset($opt['is_correct']);
            }
            $question['options'] = $options;
            $question['accepted_answers'] = []; // Never expose
        }

        $savedAnswers = $this->attemptService->getAttemptAnswers($id);

        app()->response()->json([
            'attempt' => $attempt,
            'questions' => $questions,
            'saved_answers' => $savedAnswers,
        ]);
    }

    public function apiSaveAnswer(string $attemptId): never
    {
        $user = app()->session()->get('user');
        if (!$user) {
            app()->response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $payload = app()->request()->all();
        try {
            $result = $this->attemptService->saveAnswer((int) $attemptId, (int) $user['id'], $payload);
            // Never expose 'is_correct' or 'awarded_score' on dynamic save answers APIs to avoid client side cheating!
            app()->response()->json(['success' => true, 'message' => 'Answer saved successfully.']);
        } catch (\Throwable $e) {
            app()->response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function apiSubmit(string $attemptId): never
    {
        $user = app()->session()->get('user');
        if (!$user) {
            app()->response()->json(['message' => 'Unauthenticated.'], 401);
        }

        try {
            $result = $this->attemptService->submitAttempt((int) $attemptId, (int) $user['id']);
            app()->response()->json($result);
        } catch (\Throwable $e) {
            app()->response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function apiResult(string $attemptId): never
    {
        $user = app()->session()->get('user');
        if (!$user) {
            app()->response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $id = (int) $attemptId;
        $attempt = $this->attemptService->getAttemptById($id);
        if (!$attempt) {
            app()->response()->json(['message' => 'Attempt not found.'], 404);
        }

        if (isset($attempt['user_id']) && (int) $attempt['user_id'] !== (int) $user['id']) {
            app()->response()->json(['message' => 'Unauthorized.'], 403);
        }

        if ($attempt['status'] === 'in_progress') {
            app()->response()->json(['message' => 'Attempt is still in progress.'], 400);
        }

        $result = [
            'attempt' => $attempt,
        ];

        if (filter_var($attempt['show_result'] ?? true, FILTER_VALIDATE_BOOL)) {
            $questions = $this->questionService->getQuestionsForQuiz((int) $attempt['quiz_id']);
            $savedAnswers = $this->attemptService->getAttemptAnswers($id);
            
            $answersMap = [];
            foreach ($savedAnswers as $ans) {
                $answersMap[(int) $ans['question_id']] = $ans;
            }

            $questionsWithDetails = [];
            $triggerOptionsMap = [];

            foreach ($questions as $question) {
                $details = $this->questionService->getQuestionById((int) $question['id']);
                
                $options = $details['options'] ?? [];
                foreach ($options as $opt) {
                    $rqIds = $opt['related_question_ids'] ?? [];
                    foreach ($rqIds as $rqId) {
                        $triggerOptionsMap[(int) $rqId][] = (int) $opt['id'];
                    }
                }

                if (!filter_var($attempt['show_correct_answer'] ?? true, FILTER_VALIDATE_BOOL)) {
                    foreach ($options as &$opt) {
                        unset($opt['is_correct']);
                    }
                }
                
                $question['options'] = $options;
                $question['accepted_answers'] = filter_var($attempt['show_correct_answer'] ?? true, FILTER_VALIDATE_BOOL) 
                    ? ($details['accepted_answers'] ?? []) 
                    : [];

                $question['user_answer'] = $answersMap[(int) $question['id']] ?? null;
                $questionsWithDetails[] = $question;
            }

            $visibleQuestions = [];
            foreach ($questionsWithDetails as $q) {
                $qId = (int) $q['id'];
                $isVisible = false;
                if (!isset($triggerOptionsMap[$qId])) {
                    $isVisible = true;
                } else {
                    foreach ($triggerOptionsMap[$qId] as $triggerOptId) {
                        foreach ($savedAnswers as $ans) {
                            if (in_array($triggerOptId, $ans['selected_option_ids'])) {
                                $isVisible = true;
                                break 2;
                            }
                        }
                    }
                }
                if ($isVisible) {
                    $visibleQuestions[] = $q;
                }
            }
            $questions = $visibleQuestions;

            $result['questions'] = $questions;
        }

        app()->response()->json($result);
    }
}
