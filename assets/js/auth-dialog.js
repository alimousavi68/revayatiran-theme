/* Local auth panels; only state-changing requests use the compact transport. */
(() => {
    'use strict';
    if (!window.revayatAuth) return;
    const authURL = new URL(revayatAuth.url);
    const digits = value => String(value).replace(/[۰-۹٠-٩]/g, c => String('۰۱۲۳۴۵۶۷۸۹'.includes(c) ? '۰۱۲۳۴۵۶۷۸۹'.indexOf(c) : '٠١٢٣٤٥٦٧٨٩'.indexOf(c)));
    let dialog, opener;
    const states = new WeakMap();
    const isAuth = url => url.origin === authURL.origin && url.pathname.replace(/\/$/, '') === authURL.pathname.replace(/\/$/, '');
    function notice(card, text, error = false) {
        const node = card.querySelector('[data-auth-notice]');
        node.textContent = text || ''; node.hidden = !text;
        node.classList.toggle('is-error', error);
    }
    function panel(card, name, focus = true) {
        card.querySelectorAll('[data-auth-panel]').forEach(form => { form.hidden = form.dataset.authPanel !== name; });
        card.querySelectorAll('[role=tab]').forEach(button => {
            const selected = button.dataset.authMethod === name || (name === 'verify' && button.dataset.authMethod === 'auth');
            button.setAttribute('aria-selected', String(selected));
        });
        states.get(card).panel = name;
        if (focus) card.querySelector('[data-auth-panel="' + name + '"] input:not([type=hidden]):not(:disabled)')?.focus({preventScroll: true});
    }
    function applyChallenge(card, data, focus = true) {
        const state = states.get(card);
        state.challenge = {...data, monotonic: performance.now()};
        card.querySelector('[data-auth-mobile]').textContent = data.masked_mobile || '';
        const recovery = data.context === 'recover';
        const label = card.querySelector('[data-auth-new-password]');
        label.hidden = !recovery; label.querySelector('input').disabled = !recovery; label.querySelector('input').required = recovery;
        panel(card, 'verify', focus); tick(card);
    }
    function tick(card) {
        const state = states.get(card), challenge = state?.challenge;
        if (!challenge) return;
        const now = challenge.server_time + (performance.now() - challenge.monotonic) / 1000;
        const resend = Math.max(0, Math.ceil(challenge.resend_available_at - now));
        const expiry = Math.max(0, Math.ceil(challenge.expires_at - now));
        const format = value => new Intl.NumberFormat('fa-IR', {minimumIntegerDigits: 2}).format(Math.floor(value / 60)) + ':' + new Intl.NumberFormat('fa-IR', {minimumIntegerDigits: 2}).format(value % 60);
        const button = card.querySelector('[data-auth-resend]');
        button.disabled = resend > 0 || state.busy;
        button.textContent = resend ? 'ارسال دوباره تا ' + format(resend) : 'ارسال دوباره کد';
        card.querySelector('[data-auth-expiry]').textContent = expiry ? 'اعتبار کد: ' + format(expiry) : 'اعتبار کد تمام شد.';
    }
    async function context(card, restore = false) {
        const state = states.get(card);
        if (state.pendingContext) return state.pendingContext;
        state.pendingContext = (async () => {
            const response = await fetch(revayatAuth.endpoint + '?action=revayat_auth_context', {credentials: 'same-origin', cache: 'no-store'});
            const result = await response.json();
            if (!response.ok || !result.success) throw new Error('context');
            state.nonce = result.data.nonce;
            if (result.data.logged_in) { location.assign(state.redirect || '/'); return; }
            if (restore && state.panel === 'auth' && result.data.expires_at > result.data.server_time) applyChallenge(card, result.data, false);
        })();
        try { await state.pendingContext; } finally { state.pendingContext = null; }
    }
    async function send(card, data) {
        const state = states.get(card);
        if (state.busy) return;
        state.busy = true; card.setAttribute('aria-busy', 'true');
        card.querySelectorAll('button[type=submit]').forEach(b => b.disabled = true);
        notice(card, 'در حال بررسی درخواست…');
        try {
            await context(card);
            data.set('action', 'revayat_auth_flow'); data.set('nonce', state.nonce); data.set('redirect_to', state.redirect || '');
            const controller = new AbortController(), timeout = setTimeout(() => controller.abort(), 30000);
            let response;
            try { response = await fetch(revayatAuth.endpoint, {method: 'POST', body: data, credentials: 'same-origin', cache: 'no-store', signal: controller.signal}); }
            finally { clearTimeout(timeout); }
            const result = await response.json();
            if (!result.success) {
                notice(card, result.data?.message || 'درخواست انجام نشد؛ دوباره تلاش کنید.', true);
                if (result.data?.code === 'invalid_nonce') state.nonce = '';
                return;
            }
            const output = result.data;
            if (output.redirect) { location.assign(output.redirect); return; }
            notice(card, output.message || 'کد تأیید ارسال شد.');
            if (output.step === 'verify') applyChallenge(card, output);
            else if (output.step) panel(card, output.step);
        } catch (_) { notice(card, 'ارتباط کامل نشد. اطلاعات شما حفظ شده؛ دوباره تلاش کنید.', true); }
        finally { state.busy = false; card.removeAttribute('aria-busy'); card.querySelectorAll('button[type=submit]').forEach(b => b.disabled = false); tick(card); }
    }
    function init(card, redirect = '') {
        if (states.has(card)) return;
        states.set(card, {nonce: '', redirect, busy: false, panel: 'auth'});
        card.hidden = false;
        card.addEventListener('input', e => { if (e.target.matches('[data-digits]')) e.target.value = digits(e.target.value); });
        card.addEventListener('click', e => {
            const state = states.get(card);
            const method = e.target.closest('[data-auth-method]');
            if (method && !state.busy) { notice(card, ''); panel(card, method.dataset.authMethod); }
            if (e.target.closest('[data-auth-change]') && !state.busy) { notice(card, ''); panel(card, state.challenge?.context === 'recover' ? 'recover' : 'auth'); }
            if (e.target.closest('[data-auth-resend]')) { const data = new FormData(); data.set('operation', 'resend'); send(card, data); }
        });
        card.addEventListener('submit', e => {
            e.preventDefault(); const data = new FormData(e.target); data.set('operation', e.target.dataset.operation);
            ['mobile', 'otp_code'].forEach(name => { if (data.has(name)) data.set(name, digits(data.get(name))); });
            send(card, data);
        });
        card.querySelector('[role=tablist]')?.addEventListener('keydown', e => {
            if (['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(e.key)) {
                e.preventDefault(); const buttons = [...card.querySelectorAll('[role=tab]')];
                const next = e.key === 'Home' ? 0 : e.key === 'End' ? buttons.length - 1 : (buttons.indexOf(document.activeElement) + 1) % buttons.length;
                buttons[next].click(); buttons[next].focus();
            }
        });
        context(card, true).catch(() => notice(card, 'اتصال اولیه برقرار نشد؛ هنگام ارسال دوباره تلاش می‌کنیم.', true));
    }
    document.querySelectorAll('[data-rv-auth]').forEach(card => {
        const url = new URL(location.href); init(card, url.searchParams.get('redirect_to') || '');
        if (['password', 'recover'].includes(url.searchParams.get('tab'))) panel(card, url.searchParams.get('tab'), false);
    });
    document.addEventListener('click', e => {
        const link = e.target.closest('a[href]');
        if (!link || e.ctrlKey || e.metaKey || e.shiftKey || e.altKey || e.button !== 0 || link.target === '_blank') return;
        const url = new URL(link.href); if (!isAuth(url)) return;
        const template = document.querySelector('#rv-auth-template'); if (!template || !window.HTMLDialogElement) return;
        e.preventDefault(); opener = link;
        if (!dialog) {
            dialog = document.createElement('dialog'); dialog.className = 'rv-access-dialog'; dialog.setAttribute('aria-label', 'ورود یا ثبت‌نام');
            const close = document.createElement('button'); close.type = 'button'; close.className = 'rv-access-close'; close.setAttribute('aria-label', 'بستن'); close.textContent = '×';
            close.addEventListener('click', () => dialog.close()); dialog.append(close, template.content.cloneNode(true)); document.body.append(dialog);
            dialog.addEventListener('close', () => { document.body.classList.remove('rv-auth-modal-open'); opener?.focus(); });
            dialog.addEventListener('click', event => { if (event.target === dialog) dialog.close(); });
        }
        dialog.showModal(); document.body.classList.add('rv-auth-modal-open');
        const card = dialog.querySelector('[data-rv-auth]');
        init(card, url.searchParams.get('redirect_to') || location.href);
        if (!states.get(card).busy) {
            states.get(card).redirect = url.searchParams.get('redirect_to') || (url.searchParams.get('tab') === 'upgrade' ? new URL('dashboard/?view=situation-access', authURL.origin + authURL.pathname.replace(/auth\/?$/, '')).href : location.href);
            panel(card, ['password', 'recover'].includes(url.searchParams.get('tab')) ? url.searchParams.get('tab') : 'auth');
        }
    });
    setInterval(() => document.querySelectorAll('[data-rv-auth]').forEach(tick), 1000);
})();
