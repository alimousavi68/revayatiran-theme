document.addEventListener("DOMContentLoaded", () => {
    const mediaRail = document.getElementById("media-rail");
    const mediaPrev = document.getElementById("media-prev");
    const mediaNext = document.getElementById("media-next");
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

            interactiveThumbItems.forEach(item => {
                item.classList.remove("is-active");
                item.setAttribute("aria-pressed", "false");
            });
            thumb.classList.add("is-active");
            thumb.setAttribute("aria-pressed", "true");

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
            clearInterval(autoplayInterval);
            if (window.matchMedia("(max-width: 1023px), (prefers-reduced-motion: reduce)").matches) return;
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
                if (!window.matchMedia("(hover: hover)").matches) return;
                updateInfoPanel(thumb, index);
                stopAutoplay();
            });
            thumb.addEventListener("mouseleave", startAutoplay);
            thumb.addEventListener("click", () => {
                stopAutoplay();
                updateInfoPanel(thumb, index);
            });
        });

        // توقف پخش خودکار هنگام هاور روی پنل اطلاعات
        infoPanelInner.addEventListener("mouseenter", stopAutoplay);
        infoPanelInner.addEventListener("mouseleave", startAutoplay);

        // شروع پخش خودکار
        startAutoplay();
    }

});
