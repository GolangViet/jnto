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
        $visibleQuizzesVal = setting('leaderboard_visible_quizzes');
        $visibleQuizIds = [];
        if ($visibleQuizzesVal !== null && $visibleQuizzesVal !== '') {
            $visibleQuizIds = array_filter(array_map('intval', explode(',', $visibleQuizzesVal)));
        }

        // First get top 15 unique users based on their highest single attempt percentage/score
        $sqlTopUsers = "
            SELECT DISTINCT ON (u.id)
                u.id as user_id,
                qa.percentage,
                qa.score,
                qa.submitted_at
            FROM cms.quiz_attempts qa
            JOIN cms.users u ON qa.user_id = u.id
            WHERE u.role = 'user' AND qa.status = 'submitted'
        ";

        if (!empty($visibleQuizIds)) {
            $placeholders = implode(',', array_fill(0, count($visibleQuizIds), '?'));
            $sqlTopUsers .= " AND qa.quiz_id IN ($placeholders)";
        }

        $sqlTopUsers .= " ORDER BY u.id, qa.percentage DESC, qa.score DESC, qa.submitted_at ASC";

        if (!empty($visibleQuizIds)) {
            $topUsersStmt = $db->prepare($sqlTopUsers);
            $topUsersStmt->execute($visibleQuizIds);
        } else {
            $topUsersStmt = $db->query($sqlTopUsers);
        }
        $topUsersResult = $topUsersStmt->fetchAll() ?: [];

        // Sort the top unique users by their best attempt to determine ranking
        usort($topUsersResult, function($a, $b) {
            if ((float)$b['percentage'] !== (float)$a['percentage']) {
                return (float)$b['percentage'] <=> (float)$a['percentage'];
            }
            if ((float)$b['score'] !== (float)$a['score']) {
                return (float)$b['score'] <=> (float)$a['score'];
            }
            return strcmp($a['submitted_at'], $b['submitted_at']);
        });

        $topUserIds = array_column(array_slice($topUsersResult, 0, 15), 'user_id');

        $leaderboard = [];
        if (!empty($topUserIds)) {
            $userPlaceholders = implode(',', array_fill(0, count($topUserIds), '?'));
            $sqlAttempts = "
                SELECT 
                    qa.id as attempt_id,
                    u.id as user_id,
                    u.name as user_name,
                    u.username as user_username,
                    fp.facebook_url,
                    q.id as quiz_id,
                    q.title as quiz_title,
                    qa.score,
                    qa.total_score,
                    qa.percentage,
                    qa.submitted_at
                FROM cms.quiz_attempts qa
                JOIN cms.users u ON qa.user_id = u.id
                JOIN cms.quizzes q ON qa.quiz_id = q.id
                LEFT JOIN cms.user_facebook_posts fp ON fp.user_id = u.id
                WHERE qa.status = 'submitted'
                  AND u.id IN ($userPlaceholders)
            ";

            $queryParams = $topUserIds;
            if (!empty($visibleQuizIds)) {
                $quizPlaceholders = implode(',', array_fill(0, count($visibleQuizIds), '?'));
                $sqlAttempts .= " AND qa.quiz_id IN ($quizPlaceholders)";
                $queryParams = array_merge($queryParams, $visibleQuizIds);
            }

            $sqlAttempts .= " ORDER BY qa.percentage DESC, qa.score DESC, qa.submitted_at ASC";

            $stmt = $db->prepare($sqlAttempts);
            $stmt->execute($queryParams);
            $attempts = $stmt->fetchAll() ?: [];

            // Group by user, keeping the user info and all their best attempts per quiz
            $userMap = [];
            foreach ($attempts as $attempt) {
                $uid = $attempt['user_id'];
                $qid = $attempt['quiz_id'];

                if (!isset($userMap[$uid])) {
                    $userMap[$uid] = [
                        'user_id' => $uid,
                        'user_name' => $attempt['user_name'],
                        'user_username' => $attempt['user_username'],
                        'facebook_url' => $attempt['facebook_url'] ?? null,
                        'quizzes' => [],
                        'last_submitted_at' => $attempt['submitted_at']
                    ];
                }

                // Keep only the best attempt per quiz for each user
                if (!isset($userMap[$uid]['quizzes'][$qid])) {
                    $userMap[$uid]['quizzes'][$qid] = [
                        'attempt_id' => $attempt['attempt_id'],
                        'quiz_title' => $attempt['quiz_title'],
                        'percentage' => $attempt['percentage'],
                        'score' => $attempt['score'],
                        'total_score' => $attempt['total_score'],
                        'submitted_at' => $attempt['submitted_at']
                    ];
                }

                if (strtotime($attempt['submitted_at']) > strtotime($userMap[$uid]['last_submitted_at'])) {
                    $userMap[$uid]['last_submitted_at'] = $attempt['submitted_at'];
                }
            }

            // Build final leaderboard list maintaining the rank ordering
            foreach ($topUserIds as $uid) {
                if (isset($userMap[$uid])) {
                    $leaderboard[] = $userMap[$uid];
                }
            }
        }

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
