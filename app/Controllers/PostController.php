<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\PostService;
use Core\Controller;
use Core\Csrf;
use Core\Validator;

final class PostController extends Controller
{
    private PostService $postService;

    public function __construct()
    {
        $this->postService = new PostService();
    }

    /**
     * List all posts in the admin area.
     *
     * @return string Rendered HTML for the posts index.
     */
    public function index(): string
    {
        return $this->view('admin/posts/index', ['posts' => $this->postService->getAllPosts()]);
    }

    /**
     * Show the create post form.
     *
     * @return string Rendered HTML for the create form.
     */
    public function create(): string
    {
        return $this->view('admin/posts/create');
    }

    /**
     * Store a new post in the database.
     *
     * Validates and persists post data, then redirects to the posts list.
     *
     * @return never
     */
    public function store(): never
    {
        $request = app()->request();
        Csrf::verify($request);
        $data = $request->only(['title', 'slug', 'content', 'status']);
        $data['slug'] = $data['slug'] ?: $this->slugify((string) $data['title']);

        $validator = new Validator();
        if (!$validator->validate($data, ['title' => 'required|min:3', 'slug' => 'required', 'content' => 'required|min:10'])) {
            app()->session()->flash('errors', $validator->errors());
            app()->session()->flashOldInput($data);
            $this->redirect('/admin/posts/create');
        }

        $this->postService->createPost($data);
        app()->session()->flash('success', 'Post created successfully.');
        $this->redirect('/admin/posts');
    }

    /**
     * Show the edit form for a post.
     *
     * @param string $id Post identifier.
     * @return string Rendered HTML for the edit form or an error message.
     */
    public function edit(string $id): string
    {
        $post = $this->postService->getPostById((int) $id);
        if (!$post) {
            http_response_code(404);
            return 'Post not found.';
        }

        return $this->view('admin/posts/edit', ['post' => $post]);
    }

    /**
     * Update an existing post.
     *
     * @param string $id Post identifier.
     * @return never Redirects after updating.
     */
    public function update(string $id): never
    {
        $request = app()->request();
        Csrf::verify($request);
        $data = $request->only(['title', 'slug', 'content', 'status']);
        $data['slug'] = $data['slug'] ?: $this->slugify((string) $data['title']);

        $this->postService->updatePost((int) $id, $data);
        app()->session()->flash('success', 'Post updated successfully.');
        $this->redirect('/admin/posts');
    }

    /**
     * Delete a post.
     *
     * @param string $id Post identifier.
     * @return never Redirects after deletion.
     */
    public function destroy(string $id): never
    {
        Csrf::verify(app()->request());
        $this->postService->deletePost((int) $id);
        app()->session()->flash('success', 'Post deleted successfully.');
        $this->redirect('/admin/posts');
    }

    /**
     * Generate a URL-friendly slug from a string.
     *
     * @param string $value Input string to slugify.
     * @return string Slugified string.
     */
    private function slugify(string $value): string
    {
        $value = mb_strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/u', '-', $value) ?: '';
        return trim($value, '-');
    }
}
