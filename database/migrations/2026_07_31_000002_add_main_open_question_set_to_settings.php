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

        // Seed default/empty setting for main_open_quiz_id
        $stmt = $db->prepare("
            INSERT INTO cms.settings (key, value)
            VALUES (:key, :value)
            ON CONFLICT (key) DO NOTHING
        ");
        $stmt->execute(['key' => 'main_open_quiz_id', 'value' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $db = Database::connection();
        $db->exec("DELETE FROM cms.settings WHERE key = 'main_open_quiz_id'");
    }
};
