<div id="survey-save-notice" class="message-container info d-none" style="display: none!important; background-color: #e8f4fd; color: #0d47a1; border: 1px solid #bbdefb; margin: 12px auto; max-width: 580px; border-radius: 8px; padding: 12px 16px; font-size: 14px; text-align: center;">
    <span>🎉 Bạn đã hoàn thành khảo sát! Vui lòng đăng nhập (hoặc <a href="<?= url('register') ?>" style="color: #0d47a1; text-decoration: underline; font-weight: bold;">đăng ký</a>) để lưu kết quả và tiếp tục.</span>
</div>

<section class="section-login">
    <form class="login" method="post" action="<?= url('login') ?>" data-login-demo>
        <?= csrf_field() ?>
        <div class="login-error" id="login-error" role="alert" aria-live="polite" hidden>
            <p class="login-error-text">
                <span class="login-error-line">Tên đăng nhập/ Mật khẩu không đúng.</span><br>
                Vui lòng thử lại.
            </p>
            <button type="button" class="login-error-close" id="login-error-close" aria-label="Đóng thông báo">
                <img src="<?= assets('images/login/icon-close.webp') ?>" alt="Đóng" class="login-error-close-img">
            </button>
        </div>
        <h2 class="login-title sr-only"><span>ĐĂNG NHẬP</span></h2>
        <div class="login-box">
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
                    <button type="button" class="pass_eye" id="pass-eye-btn" aria-label="Hiện/ẩn mật khẩu">
                        <img src="<?= assets('images/login/icon-hidepassword.webp') ?>" alt="Ẩn/Hiện mật khẩu" class="pass_eye_img">
                    </button>
                </div>
            </div>
        </div>

        <div class="login-footer">
            <button type="submit" class="btn-login"><span>ĐĂNG NHẬP</span></button>
            <h3>Bạn chưa có tài khoản? <a href="<?= url('register') ?>" title="Đăng ký">Đăng ký</a></h3>
        </div>
    </form>
</section>

<?php push_script(asset_with_version('js/user/login.js')); ?>
