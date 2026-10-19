<!DOCTYPE html>
<html lang="vi">
<head>
    <title>JNTO</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <link rel="canonical" href="<?= rtrim(url(), '/') ?>">
    <link rel="stylesheet" href="<?= asset_with_version('css/user/default.css') ?>">
    <link rel="stylesheet" href="<?= asset_with_version('css/user/style.css') ?>">
    <link rel="stylesheet" href="<?= asset_with_version('css/user/responsive.css') ?>">

    <?= render_styles() ?>

    <script>
        window.APP_URL = <?= json_encode(rtrim(url(), '/')) ?>;
    </script>

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-WWS45RT');</script>
    <!-- End Google Tag Manager -->
</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WWS45RT"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

    <?php
    $currentUser = app()->session()->get('user');
    $userSurveyStatus = 'not_done';
    if ($currentUser && !empty($currentUser['id'])) {
        $mainSurveyId = setting('main_survey_quiz_id');
        if ($mainSurveyId) {
            try {
                $db = \Core\Database::connection();
                $stmt = $db->prepare("SELECT COUNT(*) FROM cms.quiz_attempts WHERE quiz_id = :quiz_id AND user_id = :user_id AND status = 'submitted'");
                $stmt->execute(['quiz_id' => (int) $mainSurveyId, 'user_id' => (int) $currentUser['id']]);
                if ((int) $stmt->fetchColumn() > 0) {
                    $userSurveyStatus = 'done';
                }
            } catch (\Throwable $e) {}
        }
    }
    ?>
    <script>
        window.IS_LOGGED_IN = <?= json_encode(!empty($currentUser)) ?>;
        window.CURRENT_USER_SURVEY_STATUS = <?= json_encode($currentUser ? $userSurveyStatus : null) ?>;
    </script>
    <header class="header">
        <?php if ($currentUser && ($currentUser['role'] ?? 'user') === 'admin'): ?>
            <div class="admin-top-bar">
                <div class="admin-top-bar-content">
                    <span class="admin-welcome-text">Xin chào, <strong><?= e($currentUser['name']) ?></strong> (Quản trị viên)</span>
                    <a href="<?= url('admin/dashboard') ?>" class="admin-btn">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                        </svg>
                        Trang quản trị
                    </a>
                </div>
            </div>
        <?php endif; ?>
        <div class="header-wrapper">
            <div class="logo-japan">
                <a href="<?= url('/') ?>" style="font-size: 0;">
                    <img src="<?= assets('images/campaign-nhat-ban.png') ?>" alt="logo Campaign nhật bản">
                </a>
            </div>
            <div class="header-right">
                <div class="logos">
                    <img src="<?= assets('images/jed-logo.png') ?>" alt="logo jed">
                    <img src="<?= assets('images/jnto-logo.png') ?>" alt="logo jnto">
                </div>
                <?php if ($currentUser): ?>
                    <div class="header-divider" aria-hidden="true"></div>
                    <div class="user-menu-dropdown" id="userMenuDropdown">
                        <button type="button" class="user-menu-trigger" id="userMenuTrigger" aria-label="Menu tài khoản" aria-expanded="false" aria-haspopup="true">
                            <span class="user-avatar-circle">
                                <svg class="user-avatar-svg" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                                </svg>
                            </span>
                            <svg class="user-menu-chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div class="user-dropdown-card" id="userDropdownCard" role="menu">
                            <button type="button" class="dropdown-item" id="accountInfoBtn" role="menuitem">
                                <svg class="dropdown-item-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <span>Tài khoản</span>
                            </button>
                            <div class="dropdown-card-divider"></div>
                            <form id="header-logout-form" action="<?= url('logout') ?>" method="POST" style="display: none;">
                                <?= csrf_field() ?>
                            </form>
                            <button type="button" class="dropdown-item dropdown-item-danger" id="accountLogoutBtn" role="menuitem">
                                <svg class="dropdown-item-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                                <span>Đăng xuất</span>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
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
                <source media="(max-width: 768px)" srcset="<?= asset_with_version('images/banner-m.webp') ?>">
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
                    <h3 class="footer_socials_title"><span>JNTO - Cơ quan Xúc tiến Du lịch Nhật Bản</span></h3>
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
                        <a href="https://www.jal.co.jp/vn/vn/" title="Japan Airlines" target="_blank">
                            <img src="<?= assets('images/brand-4.png') ?>" alt="Japan Airlines">
                        </a>
                        <a target="_blank" href="https://travelcontentsapp.com/en/" title="Travel">
                            <picture>
                                <source media="(max-width: 768px)" srcset="<?= assets('images/footer/logo-travel-contents-mobile.png') ?>">
                                <img src="<?= assets('images/footer/logo-travel-contents.png') ?>" alt="Travel contents logo">
                            </picture>
                        </a>
                        <a target="_blank" href="https://www.traveloka.com/vi-vn/destination/country/japan-20001756?funnel_source=Merchandising.vacation.TravelGuides_LandingPage-web-VN-DLP&funnel_id=M_1_b41683d458b3ed10096e416ad64ee41bff455f79_0_22728a3bdc820eeec027b382e039b800007ce70f&internal_source=true&cur=VND" title="Traveloka">
                            <picture>
                                <source media="(max-width: 768px)" srcset="<?= assets('images/footer/logo-traveloka-mobile.png') ?>">
                                <img src="<?= assets('images/footer/logo-traveloka.png') ?>" alt="Traveloka logo">
                            </picture>
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

    <?php if ($currentUser = app()->session()->get('user')): ?>
        <div class="account-modal-overlay" id="accountInfoModal" role="dialog" aria-modal="true" aria-labelledby="accountModalTitle" style="display: none;">
            <div class="account-modal-backdrop" id="accountModalBackdrop"></div>
            <div class="account-modal-container">
                <div class="account-modal-header">
                    <div class="account-modal-title-wrapper">
                        <div class="account-modal-header-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <h3 class="account-modal-title" id="accountModalTitle">Thông tin tài khoản</h3>
                    </div>
                    <button type="button" class="account-modal-close" id="closeAccountModal" aria-label="Đóng">&times;</button>
                </div>
                <div class="account-modal-body">
                    <div class="account-profile-badge">
                        <div class="account-badge-avatar">
                            <?= mb_substr(e($currentUser['name'] ?: ($currentUser['username'] ?? 'U')), 0, 1, 'UTF-8') ?>
                        </div>
                        <div class="account-badge-meta">
                            <div class="account-badge-name"><?= e($currentUser['name'] ?: ($currentUser['username'] ?? 'Người dùng')) ?></div>
                            <div class="account-badge-role">
                                <?= ($currentUser['role'] ?? 'user') === 'admin' ? 'Quản trị viên' : 'Thành viên' ?>
                            </div>
                        </div>
                    </div>

                    <div class="account-info-grid">
                        <div class="account-info-item">
                            <span class="account-info-label">Tên hiển thị:</span>
                            <span class="account-info-value"><?= e($currentUser['name']) ?></span>
                        </div>
                        <?php if (!empty($currentUser['username'])): ?>
                            <div class="account-info-item">
                                <span class="account-info-label">Tên đăng nhập:</span>
                                <span class="account-info-value"><?= e($currentUser['username']) ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($currentUser['email'])): ?>
                            <div class="account-info-item">
                                <span class="account-info-label">Email:</span>
                                <span class="account-info-value"><?= e($currentUser['email']) ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="account-info-item">
                            <span class="account-info-label">Vai trò:</span>
                            <span class="account-info-value"><?= ($currentUser['role'] ?? 'user') === 'admin' ? 'Quản trị viên (Admin)' : 'Người tham gia' ?></span>
                        </div>
                    </div>

                    <?php if (($currentUser['role'] ?? 'user') === 'admin'): ?>
                        <div class="account-modal-admin-action">
                            <a href="<?= url('admin/dashboard') ?>" class="account-admin-link">
                                <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
                                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                                </svg>
                                <span>Đi đến Trang quản trị</span>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="account-modal-footer">
                    <button type="button" class="account-modal-btn" id="closeAccountModalBtn">Đóng</button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?= render_modals() ?>

    <script type="text/javascript" src="<?= asset_with_version('js/user/cursor.js') ?>"></script>
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
            // Password toggle
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

            // User dropdown toggle
            const userMenuDropdown = document.getElementById('userMenuDropdown');
            const userMenuTrigger = document.getElementById('userMenuTrigger');
            const userDropdownCard = document.getElementById('userDropdownCard');

            if (userMenuTrigger && userDropdownCard) {
                userMenuTrigger.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const isOpen = userMenuDropdown.classList.toggle('is-open');
                    userMenuTrigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                });

                document.addEventListener('click', function (e) {
                    if (userMenuDropdown && !userMenuDropdown.contains(e.target)) {
                        userMenuDropdown.classList.remove('is-open');
                        userMenuTrigger.setAttribute('aria-expanded', 'false');
                    }
                });
            }

            // Persist survey status in localStorage
            window.CURRENT_USER_SURVEY_STATUS = <?= json_encode($currentUser ? $userSurveyStatus : null) ?>;
            if (window.CURRENT_USER_SURVEY_STATUS) {
                try {
                    const raw = localStorage.getItem('jnto_survey_data');
                    const data = raw ? JSON.parse(raw) : {};
                    if (window.CURRENT_USER_SURVEY_STATUS === 'done') {
                        localStorage.setItem('jnto_survey_status', 'done');
                        data.status = 'done';
                        data.pending_sync = false;
                        localStorage.setItem('jnto_survey_data', JSON.stringify(data));
                    } else if (!data.pending_sync && data.status !== 'done') {
                        localStorage.setItem('jnto_survey_status', 'not_done');
                        data.status = 'not_done';
                        localStorage.setItem('jnto_survey_data', JSON.stringify(data));
                    }
                } catch (e) {}
            }

            // Logout handling: persist survey status before submit
            const accountLogoutBtn = document.getElementById('accountLogoutBtn');
            const logoutForm = document.getElementById('header-logout-form');
            if (accountLogoutBtn && logoutForm) {
                accountLogoutBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    if (window.CURRENT_USER_SURVEY_STATUS === 'done') {
                        try {
                            localStorage.setItem('jnto_survey_status', 'done');
                            const raw = localStorage.getItem('jnto_survey_data');
                            const data = raw ? JSON.parse(raw) : {};
                            data.status = 'done';
                            data.pending_sync = false;
                            localStorage.setItem('jnto_survey_data', JSON.stringify(data));
                        } catch (err) {
                            console.error('Failed to save survey status before logout:', err);
                        }
                    }
                    logoutForm.submit();
                });
            }

            // Account info modal
            const accountInfoBtn = document.getElementById('accountInfoBtn');
            const accountModal = document.getElementById('accountInfoModal');
            const closeAccountModal = document.getElementById('closeAccountModal');
            const closeAccountModalBtn = document.getElementById('closeAccountModalBtn');
            const accountModalBackdrop = document.getElementById('accountModalBackdrop');

            function openAccountModal() {
                if (accountModal) {
                    accountModal.style.display = 'flex';
                    if (userMenuDropdown) {
                        userMenuDropdown.classList.remove('is-open');
                        userMenuTrigger.setAttribute('aria-expanded', 'false');
                    }
                    document.body.style.overflow = 'hidden';
                }
            }

            function closeAccountModalFunc() {
                if (accountModal) {
                    accountModal.style.display = 'none';
                    document.body.style.overflow = '';
                }
            }

            if (accountInfoBtn) {
                accountInfoBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    openAccountModal();
                });
            }

            if (closeAccountModal) closeAccountModal.addEventListener('click', closeAccountModalFunc);
            if (closeAccountModalBtn) closeAccountModalBtn.addEventListener('click', closeAccountModalFunc);
            if (accountModalBackdrop) accountModalBackdrop.addEventListener('click', closeAccountModalFunc);

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    if (userMenuDropdown) {
                        userMenuDropdown.classList.remove('is-open');
                        userMenuTrigger.setAttribute('aria-expanded', 'false');
                    }
                    closeAccountModalFunc();
                }
            });
        });
    </script>
</body>
</html>
