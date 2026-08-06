<!DOCTYPE html>
<html lang="vi">
<head>
    <title>JNTO</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?= asset_with_version('css/user/default.css') ?>">
    <link rel="stylesheet" href="<?= asset_with_version('css/user/style.css') ?>">
    <link rel="stylesheet" href="<?= asset_with_version('css/user/responsive.css') ?>">

    <?= render_styles() ?>
</head>
<body>
    <header class="header">
        <?php if ($user = app()->session()->get('user')): ?>
            <?php if (($user['role'] ?? 'user') === 'admin'): ?>
                <div class="admin-top-bar">
                    <div class="admin-top-bar-content">
                        <span class="admin-welcome-text">Xin chào, <strong><?= e($user['name']) ?></strong> (Quản trị viên)</span>
                        <a href="/admin/dashboard" class="admin-btn">
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                            </svg>
                            Trang quản trị
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <div class="header-wrapper">
            <div class="logo-japan">
                <a href="/" style="font-size: 0;">
                    <img src="<?= assets('images/campaign-nhat-ban.png') ?>" alt="logo Campaign nhật bản">
                </a>
            </div>
            <div class="logos">
                <img src="<?= assets('images/jed-logo.png') ?>" alt="logo jed">
                <img src="<?= assets('images/jnto-logo.png') ?>" alt="logo jnto">
            </div>
            <div class="titles">
                <div class="title_line_lft">
                    <img class="img" src="<?= assets('images/hoa-b.png') ?>" alt="icon hoa">
                </div>
                <h1 class="title">
                    VẺ ĐẸP VÔ TẬN <br> ĐI ĐỂ TRỞ VỀ
                </h1>
                <div class="title_line_rgt">
                    <img class="img" src="<?= assets('images/hoa-b.png') ?>" alt="icon hoa">
                </div>
            </div>
        </div>
    </header>

    <main>
        <section class="banner">
            <picture>
                <source media="(max-width: 768px)" srcset="<?= assets('images/banner-m.webp') ?>">
                <img class="banner_img" src="<?= assets('images/banner.webp') ?>" alt="banner">
            </picture>
        </section>

        <?php if ($success = app()->session()->pullFlash('success')): ?>
            <div class="alert message-container success">
                <svg style="width: 20px; height: 20px; margin-right: 8px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <?= e($success) ?>
            </div>
        <?php endif; ?>

        <?= $content ?>

    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-wrapper">
                <div class="footer_socials">
                    <h3 class="footer_socials_title"><span>JNTO - Cơ quan Xúc tiến Du Lịch Nhật Bản</span></h3>
                    <ul class="footer_social">
                        <li>
                            <a class="footer_social_item" href="https://www.facebook.com/camnhannhatban" title="Fanpage" target="_blank">
                                <svg class="mb_social_icon"><use xlink:href="#facebook-icon"></use></svg>
                            </a>
                        </li>
                        <li>
                            <a class="footer_social_item" href="https://www.instagram.com/visitjapan_vn" title="instagram" target="_blank">
                                <svg class="mb_social_icon"><use xlink:href="#instagram-icon"></use></svg>
                            </a>
                        </li>
                        <li>
                            <a class="footer_social_item" href="https://www.youtube.com/user/visitjapan/featured" title="youtube" target="_blank">
                                <svg class="mb_social_icon"><use xlink:href="#youtube-icon"></use></svg>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="footer_sponsors">
                    <h3 class="footer_sponsor_title"><span>Nhà tài trợ</span></h3>
                    <div class="footer_sponser_brands">
                        <a href="https://www.vietnamairlines.com/dk/vi/lotusmiles" title="Vietnam Arilines" target="_blank">
                            <img src="<?= assets('images/brand-1.webp') ?>" alt="Vietnam Arilines">
                        </a>
                        <a target="_blank" href="https://travelcontentsapp.com/en/" title="Travel">
                            <img src="<?= assets('images/brand-2.webp') ?>" alt="Travel contents logo">
                        </a>
                        <a target="_blank" href="https://www.traveloka.com/vi-vn/destination/country/japan-20001756?funnel_source=Merchandising.vacation.TravelGuides_LandingPage-web-VN-DLP&funnel_id=M_1_b41683d458b3ed10096e416ad64ee41bff455f79_0_22728a3bdc820eeec027b382e039b800007ce70f&internal_source=true&cur=VND" title="Traveloka">
                            <img src="<?= assets('images/brand-3.webp') ?>" alt="Traveloka logo">
                        </a>

                    </div>
                </div>
            </div>
            <div class="footer-private">
                <ul class="footer_links">
                    <li><a href="https://www.japan.travel/vi/vn/privacy-policy" target="_blank" title="Chính sách Quyền riêng tư">Chính sách Quyền riêng tư</a></li>
                    <li><a href="https://www.japan.travel/vi/vn/cookie-policy" target="_blank" title="Chính sách Cookie">Chính sách Cookie</a></li>
                </ul>
            </div>
        </div>
    </footer>

    <?= render_modals() ?>

    <script type="text/javascript" src="<?= asset_with_version('js/user/app.js') ?>"></script>

    <?= render_scripts() ?>

    <svg aria-hidden="true" style="position: absolute; width: 0; height: 0; overflow: hidden;" version="1.1"
        xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
        <defs>
            <symbol id="facebook-icon" viewBox="0 0 32 32">
                <path d="M22.912 10.485l-0.582 5.515h-4.409v16h-6.622v-16h-3.3v-5.515h3.3v-3.32c0-4.486 1.865-7.164 7.168-7.164h4.407v5.515h-2.758c-2.058 0-2.195 0.777-2.195 2.214v2.756h4.99z"></path>
            </symbol>
            <symbol id="youtube-icon" viewBox="0 0 32 32">
                <path d="M31.331 7.52c-0.368-1.386-1.452-2.477-2.829-2.847-2.496-0.673-12.502-0.673-12.502-0.673s-10.007 0-12.502 0.673c-1.377 0.37-2.461 1.462-2.829 2.847-0.669 2.512-0.669 7.752-0.669 7.752s0 5.24 0.669 7.752c0.368 1.386 1.452 2.432 2.829 2.802 2.496 0.673 12.502 0.673 12.502 0.673s10.007 0 12.502-0.673c1.377-0.37 2.461-1.416 2.829-2.802 0.669-2.512 0.669-7.752 0.669-7.752s0-5.241-0.669-7.752zM12.727 20.031v-9.516l8.364 4.758-8.364 4.758z"></path>
            </symbol>
            <symbol id="instagram-icon" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                <path d="M22.3,8.4c-0.8,0-1.4,0.6-1.4,1.4c0,0.8,0.6,1.4,1.4,1.4c0.8,0,1.4-0.6,1.4-1.4C23.7,9,23.1,8.4,22.3,8.4z"/>
                <path d="M16,10.2c-3.3,0-5.9,2.7-5.9,5.9s2.7,5.9,5.9,5.9s5.9-2.7,5.9-5.9S19.3,10.2,16,10.2z M16,19.9c-2.1,0-3.8-1.7-3.8-3.8   c0-2.1,1.7-3.8,3.8-3.8c2.1,0,3.8,1.7,3.8,3.8C19.8,18.2,18.1,19.9,16,19.9z"/>
                <path d="M20.8,4h-9.5C7.2,4,4,7.2,4,11.2v9.5c0,4,3.2,7.2,7.2,7.2h9.5c4,0,7.2-3.2,7.2-7.2v-9.5C28,7.2,24.8,4,20.8,4z M25.7,20.8   c0,2.7-2.2,5-5,5h-9.5c-2.7,0-5-2.2-5-5v-9.5c0-2.7,2.2-5,5-5h9.5c2.7,0,5,2.2,5,5V20.8z"/>
            </symbol>
            <symbol id="eye-closed-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-labelledby="eyeCrossedIconTitle"  stroke-width="1" stroke-linecap="square" stroke-linejoin="miter" fill="none" color="#000000">
                <title id="eyeCrossedIconTitle">Hidden (crossed eye)</title>
                <path d="M22 12C22 12 19 18 12 18C5 18 2 12 2 12C2 12 5 6 12 6C19 6 22 12 22 12Z"/> <circle cx="12" cy="12" r="3"/> <path d="M3 21L20 4"/>
             </symbol>
             <symbol id="eye-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-labelledby="eyeIconTitle" stroke-width="1" stroke-linecap="square" stroke-linejoin="miter" fill="none" color="#000000">
                <title id="eyeIconTitle">Visible (eye)</title>
                <path d="M22 12C22 12 19 18 12 18C5 18 2 12 2 12C2 12 5 6 12 6C19 6 22 12 22 12Z"/> <circle cx="12" cy="12" r="3"/>
             </symbol>
        </defs>
    </svg>

    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.pass_eye').forEach(function (button) {
                button.addEventListener('click', function () {
                    const input = button.parentElement.querySelector('input');
                    const useTag = button.querySelector('use');
                    if (input && useTag) {
                        if (input.type === 'password') {
                            input.type = 'text';
                            useTag.setAttribute('xlink:href', '#eye-icon');
                        } else {
                            input.type = 'password';
                            useTag.setAttribute('xlink:href', '#eye-closed-icon');
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
