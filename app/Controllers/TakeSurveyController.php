<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\QuizAttemptService;
use App\Services\QuestionService;
use App\Repositories\UserFacebookPostRepository;
use Core\Controller;
use Core\Csrf;

final class TakeSurveyController extends Controller
{
    private QuizAttemptService $attemptService;
    private QuestionService $questionService;
    private UserFacebookPostRepository $facebookPostRepository;

    public function __construct()
    {
        $this->attemptService = new QuizAttemptService();
        $this->questionService = new QuestionService();
        $this->facebookPostRepository = new UserFacebookPostRepository();
    }

    /**
     * Render the survey taking page (dynamic if main_survey_quiz_id is set, otherwise static fallback).
     *
     * @return string Rendered HTML.
     */
    public function detailSurvey(): string
    {
        $mainSurveyId = setting('main_survey_quiz_id');
        if (!$mainSurveyId) {
            return $this->view('take-survey/detail-survey');
        }

        $user = app()->session()->get('user');
        if (!$user) {
            $this->redirect('/login');
        }

        try {
            $attemptId = $this->attemptService->startAttempt((int) $mainSurveyId, (int) $user['id']);
            $attempt = $this->attemptService->getAttemptById($attemptId);
            if (!$attempt) {
                http_response_code(404);
                return 'Survey attempt not found.';
            }

            if ($attempt['status'] !== 'in_progress') {
                $this->redirect('/take-questions');
            }

            $questions = $this->questionService->getQuestionsForQuiz((int) $attempt['quiz_id']);
            foreach ($questions as &$question) {
                $details = $this->questionService->getQuestionById((int) $question['id']);
                $options = $details['options'] ?? [];
                foreach ($options as &$opt) {
                    unset($opt['is_correct']);
                }

                $question['options'] = $options;
                $question['accepted_answers'] = [];
            }

            $savedAnswers = $this->attemptService->getAttemptAnswers($attemptId);

            return $this->view('take-survey/detail-survey', [
                'attempt' => $attempt,
                'questions' => $questions,
                'savedAnswers' => $savedAnswers,
            ]);
        } catch (\Throwable $e) {
            app()->session()->flash('error', $e->getMessage());
            return $this->view('take-survey/detail-survey');
        }
    }

    /**
     * Render the static detail questions page or redirect if dynamic survey is enabled.
     *
     * @return string Rendered HTML.
     */
    public function detailQuestions(): string
    {
        $user = app()->session()->get('user');
        if (!$user) {
            $this->redirect('/login');
        }

        $mainSurveyId = setting('main_survey_quiz_id');
        if ($mainSurveyId) {
            $db = \Core\Database::connection();
            $stmt = $db->prepare("SELECT COUNT(*) FROM cms.quiz_attempts WHERE quiz_id = :quiz_id AND user_id = :user_id AND status = 'submitted'");
            $stmt->execute([
                'quiz_id' => (int) $mainSurveyId,
                'user_id' => (int) $user['id']
            ]);
            $hasCompleted = (int) $stmt->fetchColumn() > 0;

            if (!$hasCompleted) {
                $this->redirect('/take-survey');
            }
        }

        $mainQuizId = setting('main_quiz_quiz_id');
        $mainOpenId = setting('main_open_quiz_id');
        $data = [
            'mainQuizId' => $mainQuizId,
            'mainOpenId' => $mainOpenId,
            'quizAttempt' => null,
            'quizQuestions' => [],
            'quizSavedAnswers' => [],
            'openAttempt' => null,
            'openQuestions' => [],
            'openSavedAnswers' => [],
        ];

        try {
            $quizFinished = false;
            $openFinished = false;

            if ($mainQuizId) {
                $quizAttemptId = $this->attemptService->startAttempt((int) $mainQuizId, (int) $user['id']);
                $attempt = $this->attemptService->getAttemptById($quizAttemptId);
                $data['quizAttempt'] = $attempt;

                if ($attempt && $attempt['status'] !== 'in_progress') {
                    $quizFinished = true;
                }

                $questions = $this->questionService->getQuestionsForQuiz((int) $mainQuizId);
                foreach ($questions as &$question) {
                    $details = $this->questionService->getQuestionById((int) $question['id']);
                    $options = $details['options'] ?? [];
                    foreach ($options as &$opt) {
                        unset($opt['is_correct']);
                    }
                    $question['options'] = $options;
                    $question['accepted_answers'] = [];
                }
                $data['quizQuestions'] = $questions;
                $data['quizSavedAnswers'] = $this->attemptService->getAttemptAnswers($quizAttemptId);
            } else {
                $quizFinished = true;
            }

            if ($mainOpenId) {
                $openAttemptId = $this->attemptService->startAttempt((int) $mainOpenId, (int) $user['id']);
                $attempt = $this->attemptService->getAttemptById($openAttemptId);
                $data['openAttempt'] = $attempt;

                if ($attempt && $attempt['status'] !== 'in_progress') {
                    $openFinished = true;
                }

                $questions = $this->questionService->getQuestionsForQuiz((int) $mainOpenId);
                foreach ($questions as &$question) {
                    $details = $this->questionService->getQuestionById((int) $question['id']);
                    $options = $details['options'] ?? [];
                    foreach ($options as &$opt) {
                        unset($opt['is_correct']);
                    }
                    $question['options'] = $options;
                    $question['accepted_answers'] = [];
                }
                $data['openQuestions'] = $questions;
                $data['openSavedAnswers'] = $this->attemptService->getAttemptAnswers($openAttemptId);
            } else {
                $openFinished = true;
            }

            if ($quizFinished && $openFinished && ($mainQuizId || $mainOpenId)) {
                $this->redirect('/confirm-post');
            }

            return $this->view('take-survey/detail-questions', $data);
        } catch (\Throwable $e) {
            app()->session()->flash('error', $e->getMessage());
            return $this->view('take-survey/detail-questions', $data);
        }
    }

