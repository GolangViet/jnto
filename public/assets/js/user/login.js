(() => {
    const form = document.querySelector('[data-login-demo]');
    if (!form) return;

    const error = form.querySelector('.login-error');
    const inputs = Array.from(form.querySelectorAll('input[required]'));

    const hideError = () => {
        error.hidden = true;
        inputs.forEach((input) => input.removeAttribute('aria-invalid'));
    };

    const showError = (message) => {
        error.hidden = false;
        const errorLine = error.querySelector('.login-error-line');
        if (errorLine && message) {
            errorLine.textContent = message;
        }

        inputs.forEach((input) => input.setAttribute('aria-invalid', 'true'));
    };

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
        const payload = {
            _token: tokenInput ? tokenInput.value : '',
            username: usernameInput ? usernameInput.value : '',
            password: passwordInput ? passwordInput.value : ''
        };

        const submitBtn = form.querySelector(".btn-login");
        if (submitBtn) submitBtn.disabled = true;

        fetch(form.action || `${window.APP_URL}/login`, {
            method: 'POST',
            body: JSON.stringify(payload),
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
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
                window.location.href = data.redirect;
            } else {
                window.location.reload();
            }
        })
        .catch((err) => {
            showError(err.message);
        })
        .finally(() => {
            if (submitBtn) submitBtn.disabled = false;
        });
    });
})();
