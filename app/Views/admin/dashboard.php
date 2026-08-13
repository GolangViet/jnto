<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

.dashboard-container {
    font-family: 'Inter', sans-serif;
    color: #1e293b;
    margin-top: 10px;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 28px;
}

.dashboard-title {
    font-size: 1.85rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}

.dashboard-subtitle {
    color: #64748b;
    font-size: 0.95rem;
    margin-top: 4px;
}

/* Stat Grid */
.stat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.stat-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    border: 1px solid #f1f5f9;
    position: relative;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
}

.stat-card-blue::before { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
.stat-card-green::before { background: linear-gradient(90deg, #10b981, #34d399); }
.stat-card-purple::before { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
}

.stat-label {
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    color: #64748b;
    letter-spacing: 0.05em;
}

.stat-value {
    font-size: 2.25rem;
    font-weight: 700;
    color: #0f172a;
    margin-top: 8px;
}

/* Two-column page stays layout */
.layout-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    margin-bottom: 32px;
}

@media(max-width: 768px) {
    .layout-grid {
        grid-template-columns: 1fr;
    }
}

.panel-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    border: 1px solid #f1f5f9;
    transition: all 0.3s ease;
}

.panel-card:hover {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
}

.panel-title {
    font-size: 1.15rem;
    font-weight: 600;
    color: #0f172a;
    margin: 0 0 20px 0;
    display: flex;
    align-items: center;
}

.pulse-indicator {
    display: inline-block;
    width: 10px;
    height: 10px;
    background-color: #10b981;
    border-radius: 50%;
    margin-right: 8px;
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    animation: pulse 1.6s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    }
    70% {
        transform: scale(1);
        box-shadow: 0 0 0 6px rgba(16, 185, 129, 0);
    }
    100% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
    }
}

/* Page Stay List */
.page-stay-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.page-stay-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    background: #f8fafc;
    border-radius: 10px;
    border-left: 4px solid #3b82f6;
    transition: all 0.2s ease;
}

.page-stay-item:hover {
    background: #f1f5f9;
    transform: translateX(4px);
}

.page-stay-item.active {
    border-left-color: #10b981;
}

.page-path {
    font-family: monospace;
    font-size: 0.9rem;
    color: #334155;
    font-weight: 600;
}

.page-count {
    background: #e2e8f0;
    padding: 4px 10px;
    border-radius: 9999px;
    font-size: 0.8rem;
    font-weight: 600;
    color: #475569;
}

.page-stay-item.active .page-count {
    background: #d1fae5;
    color: #065f46;
}

/* Leaderboard and Tables */
.table-responsive {
    overflow-x: auto;
    width: 100%;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}

.custom-table {
    width: 100%;
    border-collapse: collapse;
}

.custom-table th {
    background: #f8fafc;
    padding: 14px 16px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    color: #64748b;
    border-bottom: 2px solid #e2e8f0;
    letter-spacing: 0.05em;
    text-align: left;
}

.custom-table td {
    padding: 14px 16px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.9rem;
    color: #334155;
    vertical-align: middle;
}

.custom-table tr {
    transition: background-color 0.2s ease;
}

.custom-table tr:hover {
    background-color: #f8fafc;
}

.rank-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    font-weight: 700;
    font-size: 0.8rem;
}

