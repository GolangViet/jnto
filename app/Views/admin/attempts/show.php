<?php
$isPassed = filter_var($attempt['passed'] ?? false, FILTER_VALIDATE_BOOL);
$isSurvey = (int)$attempt['quiz_id'] === (int)setting('main_survey_quiz_id');
?>
<div style="margin-bottom: 24px;">
    <a href="javascript:history.back()" style="text-decoration: none; color: #4f46e5; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; font-size: 0.9rem;">
        <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Go Back
    </a>
    <h1 style="margin: 8px 0 0; font-size: 2rem; font-weight: 700; color: #111827;">Attempt Details</h1>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
    <!-- User Info Card -->
    <div class="card" style="padding: 24px;">
        <h3 style="margin: 0 0 16px; color: #111827; border-bottom: 1px solid #e5e7eb; padding-bottom: 8px; font-size: 1.1rem;">User Information</h3>
        <table style="width: 100%; border: none;">
            <tr style="border-bottom: none;"><td style="padding: 6px 0; font-weight: 600; color: #4b5563; width: 120px;">Name:</td><td style="padding: 6px 0; color: #111827;"><?= e($attempt['user_name']) ?></td></tr>
            <tr style="border-bottom: none;"><td style="padding: 6px 0; font-weight: 600; color: #4b5563;">Email:</td><td style="padding: 6px 0; color: #111827;"><?= e($attempt['user_email']) ?></td></tr>
            <tr style="border-bottom: none;"><td style="padding: 6px 0; font-weight: 600; color: #4b5563;">User ID:</td><td style="padding: 6px 0; color: #111827;"><?= (int)$attempt['user_id'] ?></td></tr>
        </table>
    </div>

    <!-- Attempt Stats Card -->
    <div class="card" style="padding: 24px;">
        <h3 style="margin: 0 0 16px; color: #111827; border-bottom: 1px solid #e5e7eb; padding-bottom: 8px; font-size: 1.1rem;">Attempt Status</h3>
        <table style="width: 100%; border: none;">
            <tr style="border-bottom: none;"><td style="padding: 6px 0; font-weight: 600; color: #4b5563; width: 120px;">Quiz:</td><td style="padding: 6px 0; color: #111827; font-weight: 600;"><?= e($attempt['quiz_title']) ?></td></tr>
            <?php if ($isSurvey): ?>
                <tr style="border-bottom: none;"><td style="padding: 6px 0; font-weight: 600; color: #4b5563;">Type:</td><td style="padding: 6px 0; color: #111827; font-weight: 600;">Survey</td></tr>
                <tr style="border-bottom: none;"><td style="padding: 6px 0; font-weight: 600; color: #4b5563;">Responses:</td><td style="padding: 6px 0; color: #111827;"><?= count(array_filter($questions, fn($q) => $q['user_answer'] !== null)) ?> / <?= count($questions) ?> answered</td></tr>
            <?php else: ?>
                <tr style="border-bottom: none;"><td style="padding: 6px 0; font-weight: 600; color: #4b5563;">Score:</td><td style="padding: 6px 0; color: #111827;"><?= e((string)(float)$attempt['score']) ?> / <?= e((string)(float)$attempt['total_score']) ?> (<?= e((string)(float)$attempt['percentage']) ?>%)</td></tr>
                <tr style="border-bottom: none;"><td style="padding: 6px 0; font-weight: 600; color: #4b5563;">Result:</td><td style="padding: 6px 0;">
                    <span class="btn <?= $isPassed ? '' : 'danger' ?>" style="padding: 2px 8px; font-size: 0.8rem; cursor: default; font-weight: 600;">
                        <?= $isPassed ? 'PASSED' : 'FAILED' ?>
                    </span>
                </td></tr>
            <?php endif; ?>
            <tr style="border-bottom: none;"><td style="padding: 6px 0; font-weight: 600; color: #4b5563;">Submitted At:</td><td style="padding: 6px 0; color: #111827;">
                <?= $attempt['submitted_at'] ? date('Y-m-d H:i:s', strtotime($attempt['submitted_at'])) : '<span class="muted">In Progress / Unsubmitted</span>' ?>
            </td></tr>
        </table>
    </div>
</div>

<!-- Question Breakdown -->
<h2 style="font-size: 1.5rem; font-weight: 700; color: #111827; margin: 32px 0 16px;">Question Breakdown</h2>

<?php if (empty($questions)): ?>
    <div class="card" style="padding: 32px; text-align: center; color: #6b7280;">
        No questions details found for this attempt.
    </div>
