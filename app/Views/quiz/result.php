<?php
$isPassed = filter_var($attempt['passed'] ?? false, FILTER_VALIDATE_BOOL);
$showResult = filter_var($attempt['show_result'] ?? true, FILTER_VALIDATE_BOOL);
$showCorrect = filter_var($attempt['show_correct_answer'] ?? true, FILTER_VALIDATE_BOOL);
?>

<div class="card" style="max-width: 750px; margin: 0 auto; text-align: center; padding: 40px; animation: fadeInUp 0.4s ease-out;">
    <div style="margin-bottom: 24px;">
        <span style="font-size: 0.9rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Quiz Result</span>
        <h1 style="font-size: 2rem; font-weight: 800; color: var(--text-main); margin: 6px 0 0;"><?= e($attempt['quiz_title']) ?></h1>
    </div>

    <!-- Circular Score Indicator -->
    <div style="position: relative; width: 140px; height: 140px; margin: 0 auto 28px; display: flex; align-items: center; justify-content: center; background: #f8fafc; border-radius: 50%; box-shadow: inset 0 2px 8px rgba(0,0,0,0.05); border: 4px solid <?= $isPassed ? '#e6f4ea' : '#fce8e6' ?>;">
        <div>
            <div style="font-size: 2.25rem; font-weight: 800; color: <?= $isPassed ? '#137333' : '#c5221f' ?>;">
                <?= e((string)(float)$attempt['percentage']) ?>%
            </div>
            <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">SCORE</div>
        </div>
    </div>

    <div style="margin-bottom: 32px;">
        <?php if ($isPassed): ?>
            <div style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 24px; background: #e6f4ea; color: #137333; border-radius: 9999px; font-weight: 700; font-size: 1.1rem; border: 1px solid #c2e7c9;">
                <svg style="width: 22px; height: 22px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                PASSED
            </div>
        <?php else: ?>
            <div style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 24px; background: #fce8e6; color: #c5221f; border-radius: 9999px; font-weight: 700; font-size: 1.1rem; border: 1px solid #fad2cf;">
                <svg style="width: 22px; height: 22px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                FAILED
            </div>
        <?php endif; ?>
    </div>

    <!-- Summary Details Grid -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); padding: 20px 0; margin-bottom: 32px; font-size: 0.9rem;">
        <div>
            <div class="muted">Correct Answers</div>
            <div style="font-weight: 700; font-size: 1.2rem; color: var(--text-main); margin-top: 4px;">
                <?= isset($questions) ? count(array_filter($questions, fn($q) => $q['user_answer'] && $q['user_answer']['is_correct'])) : 'N/A' ?>
            </div>
        </div>
        <div>
            <div class="muted">Awarded Score</div>
            <div style="font-weight: 700; font-size: 1.2rem; color: var(--text-main); margin-top: 4px;">
                <?= e((string)(float)$attempt['score']) ?>
            </div>
        </div>
        <div>
            <div class="muted">Total Score</div>
            <div style="font-weight: 700; font-size: 1.2rem; color: var(--text-main); margin-top: 4px;">
                <?= e((string)(float)$attempt['total_score']) ?>
            </div>
        </div>
        <div>
            <div class="muted">Pass Requirement</div>
            <div style="font-weight: 700; font-size: 1.2rem; color: var(--text-main); margin-top: 4px;">
                <?= $attempt['pass_score'] !== null ? e((string)(float)$attempt['pass_score']) . '%' : 'Any' ?>
            </div>
        </div>
    </div>

    <div style="display: flex; justify-content: center; gap: 16px;">
        <a href="<?= url('quizzes') ?>" class="btn" style="background: var(--primary); padding: 12px 28px; font-weight: 700;">
            Browse More Quizzes
        </a>
    </div>
</div>

