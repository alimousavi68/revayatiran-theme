/* Native Classic Editor controls: insert ordinary editable HTML, no stored defaults. */
(function () {
    'use strict';
    tinymce.PluginManager.add('revayat_dossier', function (editor) {
        editor.addButton('revayat_stats', {
            text: 'درج آمار', icon: false,
            onclick: function () {
                var fields = [];
                for (var i = 1; i <= 3; i++) {
                    fields.push({type: 'textbox', name: 'label' + i, label: 'عنوان شاخص ' + i});
                    fields.push({type: 'textbox', name: 'value' + i, label: 'مقدار شاخص ' + i});
                }
                editor.windowManager.open({title: 'خلاصه آماری؛ موارد خالی درج نمی‌شوند', body: fields,
                    onsubmit: function (event) {
                        var items = '';
                        for (var n = 1; n <= 3; n++) {
                            var label = event.data['label' + n].trim(), value = event.data['value' + n].trim();
                            if (label && value) {
                                items += '<div><dt>' + editor.dom.encode(label) + '</dt><dd>' + editor.dom.encode(value) + '</dd></div>';
                            }
                        }
                        if (items) { editor.insertContent('<dl class="rv-story-metrics">' + items + '</dl><p></p>'); }
                    }
                });
            }
        });
        editor.addButton('revayat_table', {
            text: 'درج جدول', icon: false,
            onclick: function () {
                editor.windowManager.open({title: 'جدول؛ متن خانه‌ها را پس از درج بنویسید', body: [
                    {type: 'textbox', name: 'rows', label: 'تعداد ردیف داده', value: '3'},
                    {type: 'textbox', name: 'columns', label: 'تعداد ستون', value: '3'}
                ], onsubmit: function (event) {
                    var rows = Math.min(20, Math.max(1, parseInt(event.data.rows, 10) || 3));
                    var columns = Math.min(8, Math.max(1, parseInt(event.data.columns, 10) || 3));
                    var html = '<table><thead><tr>';
                    for (var c = 0; c < columns; c++) { html += '<th scope="col">&nbsp;</th>'; }
                    html += '</tr></thead><tbody>';
                    for (var r = 0; r < rows; r++) {
                        html += '<tr>';
                        for (c = 0; c < columns; c++) { html += '<td>&nbsp;</td>'; }
                        html += '</tr>';
                    }
                    editor.insertContent(html + '</tbody></table><p></p>');
                }});
            }
        });
    });
}());