    /**
     * Render the static confirm post page or redirect if dynamic survey is enabled.
     *
     * @return string Rendered HTML.
     */
    public function confirmPost(): string
    {
        $mainSurveyId = setting('main_survey_quiz_id');
        if ($mainSurveyId) {
            $user = app()->session()->get('user');
            if (!$user) {
                $this->redirect('/login');
            }

            $db = \Core\Database::connection();
            $stmt = $db->prepare("SELECT COUNT(*) FROM cms.quiz_attempts WHERE quiz_id = :quiz_id AND user_id = :user_id AND status = 'submitted'");
            $stmt->execute([
                'quiz_id' => (int) $mainSurveyId,
                'user_id' => (int) $user['id']
            ]);
            $hasCompleted = (int) $stmt->fetchColumn() > 0;

            if (!$hasCompleted) {
                $this->redirect('/take-survey');
            }
        }

        $user = app()->session()->get('user');
        $existingPost = null;
        if ($user) {
            $existingPost = $this->facebookPostRepository->findByUserId((int) $user['id']);
        }

        return $this->view('take-survey/confirm-post', ['existingPost' => $existingPost]);
    }

    /**
     * Handle the Facebook post link form submission.
     *
     * @return never
     */
    public function submitPost(): never
    {
        $request = app()->request();
        Csrf::verify($request);

        $user = app()->session()->get('user');
        if (!$user) {
            $this->redirect('/login');
        }

        $facebookUrl = trim((string) $request->input('facebook_url'));
        $errors = [];

        if ($facebookUrl === '') {
            $errors['facebook_url'][] = 'Vui lòng nhập đường dẫn bài đăng Facebook.';
        } elseif (!filter_var($facebookUrl, FILTER_VALIDATE_URL)) {
            $errors['facebook_url'][] = 'Đường dẫn bài đăng Facebook không đúng định dạng.';
        } else {
            $host = parse_url($facebookUrl, PHP_URL_HOST);
            if (!$host || !preg_match('/^(?:[a-z0-9\-]+\.)*(?:facebook\.com|fb\.com)$/i', $host)) {
                $errors['facebook_url'][] = 'Đường dẫn phải là liên kết bài viết Facebook hợp lệ (ví dụ: facebook.com hoặc fb.com).';
            }
        }

        if (!empty($errors)) {
            app()->session()->flash('errors', $errors);
            app()->session()->flashOldInput(['facebook_url' => $facebookUrl]);
            $this->redirect('/confirm-post');
        }

        // Fetch settings for tracking
        $mainSurveyId = setting('main_survey_quiz_id');
        $mainQuizId = setting('main_quiz_quiz_id');
        $mainOpenId = setting('main_open_quiz_id');

        $db = \Core\Database::connection();

        // Helper to query the latest submitted attempt for a given quiz ID
        $getAttempt = function(?string $quizIdSetting) use ($db, $user): ?array {
            if (!$quizIdSetting) {
                return null;
            }
            $stmt = $db->prepare("
                SELECT id, score 
                FROM cms.quiz_attempts 
                WHERE quiz_id = :quiz_id 
                  AND user_id = :user_id 
                  AND status = 'submitted' 
                ORDER BY id DESC 
                LIMIT 1
            ");
            $stmt->execute([
                'quiz_id' => (int) $quizIdSetting,
                'user_id' => (int) $user['id']
            ]);
            return $stmt->fetch() ?: null;
        };

        $surveyAttempt = $getAttempt($mainSurveyId);
        $quizAttempt = $getAttempt($mainQuizId);
        $openAttempt = $getAttempt($mainOpenId);

        $score = $quizAttempt ? (float) $quizAttempt['score'] : 0.00;

        try {
            $this->facebookPostRepository->savePost([
                'user_id' => (int) $user['id'],
                'facebook_url' => $facebookUrl,
                'main_survey_quiz_id' => $mainSurveyId ? (int) $mainSurveyId : null,
                'main_survey_attempt_id' => $surveyAttempt ? (int) $surveyAttempt['id'] : null,
                'main_quiz_quiz_id' => $mainQuizId ? (int) $mainQuizId : null,
                'main_quiz_attempt_id' => $quizAttempt ? (int) $quizAttempt['id'] : null,
                'main_open_quiz_id' => $mainOpenId ? (int) $mainOpenId : null,
                'main_open_attempt_id' => $openAttempt ? (int) $openAttempt['id'] : null,
                'score' => $score,
            ]);

            app()->session()->flash('success', 'Nộp bài đăng Facebook thành công!');
            $this->redirect('/thank-you');
        } catch (\Throwable $e) {
            app()->session()->flash('error', 'Có lỗi xảy ra khi lưu thông tin: ' . $e->getMessage());
            app()->session()->flashOldInput(['facebook_url' => $facebookUrl]);
            $this->redirect('/confirm-post');
        }
    }
}

