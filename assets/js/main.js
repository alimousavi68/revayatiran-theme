document.addEventListener("DOMContentLoaded", () => {
    const root = document.documentElement;
    const header = document.getElementById("site-header");
    const progressBar = document.getElementById("page-progress-bar");
    const themeToggle = document.getElementById("theme-toggle");
    const storedTheme = localStorage.getItem("revayat-theme");
    const menuToggle = document.getElementById("menu-toggle");
    const mobileMenu = document.getElementById("mobile-menu");
    const mobileClose = document.getElementById("mobile-close");
    const mediaRail = document.getElementById("media-rail");
    const mediaPrev = document.getElementById("media-prev");
    const mediaNext = document.getElementById("media-next");
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
    window.addEventListener("scroll", toggleHeaderState, { passive: true });

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

    const tabButtons = document.querySelectorAll("[data-news-tab]");
    const tabPanels = document.querySelectorAll("[data-news-panel]");
    const tabSelect = document.getElementById("news-tab-select");

    if (tabButtons.length && tabPanels.length) {
        tabButtons.forEach((button) => {
            button.addEventListener("click", () => {
                const target = button.getAttribute("data-news-tab");

                if (tabSelect && tabSelect.value !== target) {
                    tabSelect.value = target;
                }

                tabButtons.forEach((item) => {
                    const isActive = item === button;
                    item.classList.toggle("is-active", isActive);
                    item.setAttribute("aria-selected", String(isActive));
                });

                tabPanels.forEach((panel) => {
                    const shouldShow = panel.getAttribute("data-news-panel") === target;
                    panel.hidden = !shouldShow;

                    if (shouldShow) {
                        requestAnimationFrame(() => {
                            panel.querySelectorAll("[data-reveal]").forEach((item) => item.classList.add("is-visible"));
                        });
                    }
                });
            });
        });

        if (tabSelect) {
            tabSelect.addEventListener("change", (e) => {
                const targetVal = e.target.value;
                const btn = document.querySelector(`[data-news-tab="${targetVal}"]`);
                if (btn) btn.click();
            });
        }
    }

    if (mediaRail && mediaPrev && mediaNext) {
        const step = () => {
            const firstCard = mediaRail.querySelector(".media-card");
            return firstCard ? firstCard.getBoundingClientRect().width + 18 : 320;
        };

        mediaPrev.setAttribute("aria-label", "اسلاید قبلی");
        mediaNext.setAttribute("aria-label", "اسلاید بعدی");

        mediaPrev.addEventListener("click", () => {
            mediaRail.scrollBy({ left: step(), behavior: "smooth" });
        });

        mediaNext.addEventListener("click", () => {
            mediaRail.scrollBy({ left: -step(), behavior: "smooth" });
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

    // ─── STORY SLIDE REEL & MODAL VIEWER LOGIC ───────────────
    const storyRail = document.getElementById("story-rail");
    const storyRailPrev = document.getElementById("story-rail-prev");
    const storyRailNext = document.getElementById("story-rail-next");
    const storyCards = document.querySelectorAll(".story-card");
    // Removed story modal elements
    // Story Rail Scroll buttons & Auto-play Carousel
    let autoPlayInterval = null;

    if (storyRail) {
        const startAutoPlay = () => {
            stopAutoPlay();
            autoPlayInterval = setInterval(() => {
                const cardWidth = 260;
                const currentScroll = Math.abs(storyRail.scrollLeft);
                const maxScroll = storyRail.scrollWidth - storyRail.clientWidth;

                if (currentScroll >= maxScroll - 20) {
                    storyRail.scrollTo({ left: 0, behavior: "smooth" });
                } else {
                    storyRail.scrollBy({ left: -cardWidth, behavior: "smooth" });
                }
            }, 3000);
        };

        const stopAutoPlay = () => {
            if (autoPlayInterval) clearInterval(autoPlayInterval);
        };

        startAutoPlay();

        // Pause auto-play on mouse enter or touch
        storyRail.addEventListener("mouseenter", stopAutoPlay);
        storyRail.addEventListener("mouseleave", startAutoPlay);
        storyRail.addEventListener("touchstart", stopAutoPlay, { passive: true });
        storyRail.addEventListener("touchend", startAutoPlay, { passive: true });

        if (storyRailPrev && storyRailNext) {
            storyRailPrev.addEventListener("click", () => {
                stopAutoPlay();
                storyRail.scrollBy({ left: 260, behavior: "smooth" });
                startAutoPlay();
            });
            storyRailNext.addEventListener("click", () => {
                stopAutoPlay();
                storyRail.scrollBy({ left: -260, behavior: "smooth" });
                startAutoPlay();
            });
        }
    }

    // Story Modal Logic removed as per new single-media routing

    // ═══════════════════════════════════════════════════════════
    // DAILY NARRATIVE INTERACTIVE THUMBNAILS (Decoupled & Isolated)
    // ═══════════════════════════════════════════════════════════
    const infoPanelInner = document.querySelector(".info-panel__inner");
    const infoPanelTag = document.getElementById("info-panel-tag");
    const infoPanelTitle = document.getElementById("info-panel-title");
    const infoPanelExcerpt = document.getElementById("info-panel-excerpt");
    const infoPanelAvatar = document.getElementById("info-panel-avatar");
    const infoPanelAuthor = document.getElementById("info-panel-author");
    const infoPanelDate = document.getElementById("info-panel-date");
    const infoPanelLink = document.getElementById("info-panel-link");
    const interactiveThumbItems = document.querySelectorAll(".interactive-thumb-item");

    if (infoPanelInner && interactiveThumbItems.length) {
        let currentIndex = 0;
        let autoplayInterval;

        const updateInfoPanel = (thumb, index) => {
            if (index !== undefined) {
                currentIndex = index;
            } else {
                currentIndex = Array.from(interactiveThumbItems).indexOf(thumb);
            }

            interactiveThumbItems.forEach(item => item.classList.remove("is-active"));
            thumb.classList.add("is-active");

            infoPanelInner.classList.add("is-updating");

            setTimeout(() => {
                const tag = thumb.getAttribute("data-tag");
                const avatar = thumb.getAttribute("data-avatar");
                const author = thumb.getAttribute("data-author");
                const date = thumb.getAttribute("data-date");
                const title = thumb.getAttribute("data-title");
                const excerpt = thumb.getAttribute("data-excerpt");
                const link = thumb.getAttribute("data-link");

                if (infoPanelTag && tag) infoPanelTag.textContent = tag;
                if (infoPanelAvatar && avatar) infoPanelAvatar.src = avatar;
                if (infoPanelAuthor && author) infoPanelAuthor.textContent = author;
                if (infoPanelDate && date) infoPanelDate.textContent = date;
                if (infoPanelTitle && title) infoPanelTitle.textContent = title;
                if (infoPanelExcerpt && excerpt) infoPanelExcerpt.textContent = excerpt;
                if (infoPanelLink && link) infoPanelLink.href = link;

                infoPanelInner.classList.remove("is-updating");
            }, 180);
        };

        const startAutoplay = () => {
            autoplayInterval = setInterval(() => {
                const nextIndex = (currentIndex + 1) % interactiveThumbItems.length;
                updateInfoPanel(interactiveThumbItems[nextIndex], nextIndex);
            }, 4000);
        };

        const stopAutoplay = () => {
            clearInterval(autoplayInterval);
        };

        interactiveThumbItems.forEach((thumb, index) => {
            thumb.addEventListener("mouseenter", () => {
                updateInfoPanel(thumb, index);
                stopAutoplay();
            });
            thumb.addEventListener("mouseleave", startAutoplay);
            thumb.addEventListener("click", () => updateInfoPanel(thumb, index));
        });

        // توقف پخش خودکار هنگام هاور روی پنل اطلاعات
        infoPanelInner.addEventListener("mouseenter", stopAutoplay);
        infoPanelInner.addEventListener("mouseleave", startAutoplay);

        // شروع پخش خودکار
        startAutoplay();
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

// ─── NEWS SWIPER INITIALIZATION ────────────────────────────────────────────
// خارج از DOMContentLoaded چون Swiper باید پس از لود کامل DOM و کتابخانه اجرا شود

(function initNewsSwipers() {
    if (typeof Swiper === "undefined") return;

    const swiperInstances = new Map();

    const createSwiper = (container) => {
        if (swiperInstances.has(container)) {
            swiperInstances.get(container).destroy(true, true);
        }

        const instance = new Swiper(container, {
            slidesPerView: 1.2,
            spaceBetween: 14,
            centeredSlides: false,
            grabCursor: true,
            speed: 600,
            autoplay: {
                delay: 3500,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            // RTL — جهت‌دهی صحیح برای فارسی
            dir: "rtl",
            navigation: {
                nextEl: container.querySelector(".news-swiper-next"),
                prevEl: container.querySelector(".news-swiper-prev"),
            },
            pagination: {
                el: container.querySelector(".news-swiper-pagination"),
                clickable: true,
                dynamicBullets: true,
            },
            breakpoints: {
                480: { slidesPerView: 1.6, spaceBetween: 14 },
                640: { slidesPerView: 2.3, spaceBetween: 16 },
                768: { slidesPerView: 3, spaceBetween: 18 },
                1024: { slidesPerView: 3.8, spaceBetween: 20 },
                1280: { slidesPerView: 4.2, spaceBetween: 22 },
                1440: { slidesPerView: 4.5, spaceBetween: 24 },
            },
        });

        swiperInstances.set(container, instance);
        return instance;
    };

    // تابع مقداردهی اولیه برای پنل فعال
    const initActivePanel = () => {
        const activePanel = document.querySelector(".news-panel.is-active");
        if (!activePanel) return;

        const swiperEl = activePanel.querySelector(".news-swiper");
        if (swiperEl) createSwiper(swiperEl);
    };

    // راه‌اندازی اولیه
    document.addEventListener("DOMContentLoaded", initActivePanel);
    // اگر DOM قبلاً لود شده
    if (document.readyState !== "loading") initActivePanel();

    // هنگام تغییر تب، Swiper پنل جدید را راه‌اندازی کن
    document.addEventListener("click", (e) => {
        const tab = e.target.closest("[data-news-tab]");
        if (!tab) return;

        setTimeout(() => {
            const targetPanel = document.getElementById("news-panel-" + tab.dataset.newsTab)
                || document.querySelector(`[data-news-panel="${tab.dataset.newsTab}"]`);
            if (!targetPanel) return;

            const swiperEl = targetPanel.querySelector(".news-swiper");
            if (swiperEl) createSwiper(swiperEl);
        }, 50); // تاخیر کوچک برای اطمینان از نمایش پنل
    });
})();

// ─── ANALYSTS TOP SWIPER CAROUSEL (با پخش خودکار و پجینیشن) ─────────────────
(function initAnalystsTopSwiper() {
    const init = () => {
        const swiperEl = document.querySelector(".analysts-top-swiper");
        if (!swiperEl || swiperEl.classList.contains("swiper-initialized")) return;

        new Swiper(swiperEl, {
            slidesPerView: 1.1,
            spaceBetween: 14,
            grabCursor: true,
            speed: 600,
            autoplay: {
                delay: 3500,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            dir: "rtl",
            navigation: {
                nextEl: swiperEl.querySelector(".analysts-swiper-next"),
                prevEl: swiperEl.querySelector(".analysts-swiper-prev"),
            },
            pagination: {
                el: swiperEl.querySelector(".analysts-swiper-pagination"),
                clickable: true,
                dynamicBullets: true,
            },
            breakpoints: {
                480: { slidesPerView: 1.5, spaceBetween: 14 },
                640: { slidesPerView: 2, spaceBetween: 16 },
                1024: { slidesPerView: 2.2, spaceBetween: 18 },
                1280: { slidesPerView: 3, spaceBetween: 20 },
            },
        });
    };

    if (document.readyState !== "loading") {
        init();
    } else {
        document.addEventListener("DOMContentLoaded", init);
    }
})();

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

// ─── SITUATION ROOM HERO ───────────────────────────────────────────────────
(function initSituationRoomHero() {
    const init = () => {
        const slider = document.querySelector(".situation-hero-swiper");
        if (!slider || typeof window.Swiper === "undefined" || slider.classList.contains("swiper-initialized")) return;

        const slidesCount = slider.querySelectorAll(".swiper-slide").length;
        new window.Swiper(slider, {
            slidesPerView: 1,
            speed: 700,
            loop: slidesCount > 1,
            effect: "fade",
            fadeEffect: { crossFade: true },
            autoplay: slidesCount > 1 ? { delay: 6500, disableOnInteraction: false, pauseOnMouseEnter: true } : false,
            navigation: {
                nextEl: ".srx-hero-next",
                prevEl: ".srx-hero-prev",
            },
            pagination: {
                el: ".srx-hero-pagination",
                clickable: true,
            },
            keyboard: { enabled: true },
            a11y: { enabled: true },
        });
    };

    if (document.readyState !== "loading") init();
    else document.addEventListener("DOMContentLoaded", init);
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
