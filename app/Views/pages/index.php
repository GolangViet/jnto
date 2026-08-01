<section class="section2">
    <picture>
        <source media="(max-width: 768px)" srcset="<?= assets('images/bg/bg-m.webp') ?>">
        <img class="bg2" src="<?= assets('images/bg/bg-cam.webp') ?>" alt="bg2">
    </picture>

    <div class="container section2_wrapper">
        <div class="section2_title">
            <h2>Cùng Cơ quan Xúc tiến Du lịch Nhật Bản (JNTO) tham gia <br>
                thử thách <strong>“NHẬT BẢN: VẺ ĐẸP VÔ TẬN, ĐI ĐỂ TRỞ VỀ"</strong> <br>
                trên microsite để nhận vô số phần quà giá trị.
            </h2>
            <div class="sec2-line-top">
                <img src="<?= assets('images/bg/hoa.webp') ?>" alt="line top">
            </div>
            <div class="sec2-line-bottom">
                <img src="<?= assets('images/bg/hoa.webp') ?>" alt="line top">
            </div>
        </div>
        <div class="section2-box">
            <div class="section2-contents">
                <div class="sec2-time">
                    <div class="sec2-time_title"><span>THỜI GIAN</span></div>
                    <div class="sec2_info">
                        <h4>04/08 - 27/09/2026</h4>
                        <h5>Thời gian công bố kết quả:</h5>
                        <h4 class="mb-2">20/10/2026</h4>
                        <p>
                            *Danh sách trúng thưởng sẽ được<br>
                            công bố trên Fanpage JNTO
                        </p>
                    </div>
                </div>
                <div class="sec2-time">
                    <div class="sec2-time_title2"><span>ĐỐI TƯỢNG THAM GIA</span></div>
                    <ul class="sec2_info2">
                        <li><p>Là công dân Việt Nam, từ đủ 18 tuổi<br> trở lên, đang sinh sống trên lãnh thổ<br> Việt Nam</p></li>
                        <li><p>Bằng việc tham gia, người chơi xác nhận<br> đã đọc, hiểu rõ và đồng ý với các<br> quy định tại Thể lệ này.</p></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="btn-cta">
        <div class="btn-cta_link">
            <strong>CÁCH THỨC THAM GIA</strong>
            <span>Tìm hiểu thêm <u>tại đây</u></span>
        </div>
    </div>
</section>

<section class="section3">
    <div class="sec3-title">
        <h2>CƠ CẤU GIẢI THƯỞNG</h2>
        <h3>50 phần quà hấp dẫn</h3>
    </div>
    <div class="sec3-contents">
        <div class="sec3-awards">
            <img class="sec3-award-img" src="<?= assets('images/giai-nhat.webp') ?>" alt="giải thưởng 1">
            <img class="sec3-award-img" src="<?= assets('images/giai-hai-ba-bon.webp') ?>" alt="giải thưởng 234">
        </div>
        <div class="sec3-footer">
            <p>
                *Vé máy bay và vé Have Fun Pass được áp dụng với điểm đến là vùng Kyushu hoặc Chubu. Vé không được chuyển nhượng, trao đổi hoặc quy đổi thành tiền mặt dưới bất kỳ hình thức nào.
            </p>
            <p>
                *Voucher Traveloka áp dụng cho tất cả các dịch vụ trên Traveloka đối với điểm đến Nhật Bản và có thể sử dụng đồng thời với các ưu đãi khác.
            </p>
        </div>
    </div>
    <a class="btn-join-now" href="/take-survey" title="Tham gia ngay">
        <span>THAM GIA NGAY</span>
    </a>
    <div class="nui-lft">
        <img class="img" src="<?= assets('images/nui-left.png') ?>" alt="nui trai bg">
    </div>
    <div class="nui-rgt">
        <img class="img" src="<?= assets('images/nui-right.png') ?>" alt="nui phai bg">
    </div>
    <div class="la-lft">
        <img class="img" src="<?= assets('images/la-left.webp') ?>" alt="la left bg">
    </div>
    <div class="la-rgt">
        <img class="img" src="<?= assets('images/la-right.webp') ?>" alt="la right bg">
    </div>
</section>

<?php push_modal(view('components/modals/common-modal')); ?>
