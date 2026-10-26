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

        <div style="margin-bottom: 24px; padding: 18px; border-radius: 8px; border: 1px solid <?= !empty($settings['maintenance_mode']) && $settings['maintenance_mode'] === '1' ? '#fca5a5; background: #fff5f5;' : '#e5e7eb; background: #f9fafb;' ?>">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                <div>
                    <label for="maintenance_mode" style="font-weight: 700; color: #111827; font-size: 1rem; margin-bottom: 2px; display: inline-flex; align-items: center; gap: 8px;">
                        <span>Maintenance Mode</span>
                        <?php if (!empty($settings['maintenance_mode']) && $settings['maintenance_mode'] === '1'): ?>
                            <span style="display: inline-block; padding: 2px 8px; font-size: 0.75rem; font-weight: 600; color: #991b1b; background: #fee2e2; border-radius: 9999px; border: 1px solid #f87171;">ACTIVE (SITE OFFLINE)</span>
                        <?php else: ?>
                            <span style="display: inline-block; padding: 2px 8px; font-size: 0.75rem; font-weight: 600; color: #065f46; background: #d1fae5; border-radius: 9999px; border: 1px solid #6ee7b7;">INACTIVE (LIVE)</span>
                        <?php endif; ?>
                    </label>
                    <span class="muted" style="display: block; font-size: 0.85rem;">When enabled, all public-facing pages show a maintenance page to visitors.</span>
                </div>
                <div>
                    <label style="position: relative; display: inline-flex; align-items: center; cursor: pointer; user-select: none;">
                        <input type="checkbox" id="maintenance_mode" name="maintenance_mode" value="1" <?= !empty($settings['maintenance_mode']) && $settings['maintenance_mode'] === '1' ? 'checked' : '' ?> style="width: 20px; height: 20px; cursor: pointer; accent-color: #dc2626;">
                    </label>
                </div>
            </div>

            <div style="margin-top: 14px;">
                <label for="maintenance_message" style="font-weight: 600; color: #374151; font-size: 0.9rem; display: block; margin-bottom: 6px;">Maintenance Page Custom Content (HTML / CSS supported)</label>
                <textarea id="maintenance_message" name="maintenance_message" rows="5" placeholder="Leave empty to show the default announcement card, or enter custom HTML / <style> CSS to completely override the maintenance page content..." style="margin-bottom: 6px; font-size: 0.88rem; font-family: monospace; line-height: 1.5;"><?= e($settings['maintenance_message'] ?? '') ?></textarea>
                <span class="muted" style="display: block; font-size: 0.8rem; color: #6b7280;">Supports raw HTML &amp; <code>&lt;style&gt;</code> tags to customize styling. If left empty, the standard JNTO announcement card will be displayed as normal.</span>
            </div>

            <div style="margin-top: 12px; padding: 10px 12px; background: #eff6ff; border-left: 4px solid #3b82f6; border-radius: 4px; font-size: 0.82rem; color: #1e40af;">
                <strong>Notice:</strong> The Admin Panel (<code style="background: #dbeafe; padding: 1px 4px; border-radius: 3px;">/admin/*</code>) and logged-in administrator accounts will always remain accessible so you can manage the site and disable maintenance mode when ready.
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
