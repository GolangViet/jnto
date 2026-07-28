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
