<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\PostService;
use Core\Controller;

final class HomeController extends Controller
{
    /**
     * @var PostService
     */
    private PostService $postService;

    /**
     * HomeController constructor.
     */
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
        return $this->view('pages/index', ['posts' => $this->postService->getPublishedPosts()]);
    }

    /**
     * Render the thank you page.
     *
     * @return string Rendered HTML for the thank you page.
     */
    public function detailThankYou(): string
    {
        return $this->view('pages/thank-you');
    }
}
