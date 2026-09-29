/* Registry-backed source checklists and independent taxonomy filter rows. */
(function (api, config) {
    'use strict';
    const el = (tag, className, text) => {
        const node = document.createElement(tag);
        if (className) node.className = className;
        if (text !== undefined) node.textContent = text;
        return node;
    };
    const button = (text, className) => { const node = el('button', className || 'button', text); node.type = 'button'; return node; };
    const csv = value => String(value || '').split(',').filter(Boolean);
    function sourceChoices(root, setting, field) {
        const search = el('input', 'rv-query-search');
        search.type = 'search'; search.placeholder = 'جستجوی نوع محتوا'; search.setAttribute('aria-label', field.label);
        const list = el('div', 'rv-query-options');
        const selected = new Set(csv(setting.get()));
        const inputs = [];
        Object.entries(field.choices).forEach(([name, label]) => {
            const row = el('label', 'rv-query-option');
            const input = el('input'); input.type = 'checkbox'; input.value = name; input.checked = selected.has(name);
            row.append(input, el('span', '', label)); list.append(row); inputs.push(input);
            input.addEventListener('change', () => {
                if (input.checked) selected.add(name); else selected.delete(name);
                setting.set(Array.from(selected).join(','));
            });
        });
        setting.bind(value => { selected.clear(); csv(value).forEach(v => selected.add(v)); inputs.forEach(input => { input.checked = selected.has(input.value); }); });
        search.addEventListener('input', () => { list.querySelectorAll('label').forEach(row => { row.hidden = !row.textContent.toLocaleLowerCase().includes(search.value.toLocaleLowerCase()); }); });
        const clear = button('پاک‌کردن انتخاب‌ها', 'button-link rv-query-clear');
        clear.addEventListener('click', () => setting.set(''));
        root.append(search, list, clear);
    }

    function termPicker(root, section, taxonomy, title, initial, onChange) {
        const fieldset = el('fieldset', 'rv-term-picker');
        const legend = el('legend', '', title);
        const chosen = el('div', 'rv-term-chips');
        const search = el('input', 'rv-query-search'); search.type = 'search'; search.placeholder = 'جستجوی نام'; search.setAttribute('aria-label', title + ' — جستجو');
        const status = el('p', 'rv-query-status'); status.setAttribute('role', 'status');
        const results = el('div', 'rv-query-options');
        const more = button('گزینه‌های بیشتر'); more.hidden = true;
        const selected = new Set(initial.map(Number));
        const labels = new Map();
        let page = 1, abort = null, sequence = 0, timer = null, disposed = false;
        function chips() {
            chosen.replaceChildren();
            selected.forEach(id => {
                const chip = button((labels.get(id) || 'گزینه انتخاب‌شده ' + id) + ' ×', 'rv-term-chip');
                chip.setAttribute('aria-label', 'حذف ' + (labels.get(id) || id));
                chip.addEventListener('click', () => { selected.delete(id); chips(); results.querySelectorAll('input').forEach(input => { input.checked = selected.has(Number(input.value)); }); onChange(Array.from(selected)); });
                chosen.append(chip);
            });
            if (results.children.length) status.textContent = selected.size + ' گزینه انتخاب شده';
        }
        async function load(append = false) {
            if (abort) abort.abort();
            abort = new AbortController();
            const ticket = ++sequence;
            status.textContent = 'در حال دریافت گزینه‌ها…'; more.disabled = true;
            if (!append) results.replaceChildren();
            try {
                const data = new URLSearchParams({action:'revayat_customizer_terms',nonce:config.nonce,section,taxonomy,page:String(page),search:search.value,selected:Array.from(selected).join(',')});
                const response = await fetch(config.ajaxUrl, {method:'POST',credentials:'same-origin',body:data,signal:abort.signal});
                const payload = await response.json();
                if (disposed || ticket !== sequence) return;
                if (!response.ok || !payload.success) throw new Error(payload.data?.message || 'دریافت گزینه‌ها ناموفق بود.');
                payload.data.selected.forEach(term => labels.set(Number(term.id), term.name));
                payload.data.terms.forEach(term => {
                    labels.set(Number(term.id), term.name);
                    const row = el('label', 'rv-query-option');
                    const input = el('input'); input.type = 'checkbox'; input.value = term.id; input.checked = selected.has(Number(term.id));
                    row.append(input, el('span', '', term.name), el('small', '', term.slug)); results.append(row);
                    input.addEventListener('change', () => {
                        const id = Number(term.id);
                        if (input.checked) selected.add(id); else selected.delete(id);
                        chips(); onChange(Array.from(selected));
                    });
                });
                chips();
                more.hidden = !payload.data.more;
                status.textContent = results.children.length ? selected.size + ' گزینه انتخاب شده' : 'گزینه‌ای برای این جستجو وجود ندارد.';
            } catch (error) {
                if (error.name !== 'AbortError' && !disposed) status.textContent = error.message;
            } finally { if (!disposed && ticket === sequence) more.disabled = false; }
        }
        search.addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(() => { page = 1; load(); }, 250); });
        more.addEventListener('click', () => { page++; load(true); });
        fieldset.append(legend, chosen, search, status, results, more); root.append(fieldset);
        chips(); load();
        return () => { disposed = true; clearTimeout(timer); if (abort) abort.abort(); };
    }

    function filtersControl(root, setting, field) {
        const section = field.section;
        const catalog = config.catalogs[section];
        const list = el('div', 'rv-tax-filter-list');
        const add = button('افزودن فیلتر دسته‌بندی / تاکسونومی');
        let rules = [], disposers = [], writing = false;
        const parse = value => { try { const rows = JSON.parse(value || '[]'); return Array.isArray(rows) ? rows : []; } catch { return []; } };
        rules = parse(setting.get());
        const save = () => { writing = true; setting.set(JSON.stringify(rules.filter(rule => rule.taxonomy))); writing = false; };
        function sourceTypes() {
            const all = api('revayat_' + section + '_source_mode').get() === 'all';
            const included = all ? Object.keys(catalog.types) : csv(api('revayat_' + section + '_post_types').get());
            const excluded = csv(api('revayat_' + section + '_excluded_post_types').get());
            return included.filter(type => !excluded.includes(type));
        }
        function render() {
            disposers.forEach(dispose => dispose()); disposers = [];
            list.replaceChildren();
            const types = sourceTypes();
            rules.forEach((rule, index) => {
                const row = el('fieldset', 'rv-tax-filter'); row.dataset.filterIndex = index;
                row.append(el('legend', '', 'فیلتر ' + (index + 1)));
                const label = el('label', 'rv-query-label', 'دسته‌بندی یا تاکسونومی');
                const select = el('select', 'rv-taxonomy-select'); select.setAttribute('aria-label', 'تاکسونومی فیلتر ' + (index + 1));
                select.append(new Option('انتخاب کنید…', ''));
                Object.entries(catalog.taxonomies).forEach(([name, tax]) => {
                    if (tax.types.some(type => types.includes(type))) select.append(new Option(tax.label + ' (' + name + ')', name));
                });
                if (rule.taxonomy && !Array.from(select.options).some(option => option.value === rule.taxonomy)) {
                    select.append(new Option((catalog.taxonomies[rule.taxonomy]?.label || rule.taxonomy) + ' — ناسازگار با منبع انتخابی', rule.taxonomy));
                    row.append(el('p', 'rv-query-error', 'این فیلتر با منبع فعلی سازگار نیست؛ منبع را تغییر دهید یا فیلتر را حذف کنید.'));
                }
                select.value = rule.taxonomy || '';
                select.addEventListener('change', () => {
                    if (!select.value) { rules.splice(index, 1); } else rules[index] = {taxonomy:select.value,include:[],exclude:[],operator:'IN',children:true};
                    save(); render();
                });
                label.append(select); row.append(label);
                const remove = button('حذف فیلتر', 'button-link-delete rv-filter-remove');
                remove.addEventListener('click', () => { rules.splice(index, 1); save(); render(); });
                row.append(remove);
                if (rule.taxonomy && catalog.taxonomies[rule.taxonomy]) {
                    disposers.push(termPicker(row, section, rule.taxonomy, 'موارد مجاز', rule.include || [], value => { rule.include = value; save(); }));
                    disposers.push(termPicker(row, section, rule.taxonomy, 'موارد مستثنا', rule.exclude || [], value => { rule.exclude = value; save(); }));
                    const operatorLabel = el('label', 'rv-query-label', 'تطبیق موارد مجاز');
                    const operator = el('select'); operator.append(new Option('حداقل یکی از موارد مجاز', 'IN'), new Option('همه موارد مجاز هم‌زمان', 'AND')); operator.value = rule.operator || 'IN';
                    operator.addEventListener('change', () => { rule.operator = operator.value; save(); }); operatorLabel.append(operator); row.append(operatorLabel);
                    if (catalog.taxonomies[rule.taxonomy].hierarchical) {
                        const childrenLabel = el('label', 'rv-query-option'); const children = el('input'); children.type = 'checkbox'; children.checked = rule.children !== false;
                        children.addEventListener('change', () => { rule.children = children.checked; save(); }); childrenLabel.append(children, el('span', '', 'زیرشاخه‌ها هم شامل شوند')); row.append(childrenLabel);
                    }
                }
                list.append(row);
            });
            add.disabled = rules.length >= 12 || !types.length;
        }
        // A pending empty row is UI state only; it is never saved as an invalid filter.
        add.addEventListener('click', () => { rules.push({taxonomy:'',include:[],exclude:[],operator:'IN',children:true}); render(); });
        setting.bind(value => { if (!writing) { rules = parse(value); render(); } });
        ['source_mode','post_types','excluded_post_types'].forEach(key => api('revayat_' + section + '_' + key).bind(render));
        root.append(list, add); render();
    }

    api.bind('ready', () => {
        Object.entries(revayatCustomizerControls.schema).forEach(([key, field]) => {
            if (!['multicheck','taxonomy_filters'].includes(field.type)) return;
            const control = api.control('revayat_' + key);
            if (!control) return;
            const root = control.container[0].querySelector('.rv-query-control');
            if (!root) return;
            const setting = api('revayat_' + key);
            if (field.type === 'multicheck') sourceChoices(root, setting, field); else filtersControl(root, setting, field);
        });
        Object.keys(config.catalogs).forEach(section => {
            const update = () => {
                api.control('revayat_' + section + '_post_types').active.set(api('revayat_' + section + '_source_mode').get() !== 'all');
                const control = api.control('revayat_' + section + '_use_default_filter');
                if (control) {
                    const types = api('revayat_' + section + '_source_mode').get() === 'all' ? Object.keys(config.catalogs[section].types) : csv(api('revayat_' + section + '_post_types').get());
                    control.active.set(types.includes('post') && !csv(api('revayat_' + section + '_excluded_post_types').get()).includes('post'));
                }
            };
            ['source_mode','post_types','excluded_post_types'].forEach(key => api('revayat_' + section + '_' + key).bind(update)); update();
        });
    });
})(wp.customize, revayatQueryControls);
