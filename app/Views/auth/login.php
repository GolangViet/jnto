<section class="section-login">
    <form class="login" method="post" action="/login" data-login-demo>
        <?= csrf_field() ?>
        <h2 class="login-title"><span>ĐĂNG NHẬP</span></h2>
        <div class="login-box">
            <p class="login-error" id="login-error" role="alert" aria-live="polite" hidden>
                <span class="login-error-line">Tên đăng nhập/ Mật khẩu không đúng</span><br>
                Vui lòng thử lại.
            </p>

            <div class="login-username">
                <label for="username" class="login-label">Tên đăng nhập:</label>
                <input
                    type="text"
                    name="username"
                    class="login-input"
                    id="username"
                    value="<?= e((string) old('username')) ?>"
                    autocomplete="username"
                    aria-describedby="login-error"
                    required />
            </div>
            <div class="line-decor">
                <img class="img" src="<?= assets('images/login/line-decor.webp') ?>" alt="line decor">
            </div>
            <div class="login-pass">
                <label for="password-input" class="login-label">Mật khẩu:</label>
                <div class="login-input-pass">
                    <input
                        aria-describedby="login-error"
                        autocomplete="current-password"
                        type="password"
                        name="password"
                        class="password-input"
                        id="password-input"
                        required />
                    <button type="button" class="pass_eye">
                        <svg class="pass_icon"><use xlink:href="#eye-closed-icon"></use></svg>
                    </button>
                </div>
            </div>

            <?php if ($error = app()->session()->pullFlash('error')): ?>
                <div class="message-container error" style="width: 100%; margin: 15px 0 15px 0;">
                    <svg class="message-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 20px; height: 20px; margin-right: 8px; flex-shrink: 0;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span><?= e($error) ?></span>
                </div>
            <?php endif; ?>

            <?php if ($errors = app()->session()->pullFlash('errors')): ?>
                <div class="message-container error" style="width: 100%; margin: 15px 0 15px 0;">
                    <svg class="message-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 20px; height: 20px; margin-right: 8px; flex-shrink: 0;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span><?= e(array_values($errors)[0][0] ?? '') ?></span>
                </div>
            <?php endif; ?>
        </div>

        <div class="login-footer">
            <button type="submit" class="btn-login"><span>ĐĂNG NHẬP</span></button>
            <h3>Bạn chưa có tài khoản? <a href="/register" title="Đăng ký">Đăng Ký</a></h3>
        </div>
    </form>
</section>
