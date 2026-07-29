<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Services\QuizService;
use Core\Controller;
use Core\Csrf;
use Core\Validator;

final class QuizController extends Controller
{
    private QuizService $quizService;

    public function __construct()
    {
        $this->quizService = new QuizService();
    }

    /**
     * Web Page: List all quizzes.
     */
    public function index(): string
    {
        $quizzes = $this->quizService->getQuizzesWithStats();
        return $this->view('admin/quizzes/index', ['quizzes' => $quizzes]);
    }

    /**
     * Web Page: Create quiz form.
     */
    public function create(): string
    {
        return $this->view('admin/quizzes/create');
    }

    /**
     * Web Page Action: Store new quiz.
     */
    public function store(): never
    {
        $request = app()->request();
        Csrf::verify($request);

        $data = $request->only([
            'title', 'description', 'status', 'duration_minutes', 'pass_score',
            'show_result', 'show_correct_answer', 'allow_resume', 'start_at', 'end_at'
        ]);

        $errors = $this->validateQuizData($data);
        if (!empty($errors)) {
            app()->session()->flash('errors', $errors);
            app()->session()->flashOldInput($data);
            $this->redirect('/admin/quizzes/create');
        }

        $data['show_result'] = isset($data['show_result']);
        $data['show_correct_answer'] = isset($data['show_correct_answer']);
        $data['allow_resume'] = isset($data['allow_resume']);
        $data['created_by'] = app()->session()->get('user')['id'] ?? null;

        $this->quizService->createQuiz($data);
        app()->session()->flash('success', 'Quiz created successfully.');
        $this->redirect('/admin/quizzes');
    }

    /**
     * Web Page: Edit quiz form.
     */
    public function edit(string $id): string
    {
        $quiz = $this->quizService->getQuizById((int) $id);
        if (!$quiz) {
            http_response_code(404);
            return 'Quiz not found.';
        }
        return $this->view('admin/quizzes/edit', ['quiz' => $quiz]);
    }

    /**
     * Web Page Action: Update quiz.
     */
    public function update(string $id): never
    {
        $request = app()->request();
        Csrf::verify($request);

        $quizId = (int) $id;
        $quiz = $this->quizService->getQuizById($quizId);
        if (!$quiz) {
            http_response_code(404);
            app()->session()->flash('error', 'Quiz not found.');
            $this->redirect('/admin/quizzes');
        }

        $data = $request->only([
            'title', 'description', 'status', 'duration_minutes', 'pass_score',
            'show_result', 'show_correct_answer', 'allow_resume', 'start_at', 'end_at'
        ]);

        $errors = $this->validateQuizData($data);
        if (!empty($errors)) {
            app()->session()->flash('errors', $errors);
            app()->session()->flashOldInput($data);
            $this->redirect("/admin/quizzes/{$quizId}/edit");
        }

        $data['show_result'] = isset($data['show_result']);
        $data['show_correct_answer'] = isset($data['show_correct_answer']);
        $data['allow_resume'] = isset($data['allow_resume']);

        $this->quizService->updateQuiz($quizId, $data);
        app()->session()->flash('success', 'Quiz updated successfully.');
        $this->redirect('/admin/quizzes');
    }

    /**
     * Web Page Action: Delete quiz.
     */
    public function destroy(string $id): never
    {
        Csrf::verify(app()->request());
        $this->quizService->deleteQuiz((int) $id);
        app()->session()->flash('success', 'Quiz deleted successfully.');
        $this->redirect('/admin/quizzes');
    }

    // --- JSON API Endpoints ---

    public function apiIndex(): never
    {
        $quizzes = $this->quizService->getAllQuizzes();
        app()->response()->json($quizzes);
    }

    public function apiStore(): never
    {
        $data = app()->request()->all();
        $errors = $this->validateQuizData($data);
        if (!empty($errors)) {
            app()->response()->json(['errors' => $errors], 422);
        }

        $data['created_by'] = app()->session()->get('user')['id'] ?? null;
        $quizId = $this->quizService->createQuiz($data);
        $quiz = $this->quizService->getQuizById($quizId);
        app()->response()->json($quiz, 217); // Or 201
    }

    public function apiShow(string $id): never
    {
        $quiz = $this->quizService->getQuizById((int) $id);
        if (!$quiz) {
            app()->response()->json(['message' => 'Quiz not found.'], 404);
        }
        app()->response()->json($quiz);
    }

    public function apiUpdate(string $id): never
    {
        $quizId = (int) $id;
        $quiz = $this->quizService->getQuizById($quizId);
        if (!$quiz) {
            app()->response()->json(['message' => 'Quiz not found.'], 404);
        }

        $data = app()->request()->all();
        $errors = $this->validateQuizData($data);
        if (!empty($errors)) {
            app()->response()->json(['errors' => $errors], 422);
        }

        $this->quizService->updateQuiz($quizId, $data);
        $updated = $this->quizService->getQuizById($quizId);
        app()->response()->json($updated);
    }

    public function apiDestroy(string $id): never
    {
        $quizId = (int) $id;
        $quiz = $this->quizService->getQuizById($quizId);
        if (!$quiz) {
            app()->response()->json(['message' => 'Quiz not found.'], 404);
        }
        $this->quizService->deleteQuiz($quizId);
        app()->response()->json(['message' => 'Quiz deleted successfully.']);
    }

    public function apiPublish(string $id): never
    {
        $quizId = (int) $id;
        $quiz = $this->quizService->getQuizById($quizId);
        if (!$quiz) {
            app()->response()->json(['message' => 'Quiz not found.'], 404);
        }

        $currentStatus = $quiz['status'] ?? 'draft';
        $newStatus = $currentStatus === 'published' ? 'inactive' : 'published';
        
        $this->quizService->updateQuiz($quizId, array_merge($quiz, ['status' => $newStatus]));
        app()->response()->json(['status' => $newStatus, 'message' => "Quiz status updated to $newStatus."]);
    }

    public function apiDuplicate(string $id): never
    {
        $quizId = (int) $id;
        try {
            $newQuizId = $this->quizService->duplicateQuiz($quizId);
            $newQuiz = $this->quizService->getQuizById($newQuizId);
            app()->response()->json([
                'message' => 'Quiz duplicated successfully.',
                'quiz' => $newQuiz
            ]);
        } catch (\Throwable $e) {
            app()->response()->json(['message' => $e->getMessage()], 500);
        }
    }

    // --- Validation Helper ---

    private function validateQuizData(array $data): array
    {
        $errors = [];
        
        $validator = new Validator();
        if (!$validator->validate($data, ['title' => 'required'])) {
            $errors = array_merge($errors, $validator->errors());
        }

        if (!empty($data['status']) && !in_array($data['status'], ['draft', 'published', 'inactive'], true)) {
            $errors['status'][] = 'Invalid status value. Allowed: draft, published, inactive';
        }

        if (isset($data['pass_score']) && $data['pass_score'] !== '') {
            $passScore = (float) $data['pass_score'];
            if ($passScore < 0.0 || $passScore > 100.0) {
                $errors['pass_score'][] = 'Pass score must be between 0 and 100.';
            }
        }

        if (isset($data['duration_minutes']) && $data['duration_minutes'] !== '') {
            $duration = (int) $data['duration_minutes'];
            if ($duration <= 0) {
                $errors['duration_minutes'][] = 'Duration must be greater than zero when provided.';
            }
        }

        if (!empty($data['start_at']) && !empty($data['end_at'])) {
            if (strtotime($data['start_at']) >= strtotime($data['end_at'])) {
                $errors['start_at'][] = 'Start date must be before end date.';
            }
        }

        return $errors;
    }
}
