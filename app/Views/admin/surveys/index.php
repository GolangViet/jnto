<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
    <div>
        <h1 style="margin: 0; font-size: 1.8rem; font-weight: 700; color: #111827;">Survey Management</h1>
        <p style="margin: 4px 0 0; color: #6b7280; font-size: 0.92rem;">
            View and analyze all survey responses from both registered users and guest visitors.
        </p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="<?= url('admin/surveys/export') ?>?quiz_id=<?= (int)$selectedQuizId ?>&type=<?= urlencode($type) ?>&search=<?= urlencode($search) ?>&start_date=<?= urlencode($startDate) ?>&end_date=<?= urlencode($endDate) ?>"
           class="btn" style="background: #059669; display: inline-flex; align-items: center; gap: 6px;">
            <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Export to CSV
        </a>
    </div>
</div>

<!-- Stats Overview Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 20px;">
    <div class="card" style="margin-bottom: 0; padding: 18px 20px; border-left: 5px solid #3b82f6;">
        <div style="font-size: 0.85rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.03em;">Total Survey Responses</div>
        <div style="font-size: 2rem; font-weight: 700; color: #111827; margin-top: 4px;"><?= number_format($totalSubmissions) ?></div>
        <div style="font-size: 0.8rem; color: #9ca3af; margin-top: 2px;">All participants combined</div>
    </div>
    <div class="card" style="margin-bottom: 0; padding: 18px 20px; border-left: 5px solid #10b981;">
        <div style="font-size: 0.85rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.03em;">Registered Users</div>
        <div style="font-size: 2rem; font-weight: 700; color: #047857; margin-top: 4px;"><?= number_format($totalUserAttempts) ?></div>
        <div style="font-size: 0.8rem; color: #9ca3af; margin-top: 2px;">Linked to user accounts</div>
    </div>
    <div class="card" style="margin-bottom: 0; padding: 18px 20px; border-left: 5px solid #f59e0b;">
        <div style="font-size: 0.85rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.03em;">Guest Visitors</div>
        <div style="font-size: 2rem; font-weight: 700; color: #b45309; margin-top: 4px;"><?= number_format($totalGuestSubmissions) ?></div>
        <div style="font-size: 0.8rem; color: #9ca3af; margin-top: 2px;">Completed without an account</div>
    </div>
</div>

<!-- Filters Card -->
<div class="card" style="margin-bottom: 20px;">
    <form method="get" action="<?= url('admin/surveys') ?>" style="display: flex; gap: 14px; align-items: flex-end; flex-wrap: wrap;">
        <div style="flex: 2; min-width: 200px;">
            <label for="quiz_id" style="font-size: 0.85rem; color: #4b5563; font-weight: bold; display: block; margin-bottom: 4px;">Survey Form</label>
            <select id="quiz_id" name="quiz_id" style="margin-bottom: 0;">
                <?php foreach ($quizzes as $q): ?>
                    <option value="<?= (int)$q['id'] ?>" <?= (int)$q['id'] === $selectedQuizId ? 'selected' : '' ?>>
                        <?= e($q['title']) ?> <?= ((int)$q['id'] === (int)setting('main_survey_quiz_id')) ? ' (Main Survey)' : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="flex: 1; min-width: 140px;">
            <label for="type" style="font-size: 0.85rem; color: #4b5563; font-weight: bold; display: block; margin-bottom: 4px;">Participant Type</label>
            <select id="type" name="type" style="margin-bottom: 0;">
                <option value="all" <?= $type === 'all' ? 'selected' : '' ?>>All (Everyone)</option>
                <option value="user" <?= $type === 'user' ? 'selected' : '' ?>>Registered Users</option>
                <option value="guest" <?= $type === 'guest' ? 'selected' : '' ?>>Guest Visitors</option>
            </select>
        </div>

        <div style="flex: 2; min-width: 180px;">
            <label for="search" style="font-size: 0.85rem; color: #4b5563; font-weight: bold; display: block; margin-bottom: 4px;">Search</label>
            <input type="text" id="search" name="search" placeholder="Name, email, username, guest token..." value="<?= e($search) ?>" style="margin-bottom: 0;">
        </div>

        <div style="flex: 1; min-width: 130px;">
            <label for="start_date" style="font-size: 0.85rem; color: #4b5563; font-weight: bold; display: block; margin-bottom: 4px;">From Date</label>
            <input type="date" id="start_date" name="start_date" value="<?= e($startDate) ?>" style="margin-bottom: 0;">
        </div>

        <div style="flex: 1; min-width: 130px;">
            <label for="end_date" style="font-size: 0.85rem; color: #4b5563; font-weight: bold; display: block; margin-bottom: 4px;">To Date</label>
            <input type="date" id="end_date" name="end_date" value="<?= e($endDate) ?>" style="margin-bottom: 0;">
        </div>

        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn">Filter</button>
            <a href="<?= url('admin/surveys') ?>?quiz_id=<?= (int)$selectedQuizId ?>" class="btn" style="background: #9ca3af; text-decoration: none; color: #fff;">Reset</a>
        </div>
    </form>
