<div style="margin-bottom: 32px; animation: fadeIn 0.4s ease-out;">
    <h1 style="font-size: 2.25rem; font-weight: 800; margin: 0 0 8px; background: linear-gradient(to right, #4f46e5, #818cf8); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Available Quizzes</h1>
    <p style="color: var(--text-muted); margin: 0; font-size: 1.1rem;">Test your knowledge about Japan and earn high scores.</p>
</div>

<?php if (empty($quizzes)): ?>
    <div class="card" style="text-align: center; padding: 48px; color: var(--text-muted);">
        <svg style="width: 48px; height: 48px; margin: 0 auto 16px; color: var(--text-muted); opacity: 0.5;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
        </svg>
        <h3 style="margin: 0 0 8px; color: var(--text-main); font-weight: 600;">No quizzes available</h3>
        <p style="margin: 0;">Please check back later for published quizzes!</p>
    </div>
<?php else: ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px; animation: fadeInUp 0.4s ease-out;">
        <?php foreach ($quizzes as $quiz): ?>
            <div class="card" style="margin-bottom: 0; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <h2 style="font-size: 1.35rem; font-weight: 700; margin: 0 0 10px; color: var(--text-main);">
                        <?= e($quiz['title']) ?>
                    </h2>
                    <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 20px; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                        <?= e($quiz['description'] ?: 'No description provided.') ?>
                    </p>
                </div>

                <div>
                    <div style="border-top: 1px solid var(--border); padding-top: 16px; margin-bottom: 18px; display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 0.85rem; color: var(--text-muted);">
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <svg style="width: 16px; height: 16px; color: var(--primary);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Questions: <strong><?= (int)$quiz['questions_count'] ?></strong></span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <svg style="width: 16px; height: 16px; color: var(--primary);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Duration: <strong><?= $quiz['duration_minutes'] ? e((string)$quiz['duration_minutes']) . ' min' : 'Unlimited' ?></strong></span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <svg style="width: 16px; height: 16px; color: var(--primary);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Pass Score: <strong><?= $quiz['pass_score'] !== null ? e((string)(float)$quiz['pass_score']) . '%' : 'Any' ?></strong></span>
                        </div>
                    </div>
                    
                    <div style="position: relative;">
                        <?php
                        component('dropdown', [
                            'id' => 'quiz-opt-' . $quiz['id'],
                            'buttonHtml' => 'Quiz Options <svg style="width: 12px; height: 12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>',
                            'buttonStyle' => 'background: var(--primary); font-size: 0.95rem; padding: 10px 22px; display: inline-flex; align-items: center; gap: 4px; font-weight: 600; width: 100%; box-sizing: border-box; justify-content: center;',
                            'items' => [
                                ['type' => 'link', 'text' => 'View Details & Instructions', 'url' => "/quizzes/{$quiz['id']}"],
                                ['type' => 'form', 'text' => 'Start Quiz Attempt', 'action' => "/quizzes/{$quiz['id']}/start"]
                            ]
                        ]);
                        ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
