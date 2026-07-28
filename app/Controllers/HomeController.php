<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\PostService;
use Core\Controller;

final class HomeController extends Controller
{
    private PostService $postService;

    public function __construct()
    {
        $this->postService = new PostService();
    }

    /**
     * Render the home page with published posts.
     *
     * @return string Rendered HTML for the home page.
     */
    public function index(): string
    {
        $user = app()->session()->get('user'); 

        return $this->view('home/index', [
            'posts' => $this->postService->getPublishedPosts(),
        ]);
    }
}
