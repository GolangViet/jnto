<div style="margin-bottom: 24px;">
    <a href="<?= url('admin/users') ?>" style="text-decoration: none; color: #4f46e5; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; font-size: 0.9rem;">
        <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back to Users
    </a>
    <h1 style="margin: 8px 0 0; font-size: 2rem; font-weight: 700; color: #111827;">Edit User</h1>
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

<div class="card" style="padding: 28px;">
    <form method="post" action="<?= url('admin/users/' . (int)$user['id']) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="_method" value="PUT">
        <?php require app()->basePath('app/Views/admin/users/form.php'); ?>
        <div style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 12px;">
            <a href="<?= url('admin/users') ?>" class="btn" style="background: #9ca3af; text-decoration: none;">Cancel</a>
            <button class="btn" style="background: #4f46e5;">Update</button>
        </div>
    </form>
</div>

<!-- User's Quiz/Survey Attempts History -->
<div style="margin-top: 40px; margin-bottom: 24px;">
    <h2 style="font-size: 1.5rem; font-weight: 700; color: #111827; margin: 0 0 16px;">User Attempts History</h2>
    <div class="card" style="padding: 0; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden;">
        <?php if (empty($attempts)): ?>
            <div style="text-align: center; padding: 32px; color: #6b7280;">
                No attempts recorded for this user yet.
            </div>
        <?php else: ?>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f9fafb;">
                        <th style="padding: 12px 16px; border-bottom: 1px solid #e5e7eb; font-weight: 600; color: #4b5563; font-size: 0.85rem;">ID</th>
                        <th style="padding: 12px 16px; border-bottom: 1px solid #e5e7eb; font-weight: 600; color: #4b5563; font-size: 0.85rem;">Quiz</th>
                        <th style="padding: 12px 16px; border-bottom: 1px solid #e5e7eb; font-weight: 600; color: #4b5563; font-size: 0.85rem;">Status</th>
                        <th style="padding: 12px 16px; border-bottom: 1px solid #e5e7eb; font-weight: 600; color: #4b5563; font-size: 0.85rem;">Score</th>
                        <th style="padding: 12px 16px; border-bottom: 1px solid #e5e7eb; font-weight: 600; color: #4b5563; font-size: 0.85rem; text-align: center;">Passed</th>
                        <th style="padding: 12px 16px; border-bottom: 1px solid #e5e7eb; font-weight: 600; color: #4b5563; font-size: 0.85rem;">Submitted At</th>
                        <th style="padding: 12px 16px; border-bottom: 1px solid #e5e7eb; font-weight: 600; color: #4b5563; font-size: 0.85rem; text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attempts as $attempt): ?>
                        <?php
                        $status = $attempt['status'] ?? 'in_progress';
                        $statusColor = '#9ca3af'; $statusBg = '#f3f4f6';
                        if ($status === 'submitted') { $statusColor = '#16a34a'; $statusBg = '#dcfce7'; }
                        elseif ($status === 'expired') { $statusColor = '#d97706'; $statusBg = '#fef3c7'; }

                        $isPassed = filter_var($attempt['passed'] ?? false, FILTER_VALIDATE_BOOL);
                        $passedBadge = $status === 'submitted'
                            ? '<span class="badge" style="background: ' . ($isPassed ? '#e6f4ea; color: #137333;' : '#fce8e6; color: #c5221f;') . ' font-weight: 600; padding: 2px 6px; font-size: 0.75rem;">' . ($isPassed ? 'Yes' : 'No') . '</span>'
                            : '<span class="muted">N/A</span>';
                        ?>
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 14px 16px; font-size: 0.9rem; color: #4b5563; font-weight: 500;"><?= (int)$attempt['id'] ?></td>
                            <td style="padding: 14px 16px; font-size: 0.95rem; font-weight: 600; color: #111827;"><?= e($attempt['quiz_title']) ?></td>
                            <td style="padding: 14px 16px;">
                                <span style="display: inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; color: <?= $statusColor ?>; background: <?= $statusBg ?>; text-transform: uppercase;">
                                    <?= e($status) ?>
                                </span>
                            </td>
                            <td style="padding: 14px 16px; font-size: 0.9rem; color: #111827;">
                                <?php if ($status === 'submitted'): ?>
                                    <strong><?= e((string)(float)$attempt['score']) ?></strong> / <?= e((string)(float)$attempt['total_score']) ?> (<?= e((string)(float)$attempt['percentage']) ?>%)
                                <?php else: ?>
                                    <span class="muted">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 14px 16px; text-align: center;"><?= $passedBadge ?></td>
                            <td style="padding: 14px 16px; font-size: 0.85rem; color: #6b7280;">
                                <?= $attempt['submitted_at'] ? date('Y-m-d H:i:s', strtotime($attempt['submitted_at'])) : '<span class="muted">N/A</span>' ?>
                            </td>
                            <td style="padding: 14px 16px; text-align: right;">
                                <a href="<?= url('admin/quiz-attempts/' . (int)$attempt['id']) ?>" class="btn" style="padding: 4px 10px; font-size: 0.8rem; font-weight: 600; background: #4f46e5;">View Answers</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

