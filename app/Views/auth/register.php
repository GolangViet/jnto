<section class="section-register">
    <div class="registers">
        <form class="frm-box" action="/register" method="post">
            <?= csrf_field() ?>
            <h2 class="frm-box-title"><span>ĐĂNG KÝ</span></h2>
            <div class="frm-wrapper">
                <div class="login-username">
                    <label for="username" class="login-label">Tên đăng nhập:</label>
                    <input
                        type="text"
                        name="username"
                        class="login-input"
                        id="username"
                        value="<?= e((string) old('username')) ?>"
                        required />
                </div>
                <div class="line-decor">
                    <img class="img" src="<?= assets('images/login/line-decor.webp') ?>" alt="line decor">
                </div>
                <div class="login-pass">
                    <label for="password-input" class="login-label">Mật khẩu:</label>
                    <div class="login-input-pass">
                        <input type="password" name="password" class="password-input" id="password-input" required />
                        <button type="button" class="pass_eye">
                            <svg class="pass_icon"><use xlink:href="#eye-closed-icon"></use></svg>
                        </button>
                    </div>
                </div>
                <div class="login-pass">
                    <label for="password-confirm-input" class="login-label">Xác nhận mật khẩu:</label>
                    <div class="login-input-pass">
                        <input
                            type="password"
                            name="password_confirmation"
                            class="password-input"
                            id="password-confirm-input"
                            required />
                        <button type="button" class="pass_eye">
                            <svg class="pass_icon"><use xlink:href="#eye-closed-icon"></use></svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="frm-footer">
                <button type="submit" class="btn-login"><span>ĐĂNG KÝ</span></button>
                <h3>Bạn đã có tài khoản? <a href="/login" title="Đăng nhập">Đăng nhập</a></h3>
            </div>
        </form>
    </div>
</section>
