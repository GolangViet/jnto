<?php push_style(asset_with_version('/assets/css/user/form.css')); ?>

<?php
$mainQuizId = setting('main_quiz_quiz_id');
$mainSurveyId = setting('main_survey_quiz_id');
$mainOpenId = setting('main_open_quiz_id');
?>

<?php if ($mainQuizId || $mainSurveyId || $mainOpenId): ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-bottom: 48px; animation: fadeInUp 0.4s ease-out;">
        <?php if ($mainQuizId): ?>
            <div class="card" style="background: linear-gradient(135deg, #1e1b4b 0%, #311042 100%); border: 1px solid rgba(255,255,255,0.08); color: #ffffff; padding: 32px; border-radius: 18px; display: flex; flex-direction: column; justify-content: space-between; overflow: hidden; box-shadow: 0 10px 30px -10px rgba(79, 70, 229, 0.3); margin-bottom: 0;">
                <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; background: radial-gradient(circle, rgba(129, 140, 248, 0.15) 0%, transparent 70%); border-radius: 50%;"></div>
                <div style="position: relative; z-index: 1;">
                    <span style="display: inline-block; padding: 4px 10px; border-radius: 9999px; background: rgba(129, 140, 248, 0.2); color: #a5b4fc; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 12px;">★ Featured Knowledge Quiz</span>
                    <h2 style="font-size: 1.6rem; font-weight: 700; margin: 0 0 10px; color: #ffffff; line-height: 1.3;">Test Your Japan Knowledge</h2>
                    <p style="color: #cbd5e1; font-size: 0.95rem; line-height: 1.5; margin-bottom: 24px;">Challenge yourself with our featured quiz, discover new regions, culture and see your score instantly.</p>
                </div>
                <div style="position: relative; z-index: 1;">
                    <a href="/quizzes/<?= (int)$mainQuizId ?>" class="btn" style="background: #ffffff; color: #1e1b4b; padding: 12px 24px; border-radius: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; width: 100%; box-sizing: border-box; justify-content: center; box-shadow: 0 4px 12px rgba(255, 255, 255, 0.15); transition: all 0.2s;">
                        Start Quiz Now
                        <svg style="width: 18px; height: 18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($mainSurveyId): ?>
            <div class="card" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border: 1px solid rgba(255,255,255,0.08); color: #ffffff; padding: 32px; border-radius: 18px; display: flex; flex-direction: column; justify-content: space-between; overflow: hidden; box-shadow: 0 10px 30px -10px rgba(244, 63, 94, 0.2); margin-bottom: 0;">
                <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; background: radial-gradient(circle, rgba(244, 63, 94, 0.12) 0%, transparent 70%); border-radius: 50%;"></div>
                <div style="position: relative; z-index: 1;">
                    <span style="display: inline-block; padding: 4px 10px; border-radius: 9999px; background: rgba(244, 63, 94, 0.2); color: #fda4af; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 12px;">📋 Official Survey</span>
                    <h2 style="font-size: 1.6rem; font-weight: 700; margin: 0 0 10px; color: #ffffff; line-height: 1.3;">Help Us Improve</h2>
                    <p style="color: #cbd5e1; font-size: 0.95rem; line-height: 1.5; margin-bottom: 24px;">Share your feedback and travel preferences through our official survey to shape future tourism projects.</p>
                </div>
                <div style="position: relative; z-index: 1;">
                    <a href="/quizzes/<?= (int)$mainSurveyId ?>" class="btn" style="background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%); color: #ffffff; padding: 12px 24px; border-radius: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; width: 100%; box-sizing: border-box; justify-content: center; box-shadow: 0 4px 12px rgba(244, 63, 94, 0.3); transition: all 0.2s;">
                        Take Survey
                        <svg style="width: 18px; height: 18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($mainOpenId): ?>
            <div class="card" style="background: linear-gradient(135deg, #064e3b 0%, #022c22 100%); border: 1px solid rgba(255,255,255,0.08); color: #ffffff; padding: 32px; border-radius: 18px; display: flex; flex-direction: column; justify-content: space-between; overflow: hidden; box-shadow: 0 10px 30px -10px rgba(16, 185, 129, 0.2); margin-bottom: 0;">
                <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; background: radial-gradient(circle, rgba(16, 185, 129, 0.15) 0%, transparent 70%); border-radius: 50%;"></div>
                <div style="position: relative; z-index: 1;">
                    <span style="display: inline-block; padding: 4px 10px; border-radius: 9999px; background: rgba(16, 185, 129, 0.2); color: #a7f3d0; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 12px;">💬 Open Questions</span>
                    <h2 style="font-size: 1.6rem; font-weight: 700; margin: 0 0 10px; color: #ffffff; line-height: 1.3;">Express Your Thoughts</h2>
                    <p style="color: #cbd5e1; font-size: 0.95rem; line-height: 1.5; margin-bottom: 24px;">Provide detailed answers, ideas, and open feedback about your travel experiences in Japan.</p>
                </div>
                <div style="position: relative; z-index: 1;">
                    <a href="/quizzes/<?= (int)$mainOpenId ?>" class="btn" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; padding: 12px 24px; border-radius: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; width: 100%; box-sizing: border-box; justify-content: center; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); transition: all 0.2s;">
                        Answer Questions
                        <svg style="width: 18px; height: 18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div style="margin-bottom: 40px; animation: fadeIn 0.4s ease-out;">
    <h1 style="font-size: 2.25rem; font-weight: 800; margin: 0 0 8px; background: linear-gradient(to right, #1e293b, #475569); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Latest Publications</h1>
    <p style="color: var(--text-muted); margin: 0; font-size: 1.1rem;">Stay updated with our latest news and announcements.</p>
</div>

<?php if (empty($posts)): ?>
    <div class="card" style="text-align: center; padding: 48px; color: var(--text-muted);">
        <svg style="width: 48px; height: 48px; margin: 0 auto 16px; color: var(--text-muted); opacity: 0.5;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
        </svg>
        <h3 style="margin: 0 0 8px; color: var(--text-main); font-weight: 600;">No posts found</h3>
        <p style="margin: 0;">Check back later for new content!</p>
    </div>
<?php else: ?>
    <div style="display: grid; gap: 24px;">
        <?php foreach ($posts as $post): ?>
            <article class="card" style="margin-bottom: 0;">
                <h2 style="font-size: 1.5rem; font-weight: 700; margin: 0 0 12px; color: var(--text-main);">
                    <?= e($post['title']) ?>
                </h2>
                <div style="color: #334155; margin-bottom: 20px; font-size: 1rem; line-height: 1.6;">
                    <?= nl2br(e($post['content'])) ?>
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--border); padding-top: 16px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <svg class="muted" style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="muted"><?= e(date('F j, Y', strtotime($post['created_at'] ?? 'now'))) ?></span>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php push_script(asset_with_version('/assets/js/home.js')); ?>

