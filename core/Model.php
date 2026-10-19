<?php

declare(strict_types=1);

namespace Core;

use PDO;

abstract class Model
{
    protected PDO $db;
    protected string $table;

    /**
     * Initialize the model and database connection.
     */
    public function __construct()
    {
        $this->db = Database::connection();
    }

    /**
     * Retrieve all records from the model's table.
     *
     * @param string $orderBy ORDER BY clause (default 'id DESC').
     * @return array List of rows.
     */
    public function all(string $orderBy = 'id DESC'): array
    {
        return $this->db->query("SELECT * FROM {$this->table} ORDER BY {$orderBy}")->fetchAll();
    }

    /**
     * Find a record by its primary key.
     *
     * @param int $id Record primary key.
     * @return array|null The record as an associative array or null if not found.
     */
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Delete a record by its primary key.
     *
     * @param int $id Record primary key to delete.
     * @return bool True on success, false on failure.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
