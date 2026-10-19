<?php

declare(strict_types=1);

namespace App\Models;

use Core\Model;

final class Post extends Model
{
    /**
     * Summary of table
     * 
     * @var string
     */
    protected string $table = 'cms.posts';

    /**
     * Summary of published
     * 
     * @return array
     */
    public function published(): array
    {
        return $this->db
            ->query("SELECT * FROM {$this->table} WHERE status = 'published' ORDER BY id DESC")
            ->fetchAll();
    }

    /**
     * Summary of create
     * 
     * @param array $data
     * @return bool
     */
    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (title, slug, content, status) VALUES (:title, :slug, :content, :status)"
        );
        
        return $stmt->execute($data);
    }

    /**
     * Summary of update
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET title = :title, slug = :slug, content = :content, status = :status WHERE id = :id"
        );

        return $stmt->execute($data + ['id' => $id]);
    }
}
