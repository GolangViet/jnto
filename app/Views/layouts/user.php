<!DOCTYPE html>
<html lang="vi">
<head>
    <title>JNTO</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?= assets('css/user/default.css') ?>">
    <link rel="stylesheet" href="<?= assets('css/user/style.css') ?>">
    <link rel="stylesheet" href="<?= assets('css/user/responsive.css') ?>">

    <?= render_styles() ?>
</head>
<body>
    <main>
        <section class="banner">
            <picture>
                <a href="/">
                    <source media="(max-width: 768px)" srcset="<?= assets('images/banner-m.webp') ?>">
                    <img class="banner_img" src="<?= assets('images/banner.webp') ?>" alt="banner">
                </a>
            </picture>
        </section>

        <?php if ($success = app()->session()->pullFlash('success')): ?>
            <div class="alert message-container-wrapper">
                <svg style="width: 20px; height: 20px; margin-right: 8px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <?= e($success) ?>
            </div>
        <?php endif; ?>

        <?php if ($error = app()->session()->pullFlash('error')): ?>
            <div class="alert message-container-wrapper error">
                <svg style="width: 20px; height: 20px; margin-right: 8px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($errors = app()->session()->pullFlash('errors')): ?>
            <div class="alert message-container-wrapper error">
                <ul style="margin: 0; padding-left: 20px; font-size: 18px; color: #F00; list-style: circle;">
                    <?php foreach ($errors as $errorField => $fieldErrors): ?>
                        <?php foreach ($fieldErrors as $err): ?>
                            <li><?= e($err) ?></li>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </ul>
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

    <script type="text/javascript" src="<?= assets('js/user/app.js') ?>"></script>

    <?= render_scripts() ?>
</body>
</html>
