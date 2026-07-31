document.addEventListener("click", (event) => {
    const pendingSlide = event.target.closest("[data-pending-link]");

    if (!pendingSlide) {
        return;
    }

    event.preventDefault();

    document.querySelectorAll("[data-pending-link].is-selected").forEach((slide) => {
        slide.classList.remove("is-selected");
        slide.removeAttribute("aria-current");
    });

    pendingSlide.classList.add("is-selected");
    pendingSlide.setAttribute("aria-current", "true");
});
