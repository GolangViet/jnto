<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Repositories\SettingRepository;
use App\Services\QuizService;
use Core\Controller;
use Core\Csrf;

final class SettingController extends Controller
{
    private SettingRepository $settingRepository;
    private QuizService $quizService;

    public function __construct()
    {
        $this->settingRepository = new SettingRepository();
        $this->quizService = new QuizService();
    }

    /**
     * Display the settings configuration page.
     */
    public function index(): string
    {
        $settings = $this->settingRepository->getSettings();
        $quizzes = $this->quizService->getPublishedQuizzes();

        return $this->view('admin/settings', [
            'settings' => $settings,
            'quizzes' => $quizzes,
        ]);
    }

    /**
     * Update the settings values.
     */
    public function update(): never
    {
        $request = app()->request();
        Csrf::verify($request);

        $mainSurveyId = $request->input('main_survey_quiz_id');
        $mainQuizId = $request->input('main_quiz_quiz_id');
        $mainOpenId = $request->input('main_open_quiz_id');

        // Convert empty string/none to null
        $mainSurveyId = ($mainSurveyId === '' || $mainSurveyId === 'none') ? null : $mainSurveyId;
        $mainQuizId = ($mainQuizId === '' || $mainQuizId === 'none') ? null : $mainQuizId;
        $mainOpenId = ($mainOpenId === '' || $mainOpenId === 'none') ? null : $mainOpenId;

        $this->settingRepository->setSetting('main_survey_quiz_id', $mainSurveyId);
        $this->settingRepository->setSetting('main_quiz_quiz_id', $mainQuizId);
        $this->settingRepository->setSetting('main_open_quiz_id', $mainOpenId);

        app()->session()->flash('success', 'Settings updated successfully.');
        $this->redirect('/admin/settings');
    }
}
