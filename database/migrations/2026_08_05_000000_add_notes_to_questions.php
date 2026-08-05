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
            ADD COLUMN IF NOT EXISTS notes TEXT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $db = Database::connection();

        $db->exec("
            ALTER TABLE cms.questions 
            DROP COLUMN IF EXISTS notes
        ");
    }
};
