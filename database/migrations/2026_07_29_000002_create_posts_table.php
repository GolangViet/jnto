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
            CREATE TABLE IF NOT EXISTS cms.posts (
                id SERIAL PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL UNIQUE,
                content TEXT NOT NULL,
                status VARCHAR(50) NOT NULL DEFAULT 'draft',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Seed a sample post
        $stmt = $db->prepare("
            INSERT INTO cms.posts (title, slug, content, status)
            VALUES (:title, :slug, :content, :status)
            ON CONFLICT (slug) DO NOTHING
        ");

        $stmt->execute([
            'title' => 'Welcome to JNTO',
            'slug' => 'welcome-to-jnto',
            'content' => 'Explore the beauty, history, and modern marvels of Japan with the Japan National Tourism Organization.',
            'status' => 'published',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $db = Database::connection();
        $db->exec("DROP TABLE IF EXISTS cms.posts CASCADE");
    }
};
