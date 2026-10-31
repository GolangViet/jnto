<div style="margin-bottom: 24px;">
    <a href="<?= url('admin/surveys') ?>" style="text-decoration: none; color: #4f46e5; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; font-size: 0.9rem;">
        <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back to Survey Management
    </a>
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 8px;">
        <div>
            <h1 style="margin: 0; font-size: 1.85rem; font-weight: 700; color: #111827;">Survey Answers Detail</h1>
            <p style="margin: 4px 0 0; color: #6b7280; font-size: 0.9rem;">
                Reviewing answers for <?= e($submission['quiz_title']) ?> (#<?= $submission['id'] ?>)
            </p>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <?php if ($type === 'user'): ?>
                <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 9999px; background: #dbeafe; color: #1e40af; font-size: 0.85rem; font-weight: 700; text-transform: uppercase;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #2563eb;"></span>
                    Registered User Attempt
                </span>
                <form action="<?= url('admin/surveys/user/' . $submission['id']) ?>" method="post" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài khảo sát này và toàn bộ câu trả lời?')">
                    <input type="hidden" name="_method" value="DELETE">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn danger" style="padding: 6px 12px; font-size: 0.85rem;">
                        Delete Submission
                    </button>
                </form>
            <?php else: ?>
                <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 9999px; background: #fef3c7; color: #92400e; font-size: 0.85rem; font-weight: 700; text-transform: uppercase;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #d97706;"></span>
                    Guest Submission
                </span>
                <form action="<?= url('admin/surveys/guest/' . $submission['id']) ?>" method="post" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài khảo sát của khách này?')">
                    <input type="hidden" name="_method" value="DELETE">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn danger" style="padding: 6px 12px; font-size: 0.85rem;">
                        Delete Submission
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
    <!-- Participant Info Card -->
    <div class="card" style="padding: 24px; margin-bottom: 0;">
        <h3 style="margin: 0 0 16px; color: #111827; border-bottom: 1px solid #e5e7eb; padding-bottom: 8px; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
            <svg style="width: 18px; height: 18px; color: #4b5563;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Participant Information
        </h3>
        <table style="width: 100%; border: none;">
            <?php if ($type === 'user'): ?>
                <tr style="border-bottom: none;"><td style="padding: 6px 0; font-weight: 600; color: #4b5563; width: 130px;">Full Name:</td><td style="padding: 6px 0; color: #111827; font-weight: 600;"><?= e($submission['user_name']) ?></td></tr>
                <tr style="border-bottom: none;"><td style="padding: 6px 0; font-weight: 600; color: #4b5563;">Email Address:</td><td style="padding: 6px 0; color: #111827;"><?= e($submission['user_email']) ?></td></tr>
                <?php if (!empty($submission['user_username']) && $submission['user_username'] !== 'N/A'): ?>
                    <tr style="border-bottom: none;"><td style="padding: 6px 0; font-weight: 600; color: #4b5563;">Username:</td><td style="padding: 6px 0; color: #111827;">@<?= e($submission['user_username']) ?></td></tr>
                <?php endif; ?>
                <tr style="border-bottom: none;"><td style="padding: 6px 0; font-weight: 600; color: #4b5563;">User ID:</td><td style="padding: 6px 0; color: #111827;"><?= (int)$submission['user_id'] ?></td></tr>
                <?php if (!empty($submission['travel_status'])): ?>
                    <tr style="border-bottom: none;"><td style="padding: 6px 0; font-weight: 600; color: #4b5563;">Travel Status:</td><td style="padding: 6px 0; color: #111827;"><span style="padding: 2px 8px; border-radius: 4px; background: #dbeafe; color: #1e40af; font-weight: 600; font-size: 0.85rem;"><?= e($submission['travel_status']) ?></span></td></tr>
                <?php endif; ?>
            <?php else: ?>
                <tr style="border-bottom: none;"><td style="padding: 6px 0; font-weight: 600; color: #4b5563; width: 130px;">Participant:</td><td style="padding: 6px 0; color: #111827; font-weight: 600;">Khách vãng lai (Guest)</td></tr>
                <tr style="border-bottom: none;"><td style="padding: 6px 0; font-weight: 600; color: #4b5563;">Guest Token:</td><td style="padding: 6px 0; color: #111827; font-family: monospace; word-break: break-all; font-size: 0.85rem;"><?= e($submission['guest_token'] ?? '') ?></td></tr>
                <?php if (!empty($submission['travel_status'])): ?>
                    <?php
                    $rawStatus = (string) $submission['travel_status'];
                    $guestLabel = match ($rawStatus) {
                        'visited' => 'Đã từng đến Nhật Bản',
                        'not-visited' => 'Chưa từng đến Nhật Bản',
                        default => $rawStatus,
                    };
                    ?>
                    <tr style="border-bottom: none;"><td style="padding: 6px 0; font-weight: 600; color: #4b5563;">Travel Status:</td><td style="padding: 6px 0; color: #111827;"><span style="padding: 2px 8px; border-radius: 4px; background: #fef3c7; color: #92400e; font-weight: 600; font-size: 0.85rem;"><?= e($guestLabel) ?></span></td></tr>
                <?php endif; ?>
                <?php if (!empty($submission['expires_at'])): ?>
                    <tr style="border-bottom: none;"><td style="padding: 6px 0; font-weight: 600; color: #4b5563;">Staging Expiry:</td><td style="padding: 6px 0; color: #9ca3af; font-size: 0.85rem;"><?= date('d/m/Y H:i:s', strtotime((string)$submission['expires_at'])) ?></td></tr>
                <?php endif; ?>
            <?php endif; ?>
        </table>
    </div>

    <!-- Survey Details Card -->
    <div class="card" style="padding: 24px; margin-bottom: 0;">
        <h3 style="margin: 0 0 16px; color: #111827; border-bottom: 1px solid #e5e7eb; padding-bottom: 8px; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
            <svg style="width: 18px; height: 18px; color: #4b5563;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Survey Overview
        </h3>
        <table style="width: 100%; border: none;">
            <tr style="border-bottom: none;"><td style="padding: 6px 0; font-weight: 600; color: #4b5563; width: 130px;">Form Title:</td><td style="padding: 6px 0; color: #111827; font-weight: 600;"><?= e($submission['quiz_title']) ?></td></tr>
            <tr style="border-bottom: none;"><td style="padding: 6px 0; font-weight: 600; color: #4b5563;">Questions Count:</td><td style="padding: 6px 0; color: #111827; font-weight: 600;">
                <?php
                $answeredCount = count(array_filter($questions, fn($q) => $q['user_answer'] !== null && (
                    !empty($q['user_answer']['selected_option_ids']) ||
                    (!empty($q['user_answer']['answer_text']) && trim($q['user_answer']['answer_text']) !== '')
                )));
                ?>
                <span style="color: #059669; font-weight: 700;"><?= $answeredCount ?></span> / <?= count($questions) ?> questions answered
            </td></tr>
            <tr style="border-bottom: none;"><td style="padding: 6px 0; font-weight: 600; color: #4b5563;">Submitted At:</td><td style="padding: 6px 0; color: #111827;">
                <?= $submission['submitted_at'] ? date('d/m/Y H:i:s', strtotime((string)$submission['submitted_at'])) : '—' ?>
            </td></tr>
            <tr style="border-bottom: none;"><td style="padding: 6px 0; font-weight: 600; color: #4b5563;">Record ID:</td><td style="padding: 6px 0; color: #6b7280; font-size: 0.85rem;">#<?= (int)$submission['id'] ?> (<?= e($type) ?>)</td></tr>
        </table>
    </div>
</div>

<!-- Question Breakdown -->
<div style="display: flex; justify-content: space-between; align-items: center; margin: 32px 0 16px;">
    <h2 style="font-size: 1.4rem; font-weight: 700; color: #111827; margin: 0;">
        Full Questions & Responses
    </h2>
    <span style="font-size: 0.85rem; color: #6b7280;">
        Displaying questions relevant to participant's response path
    </span>
</div>

<?php if (empty($questions)): ?>
    <div class="card" style="padding: 32px; text-align: center; color: #6b7280;">
        No question responses found for this survey form.
    </div>
<?php else: ?>
    <?php foreach ($questions as $index => $q): ?>
        <?php
        $ans = $q['user_answer'];
        $hasAnswer = $ans !== null && (
            !empty($ans['selected_option_ids']) ||
            (!empty($ans['answer_text']) && trim($ans['answer_text']) !== '')
        );

        $badgeBg = $hasAnswer ? '#e0e7ff' : '#f3f4f6';
        $badgeColor = $hasAnswer ? '#4338ca' : '#6b7280';
        $badgeText = $hasAnswer ? 'Answered' : 'Skipped / Unanswered';
        $borderLeft = $hasAnswer ? '#4f46e5' : '#d1d5db';
        ?>

        <div class="card" style="border-left: 5px solid <?= $borderLeft ?>; padding: 22px 24px; margin-bottom: 18px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <span style="font-weight: 700; font-size: 0.82rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">
                    QUESTION <?= $index + 1 ?> &bull; <?= strtoupper(str_replace('_', ' ', $q['type'])) ?>
                </span>
                <span style="font-size: 0.75rem; font-weight: 700; padding: 3px 10px; border-radius: 9999px; background: <?= $badgeBg ?>; color: <?= $badgeColor ?>; text-transform: uppercase;">
                    <?= $badgeText ?>
                </span>
            </div>

            <h3 style="font-size: 1.15rem; font-weight: 700; color: #111827; margin: 0 0 18px; line-height: 1.45;">
                <?= $q['question_text'] ?>
            </h3>

            <!-- Responses Section -->
            <div style="font-size: 0.95rem; line-height: 1.6; margin-bottom: 10px;">
                <?php if (in_array($q['type'], ['single_choice', 'multiple_choice', 'true_false'], true)): ?>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <?php foreach ($q['options'] as $opt): ?>
                            <?php
                            $userSelectedIds = $ans ? ($ans['selected_option_ids'] ?? []) : [];
                            $isUserSelected = in_array((int)$opt['id'], array_map('intval', $userSelectedIds), true);

                            $optBorder = $isUserSelected ? '#6366f1' : '#e5e7eb';
                            $optBg = $isUserSelected ? '#eef2ff' : '#ffffff';
                            $customTextVal = $ans['option_custom_texts'][(string)$opt['id']] ?? $ans['option_custom_texts'][(int)$opt['id']] ?? null;
                            ?>
                            <div style="padding: 12px 16px; border: 1.5px solid <?= $optBorder ?>; background: <?= $optBg ?>; border-radius: 8px; display: flex; justify-content: space-between; align-items: flex-start; transition: border-color 0.15s ease;">
                                <div style="flex: 1;">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <?php if ($opt['option_key']): ?>
                                            <span style="font-weight: 700; color: <?= $isUserSelected ? '#4338ca' : '#4b5563' ?>;">
                                                <?= e($opt['option_key']) ?>.
                                            </span>
                                        <?php endif; ?>
                                        <span style="<?= $isUserSelected ? 'font-weight: 600; color: #1e1b4b;' : 'color: #374151;' ?>">
                                            <?= e($opt['option_text']) ?>
                                        </span>
                                    </div>

                                    <?php if ($isUserSelected && !empty($customTextVal)): ?>
                                        <div style="margin-top: 8px; font-size: 0.88rem; color: #1f2937; background: #ffffff; border: 1px dashed #818cf8; padding: 8px 12px; border-radius: 6px;">
                                            <strong style="color: #4338ca;">Participant's Written Input:</strong> <?= e($customTextVal) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div style="margin-left: 14px; flex-shrink: 0;">
                                    <?php if ($isUserSelected): ?>
                                        <span style="display: inline-flex; align-items: center; gap: 4px; color: #4338ca; font-weight: 700; font-size: 0.78rem; background: #c7d2fe; padding: 3px 8px; border-radius: 9999px;">
                                            <svg style="width: 12px; height: 12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Participant Choice
                                        </span>
                                    <?php else: ?>
                                        <span style="color: #9ca3af; font-size: 0.8rem;">—</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php elseif ($q['type'] === 'open_text'): ?>
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <div style="padding: 14px 18px; border: 1.5px solid <?= $hasAnswer ? '#6366f1' : '#cbd5e1' ?>; background: <?= $hasAnswer ? '#eef2ff' : '#f8fafc' ?>; border-radius: 8px;">
                            <div style="font-size: 0.82rem; font-weight: 700; color: #4338ca; text-transform: uppercase; margin-bottom: 4px;">
                                Written Response:
                            </div>
                            <div style="font-size: 1rem; color: #0f172a; font-weight: 600; white-space: pre-wrap; line-height: 1.5;">
                                <?= e(($ans['answer_text'] ?? '') ?: '[No answer entered]') ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Question Explanation/Note if present -->
            <?php if (!empty($q['explanation'])): ?>
                <div style="margin-top: 14px; background: #f9fafb; padding: 10px 14px; border-radius: 6px; border-left: 3px solid #94a3b8; font-size: 0.85rem; color: #4b5563;">
                    <strong>Note:</strong> <?= e($q['explanation']) ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
