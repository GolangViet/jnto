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
        $db->exec("CREATE SCHEMA IF NOT EXISTS cms");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $db = Database::connection();
        $db->exec("DROP SCHEMA IF EXISTS cms CASCADE");
    }
};
