<?php push_style(assets('css/user/page4.css')); ?>

<section class="region-map" aria-labelledby="region-map-title">
    <h2 class="visually-hidden" id="region-map-title">Khám phá các vùng của Nhật Bản</h2>

    <div class="region-map__guide">
        <img class="region-map__search-line" src="<?= assets('images/page-4/line-search.webp') ?>" alt="">
        <p>Vui lòng truy cập vào từng vùng để tìm hiểu thông tin<br>và trả lời các câu hỏi tương ứng</p>
        <img class="region-map__line" src="<?= assets('images/page-4/line.webp') ?>" alt="">
    </div>

    <a class="region-card region-card--kyushu"
       href="https://www.japan.travel/en/destinations/kyushu/"
       target="_blank" rel="noopener noreferrer"
       aria-label="Tìm hiểu về vùng Kyushu">
        <img src="<?= assets('images/page-4/box-kyushu.webp') ?>" alt="">
        <span class="region-card__label">KYUSHU</span>
    </a>

    <a class="region-card region-card--hokuriku"
       href="https://www.japan.travel/en/destinations/hokuriku-shinetsu/"
       target="_blank" rel="noopener noreferrer"
       aria-label="Tìm hiểu về vùng Chubu, Hokuriku Shinetsu">
        <img src="<?= assets('images/page-4/box-hokuriki.webp') ?>" alt="">
        <span class="region-card__label">
                    <strong>CHUBU</strong>
                    <small>(Hokuriku Shinetsu)</small>
                </span>
    </a>

    <a class="region-card region-card--tokai"
       href="https://www.japan.travel/en/destinations/tokai/"
       target="_blank" rel="noopener noreferrer"
       aria-label="Tìm hiểu về vùng Chubu, Tokai">
        <img src="<?= assets('images/page-4/box-tokai.webp') ?>" alt="">
        <span class="region-card__label">
                    <strong>CHUBU</strong>
                    <small>(Tokai)</small>
                </span>
    </a>
</section>

<section class="knowledge-section" aria-labelledby="knowledge-title">
    <form class="knowledge-form" action="#" method="post">
        <h2 class="knowledge-title" id="knowledge-title"><span>CÂU HỎI KIẾN THỨC</span></h2>

        <section class="knowledge-part knowledge-part--quiz" aria-labelledby="quiz-title">
            <h3 class="knowledge-part__title knowledge-part__title--quiz" id="quiz-title"><span>TRẮC NGHIỆM</span></h3>

            <div class="knowledge-quiz-list">
                <fieldset class="knowledge-question">
                    <legend>
                        <span class="knowledge-question__number">1.</span>
                        <span class="knowledge-question__copy">
                                    Tỉnh Gifu (Vùng Chubu) nổi tiếng với trải nghiệm độc đáo vào đêm mùa hè nào diễn ra trên dòng sông Nagara?
                                </span>
                    </legend>
                    <label class="knowledge-choice">
                        <input type="radio" name="knowledge_1" value="fireworks" required>
                        <span>Múa rồng lửa trên thuyền gỗ vượt thác nước khoáng.</span>
                    </label>
                    <label class="knowledge-choice">
                        <input type="radio" name="knowledge_1" value="lanterns">
                        <span>Thả đèn hoa đăng bằng đá ngầm phát sáng.</span>
                    </label>
                    <label class="knowledge-choice">
                        <input type="radio" name="knowledge_1" value="ukai">
                        <span>Nghệ thuật đánh cá bằng chim cốc truyền thống.</span>
                    </label>
                </fieldset>

                <fieldset class="knowledge-question">
                    <legend>
                        <span class="knowledge-question__number">2.</span>
                        <span class="knowledge-question__copy">
                                    Nếu bạn ghé thăm tỉnh Fukuoka (vùng Kyushu) vào mùa hè, lễ hội truyền thống nổi tiếng nào sau đây mà bạn không nên bỏ lỡ?
                                </span>
                    </legend>
                    <label class="knowledge-choice">
                        <input type="radio" name="knowledge_2" value="hakata-gion" required>
                        <span>Lễ hội Hakata Gion Yamakasa</span>
                    </label>
                    <label class="knowledge-choice">
                        <input type="radio" name="knowledge_2" value="nagasaki-lantern">
                        <span>Lễ hội đèn lồng Nagasaki</span>
                    </label>
                    <label class="knowledge-choice">
                        <input type="radio" name="knowledge_2" value="kumamoto-castle">
                        <span>Lễ hội Thành Kumamoto</span>
                    </label>
                </fieldset>

                <fieldset class="knowledge-question">
                    <legend>
                        <span class="knowledge-question__number">3.</span>
                        <span class="knowledge-question__copy">
                                    Địa hình đặc trưng nào của vùng Kyushu tạo nên cảnh quan độc nhất vô nhị?
                                </span>
                    </legend>
                    <label class="knowledge-choice">
                        <input type="radio" name="knowledge_3" value="ice-caves" required>
                        <span>Hệ thống hang động băng vĩnh cửu</span>
                    </label>
                    <label class="knowledge-choice">
                        <input type="radio" name="knowledge_3" value="volcanoes">
                        <span>Địa hình núi lửa sôi sục phun khói</span>
                    </label>
                    <label class="knowledge-choice">
                        <input type="radio" name="knowledge_3" value="desert">
                        <span>Những cánh đồng sa mạc</span>
                    </label>
                </fieldset>
            </div>
        </section>

        <section class="knowledge-part knowledge-part--essay" aria-labelledby="essay-title">
            <h3 class="knowledge-part__title knowledge-part__title--essay" id="essay-title"><span>TỰ LUẬN</span></h3>

            <div class="knowledge-essay-list">
                <label class="knowledge-essay">
                            <span class="knowledge-essay__heading">
                                <strong>1.</strong>
                                <span>Theo gợi ý trên website JNTO, đâu là khu vườn lý tưởng nhất để ngắm phong cảnh tán lá đổi màu đẹp như tranh vẽ tại tỉnh Ishikawa (vùng Chubu)?</span>
                            </span>
                    <textarea name="knowledge_essay_1" aria-label="Câu trả lời tự luận số 1"></textarea>
                </label>

                <label class="knowledge-essay">
                            <span class="knowledge-essay__heading">
                                <strong>2.</strong>
                                <span>Theo gợi ý trên website JNTO, ngôi chùa nào là địa điểm lý tưởng để ngắm lá đỏ mùa thu tại tỉnh Saga (vùng Kyushu)?</span>
                            </span>
                    <textarea name="knowledge_essay_2" aria-label="Câu trả lời tự luận số 2"></textarea>
                </label>
            </div>
        </section>
    </form>

    <img class="knowledge-arrow" src="<?= assets('images/page-4/section-3/mui-ten.webp') ?>" alt="" />
