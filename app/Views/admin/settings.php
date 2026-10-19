<div style="margin-bottom: 24px; animation: fadeIn 0.3s ease-out;">
    <h1 style="margin: 0; font-size: 2rem; font-weight: 700; color: #111827;">System Settings</h1>
    <p class="muted" style="margin: 4px 0 0;">Configure global settings, main survey, and quiz question sets.</p>
</div>

<div class="card" style="animation: fadeIn 0.4s ease-out; padding: 28px; max-width: 650px;">
    <form action="<?= url('/admin/settings') ?>" method="post">
        <?= csrf_field() ?>

        <div style="margin-bottom: 24px;">
            <label for="main_survey_quiz_id" style="font-weight: 600; color: #374151; font-size: 0.95rem; display: block; margin-bottom: 8px;">Main Survey Question Set</label>
            <span class="muted" style="display: block; margin-top: -4px; margin-bottom: 8px; font-size: 0.85rem;">Select the default question set to be used for general user surveys.</span>
            <select id="main_survey_quiz_id" name="main_survey_quiz_id" style="margin-bottom: 0;">
                <option value="none" <?= !isset($settings['main_survey_quiz_id']) || $settings['main_survey_quiz_id'] === null ? 'selected' : '' ?>>-- None / Not Set --</option>
                <?php foreach ($quizzes as $quiz): ?>
                    <option value="<?= (int) $quiz['id'] ?>" <?= isset($settings['main_survey_quiz_id']) && (int)$settings['main_survey_quiz_id'] === (int)$quiz['id'] ? 'selected' : '' ?>>
                        <?= e($quiz['title']) ?> (ID: <?= (int)$quiz['id'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin-bottom: 24px;">
            <label for="main_quiz_quiz_id" style="font-weight: 600; color: #374151; font-size: 0.95rem; display: block; margin-bottom: 8px;">Main Quiz Question Set</label>
            <span class="muted" style="display: block; margin-top: -4px; margin-bottom: 8px; font-size: 0.85rem;">Select the primary quiz to highlight for testing knowledge.</span>
            <select id="main_quiz_quiz_id" name="main_quiz_quiz_id" style="margin-bottom: 0;">
                <option value="none" <?= !isset($settings['main_quiz_quiz_id']) || $settings['main_quiz_quiz_id'] === null ? 'selected' : '' ?>>-- None / Not Set --</option>
                <?php foreach ($quizzes as $quiz): ?>
                    <option value="<?= (int) $quiz['id'] ?>" <?= isset($settings['main_quiz_quiz_id']) && (int)$settings['main_quiz_quiz_id'] === (int)$quiz['id'] ? 'selected' : '' ?>>
                        <?= e($quiz['title']) ?> (ID: <?= (int)$quiz['id'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin-bottom: 24px;">
            <label for="main_open_quiz_id" style="font-weight: 600; color: #374151; font-size: 0.95rem; display: block; margin-bottom: 8px;">Main Open Question Set</label>
            <span class="muted" style="display: block; margin-top: -4px; margin-bottom: 8px; font-size: 0.85rem;">Select the default open-ended text question set.</span>
            <select id="main_open_quiz_id" name="main_open_quiz_id" style="margin-bottom: 0;">
                <option value="none" <?= !isset($settings['main_open_quiz_id']) || $settings['main_open_quiz_id'] === null ? 'selected' : '' ?>>-- None / Not Set --</option>
                <?php foreach ($quizzes as $quiz): ?>
                    <option value="<?= (int) $quiz['id'] ?>" <?= isset($settings['main_open_quiz_id']) && (int)$settings['main_open_quiz_id'] === (int)$quiz['id'] ? 'selected' : '' ?>>
                        <?= e($quiz['title']) ?> (ID: <?= (int)$quiz['id'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin-bottom: 24px;">
            <label style="font-weight: 600; color: #374151; font-size: 0.95rem; display: block; margin-bottom: 8px;">Leaderboard Quizzes to Show</label>
            <span class="muted" style="display: block; margin-top: -4px; margin-bottom: 12px; font-size: 0.85rem;">Select which quiz question sets should be visible on the top quiz leaderboard. If none are selected, all quiz submissions will be shown.</span>
            <div style="display: flex; flex-direction: column; gap: 10px; max-height: 200px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; background: #f9fafb;">
                <?php
                    $visibleQuizzes = isset($settings['leaderboard_visible_quizzes']) && $settings['leaderboard_visible_quizzes'] !== null
                        ? explode(',', $settings['leaderboard_visible_quizzes'])
                        : [];
                ?>
                <?php foreach ($quizzes as $quiz): ?>
                    <label style="display: flex; align-items: center; gap: 10px; font-weight: 500; color: #374151; font-size: 0.9rem; cursor: pointer; user-select: none; margin: 0;">
                        <input type="checkbox" name="leaderboard_visible_quizzes[]" value="<?= (int)$quiz['id'] ?>" <?= in_array((string)$quiz['id'], $visibleQuizzes) ? 'checked' : '' ?> style="width: 16px; height: 16px; margin: 0; cursor: pointer;">
                        <span><?= e($quiz['title']) ?> <span style="color: #6b7280; font-size: 0.8rem;">(ID: <?= (int)$quiz['id'] ?>)</span></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div style="border-top: 1px solid #e5e7eb; padding-top: 20px; margin-top: 20px; display: flex; justify-content: flex-end;">
            <button type="submit" class="btn" style="background: #4f46e5; display: inline-flex; align-items: center; gap: 6px;">
                <svg style="width: 18px; height: 18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2v-9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                </svg>
                Save Settings
            </button>
        </div>
    </form>
</div>
