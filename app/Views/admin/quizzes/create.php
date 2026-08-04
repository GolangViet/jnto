<div style="margin-bottom: 24px; animation: fadeIn 0.3s ease-out;">
    <a href="/admin/quizzes" style="text-decoration: none; color: #4f46e5; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; font-size: 0.9rem;">
        <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back to Quizzes
    </a>
    <h1 style="margin: 8px 0 0; font-size: 2rem; font-weight: 700; color: #111827;">Create New Quiz</h1>
</div>

<?php if ($errors = app()->session()->pullFlash('errors')): ?>
    <div class="alert error" style="margin-bottom: 20px;">
        <ul style="margin: 0; padding-left: 20px;">
            <?php foreach ($errors as $field => $fieldErrors): ?>
                <?php foreach ($fieldErrors as $err): ?>
                    <li><?= e($err) ?></li>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card" style="animation: fadeIn 0.4s ease-out; padding: 28px;">
    <form action="/admin/quizzes" method="post">
        <?= csrf_field() ?>

        <div style="margin-bottom: 18px;">
            <label for="title" style="font-weight: 600; color: #374151; font-size: 0.95rem;">Quiz Title *</label>
            <input type="text" id="title" name="title" value="<?= e(old('title')) ?>" required placeholder="e.g., Khám phá Nhật Bản">
        </div>

        <div style="margin-bottom: 18px;">
            <label for="description" style="font-weight: 600; color: #374151; font-size: 0.95rem;">Description</label>
            <textarea id="description" name="description" rows="4" placeholder="Brief details about the quiz topic..."><?= e(old('description')) ?></textarea>
        </div>

        <div style="margin-bottom: 18px;">
            <div>
                <label for="status" style="font-weight: 600; color: #374151; font-size: 0.95rem;">Status</label>
                <select id="status" name="status">
                    <option value="draft" <?= old('status', 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="published" <?= old('status') === 'published' ? 'selected' : '' ?>>Published</option>
                    <option value="inactive" <?= old('status') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
        </div>


        <div class="d-none">
            <label for="duration_minutes" style="font-weight: 600; color: #374151; font-size: 0.95rem;">Duration (Minutes)</label>
            <input type="number" id="duration_minutes" name="duration_minutes" value="<?= (old('duration_minutes') !== null && old('duration_minutes') !== '' && (int)old('duration_minutes') > 0) ? e((string)old('duration_minutes')) : '' ?>" min="1" placeholder="Leave empty for unlimited time">
        </div>

        <div style="display: none; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 18px;">
            <div>
                <label for="pass_score" style="font-weight: 600; color: #374151; font-size: 0.95rem;">Pass Score (%)</label>
                <input type="number" step="0.1" id="pass_score" name="pass_score" value="<?= e(old('pass_score', '0')) ?>" min="0" max="100" placeholder="e.g., 70">
            </div>
            <div>
                <label for="allow_resume" style="font-weight: 600; color: #374151; font-size: 0.95rem; display: block; margin-bottom: 8px;">Resume Unfinished Attempt</label>
                <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; margin-top: 10px;">
                    <input type="checkbox" id="allow_resume" name="allow_resume" value="1" <?= old('allow_resume', '') === '1' ? 'checked' : '' ?> style="width: auto; margin: 0;">
                    Allow users to resume unfinished attempt
                </label>
            </div>
        </div>

        <div style="display: none; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 18px;">
            <div>
                <label for="show_result" style="font-weight: 600; color: #374151; font-size: 0.95rem; display: block; margin-bottom: 8px;">Results Settings</label>
                <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; margin-top: 10px;">
                    <input type="checkbox" id="show_result" name="show_result" value="1" <?= old('show_result', '') === '1' ? 'checked' : '' ?> style="width: auto; margin: 0;">
                    Show final score and review after completion
                </label>
            </div>
            <div>
                <label for="show_correct_answer" style="font-weight: 600; color: #374151; font-size: 0.95rem; display: block; margin-bottom: 8px;">Correct Answers Settings</label>
                <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; margin-top: 10px;">
                    <input type="checkbox" id="show_correct_answer" name="show_correct_answer" value="1" <?= old('show_correct_answer', '') === '1' ? 'checked' : '' ?> style="width: auto; margin: 0;">
                    Show correct answers in the review list
                </label>
            </div>
        </div>

        <div style="display: none; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
            <div>
                <label for="start_at" style="font-weight: 600; color: #374151; font-size: 0.95rem;">Start Date & Time</label>
                <input type="datetime-local" id="start_at" name="start_at" value="<?= e(old('start_at')) ?>">
            </div>
            <div>
                <label for="end_at" style="font-weight: 600; color: #374151; font-size: 0.95rem;">End Date & Time</label>
                <input type="datetime-local" id="end_at" name="end_at" value="<?= e(old('end_at')) ?>">
            </div>
        </div>

        <div style="border-top: 1px solid #e5e7eb; padding-top: 20px; display: flex; justify-content: flex-end; gap: 12px;">
            <a href="/admin/quizzes" class="btn" style="background: #9ca3af; text-decoration: none;">Cancel</a>
            <button type="submit" class="btn" style="background: #4f46e5;">Save Quiz</button>
        </div>
    </form>
</div>
