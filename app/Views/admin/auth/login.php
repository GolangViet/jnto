<div class="card" style="max-width: 450px; margin: 40px auto; padding: 32px; border-top: 4px solid #2563eb; animation: fadeIn 0.4s ease-out;">
    <div style="text-align: center; margin-bottom: 24px;">
        <h1 style="font-size: 1.75rem; font-weight: 700; margin: 0 0 8px; color: #1e293b;">Admin Login</h1>
        <p class="muted" style="margin: 0; font-size: 0.9rem;">Access the administrative dashboard</p>
    </div>

    <?php if ($errors = app()->session()->pullFlash('errors')): ?>
        <div class="alert error" style="margin-bottom: 20px; font-size: 0.9rem; padding: 12px; border-radius: 6px; background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b;">
            <ul style="margin: 0; padding-left: 20px;">
                <?php foreach ($errors as $errorField => $fieldErrors): ?>
                    <?php foreach ($fieldErrors as $err): ?>
                        <li><?= e($err) ?></li>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="/admin/login" style="margin: 0;">
        <?= csrf_field() ?>
        
        <div style="margin-bottom: 16px;">
            <label style="font-weight: 600; font-size: 0.875rem; color: #475569; display: block; margin-bottom: 6px;">Email Address</label>
            <input type="email" name="email" value="<?= e((string) old('email')) ?>" required placeholder="admin@example.com" style="margin: 0; padding: 11px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; width: 100%; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 24px;">
            <label style="font-weight: 600; font-size: 0.875rem; color: #475569; display: block; margin-bottom: 6px;">Password</label>
            <input type="password" name="password" required placeholder="••••••••" style="margin: 0; padding: 11px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; width: 100%; box-sizing: border-box;">
        </div>

        <button class="btn" style="width: 100%; padding: 12px; font-size: 1rem; font-weight: 600; background: #2563eb; color: #fff; border: none; border-radius: 8px; cursor: pointer; transition: background 0.2s;">
            Sign In to Admin
        </button>
    </form>
</div>
