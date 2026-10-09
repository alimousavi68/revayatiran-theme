<?php
/** Select committed CSS outputs; never concatenate or write files during a request. */
if (!defined('ABSPATH')) { exit; }

function revayat_css_view(): string {
    if (is_front_page() || is_home()) { return 'home'; }
    if (is_page_template('page-templates/template-dashboard.php')) { return 'dashboard'; }
    if (is_page_template('page-templates/template-auth.php')) { return 'auth'; }
    if (is_post_type_archive('analyst_post') || is_tax('analyst_field')) { return 'analyst-archive'; }
    if (is_tax('person_author')) { return 'author-archive'; }
    if (is_post_type_archive('special_dossier') || is_tax('dossier_topic')) { return 'dossier-archive'; }
    if (is_post_type_archive('situation_room') || is_tax('security_level')) { return 'room-archive'; }
    if (is_post_type_archive('media_observatory') || is_tax('observatory_badge')) { return 'observatory-archive'; }
    if (is_post_type_archive(['multimedia', 'person']) || is_tax('media_format')) { return 'media-archive'; }
    foreach (['special_dossier' => 'dossier', 'analyst_post' => 'analyst', 'person' => 'person',
        'multimedia' => 'media', 'media_observatory' => 'observatory', 'situation_room' => 'room', 'post' => 'post'] as $type => $view) {
        if (is_singular($type)) { return $view; }
    }
    return is_archive() ? 'archive' : 'public';
}

function revayat_enqueue_css_bundle(string $name, string $handle, array $dependencies = []): void {
    static $manifest = null;
    if (null === $manifest) { $manifest = require REVAYAT_THEME_DIR . '/assets/css/dist/manifest.php'; }
    if (!isset($manifest[$name])) { return; }
    $entry = $manifest[$name];
    wp_enqueue_style($handle, REVAYAT_THEME_URI . $entry['file'], $dependencies, $entry['version']);
}
