document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".footer-accordion").forEach((section, index) => {
        const button = section.querySelector(".footer-accordion-btn");
        const content = section.querySelector(".footer-accordion-content");
        if (!button || !content) return;
        content.id = `footer-menu-${index + 1}`;
        button.setAttribute("aria-controls", content.id);
        const viewport = window.matchMedia("(max-width: 767px)");
        const setExpanded = expanded => {
            button.setAttribute("aria-expanded", String(expanded));
            content.classList.toggle("is-collapsed", !expanded);
        };
        const syncViewport = () => setExpanded(!viewport.matches);
        syncViewport();
        viewport.addEventListener("change", syncViewport);
        button.addEventListener("click", () => {
            if (viewport.matches) setExpanded(button.getAttribute("aria-expanded") !== "true");
        });
    });
    const root = document.documentElement;
    const header = document.getElementById("site-header");
    const progressBar = document.getElementById("page-progress-bar");
    const themeToggle = document.getElementById("theme-toggle");
    const storedTheme = localStorage.getItem("revayat-theme");
    const menuToggle = document.getElementById("menu-toggle");
    const mobileMenu = document.getElementById("mobile-menu");
    const mobileClose = document.getElementById("mobile-close");
    const topBarTime = document.getElementById("top-bar-time");

    const syncThemeToggle = (theme) => {
        const nextTheme = theme === "dark" ? "روشن" : "تیره";
        document.querySelectorAll(".theme-toggle-btn, #theme-toggle").forEach(btn => {
            btn.setAttribute("aria-label", `تغییر حالت رنگی به ${nextTheme}`);
            btn.setAttribute("title", `تغییر حالت رنگی به ${nextTheme}`);
        });
    };

    const applyTheme = (theme) => {
        root.setAttribute("data-theme", theme);
        localStorage.setItem("revayat-theme", theme);
        syncThemeToggle(theme);
    };

    applyTheme(storedTheme || "light");

    document.querySelectorAll(".theme-toggle-btn, #theme-toggle").forEach(btn => {
        btn.addEventListener("click", () => {
            const nextTheme = root.getAttribute("data-theme") === "dark" ? "light" : "dark";
            applyTheme(nextTheme);
        });
    });

    // Remove obsolete preferences from the retired palette customizer.
    root.removeAttribute("data-palette");
    root.removeAttribute("data-header-backdrop");
    localStorage.removeItem("revayat-palette");
    localStorage.removeItem("revayat-header-backdrop");

    const toggleHeaderState = () => {
        if (progressBar) {
            const scrollableHeight = document.documentElement.scrollHeight - window.innerHeight;
            const progress = scrollableHeight > 0 ? (window.scrollY / scrollableHeight) * 100 : 0;
            progressBar.style.width = `${Math.min(progress, 100)}%`;
        }
        // Add scrolled class for glassmorphism effect
        if (header) {
            header.classList.toggle("is-scrolled", window.scrollY > 20);
        }
    };

    toggleHeaderState();
    let headerFramePending = false;
    window.addEventListener("scroll", () => {
        if (headerFramePending) return;
        headerFramePending = true;
        requestAnimationFrame(() => {
            toggleHeaderState();
            headerFramePending = false;
        });
    }, { passive: true });

    // Top bar live clock
    const toPersianDigits = (str) => {
        const persianDigits = ["۰", "۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹"];
        return String(str).replace(/[0-9]/g, (w) => persianDigits[parseInt(w, 10)]);
    };

    const updateClock = () => {
        if (!topBarTime) return;
        const now = new Date();
        const h = String(now.getHours()).padStart(2, "0");
        const m = String(now.getMinutes()).padStart(2, "0");
        topBarTime.textContent = toPersianDigits(`${h}:${m}`);
    };
    updateClock();
    setInterval(updateClock, 30000);

    if (menuToggle && mobileMenu) {
        const setMenuState = (isOpen) => {
            mobileMenu.classList.toggle("is-open", isOpen);
            menuToggle.setAttribute("aria-expanded", String(isOpen));
            mobileMenu.setAttribute("aria-hidden", String(!isOpen));
            document.body.style.overflow = isOpen ? "hidden" : "";
        };

        menuToggle.addEventListener("click", () => {
            const isOpen = menuToggle.getAttribute("aria-expanded") === "true";
            setMenuState(!isOpen);
        });

        // Close via backdrop click
        mobileMenu.addEventListener("click", (event) => {
            if (event.target === mobileMenu) {
                setMenuState(false);
            }
        });

        // Close via X button inside panel
        if (mobileClose) {
            mobileClose.addEventListener("click", () => setMenuState(false));
        }

        // Close when nav link is clicked
        mobileMenu.querySelectorAll("a").forEach(link => {
            link.addEventListener("click", () => setMenuState(false));
        });

        // Close on Escape
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape" && menuToggle.getAttribute("aria-expanded") === "true") {
                setMenuState(false);
            }
        });
    }

    const revealItems = document.querySelectorAll("[data-reveal]");

    if ("IntersectionObserver" in window && revealItems.length) {
        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("is-visible");
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.14,
            rootMargin: "0px 0px -40px 0px"
        });

        revealItems.forEach((item) => revealObserver.observe(item));
    } else {
        revealItems.forEach((item) => item.classList.add("is-visible"));
    }

    const counters = document.querySelectorAll("[data-count]");

    if ("IntersectionObserver" in window && counters.length) {
        const counterObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                const counter = entry.target;
                const targetValue = Number(counter.getAttribute("data-count")) || 0;
                const duration = 1100;
                const startTime = performance.now();

                const tick = (now) => {
                    const progress = Math.min((now - startTime) / duration, 1);
                    const eased = 1 - Math.pow(1 - progress, 3);
                    counter.textContent = Math.floor(eased * targetValue).toLocaleString("fa-IR");

                    if (progress < 1) {
                        requestAnimationFrame(tick);
                    } else {
                        counter.textContent = targetValue.toLocaleString("fa-IR");
                    }
                };

                requestAnimationFrame(tick);
                observer.unobserve(counter);
            });
        }, {
            threshold: 0.5
        });

        counters.forEach((counter) => counterObserver.observe(counter));
    } else {
        counters.forEach((counter) => {
            const value = Number(counter.getAttribute("data-count")) || 0;
            counter.textContent = value.toLocaleString("fa-IR");
        });
    }

    // Search Overlay Logic
    const searchClose = document.getElementById('search-close');
    const searchOverlay = document.getElementById('search-overlay');
    const searchInput = document.getElementById('search-input');

    const openSearch = () => {
        if (!searchOverlay) return;
        if (mobileMenu && mobileMenu.classList.contains("is-open")) {
            mobileMenu.classList.remove("is-open");
            if (menuToggle) menuToggle.setAttribute("aria-expanded", "false");
            mobileMenu.setAttribute("aria-hidden", "true");
        }
        searchOverlay.classList.add('is-active');
        document.body.style.overflow = 'hidden';
        if (searchInput) setTimeout(() => searchInput.focus(), 120);
    };

    const closeSearch = () => {
        if (!searchOverlay) return;
        searchOverlay.classList.remove('is-active');
        document.body.style.overflow = '';
    };

    document.querySelectorAll('#search-trigger, #mobile-search-trigger, .search-trigger-btn').forEach(btn => {
        btn.addEventListener('click', openSearch);
    });
    if (searchClose) searchClose.addEventListener('click', closeSearch);

    if (searchOverlay) {
        searchOverlay.addEventListener('click', (e) => {
            if (e.target === searchOverlay) closeSearch();
        });
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeSearch();
    });

    // Leaderboard Tabs logic
    const leaderboardTabs = document.querySelectorAll('.leaderboard-tabs .tab-item');
    if (leaderboardTabs.length) {
        leaderboardTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                leaderboardTabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                // In a real app, you would filter or fetch data here
            });
        });
    }

    // Back to Top functionality
    const backToTopBtn = document.getElementById("back-to-top");
    const progressCircle = document.querySelector(".back-to-top__circle-progress");
    const circleCircumference = 132; // 2 * PI * 21

    if (backToTopBtn && progressCircle) {
        const updateScrollProgress = () => {
            const scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
            const scrolled = window.scrollY;

            // Show or hide button
            if (scrolled > 300) {
                backToTopBtn.classList.add("is-visible");
            } else {
                backToTopBtn.classList.remove("is-visible");
            }

            // Calculate progress and update circle path length
            const progress = scrollHeight > 0 ? scrolled / scrollHeight : 0;
            const offset = circleCircumference - (progress * circleCircumference);
            progressCircle.style.strokeDashoffset = Math.max(0, Math.min(offset, circleCircumference));
        };

        // Scroll to top smoothly
        backToTopBtn.addEventListener("click", () => {
            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });
        });

        // Initialize and bind events
        updateScrollProgress();
        window.addEventListener("scroll", updateScrollProgress, { passive: true });
    }

    // ═══════════════════════════════════════════════════════════
    // ARCHIVE PAGE MOBILE FILTER ACCORDION (Robust Event Delegation)
    // ═══════════════════════════════════════════════════════════
    document.addEventListener("click", (e) => {
        const toggleBtn = e.target.closest("#archive-filter-toggle");
        if (!toggleBtn) return;

        e.preventDefault();
        e.stopPropagation();

        const filterBody = document.getElementById("archive-filter-body");
        const filterIcon = document.getElementById("archive-filter-icon");

        if (filterBody) {
            const isOpen = filterBody.classList.toggle("is-open");
            toggleBtn.setAttribute("aria-expanded", String(isOpen));
            if (filterIcon) {
                filterIcon.classList.toggle("rotate-180", isOpen);
            }
        }
    });
});

