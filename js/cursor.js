(function () {
    const finePointer = window.matchMedia("(hover: hover) and (pointer: fine)");
    const root = document.documentElement;
    const cursor = document.createElement("div");
    const cursorPreloader = new Image();
    const moveEventName = "onpointerrawupdate" in window ? "pointerrawupdate" : "pointermove";
    const hotspotX = 2;
    const hotspotY = 1;

    let enabled = false;

    cursor.className = "site-custom-cursor";
    cursor.setAttribute("aria-hidden", "true");

    function handlePointerMove(event) {
        if (event.pointerType && event.pointerType !== "mouse") {
            return;
        }

        const pixelRatio = window.devicePixelRatio || 1;
        const cursorX = Math.round((event.clientX - hotspotX) * pixelRatio) / pixelRatio;
        const cursorY = Math.round((event.clientY - hotspotY) * pixelRatio) / pixelRatio;

        cursor.style.transform = `translate3d(${cursorX}px, ${cursorY}px, 0)`;
    }

    function hideCursor() {
        cursor.style.transform = "translate3d(-100px, -100px, 0)";
    }

    function enableCursor() {
        if (enabled || !finePointer.matches) {
            return;
        }

        enabled = true;
        root.classList.add("site-custom-cursor-enabled");
        document.addEventListener(moveEventName, handlePointerMove, { passive: true });
        document.addEventListener("mouseleave", hideCursor);
        window.addEventListener("blur", hideCursor);
    }

    function disableCursor() {
        if (!enabled) {
            return;
        }

        enabled = false;
        root.classList.remove("site-custom-cursor-enabled");
        hideCursor();
        document.removeEventListener(moveEventName, handlePointerMove);
        document.removeEventListener("mouseleave", hideCursor);
        window.removeEventListener("blur", hideCursor);
    }

    function handlePointerCapabilityChange() {
        if (finePointer.matches) {
            enableCursor();
        } else {
            disableCursor();
        }
    }

    cursorPreloader.addEventListener("load", function () {
        document.body.appendChild(cursor);
        handlePointerCapabilityChange();
    }, { once: true });

    cursorPreloader.addEventListener("error", function () {
        root.classList.remove("site-custom-cursor-enabled");
    }, { once: true });

    if (typeof finePointer.addEventListener === "function") {
        finePointer.addEventListener("change", handlePointerCapabilityChange);
    } else {
        finePointer.addListener(handlePointerCapabilityChange);
    }

    cursorPreloader.src = "img/form/custom-cursor.png?v=20260803-1";
}());
