(() => {
    const modal = document.querySelector("#terms-modal");
    const trigger = document.querySelector(".terms-modal-trigger");

    if (!modal || !trigger) return;

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
})();
