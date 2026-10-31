<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Services\QuizAttemptService;
use App\Services\QuestionService;
use Core\Controller;

final class QuizAttemptController extends Controller
{
    private QuizAttemptService $attemptService;
    private QuestionService $questionService;

    public function __construct()
    {
        $this->attemptService = new QuizAttemptService();
        $this->questionService = new QuestionService();
    }

    /**
     * Display a specific user's attempt details for administration tracking.
     *
     * @param string $id Attempt ID.
     * @return string Rendered HTML.
     */
    public function show(string $id): string
    {
        $attemptId = (int) $id;
        $attempt = $this->attemptService->getAttemptById($attemptId);
        
        if (!$attempt) {
            http_response_code(404);
            return 'Quiz attempt not found.';
        }

        // Get questions for this quiz
        $questions = $this->questionService->getQuestionsForQuiz((int) $attempt['quiz_id']);
        
        // Get user saved answers
        $savedAnswers = $this->attemptService->getAttemptAnswers($attemptId);
        
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

            $question['options'] = $options;
            $question['accepted_answers'] = $details['accepted_answers'] ?? [];
            $question['user_answer'] = $answersMap[(int) $question['id']] ?? null;
            $questionsWithDetails[] = $question;
        }

        // Filter visible questions if there are conditional logic questions
        $visibleQuestions = [];
        foreach ($questionsWithDetails as $q) {
            $qId = (int) $q['id'];
            $isVisible = false;
            if (!isset($triggerOptionsMap[$qId])) {
                $isVisible = true;
            } else {
                foreach ($triggerOptionsMap[$qId] as $triggerOptId) {
                    foreach ($savedAnswers as $ans) {
                        if (in_array($triggerOptId, $ans['selected_option_ids'] ?? [])) {
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

        return $this->view('admin/attempts/show', [
            'attempt' => $attempt,
            'questions' => $visibleQuestions,
        ]);
    }

    /**
     * Delete a single quiz attempt and its answers.
     */
    public function destroy(string $id): never
    {
        $request = app()->request();
        \Core\Csrf::verify($request);

        $attemptId = (int) $id;
        $this->attemptService->deleteAttempt($attemptId);

        app()->session()->flash('success', 'Đã xóa lượt làm bài và toàn bộ câu trả lời.');
        $this->redirect('/admin/dashboard');
    }

    /**
     * Bulk delete quiz attempts and all their answers.
     */
    public function bulkDestroy(): never
    {
        $request = app()->request();
        \Core\Csrf::verify($request);

        $rawIds = $request->input('attempt_ids') ?? [];
        if (!is_array($rawIds)) {
            $rawIds = $rawIds !== '' ? explode(',', (string) $rawIds) : [];
        }

        $attemptIds = array_values(array_unique(array_filter(array_map('intval', $rawIds), fn($id) => $id > 0)));

        if (empty($attemptIds)) {
            if ($request->expectsJson()) {
                app()->response()->json(['success' => false, 'message' => 'Vui lòng chọn ít nhất một lượt làm bài.'], 422);
            }
            app()->session()->flash('error', 'Vui lòng chọn ít nhất một lượt làm bài để xóa.');
            $this->redirect('/admin/dashboard');
        }

        $deletedCount = $this->attemptService->bulkDeleteAttempts($attemptIds);

        $message = "Đã xóa {$deletedCount} lượt làm bài và toàn bộ câu trả lời liên quan.";
        if ($request->expectsJson()) {
            app()->response()->json([
                'success' => true,
                'message' => $message,
                'deleted_count' => $deletedCount,
            ]);
        }

        app()->session()->flash('success', $message);
        $this->redirect('/admin/dashboard');
    }
}