</section>

<section class="open-question-section" aria-labelledby="open-question-title">
    <form class="open-question-form" id="open-question-form" action="#" method="post">
        <h2 class="open-question-title" id="open-question-title">
            <span>CÂU HỎI MỞ</span>
        </h2>

        <div class="open-question-list">
            <div class="open-question" role="group" aria-labelledby="open-question-1">
                <p class="open-question__heading" id="open-question-1">
                    <span class="open-question__number">1.</span>
                    <span class="open-question__copy">Bạn coi trọng điều gì nhất khi đi du lịch Nhật Bản?</span>
                </p>

                <label class="open-question-choice">
                    <input type="radio" name="open_question_1" value="nature" required>
                    <span>Thiên nhiên và phong cảnh đẹp</span>
                </label>
                <label class="open-question-choice">
                    <input type="radio" name="open_question_1" value="food">
                    <span>Ẩm thực và ăn uống</span>
                </label>
                <label class="open-question-choice">
                    <input type="radio" name="open_question_1" value="onsen">
                    <span>Tắm suối nước nóng và thư giãn</span>
                </label>
                <label class="open-question-choice">
                    <input type="radio" name="open_question_1" value="activities">
                    <span>Các hoạt động và trải nghiệm</span>
                </label>
            </div>

            <div class="open-question" role="group" aria-labelledby="open-question-2">
                <p class="open-question__heading" id="open-question-2">
                    <span class="open-question__number">2.</span>
                    <span class="open-question__copy">Phong cách du lịch nào phù hợp nhất với sở thích của bạn?</span>
                </p>

                <label class="open-question-choice">
                    <input type="radio" name="open_question_2" value="slow-travel" required>
                    <span>Nghỉ ngơi, thư giãn nhịp độ chậm</span>
                </label>
                <label class="open-question-choice">
                    <input type="radio" name="open_question_2" value="check-in">
                    <span>Đi check-in càng nhiều điểm nổi tiếng càng tốt</span>
                </label>
                <label class="open-question-choice">
                    <input type="radio" name="open_question_2" value="photography">
                    <span>Chụp thật nhiều ảnh</span>
                </label>
                <label class="open-question-choice">
                    <input type="radio" name="open_question_2" value="unique-experiences">
                    <span>Tận hưởng những trải nghiệm độc đáo, đáng nhớ</span>
                </label>
            </div>
        </div>
    </form>

</section>

<section class="page4-next-section" aria-label="Tiếp tục">
    <button class="page4-next-button" type="submit" form="open-question-form">
        <span>TIẾP THEO</span>
    </button>
    <a href="/submit-post" class="page4-next-button" type="submit" >Trang tiếp theo</a>
</section>
