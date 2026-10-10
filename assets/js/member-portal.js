(() => {
  'use strict';
  const dirty = new Set();
  const labels = {full_name:'نام و نام خانوادگی',display_name:'نام نمایشی',bio:'معرفی حرفه‌ای',expertise:'حوزه تخصصی',sample_url:'پیوند نمونه تحلیل',national_id:'کد ملی',portrait:'عکس پرسنلی',national_card:'تصویر کارت ملی',consent:'تأیید شرایط'};
  const validationMessage = field => {
    const label = labels[field.name] || 'این فیلد';
    if (field.validity.valueMissing) return field.type === 'checkbox' ? 'برای ارسال درخواست، صحت اطلاعات و شرایط را تأیید کنید.' : `${label} را وارد کنید.`;
    const length = Array.from(field.value || '').length;
    if (field.minLength > 0 && length < field.minLength) return `${label} باید حداقل ${field.minLength.toLocaleString('fa-IR')} نویسه باشد؛ اکنون ${length.toLocaleString('fa-IR')} نویسه وارد کرده‌اید.`;
    if (field.validity.typeMismatch) return field.type === 'url' ? 'پیوند معتبر با http:// یا https:// وارد کنید.' : `${label} را با قالب معتبر وارد کنید.`;
    if (field.validity.patternMismatch) return `${label} را با قالب خواسته‌شده وارد کنید.`;
    if (field.validity.tooLong) return `${label} طولانی‌تر از حد مجاز است.`;
    if (field.validity.rangeUnderflow || field.validity.rangeOverflow || field.validity.badInput) return 'مقدار عددی را در محدوده مشخص‌شده وارد کنید.';
    return field.validity.valid ? '' : `${label} معتبر نیست؛ مقدار آن را بررسی کنید.`;
  };
  const applyApplication = (type, item) => {
    document.querySelectorAll(`[data-application-type="${type}"] [data-application-state]`).forEach(e => e.textContent = item.label);
    document.querySelectorAll(`[data-application-type="${type}"] [data-application-link]`).forEach(e => {e.href = item.url; e.textContent = item.state === 'none' ? 'شرایط و ثبت درخواست ←' : 'مشاهده و پیگیری ←';});
    const form = document.querySelector(`[data-application-form="${type}"]`);
    if (form && item.version < +form.elements.version.value && !['pending','approved'].includes(item.state)) return;
    if (form && (['pending','approved'].includes(item.state) || +form.elements.version.value !== item.version)) {
      form.dataset.stale = 'true';
      form.querySelectorAll('button').forEach(b => b.disabled = true);
      const track = form.querySelector('[data-application-track]'); if (track) {track.hidden = false; track.href = item.url;}
      const status = form.querySelector('[data-form-status]');
      if (status) {status.hidden = false; status.textContent = ['pending','approved'].includes(item.state) ? 'درخواست فعال دارید؛ ارسال دوباره لازم نیست. برای دیدن وضعیت، پیگیری درخواست را بزنید.' : 'وضعیت درخواست تغییر کرده است؛ برای ادامه، پیگیری درخواست را بزنید. متن این فرم حفظ شده است.';}
    }
  };
  let refreshing = false;
  async function refreshApplications() {
    if (refreshing || document.hidden || !document.querySelector('[data-application-type]') || !window.revayatMember?.statusNonce) return;
    refreshing = true;
    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(),8000);
    try {
      const data = new FormData(); data.set('action','revayat_application_status'); data.set('rv_nonce',revayatMember.statusNonce);
      const r = await fetch(revayatMember.endpoint,{method:'POST',body:data,credentials:'same-origin',cache:'no-store',signal:controller.signal});
      const payload = await r.json(); if (payload.success) Object.entries(payload.data).forEach(([type,item]) => applyApplication(type,item));
    } catch (_) { /* Existing server-rendered state remains; the write handler still guards duplicates. */ }
    finally {clearTimeout(timeout); refreshing = false;}
  }
  window.addEventListener('pageshow',refreshApplications);
  window.addEventListener('focus',refreshApplications);
  window.addEventListener('storage',event => {if (event.key === 'revayat-applications-changed') refreshApplications();});
  document.addEventListener('visibilitychange',refreshApplications);
  window.addEventListener('beforeunload', event => {
    if (dirty.size) { event.preventDefault(); event.returnValue = ''; }
  });
  document.querySelectorAll('[data-rv-form]').forEach(form => {
    form.noValidate = true;
    let busy = false, revision = 0, timer, previewURL;
    const status = form.querySelector('[data-form-status]');
    const tell = (message, error = false) => {
      if (!status) return;
      status.hidden = false; status.textContent = message;
      status.classList.toggle('is-error', error);
    };
    const changed = () => {
      revision++;
      if (form.hasAttribute('data-dirty-guard')) dirty.add(form);
      clearTimeout(timer);
      if (form.hasAttribute('data-note-editor')) timer = setTimeout(() => {
        const title = form.elements.post_title.value.trim();
        if (!busy && title.length >= 5) save('draft', true);
      }, 20000);
    };
    // Reveal invalid fields before the browser attempts to focus a collapsed panel.
    form.addEventListener('invalid', event => {
      const panel = event.target.closest('details');
      if (panel) panel.open = true;
      event.preventDefault();
      tell(validationMessage(event.target) || 'موارد ضروری فرم را تکمیل کنید.', true);
    }, true);
    form.addEventListener('input', changed);
    form.addEventListener('change', changed);
    if (form.hasAttribute('data-note-editor')) {
      const attach = editor => { if (editor.id === 'rv-note-content') editor.on('input change undo redo', changed); };
      if (window.tinymce) {
        tinymce.editors.forEach(attach);
        tinymce.on('AddEditor', event => attach(event.editor));
      }
    }
    form.querySelector('[data-avatar-input]')?.addEventListener('change', event => {
      const file = event.target.files[0];
      if (!file) return;
      if (file.size > 2 * 1024 * 1024 || !['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
        tell('تصویر JPG، PNG یا WebP با حجم حداکثر ۲ مگابایت انتخاب کنید.', true); event.target.value = ''; return;
      }
      if (previewURL) URL.revokeObjectURL(previewURL);
      previewURL = URL.createObjectURL(file);
      form.querySelector('[data-avatar-preview]').src = previewURL;
    });
    async function save(mode, automatic = false) {
      if (busy) return;
      busy = true; clearTimeout(timer);
      window.tinymce?.triggerSave();
      const sentRevision = revision;
      const data = new FormData(form);
      if (mode) data.set('mode', mode);
      const buttons = [...form.querySelectorAll('button')];
      buttons.forEach(button => button.disabled = true);
      form.setAttribute('aria-busy', 'true');
      const originalLabels = buttons.map(b => b.textContent);
      buttons.forEach(b => {if (b.value === mode || (buttons.length === 1)) b.textContent = mode === 'submit' ? 'در حال ارسال…' : 'در حال ذخیره…';});
      tell(automatic ? 'در حال ذخیره خودکار…' : mode === 'submit' ? 'در حال ارسال درخواست؛ لطفاً منتظر بمانید و دوباره ارسال نکنید…' : 'در حال ذخیره…');
      let navigating = false;
      const controller = new AbortController();
      const timeout = setTimeout(() => controller.abort(), 30000);
      try {
        const response = await fetch(revayatMember.endpoint, {method: 'POST', body: data, credentials: 'same-origin', signal: controller.signal});
        let payload;
        try { payload = await response.json(); } catch (_) { throw new Error('پاسخ سرور معتبر نیست؛ متن شما در فرم محفوظ است. صفحه را نبندید.'); }
        if (!payload.success) {
          if (payload.data?.application && form.dataset.applicationForm) applyApplication(form.dataset.applicationForm,payload.data.application);
          const field = payload.data?.field && form.elements.namedItem(payload.data.field);
          if (field?.focus) { const panel = field.closest('details'); if (panel) panel.open = true; field.focus(); }
          throw new Error(payload.data?.message || 'ثبت انجام نشد. دوباره تلاش کنید.');
        }
        const result = payload.data;
        for (const [key, value] of Object.entries({version: result.version, post_id: result.id, request_key: result.request_key})) {
          if (value !== undefined && form.elements[key]) form.elements[key].value = value;
        }
        if (result.id && form.hasAttribute('data-note-editor')) {
          const url = new URL(location.href); url.searchParams.set('post_id', result.id); history.replaceState(null, '', url);
        }
        if (sentRevision === revision) {
          dirty.delete(form);
          const avatar = form.querySelector('[data-avatar-preview]');
          if (avatar && result.avatar_url) {
            avatar.src = result.avatar_url;
            const upload = form.querySelector('[data-avatar-input]');
            if (upload) upload.value = '';
            if (form.elements.remove_avatar) form.elements.remove_avatar.checked = false;
            if (previewURL) { URL.revokeObjectURL(previewURL); previewURL = undefined; }
          }
        }
        tell(result.message || 'ذخیره شد.');
        if (form.dataset.applicationForm) {try {localStorage.setItem('revayat-applications-changed',String(Date.now()));} catch (_) {}}
        if (result.redirect) { navigating = true; dirty.delete(form); location.assign(result.redirect); }
        else if (result.reload) { navigating = true; dirty.delete(form); location.reload(); }
      } catch (error) {
        if (form.dataset.applicationForm) {
          await refreshApplications();
          if (error.name === 'AbortError' || error.name === 'TypeError') {const track = form.querySelector('[data-application-track]'); if (track) track.hidden = false;}
        }
        tell(error.name === 'AbortError' ? 'پاسخ سرور دیر رسید؛ ممکن است درخواست ثبت شده باشد. پیش از ارسال دوباره، پیگیری درخواست را بررسی کنید.' : error.message, true);
      }
      finally { clearTimeout(timeout); busy = false; form.removeAttribute('aria-busy'); buttons.forEach((button,i) => {button.textContent = originalLabels[i]; button.disabled = navigating || form.dataset.stale === 'true';}); }
    }
    form.addEventListener('submit', event => {
      event.preventDefault(); if (busy || form.dataset.stale === 'true') return;
      window.tinymce?.triggerSave();
      if (!event.submitter?.formNoValidate) {
        for (const field of form.elements) {
          if (!field.willValidate) continue;
          const message = validationMessage(field);
          if (message) {const panel = field.closest('details'); if (panel) panel.open = true; tell(message,true); field.setAttribute('aria-invalid','true'); field.focus(); field.scrollIntoView({block:'center',behavior:'auto'}); return;}
          field.removeAttribute('aria-invalid');
        }
      }
      save(event.submitter?.name === 'mode' ? event.submitter.value : '');
    });
  });
})();
