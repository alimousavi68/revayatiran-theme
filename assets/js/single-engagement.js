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

/* Keep identity fields collapsed until the native comment input is activated. */
(() => {
    const initialize = () => {
        document.querySelectorAll('.rv-discussion .comment-form').forEach((form, formIndex) => {
            const comment = form.querySelector('textarea[name="comment"]');
            const fields = [...form.querySelectorAll('.comment-form-author,.comment-form-email,.comment-form-url,.comment-form-cookies-consent')];
            if (!comment || !fields.length || form.dataset.rvIdentityReady) return;
            form.dataset.rvIdentityReady = 'true';
            form.classList.add('rv-comment-enhanced');
            const toggle = document.createElement('button');
            toggle.type = 'button';
            toggle.className = 'rv-comment-identity-toggle';
            toggle.textContent = 'نام و ایمیل';
            fields.forEach((field, index) => {
                field.dataset.rvCommentIdentity = 'true';
                if (!field.id) field.id = `rv-comment-identity-${formIndex}-${index}`;
            });
            toggle.setAttribute('aria-controls', fields.map(field => field.id).join(' '));
            form.append(toggle);
            const open = value => {
                fields.forEach(field => { field.hidden = !value; });
                form.classList.toggle('rv-comment-identity-open', value);
                toggle.setAttribute('aria-expanded', String(value));
            };
            open(false);
            // pointerdown also works when clicking an input that already has focus.
            comment.addEventListener('pointerdown', () => open(true));
            comment.addEventListener('click', () => open(true));
            form.addEventListener('focusin', event => {
                if (event.target === comment) open(true);
            });
            toggle.addEventListener('click', () => open(toggle.getAttribute('aria-expanded') !== 'true'));
            form.addEventListener('invalid', () => open(true), true);
        });
    };
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initialize, { once: true });
    else initialize();
})();
