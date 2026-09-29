(function (api, config) {
    'use strict';
    const values = {};
    const style = document.createElement('style');
    style.id = 'revayat-live-customizer';
    document.head.append(style);
    const scoped = (scope, selectors) => selectors.split(',').map(s => scope + ' ' + s.trim()).join(',');
    const dark = hex => '#' + hex.slice(1).match(/.{2}/g).map(v => Math.round(parseInt(v, 16) * 0.75).toString(16).padStart(2, '0')).join('');
    const rgb = hex => hex.slice(1).match(/.{2}/g).map(v => parseInt(v, 16)).join(', ');
    function normalize(value, field) {
        if (field.type === 'color') {
            if (!value || !/^#(?:[0-9a-f]{3}|[0-9a-f]{6})$/i.test(value)) return field.default;
            return value.length === 4 ? '#' + value.slice(1).split('').map(c => c + c).join('') : value;
        }
        if (field.choices && !(value in field.choices)) return field.default;
        if (field.type === 'number') {
            const number = Number(value);
            return Number.isFinite(number) ? Math.max(field.min, Math.min(field.max, Math.trunc(number))) : field.default;
        }
        if (field.type === 'checkbox') return value === true || value === 1 || value === '1';
        return value;
    }
    const get = key => values[key] === undefined ? config.schema[key].default : values[key];
    function render() {
        const primary = get('primary');
        let css = ':root{--primary:' + primary + ';--primary-dark:' + dark(primary) + ';--accent:' + primary + ';--accent-rgb:' + rgb(primary) + ';--accent-strong:' + get('secondary') + ';--header-backdrop:' + get('header_backdrop') + ';--container:' + get('container_width') + 'px;}';
        css += 'body,button,input,select,textarea,.main-nav{font-family:"' + get('body_font') + '",sans-serif!important;}h1,h2,h3,h4,h5,h6{font-family:"' + get('heading_font') + '",sans-serif!important;}';
        // Replace saved CSS so toggling a saved hidden component back on also works.
        const saved = document.getElementById('revayat-theme-root-inline-css');
        if (saved) saved.disabled = true;
        Object.entries(config.schema).forEach(([key, field]) => {
            if (field.section === 'general') return;
            const scope = '#homepage-content #' + field.section;
            const value = get(key);
            const suffix = key.slice(field.section.length + 1);
            if (field.selector && !value) css += scoped(scope, field.selector) + '{display:none!important;}';
            if (suffix === 'accent' && value) css += scope + '{--primary:' + value + ';--accent:' + value + ';--accent-rgb:' + rgb(value) + ';--accent-gradient:linear-gradient(135deg,var(--primary) 0%,var(--accent-strong) 100%);}';
            if (suffix === 'background' && value) css += 'html:not([data-theme="dark"]) ' + scope + '{background:' + value + '!important;}';
            if (suffix === 'dark_background' && value) css += 'html[data-theme="dark"] ' + scope + '{background:' + value + '!important;}';
            const spacing = {compact:16, normal:32, relaxed:56};
            if (suffix === 'spacing' && value in spacing) css += scope + '{padding-block:' + spacing[value] + 'px!important;}';
            const size = {small:20, medium:26, large:32};
            if (suffix === 'title_size' && value in size) css += scoped(scope, '.section-head h2, .sr-sidebar__title, .hero-feature-title') + '{font-size:' + size[value] + 'px!important;}';
            const radius = {square:0, soft:8, round:20};
            if (suffix === 'radius' && value in radius) css += scoped(scope, 'article, .story-card, .an-hero--editorial') + '{border-radius:' + radius[value] + 'px!important;}';
        });
        config.layout.forEach(rule => {
            if (!rule.hidden.every(key => !get(rule.section + '_show_' + key))) return;
            const scope = '#homepage-content #' + rule.section;
            const body = rule.css.split('|').map(part => scope + part).join('');
            css += rule.media ? '@media ' + rule.media + '{' + body + '}' : body;
        });
        style.textContent = css;
        window.dispatchEvent(new Event('resize'));
    }
    let scheduled = false;
    Object.entries(config.schema).forEach(([key, field]) => {
        api('revayat_' + key, setting => {
            values[key] = normalize(setting.get(), field);
            if ((field.transport || 'postMessage') !== 'postMessage') return;
            setting.bind(value => {
                values[key] = normalize(value, field);
                if (!scheduled) {
                    scheduled = true;
                    requestAnimationFrame(() => { scheduled = false; render(); });
                }
            });
        });
    });
})(wp.customize, revayatCustomizerPreview);