<?php else: ?>
    <?php foreach ($questions as $index => $q): ?>
        <?php
        $ans = $q['user_answer'];
        $isCorrect = $ans && filter_var($ans['is_correct'] ?? false, FILTER_VALIDATE_BOOL);
        
        if ($isSurvey) {
            $cardBorderColor = '#4f46e5';
            $badgeBg = '#e0e7ff';
            $badgeColor = '#4f46e5';
            $badgeText = $ans ? 'Answered' : 'Unanswered';
        } else {
            $cardBorderColor = $isCorrect ? '#10b981' : '#ef4444';
            $badgeBg = $isCorrect ? '#d1fae5' : '#fee2e2';
            $badgeColor = $isCorrect ? '#065f46' : '#991b1b';
            $badgeText = ($isCorrect ? 'Correct' : 'Incorrect') . ' (Score: ' . e((string)(float)($ans ? ($ans['awarded_score'] ?? 0) : 0)) . '/' . e((string)(float)$q['score']) . ')';
        }
        ?>
        <div class="card" style="border-left: 6px solid <?= $cardBorderColor ?>; padding: 24px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <span style="font-weight: 700; font-size: 0.85rem; color: #6b7280; text-transform: uppercase;">
                    QUESTION <?= $index + 1 ?>
                </span>
                <span style="font-size: 0.75rem; font-weight: 700; padding: 4px 12px; border-radius: 9999px; background: <?= $badgeBg ?>; color: <?= $badgeColor ?>; text-transform: uppercase;">
                    <?= $badgeText ?>
                </span>
            </div>

            <h3 style="font-size: 1.15rem; font-weight: 700; color: #111827; margin: 0 0 20px; line-height: 1.4;">
                <?= e($q['question_text']) ?>
            </h3>

            <!-- Responses Section -->
            <div style="font-size: 0.95rem; line-height: 1.6; margin-bottom: 20px;">
                <?php if (in_array($q['type'], ['single_choice', 'multiple_choice', 'true_false'], true)): ?>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <?php foreach ($q['options'] as $opt): ?>
                            <?php
                            $isUserSelected = in_array((int)$opt['id'], $ans ? ($ans['selected_option_ids'] ?? []) : []);
                            $isCorrectOpt = filter_var($opt['is_correct'] ?? false, FILTER_VALIDATE_BOOL);
                            
                            $optBorder = '#e5e7eb';
                            $optBg = 'transparent';
                            $optIndicator = '';

                            if ($isSurvey) {
                                if ($isUserSelected) {
                                    $optBorder = '#4f46e5';
                                    $optBg = '#e0e7ff';
                                    $optIndicator = ' <span style="color: #4f46e5; font-weight: 700; font-size: 0.8rem; background: #c7d2fe; padding: 2px 6px; border-radius: 4px;">User Choice</span>';
                                }
                            } else {
                                if ($isUserSelected) {
                                    $optBorder = $isCorrectOpt ? '#10b981' : '#ef4444';
                                    $optBg = $isCorrectOpt ? '#e6f4ea' : '#fce8e6';
                                    $optIndicator = $isCorrectOpt 
                                        ? ' <span style="color: #137333; font-weight: 700; font-size: 0.8rem; background: #c2e7c9; padding: 2px 6px; border-radius: 4px;">User Choice - Correct</span>' 
                                        : ' <span style="color: #c5221f; font-weight: 700; font-size: 0.8rem; background: #fad2cf; padding: 2px 6px; border-radius: 4px;">User Choice - Incorrect</span>';
                                } elseif ($isCorrectOpt) {
                                    $optBorder = '#10b981';
                                    $optBg = '#e6f4ea';
                                    $optIndicator = ' <span style="color: #137333; font-weight: 700; font-size: 0.8rem; background: #c2e7c9; padding: 2px 6px; border-radius: 4px;">Correct Option</span>';
                                }
                            }
                            ?>
                            <div style="padding: 12px 16px; border: 1px solid <?= $optBorder ?>; background: <?= $optBg ?>; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
                                <span>
                                    <?= $opt['option_key'] ? "<strong>{$opt['option_key']}.</strong> " : '' ?>
                                    <?= e($opt['option_text']) ?>
                                    <?php if ($opt['allow_custom_text'] && $ans && isset($ans['option_custom_texts'][(string)$opt['id']])): ?>
                                        <div style="margin-top: 6px; font-size: 0.85rem; color: #4b5563; background: #fff; border: 1px solid #d1d5db; padding: 6px 12px; border-radius: 6px;">
                                            <strong>Written Text:</strong> <?= e($ans['option_custom_texts'][(string)$opt['id']]) ?>
                                        </div>
                                    <?php endif; ?>
                                </span>
                                <div><?= $optIndicator ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php elseif ($q['type'] === 'open_text'): ?>
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <?php if ($isSurvey): ?>
                            <div style="padding: 12px 16px; border: 1px solid #cbd5e1; background: #f8fafc; border-radius: 8px;">
                                <strong>User Response:</strong> 
                                <div style="margin-top: 4px; font-family: monospace; font-size: 1rem; color: #0f172a; font-weight: 600;">
                                    <?= e(($ans['answer_text'] ?? '') ?: '[No answer submitted]') ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div style="padding: 12px 16px; border: 1px solid <?= $isCorrect ? '#c2e7c9' : '#fad2cf' ?>; background: <?= $isCorrect ? '#e6f4ea' : '#fce8e6' ?>; border-radius: 8px;">
                                <strong>User Answer:</strong> 
                                <div style="margin-top: 4px; font-family: monospace; font-size: 1rem; color: <?= $isCorrect ? '#137333' : '#c5221f' ?>; font-weight: 600;">
                                    <?= e(($ans['answer_text'] ?? '') ?: '[No answer submitted]') ?>
                                </div>
                            </div>
                            
                            <div style="padding: 12px 16px; border: 1px solid #e5e7eb; background: #f9fafb; border-radius: 8px;">
                                <strong>Accepted Answers:</strong>
                                <ul style="margin: 6px 0 0; padding-left: 20px; line-height: 1.5;">
                                    <?php foreach ($q['accepted_answers'] as $acc): ?>
                                        <li>
                                            <code><?= e($acc['answer_text']) ?></code>
                                            <span class="muted" style="font-size: 0.75rem;">(Match: <?= e($acc['match_type']) ?><?= $acc['match_type'] === 'fuzzy' ? ' ' . (float)($acc['similarity_threshold'] ?? 0.85) : '' ?>)</span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Explanation Box -->
            <?php if ($q['explanation']): ?>
                <div style="background: #f3f4f6; padding: 14px 20px; border-radius: 8px; border-left: 4px solid #3b82f6; font-size: 0.88rem; color: #374151;">
                    <strong>Explanation:</strong> <?= e($q['explanation']) ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
