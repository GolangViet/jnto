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
            CREATE TABLE IF NOT EXISTS cms.quizzes (
                id BIGSERIAL PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description TEXT NULL,
                status VARCHAR(20) NOT NULL DEFAULT 'draft',
                duration_minutes INT NULL,
                pass_score NUMERIC(5,2) NULL,
                show_result BOOLEAN NOT NULL DEFAULT TRUE,
                show_correct_answer BOOLEAN NOT NULL DEFAULT TRUE,
                allow_resume BOOLEAN NOT NULL DEFAULT TRUE,
                start_at TIMESTAMP NULL,
                end_at TIMESTAMP NULL,
                created_by INT NULL REFERENCES cms.users(id) ON DELETE SET NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $db->exec("
            CREATE TABLE IF NOT EXISTS cms.questions (
                id BIGSERIAL PRIMARY KEY,
                quiz_id BIGINT NOT NULL REFERENCES cms.quizzes(id) ON DELETE CASCADE,
                type VARCHAR(30) NOT NULL,
                question_text TEXT NOT NULL,
                explanation TEXT NULL,
                score NUMERIC(8,2) NOT NULL DEFAULT 1,
                is_required BOOLEAN NOT NULL DEFAULT TRUE,
                display_order INT NOT NULL DEFAULT 0,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $db->exec("
            CREATE TABLE IF NOT EXISTS cms.question_options (
                id BIGSERIAL PRIMARY KEY,
                question_id BIGINT NOT NULL REFERENCES cms.questions(id) ON DELETE CASCADE,
                option_key VARCHAR(10) NULL,
                option_text TEXT NOT NULL,
                image_url TEXT NULL,
                is_correct BOOLEAN NOT NULL DEFAULT FALSE,
                display_order INT NOT NULL DEFAULT 0,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $db->exec("
            CREATE TABLE IF NOT EXISTS cms.question_accepted_answers (
                id BIGSERIAL PRIMARY KEY,
                question_id BIGINT NOT NULL REFERENCES cms.questions(id) ON DELETE CASCADE,
                answer_text TEXT NOT NULL,
                normalized_answer TEXT NOT NULL,
                match_type VARCHAR(20) NOT NULL DEFAULT 'exact',
                similarity_threshold NUMERIC(4,3) NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $db->exec("
            CREATE TABLE IF NOT EXISTS cms.quiz_attempts (
                id BIGSERIAL PRIMARY KEY,
                quiz_id BIGINT NOT NULL REFERENCES cms.quizzes(id) ON DELETE CASCADE,
                user_id INT NULL REFERENCES cms.users(id) ON DELETE SET NULL,
                status VARCHAR(20) NOT NULL DEFAULT 'in_progress',
                score NUMERIC(10,2) NOT NULL DEFAULT 0,
                total_score NUMERIC(10,2) NOT NULL DEFAULT 0,
                percentage NUMERIC(5,2) NOT NULL DEFAULT 0,
                passed BOOLEAN NULL,
                started_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                submitted_at TIMESTAMP NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $db->exec("
            CREATE TABLE IF NOT EXISTS cms.quiz_attempt_answers (
                id BIGSERIAL PRIMARY KEY,
                attempt_id BIGINT NOT NULL REFERENCES cms.quiz_attempts(id) ON DELETE CASCADE,
                question_id BIGINT NOT NULL REFERENCES cms.questions(id) ON DELETE CASCADE,
                answer_text TEXT NULL,
                is_correct BOOLEAN NULL,
                awarded_score NUMERIC(8,2) NOT NULL DEFAULT 0,
                answered_at TIMESTAMP NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE (attempt_id, question_id)
            )
        ");

        $db->exec("
            CREATE TABLE IF NOT EXISTS cms.quiz_attempt_answer_options (
                id BIGSERIAL PRIMARY KEY,
                attempt_answer_id BIGINT NOT NULL REFERENCES cms.quiz_attempt_answers(id) ON DELETE CASCADE,
                option_id BIGINT NOT NULL REFERENCES cms.question_options(id) ON DELETE CASCADE,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE (attempt_answer_id, option_id)
            )
        ");

        // Indexes
        $db->exec("CREATE INDEX IF NOT EXISTS idx_questions_quiz_id ON cms.questions(quiz_id)");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_question_options_question_id ON cms.question_options(question_id)");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_accepted_answers_question_id ON cms.question_accepted_answers(question_id)");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_attempts_quiz_user ON cms.quiz_attempts(quiz_id, user_id)");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_attempt_answers_attempt_id ON cms.quiz_attempt_answers(attempt_id)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $db = Database::connection();
        $db->exec("DROP TABLE IF EXISTS cms.quiz_attempt_answer_options CASCADE");
        $db->exec("DROP TABLE IF EXISTS cms.quiz_attempt_answers CASCADE");
        $db->exec("DROP TABLE IF EXISTS cms.quiz_attempts CASCADE");
        $db->exec("DROP TABLE IF EXISTS cms.question_accepted_answers CASCADE");
        $db->exec("DROP TABLE IF EXISTS cms.question_options CASCADE");
        $db->exec("DROP TABLE IF EXISTS cms.questions CASCADE");
        $db->exec("DROP TABLE IF EXISTS cms.quizzes CASCADE");
    }
};
