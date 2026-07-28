<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Post;
use Core\Model;

final class PostRepository extends BaseRepository
{
    /**
     * Get the model instance associated with the repository.
     *
     * @return Model
     */
    protected function getModel(): Model
    {
        return new Post();
    }

    /**
     * Retrieve all published posts.
     *
     * @return array List of published posts.
     */
    public function published(): array
    {
        /** @var Post $postModel */
        $postModel = $this->model;
        return $postModel->published();
    }

    /**
     * Create a new post record.
     *
     * @param array $data Post data.
     * @return bool True on success, false on failure.
     */
    public function create(array $data): bool
    {
        /** @var Post $postModel */
        $postModel = $this->model;
        return $postModel->create($data);
    }

    /**
     * Update an existing post record.
     *
     * @param int $id Post identifier.
     * @param array $data Updated post data.
     * @return bool True on success, false on failure.
     */
    public function update(int $id, array $data): bool
    {
        /** @var Post $postModel */
        $postModel = $this->model;
        return $postModel->update($id, $data);
    }
}
