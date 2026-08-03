<div style="margin-bottom: 24px;">
    <a href="/admin/users" style="text-decoration: none; color: #4f46e5; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; font-size: 0.9rem;">
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
    <form method="post" action="/admin/users/<?= (int)$user['id'] ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="_method" value="PUT">
        <?php require app()->basePath('app/Views/admin/users/form.php'); ?>
        <div style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 12px;">
            <a href="/admin/users" class="btn" style="background: #9ca3af; text-decoration: none;">Cancel</a>
            <button class="btn" style="background: #4f46e5;">Update</button>
        </div>
    </form>
</div>
