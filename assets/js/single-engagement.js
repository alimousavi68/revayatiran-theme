/* Native share sheet when supported; canonical-link copying with manual fallback. */
(() => {
    document.querySelectorAll('.rv-share').forEach((section) => {
        const { url, title } = section.dataset;
        const status = section.querySelector('[role="status"]');
        const fallback = section.querySelector('.rv-share__fallback');
        section.querySelector('[data-share-copy]').addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(url);
                status.textContent = 'پیوند مطلب کپی شد.';
                fallback.hidden = true;
            } catch (_) {
                fallback.hidden = false;
                fallback.focus();
                fallback.select();
                status.textContent = 'برای کپی، پیوند زیر را انتخاب کنید.';
            }
        });
        const native = section.querySelector('[data-share-native]');
        if (native && typeof navigator.share === 'function') {
            native.hidden = false;
            native.addEventListener('click', async () => {
                try { await navigator.share({ title, url }); }
                catch (error) {
                    if (error.name !== 'AbortError') status.textContent = 'از دکمه‌های شبکه‌ها یا کپی لینک استفاده کنید.';
                }
            });
        }
    });
})();
