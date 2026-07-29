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
            CREATE TABLE IF NOT EXISTS cms.users (
                id SERIAL PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                role VARCHAR(50) NOT NULL DEFAULT 'user',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Seed users
        $adminPassword = password_hash('password', PASSWORD_DEFAULT);
        $userPassword = password_hash('password', PASSWORD_DEFAULT);

        $stmt = $db->prepare("
            INSERT INTO cms.users (name, email, password, role)
            VALUES (:name, :email, :password, :role)
            ON CONFLICT (email) DO NOTHING
        ");

        $stmt->execute([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => $adminPassword,
            'role' => 'admin',
        ]);

        $stmt->execute([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => $userPassword,
            'role' => 'user',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $db = Database::connection();
        $db->exec("DROP TABLE IF EXISTS cms.users CASCADE");
    }
};
