document.addEventListener("click", async (event) => {
    const button = event.target.closest("[data-copy-target]");

    if (!button) {
        return;
    }

    const target = document.getElementById(button.dataset.copyTarget);

    if (!target) {
        return;
    }

    try {
        await navigator.clipboard.writeText(target.textContent.trim());
        button.classList.add("is-copied");
        button.setAttribute("aria-label", "Đã sao chép hashtag");

        window.setTimeout(() => {
            button.classList.remove("is-copied");
            button.setAttribute("aria-label", "Sao chép hashtag");
        }, 1600);
    } catch {
        const range = document.createRange();
        const selection = window.getSelection();

        range.selectNodeContents(target);
        selection.removeAllRanges();
        selection.addRange(range);

        const copied = document.execCommand("copy");

        if (copied) {
            button.classList.add("is-copied");
            button.setAttribute("aria-label", "Đã sao chép hashtag");
            selection.removeAllRanges();

            window.setTimeout(() => {
                button.classList.remove("is-copied");
                button.setAttribute("aria-label", "Sao chép hashtag");
            }, 1600);
        }
    }
});
