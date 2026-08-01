<?php push_style(assets('css/user/page6.css')); ?>

<section class="success-section" aria-labelledby="success-title">
    <div class="success-panel">
        <h2 class="success-title" id="success-title">
            <span>BẠN ĐÃ NỘP BÀI THÀNH CÔNG</span>
        </h2>

        <div class="success-copy">
            <p class="success-thanks">
                Cảm ơn bạn đã tham gia Chương trình<br>
                <strong>“NHẬT BẢN: VẺ ĐẸP VÔ TẬN, ĐI ĐỂ TRỞ VỀ”</strong>
            </p>

            <img class="success-decor" src="<?= assets('images/page-6/line-decor.webp') ?>" alt="">

            <p class="success-result">
                Hãy nhớ check kết quả được công bố trên<br>
                Fanpage JNTO <strong>ngày 20/10</strong> nhé!
            </p>
        </div>
    </div>

    <div class="success-banners">
        <a class="success-banner-link" href="https://www.vietnamairlines.com/dk/vi/lotusmiles/enroll-new"
           target="_blank" rel="noopener noreferrer"
           aria-label="Đăng ký Lotusmiles của Vietnam Airlines">
            <img src="<?= assets('images/page-6/banner-vna.webp') ?>" alt="Vietnam Airlines tăng tần suất kết nối Việt Nam và Nhật Bản">
        </a>

        <a class="success-banner-link" href="https://travelcontentsapp.com/en/"
           target="_blank" rel="noopener noreferrer"
           aria-label="Khám phá Have Fun Pass">
            <img src="<?= assets('images/page-6/banner-hfp.webp') ?>" alt="Have Fun Pass Japan">
        </a>
    </div>
</section>

<section class="explore-section" aria-labelledby="explore-title">
    <h2 class="explore-title" id="explore-title">
        <span>Khám phá thêm về các vùng tại Nhật Bản</span>
    </h2>

    <div class="region-swiper" aria-label="Danh sách các vùng tại Nhật Bản">
        <div class="region-swiper__track">
            <a class="region-swiper__slide" href="#" data-pending-link aria-label="Khám phá vùng Chugoku">
                <img src="<?= assets('images/page-6/section-3/box-chugoku.webp') ?>" alt="Phong cảnh vùng Chugoku">
                <strong>CHUGOKU</strong>
            </a>

            <a class="region-swiper__slide" href="#" data-pending-link aria-label="Khám phá vùng Kyushu">
                <img src="<?= assets('images/page-6/section-3/box-kyushu.webp') ?>" alt="Văn hóa vùng Kyushu">
                <strong>KYUSHU</strong>
            </a>

            <a class="region-swiper__slide" href="#" data-pending-link aria-label="Khám phá vùng Hokkaido">
                <img src="<?= assets('images/page-6/section-3/box-hokkaido.webp') ?>" alt="Phong cảnh vùng Hokkaido">
                <strong>HOKKAIDO</strong>
            </a>

            <a class="region-swiper__slide" href="#" data-pending-link aria-label="Khám phá vùng Chubu">
                <img src="<?= assets('images/page-6/section-3/box-chubu.webp') ?>" alt="Phong cảnh vùng Chubu">
                <strong>CHUBU</strong>
            </a>
        </div>
    </div>

    <a class="explore-home-button" href="https://www.japan.travel/vi/vn/">
        <span>Trang chủ</span>
    </a>
</section>

<?php push_script(assets('js/user/page6.js')); ?>