.rank-1 { background: #fef3c7; color: #d97706; border: 1px solid #fde68a; }
.rank-2 { background: #e2e8f0; color: #475569; border: 1px solid #cbd5e1; }
.rank-3 { background: #ffedd5; color: #ea580c; border: 1px solid #fed7aa; }
.rank-other { background: #f1f5f9; color: #64748b; }

.badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-score {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
}

.badge-percentage {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
}

.user-meta {
    font-size: 0.75rem;
    color: #64748b;
    display: block;
    margin-top: 2px;
}

.empty-state {
    padding: 32px;
    text-align: center;
    color: #64748b;
    font-size: 0.95rem;
}

/* Progress Funnel */
.funnel-container {
    display: flex;
    flex-direction: column;
    gap: 16px;
    margin-top: 12px;
}

.funnel-step {
    background: #f8fafc;
    border-radius: 12px;
    padding: 16px 20px;
    border: 1px solid #e2e8f0;
    transition: all 0.2s ease;
}

.funnel-step:hover {
    transform: scale(1.01);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
}

.funnel-step-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.funnel-step-name {
    font-size: 0.95rem;
    font-weight: 600;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 8px;
}

.funnel-step-badge {
    font-family: monospace;
    background: #e2e8f0;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.8rem;
    color: #475569;
}

.funnel-step-stats {
    font-size: 0.85rem;
    font-weight: 600;
    color: #64748b;
}

.funnel-progress-bg {
    width: 100%;
    height: 8px;
    background: #e2e8f0;
    border-radius: 9999px;
    overflow: hidden;
}

.funnel-progress-bar {
    height: 100%;
    border-radius: 9999px;
    transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.progress-survey { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
.progress-questions { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }
.progress-post { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
.progress-thankyou { background: linear-gradient(90deg, #10b981, #34d399); }

.leaderboard-badge:hover {
    border-color: #3b82f6 !important;
    background-color: #eff6ff !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
}
</style>

<div class="dashboard-container">
    <div class="dashboard-header">
        <div>
            <h1 class="dashboard-title">System Metrics & Dashboard</h1>
            <div class="dashboard-subtitle">Real-time statistics of registered users, page journey completions, and quiz score leaderboards.</div>
        </div>
    </div>

    <!-- Stat Grid -->
    <div class="stat-grid">
        <div class="stat-card stat-card-blue">
            <div class="stat-label">Registered Users</div>
            <div class="stat-value"><?= e((string) $totalUsers) ?></div>
        </div>
        <div class="stat-card stat-card-purple">
            <div class="stat-label">Admin Accounts</div>
            <div class="stat-value"><?= e((string) $totalAdmins) ?></div>
        </div>
        <div class="stat-card stat-card-green">
            <div class="stat-label">Total Accounts</div>
            <div class="stat-value"><?= e((string) $totalRegistered) ?></div>
        </div>
    </div>

    <!-- User Progress / Done Pages Funnel -->
    <div class="panel-card" style="margin-bottom: 32px;">
        <h2 class="panel-title">User Journey Progress (Completed Stages)</h2>
        <p style="color: #64748b; font-size: 0.9rem; margin-top: 0; margin-bottom: 20px;">
            The number of registered users who have completed each page/stage of the survey flow.
        </p>

        <?php
            $totalForPercent = max($totalUsers, 1);
            $surveyPercent = round(($doneSurveyCount / $totalForPercent) * 100);
            $questionsPercent = round(($doneQuestionsCount / $totalForPercent) * 100);
            $confirmPostPercent = round(($doneConfirmPostCount / $totalForPercent) * 100);
            $thankYouPercent = round(($doneThankYouCount / $totalForPercent) * 100);
        ?>

        <div class="funnel-container">
            <!-- Step 1: /take-survey -->
            <div class="funnel-step">
                <div class="funnel-step-header">
                    <div class="funnel-step-name">
                        <span class="rank-badge rank-1" style="width: 22px; height: 22px; font-size: 0.75rem;">1</span>
                        <span>Done Survey Page</span>
                        <span class="funnel-step-badge">/take-survey</span>
                    </div>
                    <div class="funnel-step-stats">
                        <?= e((string)$doneSurveyCount) ?> / <?= e((string)$totalUsers) ?> (<?= $surveyPercent ?>%)
                    </div>
                </div>
                <div class="funnel-progress-bg">
                    <div class="funnel-progress-bar progress-survey" style="width: <?= $surveyPercent ?>%;"></div>
                </div>
            </div>

            <!-- Step 2: /take-questions -->
            <div class="funnel-step">
                <div class="funnel-step-header">
                    <div class="funnel-step-name">
                        <span class="rank-badge rank-2" style="width: 22px; height: 22px; font-size: 0.75rem;">2</span>
                        <span>Done Questions Page</span>
                        <span class="funnel-step-badge">/take-questions</span>
                    </div>
                    <div class="funnel-step-stats">
                        <?= e((string)$doneQuestionsCount) ?> / <?= e((string)$totalUsers) ?> (<?= $questionsPercent ?>%)
                    </div>
                </div>
                <div class="funnel-progress-bg">
                    <div class="funnel-progress-bar progress-questions" style="width: <?= $questionsPercent ?>%;"></div>
                </div>
            </div>

            <!-- Step 3: /confirm-post -->
            <div class="funnel-step">
                <div class="funnel-step-header">
                    <div class="funnel-step-name">
                        <span class="rank-badge rank-3" style="width: 22px; height: 22px; font-size: 0.75rem;">3</span>
                        <span>Done Facebook Post Submission</span>
                        <span class="funnel-step-badge">/confirm-post</span>
                    </div>
                    <div class="funnel-step-stats">
                        <?= e((string)$doneConfirmPostCount) ?> / <?= e((string)$totalUsers) ?> (<?= $confirmPostPercent ?>%)
                    </div>
                </div>
                <div class="funnel-progress-bg">
                    <div class="funnel-progress-bar progress-post" style="width: <?= $confirmPostPercent ?>%;"></div>
                </div>
            </div>

            <!-- Step 4: /thank-you -->
            <div class="funnel-step">
                <div class="funnel-step-header">
                    <div class="funnel-step-name">
                        <span class="rank-badge rank-other" style="width: 22px; height: 22px; font-size: 0.75rem; background: #d1fae5; color: #065f46;">4</span>
                        <span>Reached Thank You Page (Fully Done)</span>
                        <span class="funnel-step-badge">/thank-you</span>
                    </div>
                    <div class="funnel-step-stats">
                        <?= e((string)$doneThankYouCount) ?> / <?= e((string)$totalUsers) ?> (<?= $thankYouPercent ?>%)
                    </div>
                </div>
                <div class="funnel-progress-bg">
                    <div class="funnel-progress-bar progress-thankyou" style="width: <?= $thankYouPercent ?>%;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaderboard: Top Users with the Best Quiz Score -->
    <div class="panel-card" style="margin-bottom: 24px;">
        <h2 class="panel-title" style="margin-bottom: 12px;">Top Quiz Leaderboard</h2>
        <p style="color: #64748b; font-size: 0.9rem; margin-top: 0; margin-bottom: 20px;">Top unique performers based on their highest percentage and raw score across quiz submissions.</p>

        <?php if (empty($leaderboard)): ?>
            <div class="empty-state" style="padding: 48px;">No quiz attempts have been submitted yet.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th style="width: 60px; text-align: center;">Rank</th>
                            <th style="width: 250px;">User</th>
                            <th>Quiz Submissions &amp; Results</th>
                            <th style="width: 180px;">Last Activity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($leaderboard as $index => $row): ?>
                            <?php
                                $rank = $index + 1;
                                $rankClass = 'rank-other';
                                if ($rank === 1) $rankClass = 'rank-1';
                                elseif ($rank === 2) $rankClass = 'rank-2';
                                elseif ($rank === 3) $rankClass = 'rank-3';
                            ?>
                            <tr>
                                <td style="text-align: center; vertical-align: middle;">
                                    <span class="rank-badge <?= $rankClass ?>"><?= $rank ?></span>
                                </td>
                                <td style="vertical-align: middle;">
                                    <strong><?= e($row['user_name']) ?></strong>
                                    <span class="user-meta">@<?= e($row['user_username']) ?> | ID: <?= e((string)$row['user_id']) ?></span>
                                    <?php if (!empty($row['facebook_url'])): ?>
                                        <div style="margin-top: 6px;">
                                            <a href="<?= e($row['facebook_url']) ?>" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.75rem; color: #1877f2; text-decoration: none; font-weight: 500; transition: color 0.2s;" class="facebook-link-leaderboard" onmouseover="this.style.color='#115cbe'" onmouseout="this.style.color='#1877f2'">
                                                <svg style="width: 12px; height: 12px; fill: currentColor;" viewBox="0 0 24 24">
                                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                                </svg>
                                                <span>Facebook Post</span>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td style="vertical-align: middle;">
                                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                        <?php foreach ($row['quizzes'] as $qid => $quiz): ?>
                                            <a href="<?= url('admin/quiz-attempts/' . (int)$quiz['attempt_id']) ?>" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 6px 12px; display: inline-flex; align-items: center; gap: 8px; font-size: 0.8rem; box-shadow: 0 1px 2px rgba(0,0,0,0.02); text-decoration: none; color: inherit; transition: all 0.2s;" class="leaderboard-badge">
                                                <span style="font-weight: 600; color: #475569;"><?= e($quiz['quiz_title']) ?>:</span>
                                                <span class="badge badge-percentage" style="padding: 2px 6px; font-size: 0.7rem;"><?= e((string)(float)$quiz['percentage']) ?>%</span>
                                                <span class="badge badge-score" style="padding: 2px 6px; font-size: 0.7rem;"><?= e((string)(float)$quiz['score']) ?>/<?= e((string)(float)$quiz['total_score']) ?></span>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td style="color: #64748b; font-size: 0.85rem; vertical-align: middle;">
                                    <?= date('Y-m-d H:i:s', strtotime($row['last_submitted_at'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
