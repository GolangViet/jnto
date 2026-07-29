<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\QuizRepository;
use App\Repositories\QuestionRepository;
use Core\Database;

final class QuizService
{
    private QuizRepository $quizRepository;
    private QuestionRepository $questionRepository;

    public function __construct()
    {
        $this->quizRepository = new QuizRepository();
        $this->questionRepository = new QuestionRepository();
    }

    public function getAllQuizzes(string $orderBy = 'id DESC'): array
    {
        return $this->quizRepository->all($orderBy);
    }

    public function getQuizzesWithStats(): array
    {
        return $this->quizRepository->getQuizzesWithStats();
    }

    public function getPublishedQuizzes(): array
    {
        return $this->quizRepository->getPublished();
    }

    public function getQuizById(int $id): ?array
    {
        return $this->quizRepository->find($id);
    }

    public function createQuiz(array $data): int
    {
        return $this->quizRepository->create($data);
    }

    public function updateQuiz(int $id, array $data): bool
    {
        return $this->quizRepository->update($id, $data);
    }

    public function deleteQuiz(int $id): bool
    {
        return $this->quizRepository->delete($id);
    }

    /**
     * Duplicate a quiz, including questions, options, and accepted answers, in a transaction.
     *
     * @param int $id Quiz ID to duplicate
     * @return int The ID of the newly duplicated quiz
     */
    public function duplicateQuiz(int $id): int
    {
        $quiz = $this->quizRepository->find($id);
        if (!$quiz) {
            throw new \RuntimeException("Quiz to duplicate not found.");
        }

        $db = Database::connection();
        $db->beginTransaction();

        try {
            // 1. Create duplicate quiz in draft status
            $newData = $quiz;
            $newData['title'] = $quiz['title'] . ' (Copy)';
            $newData['status'] = 'draft';
            $newData['created_by'] = app()->session()->get('user')['id'] ?? null;
            $newQuizId = $this->quizRepository->create($newData);

            // 2. Load all questions for source quiz
            $questions = $this->questionRepository->getQuestionsForQuiz($id);
            foreach ($questions as $question) {
                // Duplicate question
                $newQuestionData = $question;
                $newQuestionData['quiz_id'] = $newQuizId;
                $newQuestionId = $this->questionRepository->createQuestion($newQuestionData);

                // Duplicate options
                $options = $this->questionRepository->getOptionsForQuestion((int) $question['id']);
                if (!empty($options)) {
                    $this->questionRepository->saveOptions($newQuestionId, $options);
                }

                // Duplicate accepted answers
                $acceptedAnswers = $this->questionRepository->getAcceptedAnswersForQuestion((int) $question['id']);
                if (!empty($acceptedAnswers)) {
                    $this->questionRepository->saveAcceptedAnswers($newQuestionId, $acceptedAnswers);
                }
            }

            $db->commit();
            return $newQuizId;
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
