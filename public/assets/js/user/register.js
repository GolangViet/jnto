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
            if (scrollArea) scrollArea.scrollTop = 0;
            if (closeButton) closeButton.focus();
        };

        const closeModal = () => {
            modal.classList.remove("modal-open");
            document.body.classList.remove("modal-open-body");
            trigger.focus();
        };

        trigger.addEventListener("click", openModal);
        if (closeButton) closeButton.addEventListener("click", closeModal);

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
    const form = document.querySelector("#register-form") || document.querySelector("[data-register-demo]");
    if (form) {
        const error = form.querySelector("#reg-error") || form.querySelector(".login-error");
        const closeBtn = form.querySelector("#reg-error-close") || form.querySelector(".login-error-close");
        const inputs = Array.from(form.querySelectorAll("input[required]"));
        const passInput = form.querySelector("#password-input");
        const passEyeBtn = form.querySelector("#pass-eye-btn");
        const agree1 = form.querySelector('input[name="agree1"]');
        const agree2 = form.querySelector('input[name="agree2"]');
        const agreeErrorBox = document.querySelector("#agree-error-message");

        const hideError = () => {
            if (error) error.hidden = true;
            inputs.forEach((input) => input.removeAttribute("aria-invalid"));
        };

        const showError = (message) => {
            if (error) {
                error.hidden = false;
                const errorLine = error.querySelector(".reg-error-line") || error.querySelector(".login-error-line");
                if (errorLine && message) {
                    errorLine.textContent = message;
                }
            }
            inputs.forEach((input) => input.setAttribute("aria-invalid", "true"));
        };

        if (closeBtn) {
            closeBtn.addEventListener("click", (e) => {
                e.preventDefault();
                hideError();
            });
        }

        if (passEyeBtn && passInput) {
            passEyeBtn.addEventListener("click", (e) => {
                e.preventDefault();
                const isPassword = passInput.type === "password";
                passInput.type = isPassword ? "text" : "password";
                passEyeBtn.setAttribute("aria-pressed", isPassword ? "true" : "false");
                passEyeBtn.setAttribute("aria-label", isPassword ? "Ẩn mật khẩu" : "Hiện mật khẩu");
                passEyeBtn.classList.toggle("is-active", isPassword);
            });
        }

        inputs.forEach((input) => input.addEventListener("input", hideError));

        if (agree1) {
            agree1.addEventListener("change", () => {
                if (agree1.checked && (!agree2 || agree2.checked)) {
                    if (agreeErrorBox) agreeErrorBox.style.display = "none";
                }
            });
        }
        if (agree2) {
            agree2.addEventListener("change", () => {
                if (agree2.checked && (!agree1 || agree1.checked)) {
                    if (agreeErrorBox) agreeErrorBox.style.display = "none";
                }
            });
        }

        form.addEventListener("submit", (event) => {
            event.preventDefault();

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            if (agree1 && !agree1.checked) {
                if (agreeErrorBox) {
                    agreeErrorBox.style.display = "flex";
                }
                return;
            } else {
                if (agreeErrorBox) {
                    agreeErrorBox.style.display = "none";
                }
            }

            const usernameInput = form.querySelector("#username");
            const tokenInput = form.querySelector('input[name="_token"]');
            const metaToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");
            const csrfToken = metaToken || (tokenInput ? tokenInput.value : "");

            const payload = {
                _token: csrfToken,
                username: usernameInput ? usernameInput.value : "",
                password: passInput ? passInput.value : ""
            };

            const submitBtn = form.querySelector(".btn-register");
            if (submitBtn) submitBtn.disabled = true;

            fetch(form.action || `${window.APP_URL}/register`, {
                method: "POST",
                body: JSON.stringify(payload),
                headers: {
                    "Accept": "application/json",
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    "X-Requested-With": "XMLHttpRequest"
                },
            })
            .then(async (response) => {
                let data;
                const contentType = response.headers.get("content-type") ?? "";
                if (contentType.includes("application/json")) {
                    data = await response.json();
                } else {
                    const text = await response.text();
                    throw new Error(text || "Đăng ký thất bại. Vui lòng thử lại.");
                }

                if (!response.ok) {
                    let msg = data?.message ?? "Đăng ký thất bại. Vui lòng thử lại.";
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
                if (err && err.message) {
                    showError(err.message);
                }
            })
            .finally(() => {
                if (submitBtn) submitBtn.disabled = false;
            });
        });
    }
})();
