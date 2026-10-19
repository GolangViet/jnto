<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\QuestionRepository;
use Core\Database;

final class QuestionService
{
    private QuestionRepository $questionRepository;

    public function __construct()
    {
        $this->questionRepository = new QuestionRepository();
    }

    public function getQuestionsForQuiz(int $quizId): array
    {
        return $this->questionRepository->getQuestionsForQuiz($quizId);
    }

    public function getQuestionById(int $id): ?array
    {
        $question = $this->questionRepository->find($id);
        if ($question) {
            $question['options'] = $this->questionRepository->getOptionsForQuestion($id);
            $question['accepted_answers'] = $this->questionRepository->getAcceptedAnswersForQuestion($id);
        }
        return $question;
    }

    /**
     * Create a question and its options or accepted answers in a transaction.
     *
     * @param array $data
     * @return int Created question ID
     */
    public function createQuestion(array $data): int
    {
        $db = Database::connection();
        $db->beginTransaction();

        try {
            $questionId = $this->questionRepository->createQuestion($data);

            $type = $data['type'] ?? '';
            if (in_array($type, ['single_choice', 'multiple_choice', 'true_false'], true)) {
                $options = $data['options'] ?? [];
                $this->questionRepository->saveOptions($questionId, $options);
            } elseif ($type === 'open_text') {
                $acceptedAnswers = $data['accepted_answers'] ?? [];
                $this->questionRepository->saveAcceptedAnswers($questionId, $acceptedAnswers);
            }

            $db->commit();
            return $questionId;
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Update a question and replace its options or accepted answers in a transaction.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateQuestion(int $id, array $data): bool
    {
        $db = Database::connection();
        $db->beginTransaction();

        try {
            $updated = $this->questionRepository->updateQuestion($id, $data);

            $type = $data['type'] ?? '';
            if (in_array($type, ['single_choice', 'multiple_choice', 'true_false'], true)) {
                $options = $data['options'] ?? [];
                $this->questionRepository->saveOptions($id, $options);
            } elseif ($type === 'open_text') {
                $acceptedAnswers = $data['accepted_answers'] ?? [];
                $this->questionRepository->saveAcceptedAnswers($id, $acceptedAnswers);
            }

            $db->commit();
            return $updated;
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Delete a question.
     *
     * @param int $id
     * @return bool
     */
    public function deleteQuestion(int $id): bool
    {
        $db = Database::connection();
        $db->beginTransaction();

        try {
            $deleted = $this->questionRepository->delete($id);
            $db->commit();
            return $deleted;
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Reorder quiz questions.
     *
     * @param array $orders Array of ['id' => X, 'display_order' => Y]
     * @return void
     */
    public function reorderQuestions(array $orders): void
    {
        $this->questionRepository->reorderQuestions($orders);
    }
}
