<?php
/** Small delivery/metadata fixes; content and form processing remain native. */
if (!defined('ABSPATH')) { exit; }

/** Recognize both shortcode forms and CF7 blocks, including reusable blocks. */
function revayat_content_has_contact_form(string $content, int $depth = 0): bool {
    if (has_shortcode($content, 'contact-form-7') || has_block('contact-form-7/contact-form-selector', $content)) { return true; }
    if ($depth >= 5 || !has_blocks($content)) { return false; }
    $blocks = parse_blocks($content);
    while ($blocks) {
        $block = array_pop($blocks);
        if ('core/block' === $block['blockName'] && !empty($block['attrs']['ref'])) {
            $reusable = get_post((int) $block['attrs']['ref']);
            if ($reusable && revayat_content_has_contact_form($reusable->post_content, $depth + 1)) { return true; }
        }
        foreach ($block['innerBlocks'] ?? [] as $inner) { $blocks[] = $inner; }
    }
    return false;
}

/** Keep forms in active widgets working, including widgets outside the current view. */
function revayat_needs_contact_form_assets(): bool {
    $post = get_queried_object();
    if (is_singular() && $post instanceof WP_Post && revayat_content_has_contact_form($post->post_content)) { return true; }
    $sidebars = wp_get_sidebars_widgets();
    foreach ($sidebars as $sidebar => $widgets) {
        if ('wp_inactive_widgets' === $sidebar || !is_array($widgets)) { continue; }
        foreach ($widgets as $widget_id) {
            if (!preg_match('/^(text|custom_html|block)-(\d+)$/', $widget_id, $match)) { continue; }
            $options = get_option('widget_' . $match[1], []);
            $widget = $options[(int) $match[2]] ?? [];
            if (revayat_content_has_contact_form((string) ($widget['text'] ?? $widget['content'] ?? ''))) { return true; }
        }
    }
    return false;
}

function revayat_trim_unused_contact_form_assets(): void {
    if (!defined('WPCF7_VERSION') || apply_filters('revayat_needs_contact_form_assets', revayat_needs_contact_form_assets())) { return; }
    // Registered handles and CF7's own shortcode-driven enqueues remain available.
    wp_dequeue_style('contact-form-7-rtl');
    wp_dequeue_style('contact-form-7');
    wp_dequeue_script('contact-form-7');
    wp_dequeue_script('swv');
}
add_action('wp_enqueue_scripts', 'revayat_trim_unused_contact_form_assets', 99);

/** Reuse the editable site description; defer ownership to installed SEO plugins. */
function revayat_home_meta_description(): void {
    if (!is_front_page()) { return; }
    foreach (['WPSEO_VERSION', 'RANK_MATH_VERSION', 'AIOSEO_VERSION', 'SEOPRESS_VERSION', 'THE_SEO_FRAMEWORK_VERSION'] as $constant) {
        if (defined($constant)) { return; }
    }
    $default = get_bloginfo('description') ?: __('روایت ایران؛ رصد اخبار، روایت روز، پرونده‌های ویژه و تحلیل رویدادهای ایران و جهان.', 'revayatiran');
    $description = trim(wp_strip_all_tags((string) apply_filters('revayat_home_meta_description', get_theme_mod('revayat_home_meta_description', '') ?: $default)));
    if ($description) { echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n"; }
}
add_action('wp_head', 'revayat_home_meta_description', 9);

/** Editable metadata alongside native site identity; no database write until saved. */
function revayat_customize_home_meta_description($manager): void {
    $manager->add_setting('revayat_home_meta_description', ['default' => '', 'sanitize_callback' => 'sanitize_text_field']);
    $manager->add_control('revayat_home_meta_description', ['section' => 'title_tagline', 'type' => 'textarea', 'label' => __('توضیح متای صفحه اصلی', 'revayatiran'), 'description' => __('در صورت خالی‌بودن، معرفی سایت استفاده می‌شود؛ افزونه SEO فعال بر این تنظیم مقدم است.', 'revayatiran')]);
}
add_action('customize_register', 'revayat_customize_home_meta_description');

/** Avoid the browser's missing origin-root favicon request; site icon wins. */
function revayat_fallback_site_icon(): void {
    if (has_site_icon()) { return; }
    echo '<link rel="icon" type="image/png" href="' . esc_url(REVAYAT_THEME_URI . '/assets/images/branding/favicon-fe40ddcf012d.png') . '">' . "\n";
}
add_action('wp_head', 'revayat_fallback_site_icon', 99);
