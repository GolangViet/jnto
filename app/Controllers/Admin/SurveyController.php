<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Repositories\GuestSurveySubmissionRepository;
use App\Repositories\QuizAttemptRepository;
use App\Services\QuestionService;
use App\Services\QuizAttemptService;
use App\Services\QuizService;
use Core\Controller;
use Core\Csrf;
use Core\Database;

final class SurveyController extends Controller
{
    private QuizAttemptRepository $attemptRepository;
    private GuestSurveySubmissionRepository $guestRepository;
    private QuestionService $questionService;
    private QuizAttemptService $attemptService;
    private QuizService $quizService;

    public function __construct()
    {
        $this->attemptRepository = new QuizAttemptRepository();
        $this->guestRepository = new GuestSurveySubmissionRepository();
        $this->questionService = new QuestionService();
        $this->attemptService = new QuizAttemptService();
        $this->quizService = new QuizService();
    }

    /**
     * Display the surveys management dashboard with filters and submissions list.
     */
    public function index(): string
    {
        $request = app()->request();
        $quizzes = $this->quizService->getPublishedQuizzes();

        $mainSurveyId = (int) (setting('main_survey_quiz_id') ?? 0);
        $selectedQuizId = (int) ($request->input('quiz_id') ?? $mainSurveyId);
        if ($selectedQuizId <= 0 && !empty($quizzes)) {
            $selectedQuizId = (int) $quizzes[0]['id'];
        }

        $type = (string) ($request->input('type') ?? 'all');
        if (!in_array($type, ['all', 'user', 'guest'], true)) {
            $type = 'all';
        }

        $search = trim((string) ($request->input('search') ?? ''));
        $startDate = trim((string) ($request->input('start_date') ?? ''));
        $endDate = trim((string) ($request->input('end_date') ?? ''));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
            $startDate = '';
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
            $endDate = '';
        }

