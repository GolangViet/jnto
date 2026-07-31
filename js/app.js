<<<<<<< HEAD
/**
 * JNTO Application JavaScript
 */
=======
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
>>>>>>> main

/**
 * Toggle custom dropdown menus.
 * 
 * @param {Event} event
 * @param {string} id
 */
function toggleDropdown(event, id) {
    event.stopPropagation();
    
    // Close any other open dropdowns
    document.querySelectorAll('.dropdown-menu').forEach(el => {
        if (el.id !== 'dropdown-' + id) {
            el.style.display = 'none';
        }
    });
    
    const dropdown = document.getElementById('dropdown-' + id);
    if (dropdown) {
        if (dropdown.style.display === 'none' || dropdown.style.display === '') {
            dropdown.style.display = 'block';
        } else {
            dropdown.style.display = 'none';
        }
    }
}

// Global initialization when DOM loads
document.addEventListener('DOMContentLoaded', () => {
    // Close dropdowns when clicking outside
    document.addEventListener('click', () => {
        document.querySelectorAll('.dropdown-menu').forEach(el => {
            el.style.display = 'none';
        });
    });

    // Auto-dismiss alert notification banners after 4 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-8px)';
            setTimeout(() => alert.remove(), 600);
        }, 4000);
    });
});