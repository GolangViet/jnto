const AppMain = function() {
    const modal = document.querySelector('.modal');
    const closeBtn = document.querySelector('.modal-close');

    function initial() {
        setupModals();
        setupButtons();
    }

    function setupModals() {
        if (!modal) {
            return;
        }

        _openModal();
        _closeModal();
    }

    function setupButtons() {
        // Click vào nút X
        if (closeBtn) {
            closeBtn.addEventListener('click', _closeModal);
        }

        if (modal) {
            // Click ra ngoài vùng ảnh (nền tối) cũng đóng modal
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    _closeModal();
                }
            });

            // Nhấn phím Esc để đóng modal
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && modal.classList.contains('modal-open')) {
                    _closeModal();
                }
            });
        }

        const openBtn = document.querySelector('.btn-cta_link');
        if (openBtn) {
            openBtn.addEventListener('click', _openModal);
        }
    }

    // Mở modal
    function _openModal() {
        modal.classList.add('modal-open');
        document.body.classList.add('modal-open-body');
    }

    // Đóng modal
    function _closeModal() {
        modal.classList.remove('modal-open');
        document.body.classList.remove('modal-open-body');
    }

   return {
        init: function() {
            initial();
        },
    }
}

document.addEventListener('DOMContentLoaded', () => {
    AppMain().init();
});
