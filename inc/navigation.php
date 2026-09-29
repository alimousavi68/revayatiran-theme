<?php
/**
 * ثبت موقعیت‌های منو و تنظیمات ناوبری (Navigation)
 *
 * @package RevayatIran
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('revayat_register_menus')) {
    /**
     * ثبت موقعیت‌های استاندارد فهرست‌های وردپرس
     */
    function revayat_register_menus() {
        register_nav_menus([
            'primary-menu'  => esc_html__('منوی اصلی سربرگ (Desktop Primary)', 'revayatiran'),
            'top-menu'      => esc_html__('منوی نوار بالا (Top Bar)', 'revayatiran'),
            'mobile-menu'   => esc_html__('منوی کشویی موبایل (Mobile Drawer)', 'revayatiran'),
            'footer-col-1'  => esc_html__('ستون اول پیوندهای فوتر', 'revayatiran'),
            'footer-col-2'  => esc_html__('ستون دوم پیوندهای فوتر', 'revayatiran'),
            'footer-legal'  => esc_html__('پیوندهای قوانین و کپی‌رایت پایین فوتر', 'revayatiran'),
        ]);
    }
}
add_action('after_setup_theme', 'revayat_register_menus');

/** Archive actions share the native primary-menu destination; deletion removes the action. */
function revayat_archive_menu_url( string $source ): string {
    $locations = get_nav_menu_locations();
    $menu = $locations['primary-menu'] ?? 0;
    if ( ! $menu ) { return ''; }
    $term = 'multimedia' === $source ? null : get_term_by( 'slug', $source, 'category' );
    if ( 'multimedia' !== $source && ! $term ) { return ''; }
    foreach ( wp_get_nav_menu_items( $menu ) ?: array() as $item ) {
        if ( 'multimedia' === $source && 'post_type_archive' === $item->type && 'multimedia' === $item->object ) { return $item->url; }
        if ( $term && 'taxonomy' === $item->type && 'category' === $item->object && (int) $item->object_id === (int) $term->term_id ) { return $item->url; }
    }
    return '';
}
