<?php

declare(strict_types=1);

use Core\Database;

return new class {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $db = Database::connection();

        $db->exec("
            CREATE TABLE IF NOT EXISTS cms.user_facebook_posts (
                id SERIAL PRIMARY KEY,
                user_id INT NOT NULL REFERENCES cms.users(id) ON DELETE CASCADE,
                facebook_url VARCHAR(1024) NOT NULL,
                main_survey_quiz_id INT NULL REFERENCES cms.quizzes(id) ON DELETE SET NULL,
                main_survey_attempt_id BIGINT NULL REFERENCES cms.quiz_attempts(id) ON DELETE SET NULL,
                main_quiz_quiz_id INT NULL REFERENCES cms.quizzes(id) ON DELETE SET NULL,
                main_quiz_attempt_id BIGINT NULL REFERENCES cms.quiz_attempts(id) ON DELETE SET NULL,
                main_open_quiz_id INT NULL REFERENCES cms.quizzes(id) ON DELETE SET NULL,
                main_open_attempt_id BIGINT NULL REFERENCES cms.quiz_attempts(id) ON DELETE SET NULL,
                score NUMERIC(10,2) NOT NULL DEFAULT 0.00,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT uq_user_facebook_posts_user UNIQUE (user_id)
            )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $db = Database::connection();
        $db->exec("DROP TABLE IF EXISTS cms.user_facebook_posts CASCADE");
    }
};
