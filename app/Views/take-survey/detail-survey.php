<?php push_style(assets('css/user/form.css')); ?>

<section class="survey-section" aria-labelledby="survey-title">
    <form class="survey-form" id="survey-form" action="#" method="post">
        <h1 class="survey-title" id="survey-title">CÂU HỎI KHẢO SÁT</h1>

        <div class="survey-panel survey-panel--intro is-active" data-survey-panel="intro">
            <fieldset class="survey-question">
                <legend>
                    <span class="question-number">1.</span>
                    <span class="question-copy">Tuổi</span>
                </legend>
                <label class="survey-choice">
                    <input type="radio" name="age" value="under-20" required>
                    <span>Dưới 20</span>
                </label>
                <label class="survey-choice">
                    <input type="radio" name="age" value="20-30">
                    <span>Từ 20 – 30</span>
                </label>
                <label class="survey-choice">
                    <input type="radio" name="age" value="30-40">
                    <span>Từ 30 – 40</span>
                </label>
                <label class="survey-choice">
                    <input type="radio" name="age" value="40-50">
                    <span>Từ 40 – 50</span>
                </label>
                <label class="survey-choice">
                    <input type="radio" name="age" value="50-60">
                    <span>Từ 50 – 60</span>
                </label>
                <label class="survey-choice">
                    <input type="radio" name="age" value="over-60">
                    <span>60 trở lên</span>
                </label>
            </fieldset>

            <fieldset class="survey-question">
                <legend>
                    <span class="question-number">2.</span>
                    <span class="question-copy">Khu vực</span>
                </legend>
                <label class="survey-choice">
                    <input type="radio" name="region" value="ha-noi" required>
                    <span>Hà Nội</span>
                </label>
                <label class="survey-choice">
                    <input type="radio" name="region" value="da-nang">
                    <span>Đà Nẵng</span>
                </label>
                <label class="survey-choice">
                    <input type="radio" name="region" value="ho-chi-minh">
                    <span>TP.HCM</span>
                </label>
                <label class="survey-choice survey-choice--other">
                    <input type="radio" name="region" value="other">
                    <span>Khác (ghi rõ):</span>
                    <input class="survey-other-input" type="text" name="region_other" aria-label="Khu vực khác">
                </label>
            </fieldset>

            <fieldset class="survey-question">
                <legend>
                    <span class="question-number">3.</span>
                    <span class="question-copy">
                                Kinh nghiệm du lịch Nhật Bản và kế hoạch tương lai
                                <em class="question-note--inline-mobile">(Không tính đi công tác/việc công)</em>
                            </span>
                </legend>
                <label class="survey-choice">
                    <input type="radio" name="travel_experience" value="visited-return" data-travel-status="visited" required>
                    <span>Đã từng đi và muốn đi lại</span>
                </label>
                <label class="survey-choice">
                    <input type="radio" name="travel_experience" value="visited-no-plan" data-travel-status="visited">
                    <span>Đã từng đi nhưng hiện chưa có kế hoạch quay lại</span>
                </label>
                <label class="survey-choice">
                    <input type="radio" name="travel_experience" value="not-visited-plan" data-travel-status="not-visited">
                    <span>Chưa từng đi nhưng có kế hoạch đi trong tương lai</span>
                </label>
                <label class="survey-choice">
                    <input type="radio" name="travel_experience" value="not-visited-no-plan" data-travel-status="not-visited">
                    <span>Chưa từng đi và chưa có kế hoạch</span>
                </label>
            </fieldset>

            <div class="survey-actions">
                <button class="survey-button" type="button" id="survey-next"><span>Tiếp theo</span></button>
                <a class="survey-button" type="button" href="/take-questions"><span>Trang kế tiếp</span></a>
            </div>
        </div>

        <div class="survey-panel survey-panel--visited" data-survey-panel="visited" hidden>
            <fieldset class="survey-question">
                <legend>
                    <span class="question-number">4.</span>
                    <span class="question-copy">
                                Các vùng của Nhật Bản bạn quan tâm:
                                <em>(Có thể chọn nhiều đáp án)</em>
                            </span>
                </legend>
                <div data-region-options></div>
            </fieldset>

            <fieldset class="survey-question">
                <legend>
                    <span class="question-number">5.</span>
                    <span class="question-copy">Bạn muốn đi du lịch Nhật Bản theo hình thức nào?</span>
                </legend>
                <div data-travel-style-options></div>
            </fieldset>

            <fieldset class="survey-question">
                <legend>
                    <span class="question-number">6.</span>
                    <span class="question-copy">
                                Khi tìm hiểu du lịch Nhật Bản, bạn thường lấy thông tin từ đâu?
                                <em>(Có thể chọn nhiều đáp án)</em>
                            </span>
                </legend>
                <div data-information-options></div>
            </fieldset>

            <div class="survey-actions">
                <button class="survey-button survey-button--finish" type="submit"><span>Nộp bài</span></button>
            </div>
        </div>

        <div class="survey-panel survey-panel--not-visited survey-panel--large" data-survey-panel="not-visited" hidden>
            <fieldset class="survey-question">
                <legend>
                    <span class="question-number">4.</span>
                    <span class="question-copy">
                                Các vùng bạn đã từng đến:
                                <em>(Có thể chọn nhiều đáp án)</em>
                            </span>
                </legend>
                <div data-region-options></div>
            </fieldset>

            <fieldset class="survey-question">
                <legend>
                    <span class="question-number">5.</span>
                    <span class="question-copy">
                                Các vùng bạn chưa từng đến nhưng muốn đi trong tương lai:
                                <em>(Có thể chọn nhiều đáp án)</em>
                            </span>
                </legend>
                <div data-region-options></div>
            </fieldset>

            <fieldset class="survey-question">
                <legend>
                    <span class="question-number">6.</span>
                    <span class="question-copy">Bạn muốn đi du lịch Nhật Bản theo hình thức nào?</span>
                </legend>
                <div data-travel-style-options></div>
            </fieldset>

            <fieldset class="survey-question">
                <legend>
                    <span class="question-number">7.</span>
                    <span class="question-copy">
                                Khi tìm hiểu du lịch Nhật Bản, bạn thường lấy thông tin từ đâu?
                                <em>(Có thể chọn nhiều đáp án)</em>
                            </span>
                </legend>
                <div data-information-options></div>
            </fieldset>

            <div class="survey-actions">
                <button class="survey-button survey-button--finish" type="submit"><span>Nộp bài</span></button>
            </div>
        </div>
    </form>
