<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; animation: fadeIn 0.3s ease-out;">
    <div>
        <h1 style="margin: 0; font-size: 2rem; font-weight: 700; color: #111827;">Quiz Management</h1>
        <p class="muted" style="margin: 4px 0 0;">Create, edit, duplicate, and configure quizzes.</p>
    </div>
    <a href="/admin/quizzes/create" class="btn" style="display: inline-flex; align-items: center; gap: 6px;">
        <svg style="width: 18px; height: 18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Create New Quiz
    </a>
</div>

<div class="card" style="padding: 0; overflow: hidden; border: 1px solid #e5e7eb; border-radius: 10px; animation: fadeIn 0.4s ease-out;">
    <?php if (empty($quizzes)): ?>
        <div style="text-align: center; padding: 48px; color: #6b7280;">
            <svg style="width: 48px; height: 48px; margin: 0 auto 16px; opacity: 0.4;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
            <h3 style="margin: 0 0 8px; color: #111827; font-weight: 600;">No quizzes found</h3>
            <p style="margin: 0;">Click "Create New Quiz" to get started.</p>
        </div>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f9fafb;">
                    <th style="padding: 14px 16px; border-bottom: 1px solid #e5e7eb; font-weight: 600; color: #4b5563; font-size: 0.85rem;">ID</th>
                    <th style="padding: 14px 16px; border-bottom: 1px solid #e5e7eb; font-weight: 600; color: #4b5563; font-size: 0.85rem;">Title</th>
                    <th style="padding: 14px 16px; border-bottom: 1px solid #e5e7eb; font-weight: 600; color: #4b5563; font-size: 0.85rem;">Status</th>
                    <th style="padding: 14px 16px; border-bottom: 1px solid #e5e7eb; font-weight: 600; color: #4b5563; font-size: 0.85rem; text-align: center;">Questions</th>
                    <th style="padding: 14px 16px; border-bottom: 1px solid #e5e7eb; font-weight: 600; color: #4b5563; font-size: 0.85rem; text-align: center;">Attempts</th>
                    <th style="padding: 14px 16px; border-bottom: 1px solid #e5e7eb; font-weight: 600; color: #4b5563; font-size: 0.85rem; text-align: center;">Pass Score</th>
                    <th style="padding: 14px 16px; border-bottom: 1px solid #e5e7eb; font-weight: 600; color: #4b5563; font-size: 0.85rem;">Duration</th>
                    <th style="padding: 14px 16px; border-bottom: 1px solid #e5e7eb; font-weight: 600; color: #4b5563; font-size: 0.85rem;">Validity</th>
                    <th style="padding: 14px 16px; border-bottom: 1px solid #e5e7eb; font-weight: 600; color: #4b5563; font-size: 0.85rem; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($quizzes as $quiz): ?>
                    <tr id="quiz-row-<?= (int)$quiz['id'] ?>" style="border-bottom: 1px solid #f3f4f6; transition: background 0.2s;">
                        <td style="padding: 16px; font-size: 0.9rem; color: #4b5563; font-weight: 500;"><?= (int)$quiz['id'] ?></td>
                        <td style="padding: 16px; font-weight: 600; color: #111827; font-size: 0.95rem;">
                            <?= e($quiz['title']) ?>
                            <?php if (!empty($quiz['description'])): ?>
                                <div class="muted" style="font-weight: 400; font-size: 0.8rem; margin-top: 2px; max-width: 320px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <?= e($quiz['description']) ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 16px;">
                            <?php
                            $status = $quiz['status'] ?? 'draft';
                            $statusColor = '#9ca3af'; $statusBg = '#f3f4f6';
                            if ($status === 'published') { $statusColor = '#16a34a'; $statusBg = '#dcfce7'; }
                            elseif ($status === 'inactive') { $statusColor = '#dc2626'; $statusBg = '#fee2e2'; }
                            ?>
                            <span id="status-badge-<?= (int)$quiz['id'] ?>" style="display: inline-block; padding: 4px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; color: <?= $statusColor ?>; background: <?= $statusBg ?>; text-transform: uppercase;">
                                <?= e($status) ?>
                            </span>
                        </td>
                        <td style="padding: 16px; font-size: 0.9rem; text-align: center; font-weight: 500;"><?= (int)($quiz['questions_count'] ?? 0) ?></td>
                        <td style="padding: 16px; font-size: 0.9rem; text-align: center; font-weight: 500;"><?= (int)($quiz['attempts_count'] ?? 0) ?></td>
                        <td style="padding: 16px; font-size: 0.9rem; text-align: center; font-weight: 500; color: #4b5563;">
                            <?= $quiz['pass_score'] !== null ? e((string)(float)$quiz['pass_score']) . '%' : '<span class="muted">—</span>' ?>
                        </td>
                        <td style="padding: 16px; font-size: 0.9rem; color: #4b5563;">
                            <?= $quiz['duration_minutes'] ? e((string)$quiz['duration_minutes']) . 'm' : '<span class="muted">Unlimited</span>' ?>
                        </td>
                        <td style="padding: 16px; font-size: 0.8rem; color: #6b7280; line-height: 1.3;">
                            <?php if ($quiz['start_at'] || $quiz['end_at']): ?>
                                <?php if ($quiz['start_at']): ?>
                                    <div><strong>Start:</strong> <?= e(date('Y-m-d H:i', strtotime($quiz['start_at']))) ?></div>
                                <?php endif; ?>
                                <?php if ($quiz['end_at']): ?>
                                    <div><strong>End:</strong> <?= e(date('Y-m-d H:i', strtotime($quiz['end_at']))) ?></div>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="muted">Always Available</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 16px; text-align: right; position: relative;">
                            <?php
                            component('dropdown', [
                                'id' => (string)$quiz['id'],
                                'buttonHtml' => 'Actions <svg style="width: 12px; height: 12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>',
                                'buttonStyle' => 'background: #4f46e5; font-size: 0.8rem; padding: 6px 12px; display: inline-flex; align-items: center; gap: 4px; font-weight: 600;',
                                'items' => [
                                    ['type' => 'link', 'text' => 'Questions', 'url' => "/admin/quizzes/{$quiz['id']}/questions"],
                                    ['type' => 'link', 'text' => 'Edit Settings', 'url' => "/admin/quizzes/{$quiz['id']}/edit"],
                                    ['type' => 'button', 'text' => ($status === 'published' ? 'Deactivate' : 'Publish'), 'onclick' => "publishToggle({$quiz['id']})", 'id' => "pub-btn-{$quiz['id']}"],
                                    ['type' => 'button', 'text' => 'Duplicate', 'onclick' => "duplicateQuiz({$quiz['id']})"],
                                    ['type' => 'link', 'text' => 'Preview', 'url' => "/quizzes/{$quiz['id']}", 'target' => '_blank'],
                                    ['type' => 'divider'],
                                    ['type' => 'form', 'text' => 'Delete', 'action' => "/admin/quizzes/{$quiz['id']}", 'method' => 'DELETE', 'confirm' => 'Are you sure you want to delete this quiz? all questions and attempts will be deleted permanently!', 'style' => 'color: #dc2626; font-weight: 600;']
                                ]
                            ]);
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
async function publishToggle(quizId) {
    const btn = document.getElementById('pub-btn-' + quizId);
    const badge = document.getElementById('status-badge-' + quizId);
    if (!btn || !badge) return;

    btn.disabled = true;
    try {
        const response = await fetch(`/api/admin/quizzes/${quizId}/publish`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        const res = await response.json();
        if (response.ok) {
            badge.innerText = res.status.toUpperCase();
            if (res.status === 'published') {
                badge.style.color = '#16a34a';
                badge.style.background = '#dcfce7';
                btn.innerText = 'Deactivate';
            } else if (res.status === 'inactive') {
                badge.style.color = '#dc2626';
                badge.style.background = '#fee2e2';
                btn.innerText = 'Publish';
            } else {
                badge.style.color = '#9ca3af';
                badge.style.background = '#f3f4f6';
                btn.innerText = 'Publish';
            }
        } else {
            alert(res.message || 'Failed to update quiz status.');
        }
    } catch (err) {
        console.error(err);
        alert('An error occurred.');
    } finally {
        btn.disabled = false;
    }
}

async function duplicateQuiz(quizId) {
    if (!confirm('Are you sure you want to duplicate this quiz?')) return;
    try {
        const response = await fetch(`/api/admin/quizzes/${quizId}/duplicate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        const res = await response.json();
        if (response.ok) {
            alert('Quiz duplicated successfully as draft! Refreshing page...');
            window.location.reload();
        } else {
            alert(res.message || 'Failed to duplicate quiz.');
        }
    } catch (err) {
        console.error(err);
        alert('An error occurred.');
    }
}
</script>
