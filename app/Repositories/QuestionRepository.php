<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Question;
use Core\Model;
use Core\Database;

final class QuestionRepository extends BaseRepository
{
    protected function getModel(): Model
    {
        return new Question();
    }

    /**
     * Get all questions for a specific quiz, sorted by display_order.
     *
     * @param int $quizId
     * @return array
     */
    public function getQuestionsForQuiz(int $quizId): array
    {
        $db = Database::connection();
        $stmt = $db->prepare("SELECT * FROM cms.questions WHERE quiz_id = :quiz_id ORDER BY display_order ASC, id ASC");
        $stmt->execute(['quiz_id' => $quizId]);
        return $stmt->fetchAll();
    }

    /**
     * Create a new question record.
     *
     * @param array $data
     * @return int
     */
    public function createQuestion(array $data): int
    {
        $db = Database::connection();
        $sql = "INSERT INTO cms.questions (
            quiz_id, type, question_text, explanation, score, is_required, display_order
        ) VALUES (
            :quiz_id, :type, :question_text, :explanation, :score, :is_required, :display_order
        ) RETURNING id";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            'quiz_id' => (int) $data['quiz_id'],
            'type' => $data['type'],
            'question_text' => $data['question_text'],
            'explanation' => $data['explanation'] ?? null,
            'score' => isset($data['score']) ? (float) $data['score'] : 1.0,
            'is_required' => (isset($data['is_required']) ? filter_var($data['is_required'], FILTER_VALIDATE_BOOL) : true) ? 1 : 0,
            'display_order' => isset($data['display_order']) ? (int) $data['display_order'] : 0,
        ]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Update an existing question record.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateQuestion(int $id, array $data): bool
    {
        $db = Database::connection();
        $sql = "UPDATE cms.questions SET
            type = :type,
            question_text = :question_text,
            explanation = :explanation,
            score = :score,
            is_required = :is_required,
            display_order = :display_order,
            updated_at = CURRENT_TIMESTAMP
            WHERE id = :id";

        $stmt = $db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'type' => $data['type'],
            'question_text' => $data['question_text'],
            'explanation' => $data['explanation'] ?? null,
            'score' => isset($data['score']) ? (float) $data['score'] : 1.0,
            'is_required' => (isset($data['is_required']) ? filter_var($data['is_required'], FILTER_VALIDATE_BOOL) : true) ? 1 : 0,
            'display_order' => isset($data['display_order']) ? (int) $data['display_order'] : 0,
        ]);
    }

    /**
     * Get options for a specific question.
     *
     * @param int $questionId
     * @return array
     */
    public function getOptionsForQuestion(int $questionId): array
    {
        $db = Database::connection();
        $stmt = $db->prepare("
            SELECT qo.*, 
            COALESCE(
                (SELECT json_agg(related_question_id) FROM cms.option_related_questions WHERE option_id = qo.id),
                '[]'::json
            ) as related_question_ids
            FROM cms.question_options qo
            WHERE qo.question_id = :question_id 
            ORDER BY qo.display_order ASC, qo.id ASC
        ");
        $stmt->execute(['question_id' => $questionId]);
        $rows = $stmt->fetchAll();
        foreach ($rows as &$row) {
            $row['allow_custom_text'] = filter_var($row['allow_custom_text'] ?? false, FILTER_VALIDATE_BOOL);
            if (isset($row['related_question_ids'])) {
                $row['related_question_ids'] = json_decode((string) $row['related_question_ids'], true) ?: [];
                $row['related_question_ids'] = array_map('intval', $row['related_question_ids']);
            } else {
                $row['related_question_ids'] = [];
            }
        }
        return $rows;
    }

    /**
     * Get accepted answers for an open-text question.
     *
     * @param int $questionId
     * @return array
     */
    public function getAcceptedAnswersForQuestion(int $questionId): array
    {
        $db = Database::connection();
        $stmt = $db->prepare("SELECT * FROM cms.question_accepted_answers WHERE question_id = :question_id ORDER BY id ASC");
        $stmt->execute(['question_id' => $questionId]);
        return $stmt->fetchAll();
    }

    /**
     * Sync question options.
     *
     * @param int $questionId
     * @param array $options
     * @return void
     */
    public function saveOptions(int $questionId, array $options): void
    {
        $db = Database::connection();
        
        // Remove existing options not present in the new set
        $keepIds = array_filter(array_column($options, 'id'));
        if (!empty($keepIds)) {
            $inClause = implode(',', array_map('intval', $keepIds));
            $sql = "DELETE FROM cms.question_options WHERE question_id = :question_id AND id NOT IN ($inClause)";
            $stmt = $db->prepare($sql);
            $stmt->execute(['question_id' => $questionId]);
        } else {
            $stmt = $db->prepare("DELETE FROM cms.question_options WHERE question_id = :question_id");
            $stmt->execute(['question_id' => $questionId]);
        }

        // Insert or update remaining options
        $insertSql = "INSERT INTO cms.question_options (
            question_id, option_key, option_text, image_url, is_correct, display_order, allow_custom_text
        ) VALUES (
            :question_id, :option_key, :option_text, :image_url, :is_correct, :display_order, :allow_custom_text
        ) RETURNING id";
        $updateSql = "UPDATE cms.question_options SET
            option_key = :option_key,
            option_text = :option_text,
            image_url = :image_url,
            is_correct = :is_correct,
            display_order = :display_order,
            allow_custom_text = :allow_custom_text,
            updated_at = CURRENT_TIMESTAMP
            WHERE id = :id AND question_id = :question_id";

        $insertStmt = $db->prepare($insertSql);
        $updateStmt = $db->prepare($updateSql);

        foreach ($options as $option) {
            $isCorrect = filter_var($option['is_correct'] ?? false, FILTER_VALIDATE_BOOL);
            $allowCustomText = filter_var($option['allow_custom_text'] ?? false, FILTER_VALIDATE_BOOL);
            $params = [
                'question_id' => $questionId,
                'option_key' => !empty($option['option_key']) ? $option['option_key'] : null,
                'option_text' => $option['option_text'] ?? '',
                'image_url' => $option['image_url'] ?? null,
                'is_correct' => $isCorrect ? 1 : 0, // bind parameter as int/bool compatibility
                'display_order' => isset($option['display_order']) ? (int) $option['display_order'] : 0,
                'allow_custom_text' => $allowCustomText ? 1 : 0,
            ];

            if (!empty($option['id'])) {
                $params['id'] = (int) $option['id'];
                $updateStmt->execute($params);
                $optId = (int) $option['id'];
            } else {
                $insertStmt->execute($params);
                $optId = (int) $insertStmt->fetchColumn();
            }

            // Sync option-related questions (many-to-many)
            $db->prepare("DELETE FROM cms.option_related_questions WHERE option_id = :option_id")
               ->execute(['option_id' => $optId]);

            $relatedQuestionIds = $option['related_question_ids'] ?? [];
            if (!empty($relatedQuestionIds) && is_array($relatedQuestionIds)) {
                $relStmt = $db->prepare("INSERT INTO cms.option_related_questions (option_id, related_question_id) VALUES (:option_id, :related_question_id)");
                foreach ($relatedQuestionIds as $rqId) {
                    $relStmt->execute([
                        'option_id' => $optId,
                        'related_question_id' => (int) $rqId,
                    ]);
                }
            }
        }
    }

    /**
     * Sync open-text accepted answers.
     *
     * @param int $questionId
     * @param array $acceptedAnswers
     * @return void
     */
    public function saveAcceptedAnswers(int $questionId, array $acceptedAnswers): void
    {
        $db = Database::connection();

        // Since accepted answers don't hold complex state, it is cleanest to delete and recreate them
        $stmt = $db->prepare("DELETE FROM cms.question_accepted_answers WHERE question_id = :question_id");
        $stmt->execute(['question_id' => $questionId]);

        if (empty($acceptedAnswers)) {
            return;
        }

        $sql = "INSERT INTO cms.question_accepted_answers (
            question_id, answer_text, normalized_answer, match_type, similarity_threshold
        ) VALUES (
            :question_id, :answer_text, :normalized_answer, :match_type, :similarity_threshold
        )";
        $insertStmt = $db->prepare($sql);

        foreach ($acceptedAnswers as $answer) {
            $normalized = \App\Helpers\TextNormalizer::normalize(
                $answer['answer_text'],
                false // Store standard normalized answer, accent-insensitive checks happen dynamically if configured
            );

            $insertStmt->execute([
                'question_id' => $questionId,
                'answer_text' => $answer['answer_text'],
                'normalized_answer' => $normalized,
                'match_type' => $answer['match_type'] ?? 'exact',
                'similarity_threshold' => isset($answer['similarity_threshold']) && $answer['similarity_threshold'] !== '' ? (float) $answer['similarity_threshold'] : null,
            ]);
        }
    }

    /**
     * Reorder questions.
     *
     * @param array $questions Array of ['id' => X, 'display_order' => Y]
     * @return void
     */
    public function reorderQuestions(array $questions): void
    {
        $db = Database::connection();
        $stmt = $db->prepare("UPDATE cms.questions SET display_order = :display_order WHERE id = :id");
        foreach ($questions as $q) {
            $stmt->execute([
                'id' => (int) $q['id'],
                'display_order' => (int) $q['display_order']
            ]);
        }
    }
}
