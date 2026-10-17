/**
 * JNTO Application JavaScript
 */
const AppMain = (function () {
    const modal = document.querySelector('.modal');
    const closeBtn = document.querySelector('.modal-close');
    const openBtn = document.querySelector('.btn-cta_link');

    // Mở modal
    function openModal() {
        if (!modal) return;
        modal.classList.add('modal-open');
        document.body.classList.add('modal-open-body');
    }

    // Đóng modal
    function closeModal() {
        if (!modal) return;
        modal.classList.remove('modal-open');
        document.body.classList.remove('modal-open-body');
    }

    // Click vào nút X
    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }

    // Click ra ngoài vùng ảnh (nền tối) cũng đóng modal
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal();
            }
        });
    }

    // Nhấn phím Esc để đóng modal
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal && modal.classList.contains('modal-open')) {
            closeModal();
        }
    });

    if (openBtn) {
        openBtn.addEventListener('click', openModal);
    }

    return {
        init: function () {
        },
    };
})();

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
    AppMain.init();

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

    // Dynamic routing for 'Tham gia ngay' (.btn-join-top) based on survey status
    const joinBtns = document.querySelectorAll('.btn-join-top');
    joinBtns.forEach((btn) => {
        let isSyncing = false;

        const updateHref = () => {
            const isLoggedIn = !!window.IS_LOGGED_IN;
            const surveyStatus = localStorage.getItem('jnto_survey_status');

            if (!isLoggedIn) {
                if (surveyStatus === 'done') {
                    btn.href = `${window.APP_URL}/register?from=survey`;
                } else {
                    btn.href = `${window.APP_URL}/take-survey`;
                }
            } else {
                const serverStatus = window.CURRENT_USER_SURVEY_STATUS;
                if (serverStatus === 'done' || surveyStatus === 'done') {
                    btn.href = `${window.APP_URL}/take-questions`;
                } else {
                    btn.href = `${window.APP_URL}/take-survey`;
                }
            }
        };

        updateHref();

        btn.addEventListener('click', async (e) => {
            const isLoggedIn = !!window.IS_LOGGED_IN;
            const serverStatus = window.CURRENT_USER_SURVEY_STATUS;
            const surveyStatus = localStorage.getItem('jnto_survey_status');

            // If user is logged in, survey marked done locally, but not yet stored in DB
            if (isLoggedIn && serverStatus !== 'done' && surveyStatus === 'done') {
                e.preventDefault();
                if (isSyncing) return;

                let data = null;
                try {
                    const raw = localStorage.getItem('jnto_survey_data');
                    data = raw ? JSON.parse(raw) : null;
                } catch (err) {}

                if (data && data.answers && Object.keys(data.answers).length > 0) {
                    isSyncing = true;
                    btn.style.pointerEvents = 'none';
                    btn.style.opacity = '0.7';

                    try {
                        const response = await fetch(`${window.APP_URL}/api/survey/sync`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(data)
                        });

                        const res = await response.json();
                        if (response.ok && res.success) {
                            window.CURRENT_USER_SURVEY_STATUS = 'done';
                            try {
                                localStorage.setItem('jnto_survey_status', 'done');
                                data.status = 'done';
                                data.pending_sync = false;
                                localStorage.setItem('jnto_survey_data', JSON.stringify(data));
                            } catch (err) {}

                            window.location.href = res.redirect || `${window.APP_URL}/take-questions`;
                            return;
                        } else if (res && res.already_completed) {
                            window.CURRENT_USER_SURVEY_STATUS = 'done';
                            window.location.href = res.redirect || `${window.APP_URL}/take-questions`;
                            return;
                        }
                    } catch (err) {
                        console.error('Error syncing survey on join click:', err);
                    } finally {
                        isSyncing = false;
                        btn.style.pointerEvents = '';
                        btn.style.opacity = '';
                    }
                }

                // If no answers or sync failed, fallback to normal take-survey page
                window.location.href = `${window.APP_URL}/take-survey`;
                return;
            }

            updateHref();
        });
    });
});
