(() => {
    const form = document.querySelector("[data-login-demo]");

    if (!form) return;

    const error = form.querySelector(".login-error");
    const inputs = Array.from(form.querySelectorAll("input[required]"));

    const hideError = () => {
        error.hidden = true;
        inputs.forEach((input) => input.removeAttribute("aria-invalid"));
    };

    const showError = () => {
        error.hidden = false;
        inputs.forEach((input) => input.setAttribute("aria-invalid", "true"));
    };

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
