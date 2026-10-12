(() => {
    // 1. Terms & Conditions Modal
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

    // 2. Register Form Interactions
    const form = document.querySelector("[data-register-demo]");
    if (!form) return;

    const error = form.querySelector("#reg-error");
    const closeBtn = form.querySelector("#reg-error-close");
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

        // Demo front-end: hiển thị thông báo tài khoản đã tồn tại
        showError();
    });
})();
