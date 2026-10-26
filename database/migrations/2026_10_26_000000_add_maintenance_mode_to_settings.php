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

        $stmt = $db->prepare("
            INSERT INTO cms.settings (key, value)
            VALUES (:key, :value)
            ON CONFLICT (key) DO NOTHING
        ");

        $stmt->execute(['key' => 'maintenance_mode', 'value' => '0']);
        $stmt->execute([
            'key' => 'maintenance_message',
            'value' => 'Website đang trong quá trình bảo trì và nâng cấp hệ thống. Xin vui lòng quay lại sau!',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $db = Database::connection();
        $stmt = $db->prepare("DELETE FROM cms.settings WHERE key IN ('maintenance_mode', 'maintenance_message')");
        $stmt->execute();
    }
};
