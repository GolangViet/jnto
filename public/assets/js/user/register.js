(() => {
    // --- Terms Modal Behavior ---
    const modal = document.querySelector("#terms-modal");
    const trigger = document.querySelector(".terms-modal-trigger");

    if (modal && trigger) {
        const closeButton = modal.querySelector(".terms-modal-close");
        const scrollArea = modal.querySelector(".terms-modal-scroll");

        const openModal = (event) => {
            event.preventDefault();
            modal.classList.add("modal-open");
            document.body.classList.add("modal-open-body");
            scrollArea.scrollTop = 0;
            closeButton.focus();
        };

        const closeModal = () => {
            modal.classList.remove("modal-open");
            document.body.classList.remove("modal-open-body");
            trigger.focus();
        };

        trigger.addEventListener("click", openModal);
        closeButton.addEventListener("click", closeModal);

        modal.addEventListener("click", (event) => {
            if (event.target === modal) closeModal();
        });

        document.addEventListener("keydown", (event) => {
            if (event.key === "Escape" && modal.classList.contains("modal-open")) {
                closeModal();
            }
        });
    }

    // --- Register Form Submission & Validation ---
    const form = document.querySelector("#register-form");
    if (form) {
        const error = form.querySelector('.login-error');
        const inputs = Array.from(form.querySelectorAll('input[required]'));
        const agree1 = form.querySelector('input[name="agree1"]');
        const agree2 = form.querySelector('input[name="agree2"]');
        const agreeErrorBox = document.querySelector('#agree-error-message');

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

        inputs.forEach((input) => input.addEventListener('input', hideError));

        if (agree1) {
            agree1.addEventListener('change', () => {
                if (agree1.checked && agree2 && agree2.checked) {
                    if (agreeErrorBox) agreeErrorBox.style.display = 'none';
                }
            });
        }
        if (agree2) {
            agree2.addEventListener('change', () => {
                if (agree2.checked && agree1 && agree1.checked) {
                    if (agreeErrorBox) agreeErrorBox.style.display = 'none';
                }
            });
        }

        form.addEventListener('submit', (event) => {
            event.preventDefault();

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            if (!agree1 || !agree2 || !agree1.checked || !agree2.checked) {
                if (agreeErrorBox) {
                    agreeErrorBox.style.display = 'flex';
                }
                return;
            } else {
                if (agreeErrorBox) {
                    agreeErrorBox.style.display = 'none';
                }
            }

            const usernameInput = form.querySelector('#username');
            const passwordInput = form.querySelector('#password-input');
            const tokenInput = form.querySelector('input[name="_token"]');

            const payload = {
                _token: tokenInput ? tokenInput.value : '',
                username: usernameInput ? usernameInput.value : '',
                password: passwordInput ? passwordInput.value : ''
            };

            const submitBtn = form.querySelector(".btn-register");
            if (submitBtn) submitBtn.disabled = true;

            fetch(form.action || '/register', {
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
                    throw new Error(text || 'Đăng ký thất bại. Vui lòng thử lại.');
                }

                if (!response.ok) {
                    let msg = data?.message ?? 'Đăng ký thất bại. Vui lòng thử lại.';
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
    }
})();
