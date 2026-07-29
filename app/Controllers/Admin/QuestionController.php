<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Services\QuestionService;
use App\Services\QuizService;
use Core\Controller;

final class QuestionController extends Controller
{
    private QuestionService $questionService;
    private QuizService $quizService;

    public function __construct()
    {
        $this->questionService = new QuestionService();
        $this->quizService = new QuizService();
    }

    /**
     * Web Page: Render Question Builder page.
     */
    public function builder(string $quizId): string
    {
        $quiz = $this->quizService->getQuizById((int) $quizId);
        if (!$quiz) {
            http_response_code(404);
            return 'Quiz not found.';
        }

        $questions = $this->questionService->getQuestionsForQuiz((int) $quizId);
        
        return $this->view('admin/questions/builder', [
            'quiz' => $quiz,
            'questions' => $questions,
        ]);
    }

    // --- JSON API Endpoints ---

    public function apiIndex(string $quizId): never
    {
        $questions = $this->questionService->getQuestionsForQuiz((int) $quizId);
        foreach ($questions as &$question) {
            $questionDetails = $this->questionService->getQuestionById((int) $question['id']);
            $question['options'] = $questionDetails['options'] ?? [];
            $question['accepted_answers'] = $questionDetails['accepted_answers'] ?? [];
        }
        app()->response()->json($questions);
    }

    public function apiStore(string $quizId): never
    {
        $data = app()->request()->all();
        $data['quiz_id'] = (int) $quizId;

        $errors = $this->validateQuestionData($data);
        if (!empty($errors)) {
            app()->response()->json(['errors' => $errors], 422);
        }

        try {
            $questionId = $this->questionService->createQuestion($data);
            $question = $this->questionService->getQuestionById($questionId);
            app()->response()->json($question, 201);
        } catch (\Throwable $e) {
            app()->response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function apiShow(string $id): never
    {
        $question = $this->questionService->getQuestionById((int) $id);
        if (!$question) {
            app()->response()->json(['message' => 'Question not found.'], 404);
        }
        app()->response()->json($question);
    }

    public function apiUpdate(string $id): never
    {
        $questionId = (int) $id;
        $question = $this->questionService->getQuestionById($questionId);
        if (!$question) {
            app()->response()->json(['message' => 'Question not found.'], 404);
        }

        $data = app()->request()->all();
        $data['quiz_id'] = (int) $question['quiz_id'];

        $errors = $this->validateQuestionData($data);
        if (!empty($errors)) {
            app()->response()->json(['errors' => $errors], 422);
        }

        try {
            $this->questionService->updateQuestion($questionId, $data);
            $updated = $this->questionService->getQuestionById($questionId);
            app()->response()->json($updated);
        } catch (\Throwable $e) {
            app()->response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function apiDestroy(string $id): never
    {
        $questionId = (int) $id;
        $question = $this->questionService->getQuestionById($questionId);
        if (!$question) {
            app()->response()->json(['message' => 'Question not found.'], 404);
        }

        $this->questionService->deleteQuestion($questionId);
        app()->response()->json(['message' => 'Question deleted successfully.']);
    }

    public function apiReorder(string $quizId): never
    {
        $payload = app()->request()->all();
        $questions = $payload['questions'] ?? [];

        if (!is_array($questions)) {
            app()->response()->json(['message' => 'Invalid reorder data.'], 400);
        }

        $this->questionService->reorderQuestions($questions);
        app()->response()->json(['message' => 'Questions reordered successfully.']);
    }

    // --- Validation Helper ---

    private function validateQuestionData(array $data): array
    {
        $errors = [];

        if (empty($data['question_text'])) {
            $errors['question_text'][] = 'Question text is required.';
        }

        $type = $data['type'] ?? '';
        $allowedTypes = ['single_choice', 'multiple_choice', 'open_text', 'true_false'];
        if (!in_array($type, $allowedTypes, true)) {
            $errors['type'][] = 'Type must be one of: ' . implode(', ', $allowedTypes);
            return $errors;
        }

        if (isset($data['score'])) {
            $score = (float) $data['score'];
            if ($score < 0.0) {
                $errors['score'][] = 'Score must be zero or greater.';
            }
        }

        if (isset($data['display_order'])) {
            $order = (int) $data['display_order'];
            if ($order < 0) {
                $errors['display_order'][] = 'Display order must be zero or greater.';
            }
        }

        // Validate choice options
        if (in_array($type, ['single_choice', 'multiple_choice', 'true_false'], true)) {
            $options = $data['options'] ?? [];
            if (!is_array($options)) {
                $errors['options'][] = 'Options must be provided as an array.';
            } else {
                $optionCount = count($options);
                $correctCount = 0;
                $hasEmptyOption = false;

                foreach ($options as $opt) {
                    if (!isset($opt['option_text']) || trim((string) $opt['option_text']) === '') {
                        $hasEmptyOption = true;
                    }
                    $isCorrect = filter_var($opt['is_correct'] ?? false, FILTER_VALIDATE_BOOL);
                    if ($isCorrect) {
                        $correctCount++;
                    }
                }

                if ($hasEmptyOption) {
                    $errors['options'][] = 'All option text fields are required.';
                }

                if ($type === 'single_choice') {
                    if ($optionCount < 2) {
                        $errors['options'][] = 'At least two options are required.';
                    }
                    if ($correctCount !== 1) {
                        $errors['options'][] = 'Exactly one option must be correct.';
                    }
                } elseif ($type === 'multiple_choice') {
                    if ($optionCount < 2) {
                        $errors['options'][] = 'At least two options are required.';
                    }
                    if ($correctCount < 1) {
                        $errors['options'][] = 'At least one option must be correct.';
                    }
                } elseif ($type === 'true_false') {
                    if ($optionCount !== 2) {
                        $errors['options'][] = 'Exactly two options are required.';
                    }
                    if ($correctCount !== 1) {
                        $errors['options'][] = 'Exactly one option must be correct.';
                    }
                }
            }
        } elseif ($type === 'open_text') {
            $accepted = $data['accepted_answers'] ?? [];
            if (!is_array($accepted) || count($accepted) < 1) {
                $errors['accepted_answers'][] = 'At least one accepted answer is required.';
            } else {
                $hasEmptyAnswer = false;
                foreach ($accepted as $ans) {
                    if (!isset($ans['answer_text']) || trim((string) $ans['answer_text']) === '') {
                        $hasEmptyAnswer = true;
                    }
                }
                if ($hasEmptyAnswer) {
                    $errors['accepted_answers'][] = 'Accepted answers must not be empty.';
                }
            }
        }

        return $errors;
    }
}
