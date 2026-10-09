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
        if (!swiperEl || typeof window.Swiper === "undefined" || swiperEl.classList.contains("swiper-initialized")) return;

        new Swiper(swiperEl, {
            slidesPerView: 1,
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
                480: { slidesPerView: 1, spaceBetween: 14 },
                768: { slidesPerView: 2, spaceBetween: 16 },
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