<!-- Detailed Question Review Section -->
<?php if ($showResult && !empty($questions)): ?>
    <div style="max-width: 750px; margin: 40px auto 80px; animation: fadeInUp 0.5s ease-out;">
        <h2 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 24px;">Question Review</h2>

        <?php foreach ($questions as $index => $q): ?>
            <?php
            $ans = $q['user_answer'];
            $isCorrect = $ans && filter_var($ans['is_correct'] ?? false, FILTER_VALIDATE_BOOL);
            $cardBorderColor = $isCorrect ? '#137333' : '#c5221f';
            $badgeBg = $isCorrect ? '#e6f4ea' : '#fce8e6';
            $badgeColor = $isCorrect ? '#137333' : '#c5221f';
            ?>
            <div class="card" style="border-left: 6px solid <?= $cardBorderColor ?>; padding: 24px 28px; margin-bottom: 20px; transition: none;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <span style="font-weight: 700; font-size: 0.85rem; color: var(--text-muted);">
                        QUESTION <?= $index + 1 ?>
                    </span>
                    <span style="font-size: 0.75rem; font-weight: 700; padding: 4px 12px; border-radius: 9999px; background: <?= $badgeBg ?>; color: <?= $badgeColor ?>; text-transform: uppercase;">
                        <?= $isCorrect ? 'Correct' : 'Incorrect' ?> (Score: <?= e((string)(float)($ans ? ($ans['awarded_score'] ?? 0) : 0)) ?>/<?= e((string)(float)$q['score']) ?>)
                    </span>
                </div>

                <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin: 0 0 20px; line-height: 1.4;">
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

                                $optBorder = 'var(--border)';
                                $optBg = 'transparent';
                                $optIndicator = '';

                                if ($isUserSelected) {
                                    $optBorder = $isCorrectOpt ? '#81c784' : '#e57373';
                                    $optBg = $isCorrectOpt ? '#e8f5e9' : '#ffebee';
                                    $optIndicator = $isCorrectOpt ? ' <strong style="color: #2e7d32;">(Your Choice - Correct)</strong>' : ' <strong style="color: #c62828;">(Your Choice - Incorrect)</strong>';
                                } elseif ($showCorrect && $isCorrectOpt) {
                                    $optBorder = '#81c784';
                                    $optBg = '#e8f5e9';
                                    $optIndicator = ' <strong style="color: #2e7d32;">(Correct Choice)</strong>';
                                }
                                ?>
                                <div style="padding: 10px 16px; border: 1px solid <?= $optBorder ?>; background: <?= $optBg ?>; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
                                    <span>
                                        <?= $opt['option_key'] ? "<strong>{$opt['option_key']}.</strong> " : '' ?>
                                        <?= e($opt['option_text']) ?>
                                    </span>
                                    <span style="font-size: 0.8rem;"><?= $optIndicator ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php elseif ($q['type'] === 'open_text'): ?>
                        <div style="display: flex; flex-direction: column; gap: 12px;">
                            <div style="padding: 12px 16px; border: 1px solid <?= $isCorrect ? '#c2e7c9' : '#fad2cf' ?>; background: <?= $isCorrect ? '#e6f4ea' : '#fce8e6' ?>; border-radius: 8px;">
                                <strong>Your Answer:</strong> <code style="font-family: inherit; font-size: 1rem; color: <?= $isCorrect ? '#137333' : '#c5221f' ?>; font-weight: 600;"><?= e(($ans['answer_text'] ?? '') ?: '[No answer submitted]') ?></code>
                            </div>

                            <?php if ($showCorrect && !empty($q['accepted_answers'])): ?>
                                <div style="padding: 12px 16px; border: 1px solid var(--border); background: #f8fafc; border-radius: 8px;">
                                    <strong>Accepted Answers:</strong>
                                    <ul style="margin: 6px 0 0; padding-left: 20px;">
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
                    <div style="background: #f0f4f8; padding: 14px 20px; border-radius: 8px; border-left: 4px solid #3b82f6; font-size: 0.88rem; color: #334155;">
                        <strong>Explanation:</strong> <?= e($q['explanation']) ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
