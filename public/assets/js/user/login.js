(() => {
    const form = document.querySelector('[data-login-demo]');
    if (!form) return;

    const error = form.querySelector('.login-error');
    const closeBtn = form.querySelector('.login-error-close');
    const inputs = Array.from(form.querySelectorAll('input[required]'));
    const passInput = form.querySelector('#password-input');
    const passEyeBtn = form.querySelector('#pass-eye-btn');
    const surveyNotice = document.querySelector('#survey-save-notice');

    if (surveyNotice) {
        const params = new URLSearchParams(window.location.search);
        let hasPending = false;
        try {
            const raw = localStorage.getItem('jnto_survey_data');
            if (raw) {
                hasPending = !!JSON.parse(raw)?.pending_sync;
            }
        } catch (e) {}
        if (params.get('from') === 'survey' || hasPending) {
            surveyNotice.style.display = 'flex';
        }
    }

    const hideError = () => {
        if (error) error.hidden = true;
        inputs.forEach((input) => input.removeAttribute('aria-invalid'));
    };

    const showError = (message) => {
        if (error) {
            error.hidden = false;
            const errorLine = error.querySelector('.login-error-line');
            if (errorLine && message) {
                errorLine.textContent = message;
            }
        }
        inputs.forEach((input) => input.setAttribute('aria-invalid', 'true'));
    };

    if (closeBtn) {
        closeBtn.addEventListener('click', (e) => {
            e.preventDefault();
            hideError();
        });
    }

    if (passEyeBtn && passInput) {
        passEyeBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const isPassword = passInput.type === 'password';
            passInput.type = isPassword ? 'text' : 'password';
            passEyeBtn.setAttribute('aria-pressed', isPassword ? 'true' : 'false');
            passEyeBtn.setAttribute('aria-label', isPassword ? 'Ẩn mật khẩu' : 'Hiện mật khẩu');
            passEyeBtn.classList.toggle('is-active', isPassword);
        });
    }

    inputs.forEach((input) => input.addEventListener('input', hideError));

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const usernameInput = form.querySelector('#username');
        const passwordInput = form.querySelector('#password-input');
        const tokenInput = form.querySelector('input[name="_token"]');
        const metaToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const csrfToken = metaToken || (tokenInput ? tokenInput.value : '');

        const urlParams = new URLSearchParams(window.location.search);
        const payload = {
            _token: csrfToken,
            username: usernameInput ? usernameInput.value : '',
            password: passwordInput ? passwordInput.value : '',
            from: urlParams.get('from') || '',
            guest_token: urlParams.get('guest_token') || ''
        };

        const submitBtn = form.querySelector('.btn-login');
        if (submitBtn) submitBtn.disabled = true;

        fetch(form.action || `${window.APP_URL}/login`, {
            method: 'POST',
            body: JSON.stringify(payload),
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
        })
        .then(async (response) => {
            let data;
            const contentType = response.headers.get('content-type') ?? '';
            if (contentType.includes('application/json')) {
                data = await response.json();
            } else {
                const text = await response.text();
                throw new Error(text || 'Đăng nhập thất bại. Vui lòng thử lại.');
            }

            if (!response.ok) {
                if (response.status === 419) {
                    showError('Phiên làm việc đã hết hạn. Trang sẽ tự động tải lại...');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                    return;
                }

                let msg = data?.message ?? 'Đăng nhập thất bại. Vui lòng thử lại.';
                if (data.errors) {
                    const firstField = Object.keys(data.errors)[0];
                    if (firstField && data.errors[firstField].length > 0) {
                        msg = data.errors[firstField][0];
                    }
                }

                throw new Error(msg);
            }

            if (data.redirect) {
                let redirectUrl = data.redirect;
                try {
                    const raw = localStorage.getItem('jnto_survey_data');
                    if (raw && JSON.parse(raw)?.pending_sync) {
                        redirectUrl = `${window.APP_URL}/take-survey`;
                    }
                } catch (e) {}
                window.location.href = redirectUrl;
            } else {
                window.location.reload();
            }
        })
        .catch((err) => {
            if (err && err.message) {
                showError(err.message);
            }
        })
        .finally(() => {
            if (submitBtn) submitBtn.disabled = false;
        });
    });
})();
