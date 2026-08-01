<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;

final class TakeSurveyController extends Controller
{
    /**
     * List all posts in the admin area.
     *
     * @return string Rendered HTML for the posts index.
     */
    public function detailSurvey(): string
    {
        return $this->view('take-survey/detail-survey');
    }

    /**
     * Show the create form.
     *
     * @return string Rendered HTML for the create form.
     */
    public function detailQuestions(): string
    {
        return $this->view('take-survey/detail-questions');
    }

    /**
     * Show the submit form.
     *
     * @return string Rendered HTML for the create form.
     */
    public function confirmPost(): string
    {
        return $this->view('take-survey/confirm-post');
    }
}
