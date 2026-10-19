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
            CREATE TABLE IF NOT EXISTS cms.settings (
                key VARCHAR(255) PRIMARY KEY,
                value TEXT NULL,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Seed default/empty settings
        $stmt = $db->prepare("
            INSERT INTO cms.settings (key, value)
            VALUES (:key, :value)
            ON CONFLICT (key) DO NOTHING
        ");
        $stmt->execute(['key' => 'main_survey_quiz_id', 'value' => null]);
        $stmt->execute(['key' => 'main_quiz_quiz_id', 'value' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $db = Database::connection();
        $db->exec("DROP TABLE IF EXISTS cms.settings CASCADE");
    }
};