</div>

<!-- Survey Submissions Table Card with Bulk Delete -->
<div class="card" style="padding: 0; overflow: hidden;">
    <div style="padding: 14px 20px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; background: #fafafa; flex-wrap: wrap; gap: 10px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <span style="font-weight: 700; color: #374151; font-size: 1rem;">
                Submissions List (<?= number_format($totalItems) ?> records found)
            </span>
            <span id="selectionSummary" style="font-size: 0.85rem; color: #6b7280; font-weight: 600; padding: 2px 8px; background: #e5e7eb; border-radius: 9999px;">
                0 selected
            </span>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <button type="button" id="bulkDeleteBtn" class="btn danger" disabled
                    style="opacity: 0.5; cursor: not-allowed; font-size: 0.85rem; padding: 7px 14px; display: inline-flex; align-items: center; gap: 6px;">
                <svg style="width: 15px; height: 15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Delete Selected (<span id="selectedCountText">0</span>)
            </button>
        </div>
    </div>

    <!-- Hidden Bulk Delete Form -->
    <form id="bulkDeleteForm" method="post" action="<?= url('admin/surveys/bulk-delete') ?>" style="display: none;">
        <?= csrf_field() ?>
        <input type="hidden" name="quiz_id" value="<?= (int)$selectedQuizId ?>">
        <input type="hidden" name="type" value="<?= e($type) ?>">
        <input type="hidden" name="search" value="<?= e($search) ?>">
        <input type="hidden" name="start_date" value="<?= e($startDate) ?>">
        <input type="hidden" name="end_date" value="<?= e($endDate) ?>">
        <input type="hidden" name="page" value="<?= (int)$page ?>">
        <div id="bulkDeleteInputs"></div>
    </form>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f9fafb; border-bottom: 2px solid #e5e7eb;">
                    <th style="padding: 12px 16px; width: 38px; text-align: center;">
                        <input type="checkbox" id="selectAllSurveys" title="Select all on this page" style="width: 16px; height: 16px; margin: 0; cursor: pointer;">
                    </th>
                    <th style="padding: 12px 16px; font-size: 0.8rem; font-weight: 700; color: #4b5563; text-transform: uppercase;">ID</th>
                    <th style="padding: 12px 16px; font-size: 0.8rem; font-weight: 700; color: #4b5563; text-transform: uppercase;">Type</th>
                    <th style="padding: 12px 16px; font-size: 0.8rem; font-weight: 700; color: #4b5563; text-transform: uppercase;">Participant Info</th>
                    <th style="padding: 12px 16px; font-size: 0.8rem; font-weight: 700; color: #4b5563; text-transform: uppercase;">Travel Experience</th>
                    <th style="padding: 12px 16px; font-size: 0.8rem; font-weight: 700; color: #4b5563; text-transform: uppercase;">Answered</th>
                    <th style="padding: 12px 16px; font-size: 0.8rem; font-weight: 700; color: #4b5563; text-transform: uppercase;">Submitted At</th>
                    <th style="padding: 12px 16px; font-size: 0.8rem; font-weight: 700; color: #4b5563; text-transform: uppercase; text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="8" style="padding: 32px 16px; text-align: center; color: #6b7280;">
                            No survey submissions found matching your search criteria.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <tr id="survey-row-<?= $item['type'] ?>-<?= $item['id'] ?>" style="border-bottom: 1px solid #e5e7eb; transition: background-color 0.15s ease;">
                            <td style="padding: 14px 16px; text-align: center;">
                                <input type="checkbox" class="survey-select-chk"
                                       value="<?= $item['type'] ?>:<?= $item['id'] ?>"
                                       id="chk-<?= $item['type'] ?>-<?= $item['id'] ?>"
                                       data-type="<?= $item['type'] ?>"
                                       data-id="<?= $item['id'] ?>"
                                       style="width: 16px; height: 16px; margin: 0; cursor: pointer;">
                            </td>

                            <td style="padding: 14px 16px; font-size: 0.88rem; font-weight: 600; color: #6b7280;">
                                #<?= $item['id'] ?>
                            </td>

                            <td style="padding: 14px 16px;">
                                <?php if ($item['type'] === 'user'): ?>
                                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 9999px; background: #dbeafe; color: #1e40af; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">
                                        <svg style="width: 12px; height: 12px;" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                        </svg>
                                        User
                                    </span>
                                <?php else: ?>
                                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 9999px; background: #fef3c7; color: #92400e; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">
                                        <svg style="width: 12px; height: 12px;" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
                                        </svg>
                                        Guest
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td style="padding: 14px 16px;">
                                <?php if ($item['type'] === 'user'): ?>
                                    <div style="font-weight: 600; color: #111827;"><?= e($item['name']) ?></div>
                                    <div style="font-size: 0.82rem; color: #6b7280;"><?= e($item['email']) ?></div>
                                    <?php if (!empty($item['username']) && $item['username'] !== 'N/A'): ?>
                                        <div style="font-size: 0.78rem; color: #9ca3af;">@<?= e($item['username']) ?></div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div style="font-weight: 600; color: #4b5563;">Khách vãng lai</div>
                                    <div style="font-size: 0.78rem; color: #6b7280; font-family: monospace;" title="<?= e($item['guest_token'] ?? '') ?>">
                                        Token: <?= e(substr((string)$item['guest_token'], 0, 14)) ?>...
                                    </div>
                                <?php endif; ?>
                            </td>

                            <td style="padding: 14px 16px; font-size: 0.88rem; color: #374151;">
                                <?php if (!empty($item['travel_status'])): ?>
                                    <?php
                                    $rawStatus = (string) $item['travel_status'];
                                    $statusLabel = match ($rawStatus) {
                                        'visited' => 'Đã từng đến Nhật Bản',
                                        'not-visited' => 'Chưa từng đến Nhật Bản',
                                        default => $rawStatus,
                                    };
                                    ?>
                                    <span style="display: inline-block; padding: 2px 8px; border-radius: 4px; background: #f3f4f6; color: #1f2937; font-size: 0.82rem; border: 1px solid #e5e7eb;">
                                        <?= e($statusLabel) ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: #9ca3af; font-size: 0.85rem;">—</span>
                                <?php endif; ?>
                            </td>

                            <td style="padding: 14px 16px; font-size: 0.88rem; font-weight: 600; color: #111827;">
                                <?= (int)$item['answers_count'] ?> câu đã trả lời
                            </td>

                            <td style="padding: 14px 16px; font-size: 0.85rem; color: #4b5563;">
                                <?= $item['submitted_at'] ? date('d/m/Y H:i:s', strtotime((string)$item['submitted_at'])) : '—' ?>
                            </td>

                            <td style="padding: 14px 16px; text-align: right; white-space: nowrap;">
                                <?php if ($item['type'] === 'user'): ?>
                                    <a href="<?= url('admin/surveys/user/' . $item['id']) ?>"
                                       class="btn" style="padding: 5px 12px; font-size: 0.82rem; background: #2563eb; text-decoration: none; margin-right: 4px;">
                                        View Answers
                                    </a>
                                    <form action="<?= url('admin/surveys/user/' . $item['id']) ?>" method="post" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài khảo sát của người dùng này cùng toàn bộ câu trả lời?')">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn danger" style="padding: 5px 10px; font-size: 0.82rem;">
                                            Delete
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <a href="<?= url('admin/surveys/guest/' . $item['id']) ?>"
                                       class="btn" style="padding: 5px 12px; font-size: 0.82rem; background: #d97706; text-decoration: none; margin-right: 4px;">
                                        View Answers
                                    </a>
                                    <form action="<?= url('admin/surveys/guest/' . $item['id']) ?>" method="post" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài khảo sát của khách này?')">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn danger" style="padding: 5px 10px; font-size: 0.82rem;">
                                            Delete
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div style="padding: 16px 20px; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; background: #fff;">
            <div style="font-size: 0.85rem; color: #6b7280;">
                Showing page <strong><?= $page ?></strong> of <strong><?= $totalPages ?></strong>
            </div>
            <div style="display: flex; gap: 6px;">
                <?php
                $queryParams = [
                    'quiz_id' => $selectedQuizId,
                    'type' => $type,
                    'search' => $search,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ];
                ?>
                <?php if ($page > 1): ?>
                    <a href="<?= url('admin/surveys') ?>?<?= http_build_query(array_merge($queryParams, ['page' => $page - 1])) ?>"
                       class="btn" style="background: #e5e7eb; color: #374151; padding: 6px 12px; font-size: 0.82rem;">&laquo; Previous</a>
                <?php endif; ?>

                <?php for ($p = max(1, $page - 2); $p <= min($totalPages, $page + 2); $p++): ?>
                    <a href="<?= url('admin/surveys') ?>?<?= http_build_query(array_merge($queryParams, ['page' => $p])) ?>"
                       class="btn" style="<?= $p === $page ? 'background: #2563eb; color: #fff;' : 'background: #f3f4f6; color: #374151;' ?> padding: 6px 12px; font-size: 0.82rem;">
                        <?= $p ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="<?= url('admin/surveys') ?>?<?= http_build_query(array_merge($queryParams, ['page' => $page + 1])) ?>"
                       class="btn" style="background: #e5e7eb; color: #374151; padding: 6px 12px; font-size: 0.82rem;">Next &raquo;</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAllSurveys');
    const checkboxes = document.querySelectorAll('.survey-select-chk');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const selectedCountText = document.getElementById('selectedCountText');
    const selectionSummary = document.getElementById('selectionSummary');
    const bulkDeleteForm = document.getElementById('bulkDeleteForm');
    const bulkDeleteInputs = document.getElementById('bulkDeleteInputs');

    function updateSelectionState() {
        const checkedBoxes = Array.from(checkboxes).filter(cb => cb.checked);
        const count = checkedBoxes.length;
        const total = checkboxes.length;

        if (selectedCountText) {
            selectedCountText.textContent = count;
        }

        if (bulkDeleteBtn && selectionSummary) {
            if (count > 0) {
                bulkDeleteBtn.disabled = false;
                bulkDeleteBtn.style.opacity = '1';
                bulkDeleteBtn.style.cursor = 'pointer';
                selectionSummary.textContent = count + ' selected';
                selectionSummary.style.background = '#fee2e2';
                selectionSummary.style.color = '#991b1b';
            } else {
                bulkDeleteBtn.disabled = true;
                bulkDeleteBtn.style.opacity = '0.5';
                bulkDeleteBtn.style.cursor = 'not-allowed';
                selectionSummary.textContent = '0 selected';
                selectionSummary.style.background = '#e5e7eb';
                selectionSummary.style.color = '#6b7280';
            }
        }

        if (selectAll && total > 0) {
            if (count === total) {
                selectAll.checked = true;
                selectAll.indeterminate = false;
            } else if (count > 0) {
                selectAll.checked = false;
                selectAll.indeterminate = true;
            } else {
                selectAll.checked = false;
                selectAll.indeterminate = false;
            }
        }

        checkboxes.forEach(cb => {
            const row = document.getElementById('survey-row-' + cb.value.replace(':', '-'));
            if (row) {
                row.style.backgroundColor = cb.checked ? '#fef2f2' : '';
            }
        });
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            const isChecked = this.checked;
            checkboxes.forEach(cb => {
                cb.checked = isChecked;
            });
            updateSelectionState();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateSelectionState);
    });

    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function() {
            const checkedBoxes = Array.from(checkboxes).filter(cb => cb.checked);
            if (checkedBoxes.length === 0) return;

            const count = checkedBoxes.length;
            const confirmMsg = `Bạn có chắc chắn muốn xóa ${count} bài làm khảo sát đã chọn cùng TOÀN BỘ câu trả lời của các bài này?\n\nHành động này không thể hoàn tác.`;
            if (!confirm(confirmMsg)) {
                return;
            }

            bulkDeleteInputs.innerHTML = '';
            checkedBoxes.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'items[]';
                input.value = cb.value;
                bulkDeleteInputs.appendChild(input);
            });

            bulkDeleteForm.submit();
        });
    }
});
</script>
