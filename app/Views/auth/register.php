<div id="survey-save-notice" class="message-container info d-none" style="display: none!important; background-color: #e8f4fd; color: #0d47a1; border: 1px solid #bbdefb; margin: 12px auto; max-width: 580px; border-radius: 8px; padding: 12px 16px; font-size: 14px; text-align: center;">
    <span>🎉 Bạn đã hoàn thành khảo sát! Vui lòng đăng ký tài khoản (hoặc <a href="<?= url('login') ?>" style="color: #0d47a1; text-decoration: underline; font-weight: bold;">đăng nhập</a>) để lưu kết quả và tiếp tục.</span>
</div>

<section class="section-register">
    <form class="registers" id="register-form" action="<?= url('register') ?>" method="post" data-register-demo novalidate>
        <?= csrf_field() ?>
        <div class="reg-error" id="reg-error" role="alert" aria-live="polite" hidden>
            <p class="reg-error-text">
                <span class="reg-error-line">Tên đăng nhập này đã tồn tại. Vui lòng chọn tên khác.</span>
            </p>
            <button type="button" class="reg-error-close" id="reg-error-close" aria-label="Đóng thông báo">
                <img src="<?= assets('images/register/icon-close.webp') ?>" alt="Đóng" class="reg-error-close-img">
            </button>
        </div>
        <div class="frm-box">
            <h2 class="frm-box-title sr-only"><span>ĐĂNG KÝ</span></h2>
            <div class="frm-wrapper">
                <div class="login-username">
                    <label for="username" class="login-label">
                        Tên đăng nhập: <span class="label-sub">(Vui lòng KHÔNG sử dụng email cá nhân)</span>
                    </label>
                    <input type="text" class="login-input" id="username" name="username" value="<?= e((string) old('username')) ?>" autocomplete="username" required aria-describedby="reg-error">
                </div>
                <div class="login-pass">
                    <label for="password-input" class="login-label">Mật khẩu:</label>
                    <div class="login-input-pass">
                        <input type="password" class="password-input" id="password-input" name="password" autocomplete="new-password" required aria-describedby="reg-error">
                        <button type="button" class="pass_eye" id="pass-eye-btn" aria-label="Hiện/ẩn mật khẩu">
                            <img src="<?= assets('images/register/icon-hidepassword.webp') ?>" alt="Ẩn/Hiện mật khẩu" class="pass_eye_img">
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="frm-footer">
            <div class="agree-box">
                <label class="agree-item">
                    <input type="checkbox" name="agree1" value="checked">
                    <span class="checkmark"></span>
                    <span class="text">
                        Tôi đã đọc và đồng ý với
                        <a class="terms-modal-trigger" href="#terms-modal">Điều kiện &amp; Điều khoản.</a>
                    </span>
                </label>
                <label class="agree-item">
                    <input type="checkbox" name="agree2" value="0">
                    <span class="checkmark"></span>
                    <span class="text">
                        Tôi đồng ý cho Ban tổ chức sử dụng thông tin và hình ảnh cá nhân để liên hệ và trao giải.
                    </span>
                </label>
            </div>

            <div id="agree-error-message" class="message-container error" style="display: none; width: 100%; margin: 10px 0 10px 0; font-size: 13px;">
                <svg class="message-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 18px; height: 18px; flex-shrink: 0; margin-right: 6px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Vui lòng đọc và đồng ý với các điều khoản trước khi đăng ký.</span>
            </div>

            <button type="submit" class="btn-register"><span>ĐĂNG KÝ</span></button>
            <h3>Bạn đã có tài khoản? <a href="<?= url('login') ?>" title="Đăng nhập">Đăng nhập</a></h3>
        </div>
    </form>

    <div class="alert_info">
        <h3 class="alert_info_title">LƯU Ý</h3>
        <p class="alert_info_desc"><span>*Người tham gia <strong>vui lòng ghi nhớ thông tin tài khoản đã đăng ký.</strong> Trong trường hợp quên tài khoản hoặc mật khẩu, người tham gia sẽ cần đăng ký tài khoản mới và thực hiện các bước tham gia chương trình lại từ đầu.</span>
        </p>
    </div>
</section>

<div class="modal terms-modal" id="terms-modal" role="dialog" aria-modal="true" aria-label="Điều kiện và Điều khoản">
    <div class="terms-modal-dialog">
        <button type="button" class="modal-close terms-modal-close" aria-label="Đóng Điều kiện và Điều khoản"></button>
        <div class="terms-modal-scroll">
            <img src="<?= assets('images/JNTO-T&C-R2.png') ?>" alt="Nội dung Điều kiện và Điều khoản của chương trình">
        </div>
    </div>
</div>

<?php push_script(asset_with_version('js/user/register.js')); ?>
