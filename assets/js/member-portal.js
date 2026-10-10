(() => {
  'use strict';
  const dirty = new Set();
  window.addEventListener('beforeunload', event => {
    if (dirty.size) { event.preventDefault(); event.returnValue = ''; }
  });
  document.querySelectorAll('[data-rv-form]').forEach(form => {
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
      tell(automatic ? 'در حال ذخیره خودکار…' : 'در حال ثبت…');
      const controller = new AbortController();
      const timeout = setTimeout(() => controller.abort(), 30000);
      try {
        const response = await fetch(revayatMember.endpoint, {method: 'POST', body: data, credentials: 'same-origin', signal: controller.signal});
        let payload;
        try { payload = await response.json(); } catch (_) { throw new Error('پاسخ سرور معتبر نیست؛ متن شما در فرم محفوظ است. صفحه را نبندید.'); }
        if (!payload.success) throw new Error(payload.data?.message || 'ثبت انجام نشد. دوباره تلاش کنید.');
        const result = payload.data;
        for (const [key, value] of Object.entries({version: result.version, post_id: result.id, request_key: result.request_key})) {
          if (value !== undefined && form.elements[key]) form.elements[key].value = value;
        }
        if (result.id && form.hasAttribute('data-note-editor')) {
          const url = new URL(location.href); url.searchParams.set('post_id', result.id); history.replaceState(null, '', url);
        }
        if (sentRevision === revision) dirty.delete(form);
        tell(result.message || 'ذخیره شد.');
        if (result.redirect) { dirty.delete(form); location.assign(result.redirect); }
        else if (result.reload) { dirty.delete(form); location.reload(); }
      } catch (error) { tell(error.name === 'AbortError' ? 'پاسخ سرور دیر رسید؛ با همان فرم دوباره تلاش کنید.' : error.message, true); }
      finally { clearTimeout(timeout); busy = false; form.removeAttribute('aria-busy'); buttons.forEach(button => button.disabled = false); }
    }
    form.addEventListener('submit', event => { event.preventDefault(); save(event.submitter?.name === 'mode' ? event.submitter.value : ''); });
  });
})();
