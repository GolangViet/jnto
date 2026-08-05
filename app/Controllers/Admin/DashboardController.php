<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Database;

final class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with stats, active users, page stay distributions, and leaderboard.
     */
    public function index(): string
    {
        $db = Database::connection();

        // 1. Total users registered
        $totalUsers = (int) $db->query("SELECT COUNT(*) FROM cms.users WHERE role = 'user'")->fetchColumn();
        $totalAdmins = (int) $db->query("SELECT COUNT(*) FROM cms.users WHERE role = 'admin'")->fetchColumn();
        $totalRegistered = (int) $db->query("SELECT COUNT(*) FROM cms.users")->fetchColumn();

        // 2. Leaderboard: Top users with best quiz scores
        $stmtLeaderboard = $db->query("
            SELECT 
                u.id as user_id,
                u.name as user_name,
                u.username as user_username,
                q.title as quiz_title,
                qa.score,
                qa.total_score,
                qa.percentage,
                qa.submitted_at
            FROM cms.quiz_attempts qa
            JOIN cms.users u ON qa.user_id = u.id
            JOIN cms.quizzes q ON qa.quiz_id = q.id
            WHERE u.role = 'user' AND qa.status = 'submitted'
            ORDER BY qa.percentage DESC, qa.score DESC, qa.submitted_at ASC
            LIMIT 15
        ");
        $leaderboard = $stmtLeaderboard->fetchAll() ?: [];

        // 3. Completion counts for specific pages (Done states)
        $mainSurveyId = setting('main_survey_quiz_id');
        $mainQuizId = setting('main_quiz_quiz_id');
        $mainOpenId = setting('main_open_quiz_id');

        if ($mainSurveyId) {
            $stmtDoneSurvey = $db->prepare("
                SELECT COUNT(*) FROM cms.users u
                WHERE u.role = 'user' AND EXISTS (
                    SELECT 1 FROM cms.quiz_attempts qa 
                    WHERE qa.quiz_id = :quiz_id AND qa.user_id = u.id AND qa.status = 'submitted'
                )
            ");
            $stmtDoneSurvey->execute(['quiz_id' => (int) $mainSurveyId]);
            $doneSurveyCount = (int) $stmtDoneSurvey->fetchColumn();
        } else {
            $doneSurveyCount = $totalUsers;
        }

        $qConditions = ["u.role = 'user'"];
        $qParams = [];

        if ($mainSurveyId) {
            $qConditions[] = "EXISTS (
                SELECT 1 FROM cms.quiz_attempts qa 
                WHERE qa.quiz_id = :main_survey_id AND qa.user_id = u.id AND qa.status = 'submitted'
            )";
            $qParams['main_survey_id'] = (int) $mainSurveyId;
        }

        if ($mainQuizId) {
            $qConditions[] = "EXISTS (
                SELECT 1 FROM cms.quiz_attempts qa 
                WHERE qa.quiz_id = :main_quiz_id AND qa.user_id = u.id AND qa.status != 'in_progress'
            )";
            $qParams['main_quiz_id'] = (int) $mainQuizId;
        }

        if ($mainOpenId) {
            $qConditions[] = "EXISTS (
                SELECT 1 FROM cms.quiz_attempts qa 
                WHERE qa.quiz_id = :main_open_id AND qa.user_id = u.id AND qa.status != 'in_progress'
            )";
            $qParams['main_open_id'] = (int) $mainOpenId;
        }

        $stmtDoneQuestions = $db->prepare("
            SELECT COUNT(*) FROM cms.users u
            WHERE " . implode(' AND ', $qConditions) . "
        ");
        $stmtDoneQuestions->execute($qParams);
        $doneQuestionsCount = (int) $stmtDoneQuestions->fetchColumn();

        $doneConfirmPostCount = (int) $db->query("
            SELECT COUNT(*) FROM cms.users u
            WHERE u.role = 'user' AND EXISTS (
                SELECT 1 FROM cms.user_facebook_posts fp 
                WHERE fp.user_id = u.id
            )
        ")->fetchColumn();

        $doneThankYouCount = $doneConfirmPostCount; // Since thank you is the final page after submitting post

        return $this->view('admin/dashboard', [
            'totalUsers' => $totalUsers,
            'totalAdmins' => $totalAdmins,
            'totalRegistered' => $totalRegistered,
            'leaderboard' => $leaderboard,
            'doneSurveyCount' => $doneSurveyCount,
            'doneQuestionsCount' => $doneQuestionsCount,
            'doneConfirmPostCount' => $doneConfirmPostCount,
            'doneThankYouCount' => $doneThankYouCount,
        ]);
    }
}
