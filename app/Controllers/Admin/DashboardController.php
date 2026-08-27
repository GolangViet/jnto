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
        $request = app()->request();
        $search = $request->input('search');
        $search = is_string($search) ? trim($search) : '';
        $quizFilter = (int)$request->input('quiz_id', 0);
        $startDate = $request->input('start_date');
        $startDate = is_string($startDate) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate) ? $startDate : '';
        $endDate = $request->input('end_date');
        $endDate = is_string($endDate) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate) ? $endDate : '';
        $page = (int)$request->input('page', 1);

        $totalCount = 0;
        $leaderboard = $this->getLeaderboardData(true, $totalCount);

        // Fetch all quizzes for the filter dropdown
        $stmtAllQuizzes = $db->query("SELECT id, title FROM cms.quizzes ORDER BY title ASC");
        $quizzes = $stmtAllQuizzes->fetchAll() ?: [];

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
            'leaderboardPage' => $page,
            'leaderboardTotalPages' => (int)ceil($totalCount / 10),
            'leaderboardTotalCount' => $totalCount,
            'leaderboardPerPage' => 10,
            'leaderboardSearch' => $search,
            'leaderboardQuizFilter' => $quizFilter,
            'leaderboardStartDate' => $startDate,
            'leaderboardEndDate' => $endDate,
            'leaderboardQuizzes' => $quizzes,
            'doneSurveyCount' => $doneSurveyCount,
            'doneQuestionsCount' => $doneQuestionsCount,
            'doneConfirmPostCount' => $doneConfirmPostCount,
            'doneThankYouCount' => $doneThankYouCount,
        ]);
    }

    /**
     * Get the leaderboard data.
     *
     * @return array
     */
    private function getLeaderboardData(bool $paginate = true, int &$totalCount = null): array
    {
        $db = Database::connection();
        $request = app()->request();

        // 1. Get visible quizzes from settings if none specified
        $visibleQuizzesVal = setting('leaderboard_visible_quizzes');
        $visibleQuizIds = [];
        if ($visibleQuizzesVal !== null && $visibleQuizzesVal !== '') {
            $visibleQuizIds = array_filter(array_map('intval', explode(',', $visibleQuizzesVal)));
        }

        // Filters from request
        $search = $request->input('search');
        $search = is_string($search) ? trim($search) : '';
        $quizFilter = (int)$request->input('quiz_id', 0);
        $startDate = $request->input('start_date');
        $startDate = is_string($startDate) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate) ? $startDate : '';
        $endDate = $request->input('end_date');
        $endDate = is_string($endDate) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate) ? $endDate : '';

        // Base where clause and params
        $where = "u.role = 'user' AND qa.status = 'submitted'";
        $params = [];

        // Apply search filter
        if ($search !== '') {
            $where .= " AND (u.name ILIKE :search OR u.username ILIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        // Apply quiz filter
        if ($quizFilter > 0) {
            $where .= " AND qa.quiz_id = :quiz_filter";
            $params['quiz_filter'] = $quizFilter;
        } elseif (!empty($visibleQuizIds)) {
            $placeholders = [];
            foreach ($visibleQuizIds as $index => $vid) {
                $paramName = 'v_quiz_' . $index;
                $placeholders[] = ':' . $paramName;
                $params[$paramName] = $vid;
            }
            $where .= " AND qa.quiz_id IN (" . implode(',', $placeholders) . ")";
        }

        // Apply date filters
        if ($startDate !== '') {
            $where .= " AND qa.submitted_at >= :start_date";
            $params['start_date'] = $startDate . ' 00:00:00';
        }
        if ($endDate !== '') {
            $where .= " AND qa.submitted_at <= :end_date";
            $params['end_date'] = $endDate . ' 23:59:59';
        }

        // Calculate count if required
        if ($totalCount !== null || $paginate) {
            $countSql = "
                SELECT COUNT(DISTINCT u.id)
                FROM cms.quiz_attempts qa
                JOIN cms.users u ON qa.user_id = u.id
                WHERE $where
            ";
            $stmtCount = $db->prepare($countSql);
            $stmtCount->execute($params);
            $totalCount = (int) $stmtCount->fetchColumn();
        }

        // SQL to get top users ordered by best score
        $sqlTopUsers = "
            SELECT * FROM (
                SELECT DISTINCT ON (u.id)
                    u.id as user_id,
                    qa.percentage,
                    qa.score,
                    qa.submitted_at
                FROM cms.quiz_attempts qa
                JOIN cms.users u ON qa.user_id = u.id
                WHERE $where
                ORDER BY u.id, qa.percentage DESC, qa.score DESC, qa.submitted_at ASC
            ) AS best_attempts
            ORDER BY percentage DESC, score DESC, submitted_at ASC
        ";

        if ($paginate) {
            $page = $request->input('page') ? max(1, (int)$request->input('page')) : 1;
            $perPage = 10;
            $offset = ($page - 1) * $perPage;
            $sqlTopUsers .= " LIMIT :limit OFFSET :offset";
        }

        $stmtTop = $db->prepare($sqlTopUsers);
        foreach ($params as $key => $val) {
            $stmtTop->bindValue($key, $val);
        }
        if ($paginate) {
            $stmtTop->bindValue('limit', $perPage, \PDO::PARAM_INT);
            $stmtTop->bindValue('offset', $offset, \PDO::PARAM_INT);
        }
        $stmtTop->execute();
        $topUsersResult = $stmtTop->fetchAll() ?: [];

        $topUserIds = array_column($topUsersResult, 'user_id');

        $leaderboard = [];
        if (!empty($topUserIds)) {
            // Build placeholders for user IDs
            $userPlaceholders = [];
            $attemptsParams = [];
            foreach ($topUserIds as $index => $uid) {
                $paramName = 'top_user_' . $index;
                $userPlaceholders[] = ':' . $paramName;
                $attemptsParams[$paramName] = $uid;
            }

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
                  AND u.id IN (" . implode(',', $userPlaceholders) . ")
            ";

            // Add the same quiz filters for attempts
            if ($quizFilter > 0) {
                $sqlAttempts .= " AND qa.quiz_id = :quiz_filter_attempts";
                $attemptsParams['quiz_filter_attempts'] = $quizFilter;
            } elseif (!empty($visibleQuizIds)) {
                $attemptsQuizPlaceholders = [];
                foreach ($visibleQuizIds as $index => $vid) {
                    $paramName = 'v_quiz_att_' . $index;
                    $attemptsQuizPlaceholders[] = ':' . $paramName;
                    $attemptsParams[$paramName] = $vid;
                }
                $sqlAttempts .= " AND qa.quiz_id IN (" . implode(',', $attemptsQuizPlaceholders) . ")";
            }

            // Apply date filters to attempts query to keep them consistent
            if ($startDate !== '') {
                $sqlAttempts .= " AND qa.submitted_at >= :start_date_attempts";
                $attemptsParams['start_date_attempts'] = $startDate . ' 00:00:00';
            }
            if ($endDate !== '') {
                $sqlAttempts .= " AND qa.submitted_at <= :end_date_attempts";
                $attemptsParams['end_date_attempts'] = $endDate . ' 23:59:59';
            }

            $sqlAttempts .= " ORDER BY qa.percentage DESC, qa.score DESC, qa.submitted_at ASC";

            $stmtAttempts = $db->prepare($sqlAttempts);
            $stmtAttempts->execute($attemptsParams);
            $attempts = $stmtAttempts->fetchAll() ?: [];

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

        return $leaderboard;
    }

    /**
     * Export the leaderboard data to CSV.
     *
     * @return void
     */
    public function exportLeaderboard(): void
    {
        $leaderboard = $this->getLeaderboardData();
        $db = Database::connection();

        // Send headers
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="leaderboard_' . date('Ymd_His') . '.csv"');

        $output = fopen('php://output', 'w');

        // Add UTF-8 BOM for proper encoding in Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Collect all unique quizzes present in the leaderboard data
        $allQuizzes = [];
        foreach ($leaderboard as $row) {
            foreach ($row['quizzes'] as $qid => $quiz) {
                if (!isset($allQuizzes[$qid])) {
                    $allQuizzes[$qid] = $quiz['quiz_title'];
                }
            }
        }

        // CSV Headers: Rank, Name, Username, User ID, Facebook Post URL, then dynamic columns per quiz
        $headers = ['Rank', 'Name', 'Username', 'User ID', 'Facebook Post URL'];
        foreach ($allQuizzes as $qid => $title) {
            $headers[] = $title . ' Score';
            $headers[] = $title . ' Percentage';
            $headers[] = $title . ' Detailed Answers';
            $headers[] = $title . ' Submitted At';
        }
        $headers[] = 'Last Activity';

        fputcsv($output, $headers);

        foreach ($leaderboard as $index => $row) {
            $rank = $index + 1;

            $csvRow = [
                $rank,
                $row['user_name'],
                '@' . $row['user_username'],
                $row['user_id'],
                $row['facebook_url'] ?? ''
            ];

            foreach ($allQuizzes as $qid => $title) {
                if (isset($row['quizzes'][$qid])) {
                    $quiz = $row['quizzes'][$qid];

                    // Fetch details of user answers for this attempt
                    $stmtAnswers = $db->prepare("
                        SELECT 
                            q.id as question_id,
                            q.question_text,
                            q.type as question_type,
                            qaa.answer_text,
                            qo.id as option_id,
                            qo.option_text,
                            qaao.custom_text as option_custom_text
                        FROM cms.questions q
                        LEFT JOIN cms.quiz_attempt_answers qaa ON qaa.question_id = q.id AND qaa.attempt_id = :attempt_id
                        LEFT JOIN cms.quiz_attempt_answer_options qaao ON qaao.attempt_answer_id = qaa.id
                        LEFT JOIN cms.question_options qo ON qo.id = qaao.option_id
                        WHERE q.quiz_id = :quiz_id
                        ORDER BY q.display_order ASC, q.id ASC, qo.display_order ASC, qo.id ASC
                    ");
                    $stmtAnswers->execute([
                        'attempt_id' => (int) $quiz['attempt_id'],
                        'quiz_id' => (int) $qid
                    ]);
                    $rowsAnswers = $stmtAnswers->fetchAll() ?: [];

                    // Fetch conditional visibility triggers for this quiz
                    $stmtTriggers = $db->prepare("
                        SELECT orq.option_id, orq.related_question_id 
                        FROM cms.option_related_questions orq
                        JOIN cms.question_options qo ON qo.id = orq.option_id
                        JOIN cms.questions q ON q.id = qo.question_id
                        WHERE q.quiz_id = :quiz_id
                    ");
                    $stmtTriggers->execute(['quiz_id' => (int) $qid]);
                    $triggerRows = $stmtTriggers->fetchAll() ?: [];
                    
                    $triggerOptionsMap = [];
                    foreach ($triggerRows as $tRow) {
                        $triggerOptionsMap[(int)$tRow['related_question_id']][] = (int)$tRow['option_id'];
                    }

                    // Group answer options by question_id
                    $groupedQuestions = [];
                    $selectedOptionIds = [];
                    foreach ($rowsAnswers as $ansRow) {
                        $qId = (int)$ansRow['question_id'];
                        if (!isset($groupedQuestions[$qId])) {
                            $groupedQuestions[$qId] = [
                                'question_text' => $ansRow['question_text'],
                                'question_type' => $ansRow['question_type'],
                                'answer_text' => $ansRow['answer_text'],
                                'options' => []
                            ];
                        }
                        if ($ansRow['option_id'] !== null) {
                            $selectedOptionIds[] = (int)$ansRow['option_id'];
                            $optVal = $ansRow['option_text'];
                            if ($ansRow['option_custom_text'] !== null && trim((string)$ansRow['option_custom_text']) !== '') {
                                $optVal .= ' (' . $ansRow['option_custom_text'] . ')';
                            }
                            $groupedQuestions[$qId]['options'][] = $optVal;
                        }
                    }
                    $selectedOptionIds = array_unique($selectedOptionIds);

                    // Filter visible questions if there are conditional logic questions (matching UI logic)
                    $visibleQuestions = [];
                    foreach ($groupedQuestions as $qId => $q) {
                        $isVisible = false;
                        if (!isset($triggerOptionsMap[$qId])) {
                            $isVisible = true;
                        } else {
                            foreach ($triggerOptionsMap[$qId] as $triggerOptId) {
                                if (in_array($triggerOptId, $selectedOptionIds)) {
                                    $isVisible = true;
                                    break;
                                }
                            }
                        }
                        if ($isVisible) {
                            $visibleQuestions[$qId] = $q;
                        }
                    }

                    // Format answers for this quiz
                    $quizAnswersText = '';
                    if (!empty($visibleQuestions)) {
                        $qNum = 1;
                        foreach ($visibleQuestions as $q) {
                            $ansVal = 'No Answer';
                            if ($q['question_type'] === 'open_text') {
                                if ($q['answer_text'] !== null && trim((string)$q['answer_text']) !== '') {
                                    $ansVal = $q['answer_text'];
                                }
                            } else {
                                if (!empty($q['options'])) {
                                    $ansVal = implode(', ', $q['options']);
                                }
                            }
                            $quizAnswersText .= sprintf("%d. %s -> %s\n", $qNum++, $q['question_text'], $ansVal);
                        }
                    }

                    $csvRow[] = (float)$quiz['score'] . '/' . (float)$quiz['total_score'];
                    $csvRow[] = (float)$quiz['percentage'] . '%';
                    $csvRow[] = trim($quizAnswersText);
                    $csvRow[] = date('Y-m-d H:i:s', strtotime($quiz['submitted_at']));
                } else {
                    $csvRow[] = '';
                    $csvRow[] = '';
                    $csvRow[] = '';
                    $csvRow[] = '';
                }
            }

            $csvRow[] = date('Y-m-d H:i:s', strtotime($row['last_submitted_at']));

            fputcsv($output, $csvRow);
        }

        fclose($output);
        exit;
    }
}