        $page = max(1, (int) ($request->input('page') ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        // Statistics counts
        $totalUserAttempts = $selectedQuizId > 0
            ? $this->attemptRepository->countSurveyAttempts($selectedQuizId)
            : 0;

        $totalGuestSubmissions = $selectedQuizId > 0
            ? $this->guestRepository->countSubmissions($selectedQuizId)
            : 0;

        $totalSubmissions = $totalUserAttempts + $totalGuestSubmissions;

        $items = [];
        $totalItems = 0;

        if ($selectedQuizId > 0) {
            if ($type === 'user') {
                $totalItems = $this->attemptRepository->countSurveyAttempts($selectedQuizId, $search, $startDate, $endDate);
                $attempts = $this->attemptRepository->getSurveyAttempts($selectedQuizId, $search, $startDate, $endDate, $perPage, $offset);
                foreach ($attempts as $a) {
                    $items[] = [
                        'id' => (int) $a['id'],
                        'type' => 'user',
                        'quiz_id' => (int) $a['quiz_id'],
                        'quiz_title' => $a['quiz_title'] ?? '',
                        'user_id' => $a['user_id'] ? (int) $a['user_id'] : null,
                        'name' => $a['user_name'] ?? 'N/A',
                        'email' => $a['user_email'] ?? 'N/A',
                        'username' => $a['user_username'] ?? 'N/A',
                        'guest_token' => null,
                        'travel_status' => $a['travel_status'] ?? null,
                        'status' => $a['status'] ?? 'submitted',
                        'answers_count' => (int) ($a['answers_count'] ?? 0),
                        'submitted_at' => $a['submitted_at'] ?? $a['created_at'],
                    ];
                }
            } elseif ($type === 'guest') {
                $totalItems = $this->guestRepository->countSubmissions($selectedQuizId, $search, $startDate, $endDate);
                $guestRows = $this->guestRepository->getSubmissions($selectedQuizId, $search, $startDate, $endDate, $perPage, $offset);
                foreach ($guestRows as $g) {
                    $answersCount = is_array($g['answers_payload'] ?? null) ? count($g['answers_payload']) : 0;
                    $items[] = [
                        'id' => (int) $g['id'],
                        'type' => 'guest',
                        'quiz_id' => (int) $g['quiz_id'],
                        'quiz_title' => $g['quiz_title'] ?? '',
                        'user_id' => null,
                        'name' => 'Khách vãng lai (Guest)',
                        'email' => null,
                        'username' => null,
                        'guest_token' => $g['guest_token'] ?? '',
                        'travel_status' => $g['travel_status'] ?? null,
                        'status' => 'guest_submitted',
                        'answers_count' => $answersCount,
                        'submitted_at' => $g['created_at'],
                    ];
                }
            } else {
                // Unified list for 'all'
                $result = $this->getUnifiedSubmissions($selectedQuizId, $search, $startDate, $endDate, $perPage, $offset);
                $items = $result['items'];
                $totalItems = $result['total'];
            }
        }

        $totalPages = $totalItems > 0 ? (int) ceil($totalItems / $perPage) : 1;

        return $this->view('admin/surveys/index', [
            'quizzes' => $quizzes,
            'selectedQuizId' => $selectedQuizId,
            'type' => $type,
            'search' => $search,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalItems' => $totalItems,
            'totalSubmissions' => $totalSubmissions,
            'totalUserAttempts' => $totalUserAttempts,
            'totalGuestSubmissions' => $totalGuestSubmissions,
            'items' => $items,
        ]);
    }

    /**
     * Show detailed answers for a registered user's survey attempt.
     */
    public function showUserAttempt(string $id): string
    {
        $attemptId = (int) $id;
        $attempt = $this->attemptService->getAttemptById($attemptId);

        if (!$attempt) {
            http_response_code(404);
            return 'Survey attempt not found.';
        }

        $questions = $this->questionService->getQuestionsForQuiz((int) $attempt['quiz_id']);
        $savedAnswers = $this->attemptService->getAttemptAnswers($attemptId);

        $answersMap = [];
        foreach ($savedAnswers as $ans) {
            $answersMap[(int) $ans['question_id']] = $ans;
        }

        $questionsWithDetails = $this->buildQuestionBreakdown($questions, $answersMap);
        $visibleQuestions = $this->filterVisibleQuestions($questionsWithDetails, $savedAnswers);

        // Find travel status from answered question with branching options if available
        $travelStatus = null;
        foreach ($questionsWithDetails as $q) {
            foreach ($q['options'] ?? [] as $opt) {
                if (!empty($opt['related_question_ids'])) {
                    $userAns = $q['user_answer'];
                    if ($userAns && in_array((int)$opt['id'], array_map('intval', $userAns['selected_option_ids'] ?? []), true)) {
                        $travelStatus = $opt['option_text'];
                        break 2;
                    }
                }
            }
        }

        return $this->view('admin/surveys/show', [
            'type' => 'user',
            'submission' => [
                'id' => $attempt['id'],
                'quiz_id' => $attempt['quiz_id'],
                'quiz_title' => $attempt['quiz_title'],
                'user_id' => $attempt['user_id'],
                'user_name' => $attempt['user_name'] ?? 'Unknown User',
                'user_email' => $attempt['user_email'] ?? 'Unknown Email',
                'user_username' => $attempt['user_username'] ?? 'N/A',
                'status' => $attempt['status'],
                'submitted_at' => $attempt['submitted_at'] ?? $attempt['created_at'],
                'travel_status' => $travelStatus,
                'guest_token' => null,
            ],
            'questions' => $visibleQuestions,
        ]);
    }

    /**
     * Show detailed answers for a guest's survey submission.
     */
    public function showGuestSubmission(string $id): string
    {
        $submissionId = (int) $id;
        $guestRow = $this->guestRepository->findById($submissionId);

        if (!$guestRow) {
            http_response_code(404);
            return 'Guest survey submission not found.';
        }

        $quizId = (int) $guestRow['quiz_id'];
        $questions = $this->questionService->getQuestionsForQuiz($quizId);

        $answersMap = [];
        $savedAnswers = [];
        $rawAnswers = $guestRow['answers_payload'] ?? [];

        foreach ($rawAnswers as $qIdKey => $ans) {
            if (is_array($ans)) {
                $qId = (int) ($ans['question_id'] ?? $qIdKey);
                $selectedOptionIds = array_map('intval', (array) ($ans['selected_option_ids'] ?? []));
                $customTexts = (array) ($ans['option_custom_texts'] ?? []);
                $ansItem = [
                    'question_id' => $qId,
                    'answer_text' => $ans['answer_text'] ?? null,
                    'selected_option_ids' => $selectedOptionIds,
                    'option_custom_texts' => $customTexts,
                    'is_correct' => null,
                    'awarded_score' => 0.00,
                ];
                $answersMap[$qId] = $ansItem;
                $savedAnswers[] = $ansItem;
            }
        }

        $questionsWithDetails = $this->buildQuestionBreakdown($questions, $answersMap);
        $visibleQuestions = $this->filterVisibleQuestions($questionsWithDetails, $savedAnswers);

        return $this->view('admin/surveys/show', [
            'type' => 'guest',
            'submission' => [
                'id' => $guestRow['id'],
                'quiz_id' => $quizId,
                'quiz_title' => $guestRow['quiz_title'] ?? 'Survey',
                'user_id' => null,
                'user_name' => 'Khách vãng lai (Guest)',
                'user_email' => null,
                'user_username' => null,
                'status' => 'guest_submitted',
                'submitted_at' => $guestRow['created_at'],
                'travel_status' => $guestRow['travel_status'] ?? null,
                'guest_token' => $guestRow['guest_token'] ?? null,
                'expires_at' => $guestRow['expires_at'] ?? null,
            ],
            'questions' => $visibleQuestions,
        ]);
    }

    /**
     * Delete a guest submission.
     */
    public function destroyGuestSubmission(string $id): never
    {
        $request = app()->request();
        Csrf::verify($request);

        $submissionId = (int) $id;
        $this->guestRepository->deleteById($submissionId);

        app()->session()->flash('success', 'Đã xóa bài làm khảo sát của khách.');
        $this->redirect($this->getRedirectUrlWithFilters($request));
    }

    /**
     * Delete a user survey attempt and its answers.
     */
    public function destroyUserAttempt(string $id): never
    {
        $request = app()->request();
        Csrf::verify($request);

        $attemptId = (int) $id;
        $this->attemptService->deleteAttempt($attemptId);

        app()->session()->flash('success', 'Đã xóa bài làm khảo sát và các câu trả lời.');
        $this->redirect($this->getRedirectUrlWithFilters($request));
    }

    /**
     * Bulk delete survey attempts/submissions (user attempts and guest submissions) and their answers.
     */
    public function bulkDestroy(): never
    {
        $request = app()->request();
        Csrf::verify($request);

        $rawItems = $request->input('items') ?? [];
        if (!is_array($rawItems)) {
            $rawItems = $rawItems !== '' ? explode(',', (string) $rawItems) : [];
        }

        $rawAttemptIds = $request->input('attempt_ids') ?? [];
        if (!is_array($rawAttemptIds)) {
            $rawAttemptIds = $rawAttemptIds !== '' ? explode(',', (string) $rawAttemptIds) : [];
        }

        $rawGuestIds = $request->input('guest_ids') ?? [];
        if (!is_array($rawGuestIds)) {
            $rawGuestIds = $rawGuestIds !== '' ? explode(',', (string) $rawGuestIds) : [];
        }

        $userAttemptIds = array_map('intval', $rawAttemptIds);
        $guestIds = array_map('intval', $rawGuestIds);

        foreach ($rawItems as $item) {
            if (is_string($item) && str_contains($item, ':')) {
                [$t, $val] = explode(':', $item, 2);
                $num = (int) $val;
                if ($num > 0) {
                    if ($t === 'user') {
                        $userAttemptIds[] = $num;
                    } elseif ($t === 'guest') {
                        $guestIds[] = $num;
                    }
                }
            } elseif (is_numeric($item) && (int) $item > 0) {
                $userAttemptIds[] = (int) $item;
            }
        }

        $userAttemptIds = array_values(array_unique(array_filter($userAttemptIds, fn($id) => $id > 0)));
        $guestIds = array_values(array_unique(array_filter($guestIds, fn($id) => $id > 0)));

        if (empty($userAttemptIds) && empty($guestIds)) {
            if ($request->expectsJson()) {
                app()->response()->json(['success' => false, 'message' => 'Vui lòng chọn ít nhất một bài làm để xóa.'], 422);
            }
            app()->session()->flash('error', 'Vui lòng chọn ít nhất một bài làm để xóa.');
            $this->redirect($this->getRedirectUrlWithFilters($request));
        }

        $deletedUserAttempts = 0;
        $deletedGuests = 0;

        $db = Database::connection();
        $db->beginTransaction();
        try {
            if (!empty($userAttemptIds)) {
                $deletedUserAttempts = $this->attemptService->bulkDeleteAttempts($userAttemptIds);
            }
            if (!empty($guestIds)) {
                $deletedGuests = $this->guestRepository->bulkDeleteByIds($guestIds);
            }
            $db->commit();
        } catch (\Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            if ($request->expectsJson()) {
                app()->response()->json(['success' => false, 'message' => 'Lỗi khi xóa: ' . $e->getMessage()], 500);
            }
            app()->session()->flash('error', 'Có lỗi xảy ra khi xóa bài làm: ' . $e->getMessage());
            $this->redirect($this->getRedirectUrlWithFilters($request));
        }

        $totalDeleted = $deletedUserAttempts + $deletedGuests;
        $message = "Đã xóa thành công {$totalDeleted} bài làm khảo sát và các câu trả lời liên quan.";

        if ($request->expectsJson()) {
            app()->response()->json([
                'success' => true,
                'message' => $message,
                'deleted_count' => $totalDeleted,
                'deleted_user_attempts' => $deletedUserAttempts,
                'deleted_guests' => $deletedGuests,
            ]);
        }

        app()->session()->flash('success', $message);
        $this->redirect($this->getRedirectUrlWithFilters($request));
    }

    /**
     * Helper to preserve query parameters during redirects.
     */
    private function getRedirectUrlWithFilters($request): string
    {
        $params = [];
        foreach (['quiz_id', 'type', 'search', 'start_date', 'end_date', 'page'] as $key) {
            $val = $request->input($key);
            if ($val !== null && $val !== '') {
                $params[$key] = $val;
            }
        }
        $query = !empty($params) ? '?' . http_build_query($params) : '';
        return '/admin/surveys' . $query;
    }

    /**
     * Export survey submissions with answers to CSV.
     */
    public function exportCsv(): void
    {
        $request = app()->request();
        $mainSurveyId = (int) (setting('main_survey_quiz_id') ?? 0);
        $selectedQuizId = (int) ($request->input('quiz_id') ?? $mainSurveyId);

        if ($selectedQuizId <= 0) {
            http_response_code(400);
            echo 'Survey quiz ID is required.';
            exit;
        }

        $type = (string) ($request->input('type') ?? 'all');
        $search = trim((string) ($request->input('search') ?? ''));
        $startDate = trim((string) ($request->input('start_date') ?? ''));
        $endDate = trim((string) ($request->input('end_date') ?? ''));

        // Fetch all questions for this quiz
        $questions = $this->questionService->getQuestionsForQuiz($selectedQuizId);
        $questionsIndexed = [];
        $allOptionsMap = [];
        foreach ($questions as $q) {
            $qDetails = $this->questionService->getQuestionById((int) $q['id']);
            $questionsIndexed[(int) $q['id']] = $qDetails;
            foreach ($qDetails['options'] ?? [] as $opt) {
                $allOptionsMap[(int) $opt['id']] = $opt['option_text'];
            }
        }

        // Fetch submissions (limit to 2000 for export)
        $submissions = [];
        if ($type === 'user' || $type === 'all') {
            $userAttempts = $this->attemptRepository->getSurveyAttempts($selectedQuizId, $search, $startDate, $endDate, 1500, 0);
            foreach ($userAttempts as $att) {
                $answers = $this->attemptRepository->getAttemptAnswers((int) $att['id']);
                $answersMap = [];
                foreach ($answers as $ans) {
                    $answersMap[(int) $ans['question_id']] = $ans;
                }
                $submissions[] = [
                    'type' => 'Registered User',
                    'id' => $att['id'],
                    'name' => $att['user_name'] ?? '',
                    'email' => $att['user_email'] ?? '',
                    'username' => $att['user_username'] ?? '',
                    'guest_token' => '',
                    'travel_status' => $att['travel_status'] ?? '',
                    'submitted_at' => $att['submitted_at'] ?? $att['created_at'],
                    'answers' => $answersMap,
                ];
            }
        }

        if ($type === 'guest' || $type === 'all') {
            $guestRows = $this->guestRepository->getSubmissions($selectedQuizId, $search, $startDate, $endDate, 1500, 0);
            foreach ($guestRows as $grow) {
                $rawAnswers = $grow['answers_payload'] ?? [];
                $answersMap = [];
                foreach ($rawAnswers as $qIdKey => $ans) {
                    if (is_array($ans)) {
                        $qId = (int) ($ans['question_id'] ?? $qIdKey);
                        $answersMap[$qId] = [
                            'selected_option_ids' => array_map('intval', (array) ($ans['selected_option_ids'] ?? [])),
                            'answer_text' => $ans['answer_text'] ?? null,
                            'option_custom_texts' => (array) ($ans['option_custom_texts'] ?? []),
                        ];
                    }
                }
                $submissions[] = [
                    'type' => 'Guest',
                    'id' => $grow['id'],
                    'name' => 'Khách (Guest)',
                    'email' => '',
                    'username' => '',
                    'guest_token' => $grow['guest_token'] ?? '',
                    'travel_status' => $grow['travel_status'] ?? '',
                    'submitted_at' => $grow['created_at'],
                    'answers' => $answersMap,
                ];
            }
        }

        // Sort descending by date
        usort($submissions, fn($a, $b) => strcmp((string) $b['submitted_at'], (string) $a['submitted_at']));

        $filename = 'survey_export_quiz_' . $selectedQuizId . '_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        // UTF-8 BOM for Excel
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // CSV Header
        $headers = [
            'Submission ID',
            'Participant Type',
            'User Name',
            'User Email',
            'Username',
            'Guest Token',
            'Travel Status',
            'Submitted At',
            'Summary',
        ];

        foreach ($questions as $q) {
            $cleanText = strip_tags($q['question_text']);
            $headers[] = 'Q' . $q['id'] . ': ' . $cleanText;
        }

        fputcsv($output, $headers);

        foreach ($submissions as $sub) {
            $summaryParts = [];
            $questionAnswers = [];

            foreach ($questions as $q) {
                $qId = (int) $q['id'];
                $ans = $sub['answers'][$qId] ?? null;
                $answerString = '';
                $cleanQText = trim(preg_replace('/\s+/', ' ', strip_tags($q['question_text'])));

                if ($ans) {
                    $selectedIds = $ans['selected_option_ids'] ?? [];
                    $customTexts = $ans['option_custom_texts'] ?? [];
                    $selectedLabels = [];

                    foreach ($selectedIds as $optId) {
                        $label = $allOptionsMap[(int) $optId] ?? ('Option ' . $optId);
                        if (!empty($customTexts[(string) $optId])) {
                            $label .= ' [Khác: ' . $customTexts[(string) $optId] . ']';
                        }
                        $selectedLabels[] = $label;
                    }

                    if (!empty($selectedLabels)) {
                        $answerString = implode('; ', $selectedLabels);
                    } elseif (!empty($ans['answer_text']) && trim((string)$ans['answer_text']) !== '') {
                        $answerString = trim((string) $ans['answer_text']);
                    }
                }

                if ($answerString !== '') {
                    $summaryParts[] = "• {$cleanQText}: {$answerString}";
                }

                $questionAnswers[] = $answerString;
            }

            $summaryText = implode("\n", $summaryParts);

            $row = [
                $sub['id'],
                $sub['type'],
                $sub['name'],
                $sub['email'],
                $sub['username'],
                $sub['guest_token'],
                $this->formatTravelStatus($sub['travel_status']),
                $sub['submitted_at'],
                $summaryText,
            ];

            foreach ($questionAnswers as $qa) {
                $row[] = $qa;
            }

            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    /**
     * Unified query across user attempts and guest submissions.
     *
     * @return array{items: array, total: int}
     */
    private function getUnifiedSubmissions(
        int $quizId,
        string $search,
        string $startDate,
        string $endDate,
        int $limit,
        int $offset
    ): array {
        $db = Database::connection();

        // 1. User Conditions
        $userConditions = ["qa.quiz_id = :quiz_id_u"];
        $userParams = ['quiz_id_u' => $quizId];

        if ($search !== '') {
            $userConditions[] = "(u.name ILIKE :search_u OR u.email ILIKE :search_u OR u.username ILIKE :search_u)";
            $userParams['search_u'] = '%' . $search . '%';
        }
        if ($startDate !== '') {
            $userConditions[] = "COALESCE(qa.submitted_at, qa.created_at) >= :start_u";
            $userParams['start_u'] = $startDate . ' 00:00:00';
        }
        if ($endDate !== '') {
            $userConditions[] = "COALESCE(qa.submitted_at, qa.created_at) <= :end_u";
            $userParams['end_u'] = $endDate . ' 23:59:59';
        }

        // 2. Guest Conditions
        $guestConditions = ["gss.quiz_id = :quiz_id_g"];
        $guestParams = ['quiz_id_g' => $quizId];

        if ($search !== '') {
            $guestConditions[] = "(gss.guest_token ILIKE :search_g OR gss.travel_status ILIKE :search_g)";
            $guestParams['search_g'] = '%' . $search . '%';
        }
        if ($startDate !== '') {
            $guestConditions[] = "gss.created_at >= :start_g";
            $guestParams['start_g'] = $startDate . ' 00:00:00';
        }
        if ($endDate !== '') {
            $guestConditions[] = "gss.created_at <= :end_g";
            $guestParams['end_g'] = $endDate . ' 23:59:59';
        }

        // Count queries
        $userCountSql = "SELECT COUNT(*) FROM cms.quiz_attempts qa LEFT JOIN cms.users u ON qa.user_id = u.id WHERE " . implode(' AND ', $userConditions);
        $stmtUC = $db->prepare($userCountSql);
        $stmtUC->execute($userParams);
        $userCount = (int) $stmtUC->fetchColumn();

        $guestCountSql = "SELECT COUNT(*) FROM cms.guest_survey_submissions gss WHERE " . implode(' AND ', $guestConditions);
        $stmtGC = $db->prepare($guestCountSql);
        $stmtGC->execute($guestParams);
        $guestCount = (int) $stmtGC->fetchColumn();

        $total = $userCount + $guestCount;

        // Unified Union Query
        $sql = "
            SELECT 
                'user' AS participant_type,
                qa.id AS raw_id,
                qa.quiz_id,
                q.title AS quiz_title,
                qa.user_id,
                u.name AS user_name,
                u.email AS user_email,
                u.username AS user_username,
                NULL AS guest_token,
                (
                    SELECT qo.option_text
                    FROM cms.quiz_attempt_answers qaa_sub
                    JOIN cms.quiz_attempt_answer_options qaa_opt ON qaa_opt.attempt_answer_id = qaa_sub.id
                    JOIN cms.question_options qo ON qo.id = qaa_opt.option_id
                    WHERE qaa_sub.attempt_id = qa.id 
                      AND EXISTS (SELECT 1 FROM cms.option_related_questions orq WHERE orq.option_id = qo.id)
                    LIMIT 1
                ) AS travel_status,
                qa.status AS status,
                (SELECT COUNT(*) FROM cms.quiz_attempt_answers WHERE attempt_id = qa.id) AS answers_count,
                COALESCE(qa.submitted_at, qa.created_at) AS submitted_at,
                NULL::jsonb AS guest_payload
            FROM cms.quiz_attempts qa
            LEFT JOIN cms.users u ON qa.user_id = u.id
            LEFT JOIN cms.quizzes q ON qa.quiz_id = q.id
            WHERE " . implode(' AND ', $userConditions) . "

            UNION ALL

            SELECT 
                'guest' AS participant_type,
                gss.id AS raw_id,
                gss.quiz_id,
                q.title AS quiz_title,
                NULL AS user_id,
                'Khách vãng lai (Guest)' AS user_name,
                NULL AS user_email,
                NULL AS user_username,
                gss.guest_token,
                gss.travel_status,
                'guest_submitted' AS status,
                0 AS answers_count,
                gss.created_at AS submitted_at,
                gss.answers_payload AS guest_payload
            FROM cms.guest_survey_submissions gss
            LEFT JOIN cms.quizzes q ON gss.quiz_id = q.id
            WHERE " . implode(' AND ', $guestConditions) . "

            ORDER BY submitted_at DESC, raw_id DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $db->prepare($sql);
        $allParams = array_merge($userParams, $guestParams);
        foreach ($allParams as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll() ?: [];
        $items = [];
        foreach ($rows as $r) {
            $ansCount = (int) ($r['answers_count'] ?? 0);
            if ($r['participant_type'] === 'guest') {
                $payload = isset($r['guest_payload']) && is_string($r['guest_payload'])
                    ? json_decode($r['guest_payload'], true)
                    : ($r['guest_payload'] ?? []);
                $ansCount = is_array($payload) ? count($payload) : 0;
            }

            $items[] = [
                'id' => (int) $r['raw_id'],
                'type' => $r['participant_type'],
                'quiz_id' => (int) $r['quiz_id'],
                'quiz_title' => $r['quiz_title'] ?? '',
                'user_id' => $r['user_id'] ? (int) $r['user_id'] : null,
                'name' => $r['user_name'] ?? 'N/A',
                'email' => $r['user_email'] ?? null,
                'username' => $r['user_username'] ?? null,
                'guest_token' => $r['guest_token'] ?? null,
                'travel_status' => $r['travel_status'] ?? null,
                'status' => $r['status'] ?? 'submitted',
                'answers_count' => $ansCount,
                'submitted_at' => $r['submitted_at'],
            ];
        }

        return [
            'items' => $items,
            'total' => $total,
        ];
    }

    /**
     * Build detailed question items mapped with options and user answers.
     */
    private function buildQuestionBreakdown(array $questions, array $answersMap): array
    {
        $questionsWithDetails = [];

        foreach ($questions as $question) {
            $details = $this->questionService->getQuestionById((int) $question['id']);
            $options = $details['options'] ?? [];

            $question['options'] = $options;
            $question['accepted_answers'] = $details['accepted_answers'] ?? [];
            $question['user_answer'] = $answersMap[(int) $question['id']] ?? null;
            $questionsWithDetails[] = $question;
        }

        return $questionsWithDetails;
    }

    /**
     * Filter questions based on conditional branch triggers.
     */
    private function filterVisibleQuestions(array $questionsWithDetails, array $savedAnswers): array
    {
        $triggerOptionsMap = [];

        foreach ($questionsWithDetails as $q) {
            foreach ($q['options'] ?? [] as $opt) {
                $rqIds = $opt['related_question_ids'] ?? [];
                foreach ($rqIds as $rqId) {
                    $triggerOptionsMap[(int) $rqId][] = (int) $opt['id'];
                }
            }
        }

        $visibleQuestions = [];
        foreach ($questionsWithDetails as $q) {
            $qId = (int) $q['id'];
            $isVisible = false;

            if (!isset($triggerOptionsMap[$qId])) {
                $isVisible = true;
            } else {
                foreach ($triggerOptionsMap[$qId] as $triggerOptId) {
                    foreach ($savedAnswers as $ans) {
                        if (in_array($triggerOptId, $ans['selected_option_ids'] ?? [], true)) {
                            $isVisible = true;
                            break 2;
                        }
                    }
                }
            }

            if ($isVisible) {
                $visibleQuestions[] = $q;
            }
        }

        return $visibleQuestions;
    }

    /**
     * Format raw travel status code to human-readable label.
     */
    private function formatTravelStatus(?string $status): string
    {
        if ($status === null || $status === '') {
            return '';
        }

        return match ($status) {
            'visited' => 'Đã từng đến Nhật Bản',
            'not-visited' => 'Chưa từng đến Nhật Bản',
            default => $status,
        };
    }
}
