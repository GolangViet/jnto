const AppMain = (function(){
    const modal = document.querySelector('.modal');
    const closeBtn = document.querySelector('.modal-close');

    // Mở modal
    function openModal() {
        modal.classList.add('modal-open');
        document.body.classList.add('modal-open-body');
    }

    // Đóng modal
    function closeModal() {
        modal.classList.remove('modal-open');
        document.body.classList.remove('modal-open-body');
    }

    // Click vào nút X
    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }

    // Click ra ngoài vùng ảnh (nền tối) cũng đóng modal
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Nhấn phím Esc để đóng modal
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.classList.contains('modal-open')) {
            closeModal();
        }
    });

    const openBtn = document.querySelector('.btn-cta_link');
    if (openBtn) {
        openBtn.addEventListener('click', openModal);
    }


   return {
        init: function(){
        },
    }
})();

document.addEventListener("DOMContentLoaded", function (event) {
    AppMain.init();
});