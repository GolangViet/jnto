/**
 * JNTO Application JavaScript
 */

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

/**
 * Toggle mobile admin navigation collapse.
 */
function toggleAdminNav() {
    const collapse = document.getElementById('admin-nav-collapse');
    const toggleBtn = document.getElementById('admin-nav-toggle');
    if (!collapse || !toggleBtn) return;

    const isOpen = collapse.classList.toggle('is-open');
    toggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

    const iconMenu = toggleBtn.querySelector('.icon-menu');
    const iconClose = toggleBtn.querySelector('.icon-close');
    if (iconMenu && iconClose) {
        iconMenu.style.display = isOpen ? 'none' : 'block';
        iconClose.style.display = isOpen ? 'block' : 'none';
    }
}

// Global initialization when DOM loads
document.addEventListener('DOMContentLoaded', () => {
    // Close dropdowns and mobile nav when clicking outside
    document.addEventListener('click', (e) => {
        document.querySelectorAll('.dropdown-menu').forEach(el => {
            el.style.display = 'none';
        });

        const navbar = document.querySelector('.admin-navbar');
        const collapse = document.getElementById('admin-nav-collapse');
        const toggleBtn = document.getElementById('admin-nav-toggle');
        if (navbar && collapse && toggleBtn && collapse.classList.contains('is-open')) {
            if (!navbar.contains(e.target)) {
                collapse.classList.remove('is-open');
                toggleBtn.setAttribute('aria-expanded', 'false');
                const iconMenu = toggleBtn.querySelector('.icon-menu');
                const iconClose = toggleBtn.querySelector('.icon-close');
                if (iconMenu && iconClose) {
                    iconMenu.style.display = 'block';
                    iconClose.style.display = 'none';
                }
            }
        }
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
