<div style="display:flex;justify-content:space-between;align-items:center">
    <h1>Users</h1>
    <a class="btn" href="<?= url('admin/users/create') ?>">Create user</a>
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
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= (int)$u['id'] ?></td>
                    <td><?= e($u['username']) ?></td>
                    <td><?= e($u['name']) ?></td>
                    <td><?= e($u['email']) ?></td>
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
                    <td><?= e($u['created_at']) ?></td>
                    <td>
                        <a class="btn" href="<?= url('admin/users/' . (int)$u['id'] . '/edit') ?>">Edit</a>
                        <form method="post" action="<?= url('admin/users/' . (int)$u['id']) ?>" style="display:inline">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="DELETE">
                            <button class="btn danger" onclick="return confirm('Delete this user?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
