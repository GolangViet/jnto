<div class="card" style="max-width: 450px; margin: 40px auto; padding: 36px; animation: fadeIn 0.4s ease-out;">
    <div style="text-align: center; margin-bottom: 28px;">
        <h1 style="font-size: 2rem; font-weight: 800; margin: 0 0 8px; background: linear-gradient(to right, #1e293b, #475569); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Sign In</h1>
        <p class="muted" style="margin: 0; font-size: 1rem;">Login to your end-user account</p>
    </div>

    <?php if ($errors = app()->session()->pullFlash('errors')): ?>
        <div class="alert error" style="margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px;">
                <?php foreach ($errors as $errorField => $fieldErrors): ?>
                    <?php foreach ($fieldErrors as $err): ?>
                        <li><?= e($err) ?></li>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="/login" style="margin: 0;">
        <?= csrf_field() ?>
        
        <div style="margin-bottom: 18px;">
            <label style="font-weight: 600; font-size: 0.875rem; color: #475569; display: block; margin-bottom: 6px;">Email Address</label>
            <input type="email" name="email" value="<?= e((string) old('email')) ?>" required placeholder="you@example.com" style="margin: 0;">
        </div>

        <div style="margin-bottom: 24px;">
            <label style="font-weight: 600; font-size: 0.875rem; color: #475569; display: block; margin-bottom: 6px;">Password</label>
            <input type="password" name="password" required placeholder="••••••••" style="margin: 0;">
        </div>

        <button class="btn" style="width: 100%; padding: 12px; font-size: 1rem;">
            Sign In
        </button>
    </form>
</div>