// ─── FLOATING SOCIAL FAB CONTROLLER ─────────────────────────────────────────
(function initSocialFab() {
    const fab = document.getElementById("social-fab");
    if (!fab) return;

    const toggleBtn = document.getElementById("social-fab-toggle");
    const panel = document.getElementById("social-fab-panel");
    const closeBtn = document.getElementById("social-fab-close");

    if (!toggleBtn || !panel) return;

    const openFab = () => {
        fab.classList.add("is-open");
        toggleBtn.setAttribute("aria-expanded", "true");
        panel.setAttribute("aria-hidden", "false");
        try {
            localStorage.setItem("revayat_social_seen", "true");
        } catch (e) { }
    };

    const closeFab = () => {
        fab.classList.remove("is-open");
        toggleBtn.setAttribute("aria-expanded", "false");
        panel.setAttribute("aria-hidden", "true");
    };

    const toggleFab = () => {
        if (fab.classList.contains("is-open")) {
            closeFab();
        } else {
            openFab();
        }
    };

    toggleBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        toggleFab();
    });

    if (closeBtn) {
        closeBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            closeFab();
        });
    }

    // Close on click outside
    document.addEventListener("click", (e) => {
        if (fab.classList.contains("is-open") && !fab.contains(e.target)) {
            closeFab();
        }
    });

    // Close on Escape key
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && fab.classList.contains("is-open")) {
            closeFab();
            toggleBtn.focus();
        }
    });
})();

// ─── USER ACCOUNT MENU DROPDOWN CONTROLLER ─────────────────────────────────
(function initHeaderAccountDropdown() {
    const bindDropdowns = () => {
        document.querySelectorAll("details.rv-header-account").forEach(details => {
            if (details.dataset.accountBound) return;
            details.dataset.accountBound = "true";

            const summary = details.querySelector("summary");

            // Close on click outside
            document.addEventListener("click", (e) => {
                if (details.hasAttribute("open") && !details.contains(e.target)) {
                    details.removeAttribute("open");
                }
            });

            // Close on Escape key and focus back to summary
            details.addEventListener("keydown", (e) => {
                if (e.key === "Escape" && details.hasAttribute("open")) {
                    details.removeAttribute("open");
                    if (summary) summary.focus();
                }
            });

            // Close when an action link inside is clicked
            details.querySelectorAll("nav a").forEach(link => {
                link.addEventListener("click", () => {
                    details.removeAttribute("open");
                });
            });
        });
    };

    if (document.readyState !== "loading") bindDropdowns();
    else document.addEventListener("DOMContentLoaded", bindDropdowns);
})();
