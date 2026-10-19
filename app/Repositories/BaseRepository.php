<?php

declare(strict_types=1);

namespace App\Repositories;

use Core\Model;

abstract class BaseRepository
{
    protected Model $model;

    /**
     * Initialize the repository and resolve the associated model.
     */
    public function __construct()
    {
        $this->model = $this->getModel();
    }

    /**
     * Get the model instance associated with the repository.
     *
     * @return Model
     */
    abstract protected function getModel(): Model;

    /**
     * Retrieve all records from the model's table.
     *
     * @param string $orderBy ORDER BY clause (default 'id DESC').
     * @return array List of rows.
     */
    public function all(string $orderBy = 'id DESC'): array
    {
        return $this->model->all($orderBy);
    }

    /**
     * Find a record by its primary key.
     *
     * @param int $id Record primary key.
     * @return array|null The record as an associative array or null if not found.
     */
    public function find(int $id): ?array
    {
        return $this->model->find($id);
    }

    /**
     * Delete a record by its primary key.
     *
     * @param int $id Record primary key to delete.
     * @return bool True on success, false on failure.
     */
    public function delete(int $id): bool
    {
        return $this->model->delete($id);
    }
}
