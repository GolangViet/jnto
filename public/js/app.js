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