</section>

<template id="region-options-template">
    <label class="survey-choice"><input type="checkbox" value="hokkaido"><span>Hokkaido</span></label>
    <label class="survey-choice"><input type="checkbox" value="chubu"><span>Chubu (Nagoya, Takayama, núi Phú Sĩ...)</span></label>
    <label class="survey-choice"><input type="checkbox" value="kanto"><span>Kanto (Tokyo, Ibaraki, Chiba,...)</span></label>
    <label class="survey-choice"><input type="checkbox" value="chugoku"><span>Chugoku (Hiroshima, Okayama...)</span></label>
    <label class="survey-choice"><input type="checkbox" value="kyushu"><span>Kyushu (Fukuoka, Kumamoto, Beppu...)</span></label>
    <label class="survey-choice"><input type="checkbox" value="tohoku"><span>Tohoku (Sendai, Aomori...)</span></label>
    <label class="survey-choice"><input type="checkbox" value="kansai"><span>Kansai (Osaka, Kyoto, Wakayama,...)</span></label>
    <label class="survey-choice"><input type="checkbox" value="shikoku"><span>Shikoku (Takamatsu, Matsuyama...)</span></label>
    <label class="survey-choice"><input type="checkbox" value="okinawa"><span>Okinawa</span></label>
    <label class="survey-choice survey-choice--other">
        <input type="checkbox" value="other">
        <span>Khác:</span>
        <input class="survey-other-input" type="text" aria-label="Vùng khác">
    </label>
</template>

<template id="travel-style-options-template">
    <label class="survey-choice"><input type="radio" value="package-tour"><span>Mua tour trọn gói của công ty lữ hành</span></label>
    <label class="survey-choice"><input type="radio" value="custom-tour"><span>Đặt tour thiết kế riêng qua công ty lữ hành</span></label>
    <label class="survey-choice"><input type="radio" value="self-guided"><span>Du lịch tự túc, tự đặt vé máy bay, khách sạn và các dịch vụ qua OTA (Traveloka, Booking, Agoda,...)</span></label>
    <label class="survey-choice"><input type="radio" value="undecided"><span>Chưa quyết định</span></label>
</template>

<template id="information-options-template">
    <label class="survey-choice"><input type="checkbox" value="jnto-social"><span>Các kênh SNS (Cảm Nhận Nhật Bản và visitjapan_vn) và website của JNTO Việt Nam</span></label>
    <label class="survey-choice"><input type="checkbox" value="kol"><span>Các kênh SNS của những người có tầm ảnh hưởng (KOL)</span></label>
    <label class="survey-choice"><input type="checkbox" value="tiktok"><span>TikTok</span></label>
    <label class="survey-choice"><input type="checkbox" value="facebook"><span>Facebook</span></label>
    <label class="survey-choice"><input type="checkbox" value="youtube"><span>YouTube</span></label>
    <label class="survey-choice"><input type="checkbox" value="threads-instagram"><span>Threads/Instagram</span></label>
    <label class="survey-choice"><input type="checkbox" value="travel-company"><span>Tư vấn với công ty du lịch</span></label>
    <label class="survey-choice"><input type="checkbox" value="ota-blog"><span>Các bài blog du lịch trên các trang OTA (Traveloka, Agoda, Klook,...)</span></label>
    <label class="survey-choice"><input type="checkbox" value="print"><span>Sách du lịch, cẩm nang, tạp chí, tờ rơi,...</span></label>
    <label class="survey-choice"><input type="checkbox" value="word-of-mouth"><span>Truyền miệng (từ bạn bè, người quen)</span></label>
    <label class="survey-choice survey-choice--other">
        <input type="checkbox" value="other">
        <span>Khác:</span>
        <input class="survey-other-input" type="text" aria-label="Nguồn thông tin khác">
    </label>
</template>

<?php push_script(assets('js/user/form.js')); ?>
