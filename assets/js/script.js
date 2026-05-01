document.addEventListener("DOMContentLoaded", () => {
    const body = document.body;
    const navToggle = document.querySelector(".nav-toggle");
    const navMenu = document.querySelector(".nav-menu");

    if (navToggle && navMenu) {
        navToggle.addEventListener("click", () => {
            const isOpen = navMenu.classList.toggle("open");
            navToggle.setAttribute("aria-expanded", String(isOpen));
        });

        navMenu.querySelectorAll("a").forEach((link) => {
            link.addEventListener("click", () => {
                navMenu.classList.remove("open");
                navToggle.setAttribute("aria-expanded", "false");
            });
        });
    }

    const themeToggle = document.getElementById("theme-toggle");
    const updateThemeToggle = () => {
        if (!themeToggle) {
            return;
        }

        const label = body.classList.contains("theme-light") ? "Donker" : "Licht";
        const span = themeToggle.querySelector("span");

        if (span) {
            span.textContent = label;
        } else {
            themeToggle.textContent = label;
        }

        themeToggle.setAttribute(
            "aria-label",
            body.classList.contains("theme-light")
                ? "Schakel naar donkere modus"
                : "Schakel naar lichte modus"
        );
    };

    try {
        if (localStorage.getItem("site-theme") === "light") {
            body.classList.add("theme-light");
        }
    } catch (error) {
        // Ignore storage issues and keep the default theme.
    }

    updateThemeToggle();

    if (themeToggle) {
        themeToggle.addEventListener("click", () => {
            body.classList.toggle("theme-light");

            try {
                localStorage.setItem(
                    "site-theme",
                    body.classList.contains("theme-light") ? "light" : "dark"
                );
            } catch (error) {
                // Ignore storage issues and continue.
            }

            updateThemeToggle();
        });
    }

    const ageModal = document.getElementById("age-modal");

    const showAgeModal = () => {
        if (!ageModal) {
            return;
        }

        ageModal.classList.add("is-visible");
        ageModal.setAttribute("aria-hidden", "false");
        body.classList.add("modal-open");
    };

    const hideAgeModal = () => {
        if (!ageModal) {
            return;
        }

        ageModal.classList.remove("is-visible");
        ageModal.setAttribute("aria-hidden", "true");
        body.classList.remove("modal-open");
    };

    try {
        if (!localStorage.getItem("ageConfirmed")) {
            showAgeModal();
        }
    } catch (error) {
        showAgeModal();
    }

    window.confirmAge = () => {
        hideAgeModal();

        try {
            localStorage.setItem("ageConfirmed", "true");
        } catch (error) {
            // Ignore storage issues and keep the modal closed for this session.
        }
    };

    const serviceButtons = Array.from(document.querySelectorAll("[data-services-tab]"));
    const servicePanels = Array.from(document.querySelectorAll("[data-services-panel]"));

    if (serviceButtons.length && servicePanels.length) {
        const activatePanel = (panelName) => {
            serviceButtons.forEach((button) => {
                const isActive = button.dataset.servicesTab === panelName;
                button.classList.toggle("is-active", isActive);
                button.setAttribute("aria-selected", String(isActive));
            });

            servicePanels.forEach((panel) => {
                const isActive = panel.dataset.servicesPanel === panelName;
                panel.classList.toggle("is-active", isActive);
                panel.hidden = !isActive;
            });
        };

        const defaultPanel =
            serviceButtons.find((button) => button.classList.contains("is-active"))?.dataset.servicesTab ||
            serviceButtons[0].dataset.servicesTab;

        activatePanel(defaultPanel);

        serviceButtons.forEach((button) => {
            button.addEventListener("click", () => {
                activatePanel(button.dataset.servicesTab);
            });
        });
    }

    const contactForm = document.getElementById("contact-form");
    const formMessage = document.getElementById("form-message");

    if (contactForm && formMessage) {
        contactForm.addEventListener("submit", (event) => {
            event.preventDefault();
            formMessage.textContent = "Bedankt. Je aanvraag is ontvangen en kan nu verder worden opgevolgd.";
            formMessage.style.display = "block";
            contactForm.reset();
        });
    }
});
