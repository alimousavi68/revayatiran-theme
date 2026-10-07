<?php
/** Small native editor helpers; all values remain editable post content. */
if (!defined('ABSPATH')) { exit; }
function revayat_dossier_editor_plugins($plugins) {
    if (get_post_type() === 'special_dossier' && current_user_can('edit_posts')) {
        $plugins['revayat_dossier'] = get_template_directory_uri() . '/assets/js/dossier-editor.js?ver=' . filemtime(get_template_directory() . '/assets/js/dossier-editor.js');
    }
    return $plugins;
}
function revayat_dossier_editor_buttons($buttons) {
    if (get_post_type() === 'special_dossier') { $buttons[] = 'revayat_stats'; $buttons[] = 'revayat_table'; }
    return $buttons;
}
add_filter('mce_external_plugins', 'revayat_dossier_editor_plugins');
add_filter('mce_buttons', 'revayat_dossier_editor_buttons');
add_filter('tiny_mce_before_init', function ($settings) {
    if (get_post_type() === 'special_dossier') {
        // Preserve semantic stats and expandable chart tables when editing existing posts.
        $settings['extended_valid_elements'] = ($settings['extended_valid_elements'] ?? '') . ',dl[class],dt,dd,details[class|open],summary';
        $settings['valid_children'] = ($settings['valid_children'] ?? '') . ',+dl[div],+div[dt|dd]';
    }
    return $settings;
});
add_action('after_setup_theme', function () { add_editor_style('assets/css/modules/dossier-story.css'); });
