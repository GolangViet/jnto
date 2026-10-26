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
            ALTER TABLE cms.questions 
            ADD COLUMN IF NOT EXISTS status VARCHAR(20) NOT NULL DEFAULT 'active'
        ");

        $db->exec("
            CREATE INDEX IF NOT EXISTS idx_questions_quiz_status 
            ON cms.questions (quiz_id, status)
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $db = Database::connection();

        $db->exec("DROP INDEX IF EXISTS cms.idx_questions_quiz_status");

        $db->exec("
            ALTER TABLE cms.questions 
            DROP COLUMN IF EXISTS status
        ");
    }
};
