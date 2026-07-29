<div style="margin-bottom: 24px; animation: fadeIn 0.3s ease-out;">
    <a href="/quizzes" style="text-decoration: none; color: var(--primary); font-weight: 600; display: inline-flex; align-items: center; gap: 4px; font-size: 0.95rem;">
        <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back to Quizzes
    </a>
</div>

<div class="card" style="max-width: 650px; margin: 0 auto; padding: 36px; animation: fadeInUp 0.4s ease-out;">
    <h1 style="font-size: 2rem; font-weight: 800; margin: 0 0 16px; color: var(--text-main); text-align: center;">
        <?= e($quiz['title']) ?>
    </h1>
    
    <p style="color: var(--text-muted); font-size: 1.05rem; line-height: 1.6; text-align: center; margin-bottom: 32px;">
        <?= e($quiz['description'] ?: 'No description available for this quiz.') ?>
    </p>

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 36px; text-align: center; border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); padding: 24px 0;">
        <div>
            <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary);"><?= (int)$questionsCount ?></div>
            <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500; text-transform: uppercase; margin-top: 4px;">Questions</div>
        </div>
        <div>
            <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary);">
                <?= $quiz['duration_minutes'] ? e((string)$quiz['duration_minutes']) . 'm' : '∞' ?>
            </div>
            <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500; text-transform: uppercase; margin-top: 4px;">Duration</div>
        </div>
        <div>
            <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary);">
                <?= $quiz['pass_score'] !== null ? e((string)(float)$quiz['pass_score']) . '%' : 'Any' ?>
            </div>
            <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500; text-transform: uppercase; margin-top: 4px;">Pass Score</div>
        </div>
    </div>

    <div style="background: rgba(79, 70, 229, 0.05); padding: 20px; border-radius: 12px; border: 1px solid rgba(79, 70, 229, 0.1); margin-bottom: 36px;">
        <h4 style="margin: 0 0 8px; color: var(--primary); font-size: 0.95rem; font-weight: 700;">Instructions:</h4>
        <ul style="margin: 0; padding-left: 20px; font-size: 0.9rem; color: var(--text-main); line-height: 1.6;">
            <li>Ensure you have a stable internet connection before beginning.</li>
            <?php if ($quiz['duration_minutes']): ?>
                <li>You will have exactly <strong><?= e((string)$quiz['duration_minutes']) ?> minutes</strong> to complete the quiz once started.</li>
            <?php endif; ?>
            <li>You can navigate between questions during the quiz.</li>
            <li>Answers are automatically saved as you navigate or click option buttons.</li>
            <li>Click "Submit Quiz" at the end when you are finished.</li>
        </ul>
    </div>

    <form action="/quizzes/<?= (int)$quiz['id'] ?>/start" method="post" style="text-align: center;">
        <?= csrf_field() ?>
        <button type="submit" class="btn" style="padding: 14px 40px; font-size: 1.1rem; border-radius: 12px; font-weight: 700; width: 100%;">
            Start Quiz Attempt
        </button>
    </form>
</div>
