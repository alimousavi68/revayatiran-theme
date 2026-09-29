/* Native sections remain usable if this enhancement cannot load. */
(function (api, config) {
    'use strict';
    api.bind('ready', function () {
        const groups = new Map();
        const placements = [];
        Object.entries(config.schema).forEach(([key, field]) => {
            const control = api.control('revayat_' + key);
            if (!control) return;
            const container = control.container[0];
            const section = container.parentElement;
            if (!section) return;
            const groupKey = field.section + ':' + field.group;
            if (!groups.has(groupKey)) {
                const item = document.createElement('li');
                item.className = 'rv-customizer-group';
                item.dataset.rvGroup = field.group;
                item.dataset.rvSection = field.section;
                const details = document.createElement('details');
                details.open = field.group === 'display' || field.section === 'general';
                const summary = document.createElement('summary');
                summary.textContent = config.groups[field.group];
                const list = document.createElement('ul');
                list.className = 'rv-customizer-group__controls';
                details.append(summary, list);
                item.append(details);
                section.insertBefore(item, container);
                groups.set(groupKey, list);
                // Opening a group closes its siblings, with native keyboard support.
                details.addEventListener('toggle', () => {
                    if (!details.open) return;
                    details.parentElement.parentElement.querySelectorAll(':scope > .rv-customizer-group > details').forEach(other => {
                        if (other !== details) other.open = false;
                    });
                });
            }
            const list = groups.get(groupKey);
            list.append(container);
            placements.push({key, field});
            control.focus = (function (original) {
                return function () {
                    const details = container.closest('details');
                    if (details) details.open = true;
                    return original.apply(control, arguments);
                };
            })(control.focus);
        });
        // WordPress reorders controls when sections embed/expand. Restore grouping
        // after that native reflow, moving only nodes that left their group.
        let scheduled = false;
        const observer = new MutationObserver(() => {
            if (scheduled) return;
            scheduled = true;
            queueMicrotask(() => {
                scheduled = false;
                placements.forEach(({key, field}) => {
                    const container = document.getElementById('customize-control-revayat_' + key);
                    const group = document.querySelector('.rv-customizer-group[data-rv-section="' + field.section + '"][data-rv-group="' + field.group + '"]');
                    const list = group && group.querySelector('.rv-customizer-group__controls');
                    if (container && list && container.parentElement !== list) list.append(container);
                });
            });
        });
        observer.observe(document.documentElement, {childList:true, subtree:true});
        const link = api.control('revayat_home_link');
        if (link) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'button button-primary rv-home-link';
            button.textContent = 'مدیریت سکشن‌های صفحه اصلی';
            button.addEventListener('click', () => api.panel('revayat_home').focus());
            link.container[0].append(button);
        }
    });
})(wp.customize, revayatCustomizerControls);
