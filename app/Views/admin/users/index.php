<style>
.copyable-cell {
    position: relative;
    padding-right: 28px !important; /* Make room for copy button */
}
.copyable-cell .cell-text {
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.copyable-cell .copy-btn {
    position: absolute;
    right: 6px;
    top: 50%;
    transform: translateY(-50%);
    opacity: 0;
    pointer-events: none;
    transition: all 0.15s ease-in-out;
    background: none;
    border: none;
    padding: 4px;
    border-radius: 4px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
}
.copyable-cell .copy-btn:hover {
    color: #4f46e5;
    background-color: #f1f5f9;
}
.copyable-cell:hover .copy-btn {
    opacity: 1;
    pointer-events: auto;
}
</style>

<div style="display:flex;justify-content:space-between;align-items:center">
    <h1>Users</h1>
    <a class="btn" href="<?= url('admin/users/create') ?>">Create user</a>
</div>

<div class="card" style="margin-bottom: 16px;">
    <form method="get" action="<?= url('admin/users') ?>" style="display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap;">
        <div style="flex: 2; min-width: 200px;">
            <label for="search" style="font-size: 0.85rem; color: #4b5563; font-weight: bold; display: block; margin-bottom: 4px;">Search</label>
            <input type="text" id="search" name="search" placeholder="Username, name, email..." value="<?= e($filters['search'] ?? '') ?>" style="margin-bottom: 0;">
        </div>
        <div style="flex: 1; min-width: 120px;">
            <label for="role" style="font-size: 0.85rem; color: #4b5563; font-weight: bold; display: block; margin-bottom: 4px;">Role</label>
            <select id="role" name="role" style="margin-bottom: 0;">
                <option value="">All Roles</option>
                <option value="admin" <?= ($filters['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="user" <?= ($filters['role'] ?? '') === 'user' ? 'selected' : '' ?>>User</option>
            </select>
        </div>
        <div style="flex: 1; min-width: 150px;">
            <label for="facebook_post" style="font-size: 0.85rem; color: #4b5563; font-weight: bold; display: block; margin-bottom: 4px;">Facebook Post</label>
            <select id="facebook_post" name="facebook_post" style="margin-bottom: 0;">
                <option value="">All Statuses</option>
                <option value="yes" <?= ($filters['facebook_post'] ?? '') === 'yes' ? 'selected' : '' ?>>Has Post</option>
                <option value="no" <?= ($filters['facebook_post'] ?? '') === 'no' ? 'selected' : '' ?>>No Post</option>
            </select>
        </div>
        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn">Filter</button>
            <a href="<?= url('admin/users') ?>" class="btn" style="background: #9ca3af; text-decoration: none; color: #fff;">Clear</a>
        </div>
    </form>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Facebook Post</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; color: #6b7280; padding: 24px;">No users found matching the criteria.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= (int)$u['id'] ?></td>
                        <td class="copyable-cell" style="max-width: 150px;" data-copy-text="<?= e($u['username']) ?>" title="<?= e($u['username']) ?>">
                            <span class="cell-text"><?= e($u['username']) ?></span>
                            <button type="button" class="copy-btn" onclick="copyToClipboard(this)" title="Copy username">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                            </button>
                        </td>
                        <td class="copyable-cell" style="max-width: 150px;" data-copy-text="<?= e($u['name']) ?>" title="<?= e($u['name']) ?>">
                            <span class="cell-text"><?= e($u['name']) ?></span>
                            <button type="button" class="copy-btn" onclick="copyToClipboard(this)" title="Copy name">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                            </button>
                        </td>
                        <td class="copyable-cell" style="max-width: 200px;" data-copy-text="<?= e($u['email']) ?>" title="<?= e($u['email']) ?>">
                            <span class="cell-text"><?= e($u['email']) ?></span>
                            <button type="button" class="copy-btn" onclick="copyToClipboard(this)" title="Copy email">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                            </button>
                        </td>
                        <td>
                            <span class="btn <?= $u['role'] === 'admin' ? 'danger' : 'muted' ?>" style="padding: 2px 6px; font-size: 0.8rem; cursor: default;">
                                <?= e(ucfirst($u['role'])) ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($u['facebook_url'])): ?>
                                <a href="<?= e($u['facebook_url']) ?>" target="_blank" rel="noopener noreferrer">View Post</a>
                                <?php if (isset($u['facebook_score'])): ?>
                                    <span style="font-size:0.8rem; color:#6b7280; margin-left: 4px;">(Score: <?= (float)$u['facebook_score'] ?>)</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="muted">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= date('Y-m-d H:i:s', strtotime($u['created_at'])) ?>
                        </td>
                        <td>
                            <a class="btn" style="font-size: 13px;" href="<?= url('admin/users/' . (int)$u['id'] . '/edit') ?>">Edit</a>
                            <form method="post" action="<?= url('admin/users/' . (int)$u['id']) ?>" style="display:inline">
                                <?= csrf_field() ?>
                                <input type="hidden" name="_method" value="DELETE">
                                <button class="btn danger" onclick="return confirm('Delete this user?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Pagination Footer -->
    <?php if ($totalPages > 1): ?>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding-top: 16px; border-top: 1px solid #e2e8f0; flex-wrap: wrap; gap: 12px;">
            <div style="color: #64748b; font-size: 0.85rem;">
                Showing Page <strong><?= $page ?></strong> of <strong><?= $totalPages ?></strong> (Total <strong><?= $total ?></strong> users)
            </div>
            <div style="display: flex; gap: 6px; align-items: center;">
                <?php 
                    $buildUrl = function($p) use ($filters) {
                        return url('admin/users?' . http_build_query(array_filter(array_merge($filters, [
                            'page' => $p > 1 ? $p : null,
                        ]))));
                    };
                ?>
                <?php if ($page > 1): ?>
                    <a href="<?= $buildUrl($page - 1) ?>" class="btn" style="background: #fff; border: 1px solid #cbd5e1; color: #475569; padding: 6px 12px; font-size: 0.85rem; font-weight: 500; border-radius: 6px; text-decoration: none; transition: all 0.2s; display: inline-flex; align-items: center;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">Previous</a>
                <?php endif; ?>

                <?php 
                $start = max(1, $page - 2);
                $end = min($totalPages, $page + 2);
                
                if ($start > 1) {
                    echo '<a href="' . $buildUrl(1) . '" class="btn" style="background: #fff; border: 1px solid #cbd5e1; color: #475569; padding: 6px 12px; font-size: 0.85rem; font-weight: 500; border-radius: 6px; text-decoration: none; transition: all 0.2s; display: inline-flex; align-items: center; justify-content: center; min-width: 32px; box-sizing: border-box;" onmouseover="this.style.background=\'#f8fafc\'" onmouseout="this.style.background=\'#fff\'">1</a>';
                    if ($start > 2) {
                        echo '<span style="color: #94a3b8; padding: 0 4px;">...</span>';
                    }
                }

                for ($i = $start; $i <= $end; $i++): 
                ?>
                    <?php if ($i === $page): ?>
                        <span style="background: #4f46e5; color: #fff; padding: 6px 12px; font-size: 0.85rem; font-weight: 600; border-radius: 6px; border: 1px solid #4f46e5; display: inline-flex; align-items: center; justify-content: center; min-width: 32px; box-sizing: border-box;"><?= $i ?></span>
                    <?php else: ?>
                        <a href="<?= $buildUrl($i) ?>" class="btn" style="background: #fff; border: 1px solid #cbd5e1; color: #475569; padding: 6px 12px; font-size: 0.85rem; font-weight: 500; border-radius: 6px; text-decoration: none; transition: all 0.2s; display: inline-flex; align-items: center; justify-content: center; min-width: 32px; box-sizing: border-box;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php 
                if ($end < $totalPages) {
                    if ($end < $totalPages - 1) {
                        echo '<span style="color: #94a3b8; padding: 0 4px;">...</span>';
                    }
                    echo '<a href="' . $buildUrl($totalPages) . '" class="btn" style="background: #fff; border: 1px solid #cbd5e1; color: #475569; padding: 6px 12px; font-size: 0.85rem; font-weight: 500; border-radius: 6px; text-decoration: none; transition: all 0.2s; display: inline-flex; align-items: center; justify-content: center; min-width: 32px; box-sizing: border-box;" onmouseover="this.style.background=\'#f8fafc\'" onmouseout="this.style.background=\'#fff\'">' . $totalPages . '</a>';
                }
                ?>

                <?php if ($page < $totalPages): ?>
                    <a href="<?= $buildUrl($page + 1) ?>" class="btn" style="background: #fff; border: 1px solid #cbd5e1; color: #475569; padding: 6px 12px; font-size: 0.85rem; font-weight: 500; border-radius: 6px; text-decoration: none; transition: all 0.2s; display: inline-flex; align-items: center;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">Next</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function copyToClipboard(btn) {
    const cell = btn.closest('.copyable-cell');
    if (!cell) return;
    const text = cell.getAttribute('data-copy-text');
    if (!text) return;

    navigator.clipboard.writeText(text).then(() => {
        const origHTML = btn.innerHTML;
        // Temporary success checkmark icon
        btn.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        `;
        btn.style.color = '#22c55e';
        
        setTimeout(() => {
            btn.innerHTML = origHTML;
            btn.style.color = '';
        }, 1500);
    }).catch(err => {
        console.error('Failed to copy: ', err);
    });
}
</script>