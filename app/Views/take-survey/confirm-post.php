<?php push_style(asset_with_version('css/user/page4.css')); ?>
<?php push_style(asset_with_version('css/user/page5.css')); ?>

<section class="region-map" aria-labelledby="region-map-title">
    <h2 class="visually-hidden" id="region-map-title">Khám phá các vùng của Nhật Bản</h2>

    <div class="region-map__guide">
        <img class="region-map__search-line" src="<?= assets('images/page-4/line-search.webp') ?>" alt="">
        <p>Vui lòng truy cập vào từng vùng để tìm hiểu thông tin<br>và trả lời các câu hỏi tương ứng</p>
        <img class="region-map__line" src="<?= assets('images/page-4/line.webp') ?>" alt="">
    </div>

    <a class="region-card region-card--kyushu"
       href="https://www.japan.travel/vi/destinations/kyushu/"
       target="_blank" rel="noopener noreferrer"
       aria-label="Tìm hiểu về vùng Kyushu">
        <img src="<?= assets('images/page-4/box-kyushu.webp') ?>" alt="">
    </a>

    <a class="region-card region-card--hokuriku"
       href="https://www.japan.travel/vi/destinations/hokuriku-shinetsu/"
       target="_blank" rel="noopener noreferrer"
       aria-label="Tìm hiểu về vùng Chubu, Hokuriku Shinetsu">
        <img src="<?= assets('images/page-4/box-hokuriki.webp') ?>" alt="">
        <span class="region-card__label">
                    <strong>CHUBU</strong>
                    <small>(Hokuriku Shinetsu)</small>
                </span>
    </a>

    <a class="region-card region-card--tokai"
       href="https://www.japan.travel/vi/destinations/tokai/"
       target="_blank" rel="noopener noreferrer"
       aria-label="Tìm hiểu về vùng Chubu, Tokai">
        <img src="<?= assets('images/page-4/box-tokai.webp') ?>" alt="">
        <span class="region-card__label">
                    <strong>CHUBU</strong>
                    <small>(Tokai)</small>
                </span>
    </a>
</section>

<section class="facebook-section" aria-labelledby="facebook-title">
    <form class="facebook-form" action="/submit-post" method="post">
        <?= csrf_field() ?>
        <div class="facebook-panel">
            <h2 class="facebook-title" id="facebook-title">
                <span>BÀI ĐĂNG FACEBOOK</span>
            </h2>

            <div class="facebook-intro">
                <img src="<?= assets('images/page-5/line-decor.webp') ?>" alt="">
                <p>
                    Người tham gia đăng tải một bài viết trên trang Facebook cá nhân,<br>
                    chia sẻ cảm nghĩ về du lịch Nhật Bản thông qua việc trả lời câu hỏi<br>
                    yêu cầu của chương trình.
                </p>
                <p>
                    Sau đó, người tham gia vui lòng sao chép đường dẫn (link)<br>
                    của bài viết và dán vào ô bên dưới.
                </p>
                <img src="<?= assets('images/page-5/line-decor.webp') ?>" alt="">
            </div>

            <section class="facebook-question" aria-labelledby="facebook-question-title">
                <h3 class="facebook-tag facebook-tag--question" id="facebook-question-title">
                    <span>CÂU HỎI</span>
                </h3>
                <p>
                    Dựa trên 2 câu hỏi mở bạn đã chọn ở trang trước (về sở thích và phong cách<br>
                    du lịch của bạn), hãy chọn một điểm đến tại Chubu hoặc Kyushu và chia sẻ lý do bạn<br>
                    muốn đến đó, bạn muốn đi cùng ai và trải nghiệm những gì.
                </p>

                <div class="facebook-hashtag-row">
                    <strong>Hashtags:</strong>
                    <div class="facebook-hashtag-box">
                        <span id="facebook-hashtags">#visitJP_Chubu #visitJP_Kyushu #nhatban_vedepvotan</span>
                        <button class="facebook-copy-button" type="button"
                                data-copy-target="facebook-hashtags"
                                aria-label="Sao chép hashtag">
                            <img src="<?= assets('images/page-5/icon-copy.webp') ?>" alt="">
                        </button>
                    </div>
                </div>
            </section>

            <section class="facebook-requirements" aria-labelledby="facebook-requirements-title">
                <h3 class="facebook-tag facebook-tag--requirements" id="facebook-requirements-title">
                    <span>YÊU CẦU</span>
                </h3>
                <ul>
                    <li><strong>Like và follow</strong> Fanpage JNTO <a href="https://www.facebook.com/share/1DDuphAmqn/?mibextid=wwXIfr" target="_blank" rel="noopener noreferrer">Cảm nhận Nhật Bản</a>.</li>
                    <li><strong>Chọn một vùng</strong> (Chubu hoặc Kyushu) để trả lời.</li>
                    <li>Người tham gia được khuyến khích tham khảo <strong>nội dung trên trang web</strong> khi chuẩn bị câu trả lời.</li>
                    <li>Người tham gia cần <strong>đính kèm các thẻ hashtag</strong> được chỉ định khi đăng bài lên Facebook.</li>
                    <li>Bài dự thi phải hiển thị ở <strong>chế độ Công khai</strong> đến khi chương trình kết thúc. Tài khoản Facebook bật Profile Lock sẽ không đủ điều kiện tham gia.</li>
                </ul>
            </section>
        </div>

        <label class="facebook-link-field">
            <span class="visually-hidden">Link bài đăng Facebook</span>
            <input
                type="url"
                name="facebook_url"
                value="<?= e(old('facebook_url') ?: ($existingPost['facebook_url'] ?? '')) ?>"
                placeholder="Thêm link bài đăng Facebook tại đây"
                aria-label="Link bài đăng Facebook" required>
        </label>

        <div class="">
            <button class="facebook-submit-button" type="submit">
                <span>NỘP BÀI</span>
            </button>
        </div>
    </form>
</section>

<?php push_script(asset_with_version('js/user/page5.js')); ?>
