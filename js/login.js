(() => {
    const form = document.querySelector("[data-login-demo]");

    if (!form) return;

    const error = form.querySelector(".login-error");
    const closeBtn = form.querySelector(".login-error-close");
    const inputs = Array.from(form.querySelectorAll("input[required]"));
    const passInput = form.querySelector("#password-input");
    const passEyeBtn = form.querySelector("#pass-eye-btn");

    const hideError = () => {
        if (error) error.hidden = true;
        inputs.forEach((input) => input.removeAttribute("aria-invalid"));
    };

    const showError = () => {
        if (error) error.hidden = false;
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

    form.addEventListener("submit", (event) => {
        event.preventDefault();

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // Demo front-end. Khi nối API, gọi showError() cho phản hồi đăng nhập thất bại.
        showError();
    });
})();

