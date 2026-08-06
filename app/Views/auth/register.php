<div id="agree-error-message" class="alert message-container error" style="display: none;">
    <svg class="message-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 20px; height: 20px; flex-shrink: 0;">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    <span>Vui lòng đọc và đồng ý với các điều khoản trước khi đăng ký.</span>
</div>

<section class="section-register">
    <div class="registers">
        <form class="frm-box" action="/register" method="post" id="register-form">
            <?= csrf_field() ?>
            <h2 class="frm-box-title"><span>ĐĂNG KÝ</span></h2>
            <div class="frm-wrapper">
                <p class="login-error" id="login-error" role="alert" aria-live="polite" hidden>
                    <span class="login-error-line">Tên đăng nhập / Mật khẩu không đúng</span><br>
                </p>

                <div class="login-username">
                    <label for="username" class="login-label">Tên đăng nhập:</label>
                    <input type="text" name="username" class="login-input" id="username" value="<?= e((string) old('username')) ?>" required />
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
            </div>
            <div class="frm-footer">
                <div class="agree-box">
                    <label class="agree-item">
                        <input type="checkbox" name="agree1" value="checked" />
                        <span class="checkmark"></span>
                        <span class="text">
                            Tôi đã đọc và đồng ý với
                            <a class="terms-modal-trigger" href="#terms-modal">Điều kiện &amp; Điều khoản.</a>
                        </span>
                    </label>
                    <label class="agree-item">
                        <input type="checkbox" name="agree2" value="0" />
                        <span class="checkmark"></span>
                        <span class="text">
                            Tôi đồng ý cho Ban tổ chức sử dụng thông tin và hình ảnh cá nhân để liên hệ và trao giải.
                        </span>
                    </label>
                </div>

                <button type="submit" class="btn-register"><span>ĐĂNG KÝ</span></button>
                <h3>Bạn đã có tài khoản? <a href="/login" title="Đăng nhập">Đăng nhập</a></h3>

                <div class="alert_info">
                    <h3>LƯU Ý</h3>
                    <p><span>*Người tham gia <strong>vui lòng ghi nhớ thông tin tài khoản đã<br>
                            đăng ký.</strong> Trong trường hợp quên tài khoản hoặc mật khẩu,<br>
                            người tham gia sẽ cần <strong>đăng ký tài khoản mới</strong> và thực hiện<br>
                            các bước tham gia chương trình lại từ đầu.</span>
                    </p>
                </div>
            </div>
        </form>
    </div>
</section>

<div class="modal terms-modal" id="terms-modal" role="dialog" aria-modal="true" aria-label="Điều kiện và Điều khoản">
    <div class="terms-modal-dialog">
        <button type="button" class="modal-close terms-modal-close" aria-label="Đóng Điều kiện và Điều khoản"></button>
        <div class="terms-modal-scroll">
            <img src="<?= assets('images/JNTO-T&amp;C-R3.png') ?>" alt="Nội dung Điều kiện và Điều khoản của chương trình">
        </div>
    </div>
</div>

<?php push_script(asset_with_version('js/user/register.js')); ?>
