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

        // 1. Add allow_custom_text to cms.question_options
        $db->exec("
            ALTER TABLE cms.question_options 
            ADD COLUMN IF NOT EXISTS allow_custom_text BOOLEAN NOT NULL DEFAULT FALSE
        ");

        // 2. Add custom_text to cms.quiz_attempt_answer_options
        $db->exec("
            ALTER TABLE cms.quiz_attempt_answer_options 
            ADD COLUMN IF NOT EXISTS custom_text TEXT NULL
        ");

        // 3. Create cms.option_related_questions table
        $db->exec("
            CREATE TABLE IF NOT EXISTS cms.option_related_questions (
                option_id BIGINT NOT NULL REFERENCES cms.question_options(id) ON DELETE CASCADE,
                related_question_id BIGINT NOT NULL REFERENCES cms.questions(id) ON DELETE CASCADE,
                PRIMARY KEY (option_id, related_question_id)
            )
        ");

        // Index for performance
        $db->exec("
            CREATE INDEX IF NOT EXISTS idx_option_related_questions_related_question_id 
            ON cms.option_related_questions(related_question_id)
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $db = Database::connection();

        $db->exec("DROP TABLE IF EXISTS cms.option_related_questions CASCADE");

        $db->exec("
            ALTER TABLE cms.quiz_attempt_answer_options 
            DROP COLUMN IF EXISTS custom_text
        ");

        $db->exec("
            ALTER TABLE cms.question_options 
            DROP COLUMN IF EXISTS allow_custom_text
        ");
    }
};
