<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\PostRepository;

final class PostService
{
    private PostRepository $postRepository;

    /**
     * Initialize PostService and inject PostRepository.
     */
    public function __construct()
    {
        $this->postRepository = new PostRepository();
    }

    /**
     * Get all posts, ordered by the specified field.
     *
     * @param string $orderBy ORDER BY clause.
     * @return array List of all posts.
     */
    public function getAllPosts(string $orderBy = 'id DESC'): array
    {
        return $this->postRepository->all($orderBy);
    }

    /**
     * Get all published posts.
     *
     * @return array List of published posts.
     */
    public function getPublishedPosts(): array
    {
        return $this->postRepository->published();
    }

    /**
     * Get a specific post by its ID.
     *
     * @param int $id Post identifier.
     * @return array|null The post record or null if not found.
     */
    public function getPostById(int $id): ?array
    {
        return $this->postRepository->find($id);
    }

    /**
     * Create a new post.
     *
     * @param array $data Post data.
     * @return bool True on success, false on failure.
     */
    public function createPost(array $data): bool
    {
        return $this->postRepository->create($data);
    }

    /**
     * Update an existing post.
     *
     * @param int $id Post identifier.
     * @param array $data Updated post data.
     * @return bool True on success, false on failure.
     */
    public function updatePost(int $id, array $data): bool
    {
        return $this->postRepository->update($id, $data);
    }

    /**
     * Delete a post.
     *
     * @param int $id Post identifier.
     * @return bool True on success, false on failure.
     */
    public function deletePost(int $id): bool
    {
        return $this->postRepository->delete($id);
    }
}
