<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\UserFacebookPost;
use Core\Model;
use Core\Database;

final class UserFacebookPostRepository extends BaseRepository
{
    protected function getModel(): Model
    {
        return new UserFacebookPost();
    }

    /**
     * Save or update the Facebook post tracking record.
     *
     * @param array $data
     * @return bool
     */
    public function savePost(array $data): bool
    {
        $db = Database::connection();
        $sql = "INSERT INTO cms.user_facebook_posts (
            user_id, 
            facebook_url, 
            main_survey_quiz_id, 
            main_survey_attempt_id, 
            main_quiz_quiz_id, 
            main_quiz_attempt_id, 
            main_open_quiz_id, 
            main_open_attempt_id, 
            score, 
            updated_at
        ) VALUES (
            :user_id, 
            :facebook_url, 
            :main_survey_quiz_id, 
            :main_survey_attempt_id, 
            :main_quiz_quiz_id, 
            :main_quiz_attempt_id, 
            :main_open_quiz_id, 
            :main_open_attempt_id, 
            :score, 
            CURRENT_TIMESTAMP
        )
        ON CONFLICT (user_id) DO UPDATE SET
            facebook_url = EXCLUDED.facebook_url,
            main_survey_quiz_id = EXCLUDED.main_survey_quiz_id,
            main_survey_attempt_id = EXCLUDED.main_survey_attempt_id,
            main_quiz_quiz_id = EXCLUDED.main_quiz_quiz_id,
            main_quiz_attempt_id = EXCLUDED.main_quiz_attempt_id,
            main_open_quiz_id = EXCLUDED.main_open_quiz_id,
            main_open_attempt_id = EXCLUDED.main_open_attempt_id,
            score = EXCLUDED.score,
            updated_at = CURRENT_TIMESTAMP";

        $stmt = $db->prepare($sql);
        return $stmt->execute([
            'user_id' => (int) $data['user_id'],
            'facebook_url' => $data['facebook_url'],
            'main_survey_quiz_id' => $data['main_survey_quiz_id'] !== null ? (int) $data['main_survey_quiz_id'] : null,
            'main_survey_attempt_id' => $data['main_survey_attempt_id'] !== null ? (int) $data['main_survey_attempt_id'] : null,
            'main_quiz_quiz_id' => $data['main_quiz_quiz_id'] !== null ? (int) $data['main_quiz_quiz_id'] : null,
            'main_quiz_attempt_id' => $data['main_quiz_attempt_id'] !== null ? (int) $data['main_quiz_attempt_id'] : null,
            'main_open_quiz_id' => $data['main_open_quiz_id'] !== null ? (int) $data['main_open_quiz_id'] : null,
            'main_open_attempt_id' => $data['main_open_attempt_id'] !== null ? (int) $data['main_open_attempt_id'] : null,
            'score' => (float) ($data['score'] ?? 0.00),
        ]);
    }

    /**
     * Find a user's Facebook post submission.
     *
     * @param int $userId
     * @return array|null
     */
    public function findByUserId(int $userId): ?array
    {
        $db = Database::connection();
        $stmt = $db->prepare("SELECT * FROM cms.user_facebook_posts WHERE user_id = :user_id LIMIT 1");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch() ?: null;
    }
}
