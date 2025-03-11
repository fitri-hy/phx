/*!
 * Copyright ©2025 I-As Dev. All rights reserved.
 * Author: I-As Dev
 * Website: https://i-as.dev
 * License: MIT
 */


if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('service-worker.js')
        .then(() => console.log("Worker Service ✅"))
        .catch((err) => console.error("Worker Failed Register ❌", err));
}

let deferredPrompt;
const installModal = document.getElementById("install-modal");
const installButton = document.getElementById("install-button");
const closeModal = document.querySelector(".close");

function isAppInstalled() {
    return localStorage.getItem("appInstalled") === "true" ||
           window.matchMedia('(display-mode: standalone)').matches ||
           (navigator.standalone !== undefined && navigator.standalone);
}

function shouldShowModal() {
    const modalClosedUntil = localStorage.getItem("modalClosedUntil");

    if (isAppInstalled()) return false;
    if (modalClosedUntil) {
        return Date.now() > parseInt(modalClosedUntil);
    }
    return true;
}

if (!shouldShowModal()) {
    installModal.style.display = "none";
}

if ('onbeforeinstallprompt' in window) {
    window.addEventListener("beforeinstallprompt", (e) => {
        e.preventDefault();
        deferredPrompt = e;

        if (shouldShowModal()) {
            installModal.style.display = "flex";
        }
    });

    installButton.addEventListener("click", () => {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === "accepted") {
                    console.log("Installing");
                    localStorage.setItem("appInstalled", "true");
                    installModal.style.display = "none";
                } else {
                    console.log("Refused Installing");
                }
                deferredPrompt = null;
            });
        } else {
            console.log("PWA installation is not available.");
        }
    });

    closeModal.addEventListener("click", () => {
        installModal.style.display = "none";
        const closeTime = Date.now() + 30 * 60 * 1000;
        localStorage.setItem("modalClosedUntil", closeTime);
    });

    window.addEventListener("click", (event) => {
        if (event.target === installModal) {
            installModal.style.display = "none";
            const closeTime = Date.now() + 30 * 60 * 1000;
            localStorage.setItem("modalClosedUntil", closeTime);
        }
    });
} else {
    console.log("PWA is not supported in this browser.");
    installModal.style.display = "none";
}

window.addEventListener("appinstalled", () => {
    console.log("Application is already installed!");
    localStorage.setItem("appInstalled", "true");
    installModal.style.display = "none";
});
