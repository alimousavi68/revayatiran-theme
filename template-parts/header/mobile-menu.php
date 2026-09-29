<?php
/**
 * پنل کشویی ناوبری موبایل (Mobile Menu Drawer)
 *
 * @package RevayatIran
 * @subpackage Template_Parts/Header
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="mobile-menu" id="mobile-menu" aria-hidden="true">
    <div class="mobile-menu__panel">
        <div class="mobile-menu__header">
            <span class="mobile-menu__title"><?php esc_html_e('ناوبری منو', 'revayatiran'); ?></span>
            <button class="mobile-menu__close" id="mobile-close" aria-label="<?php esc_attr_e('بستن منو', 'revayatiran'); ?>">
                <i class="ph ph-x"></i>
            </button>
        </div>
        
        <!-- دکمه‌های کنترل سریع در بالای منوی موبایل -->
        <div class="p-3 border-b border-border flex items-center gap-2 bg-bg-soft/50">
            <button type="button" class="search-trigger-btn flex-1 flex items-center justify-center gap-2 py-2 px-3 rounded-xl border border-border bg-bg-elevated text-text text-sm font-semibold transition-all hover:border-primary hover:text-primary" id="mobile-search-trigger">
                <i class="ph ph-magnifying-glass text-base"></i>
                <span><?php esc_html_e('جستجو', 'revayatiran'); ?></span>
            </button>
            <button type="button" class="theme-toggle-btn flex-1 flex items-center justify-center gap-2 py-2 px-3 rounded-xl border border-border bg-bg-elevated text-text text-sm font-semibold transition-all hover:border-primary hover:text-primary" id="mobile-theme-toggle">
                <i class="ph ph-sun dark:hidden text-base"></i>
                <i class="ph ph-moon hidden dark:block text-base"></i>
                <span class="dark:hidden"><?php esc_html_e('حالت تیره', 'revayatiran'); ?></span>
                <span class="hidden dark:inline"><?php esc_html_e('حالت روشن', 'revayatiran'); ?></span>
            </button>
        </div>

        <nav class="mobile-menu__nav" aria-label="<?php esc_attr_e('منوی ناوبری موبایل', 'revayatiran'); ?>">
            <?php
            wp_nav_menu([
                'theme_location' => 'mobile-menu',
                'container'      => false,
                'menu_class'     => 'flex flex-col list-none m-0 p-0 gap-1',
                'fallback_cb'    => 'revayat_nav_menu_fallback',
                'walker'         => new Revayat_Nav_Walker(),
                'depth'          => 2,
            ]);
            ?>
        </nav>

        <div class="mobile-menu__footer">
            <a href="<?php echo esc_url(home_url('/auth/')); ?>" class="mobile-cta">
                <i class="ph ph-user-circle-plus"></i>
                <span><?php esc_html_e('ورود / عضویت', 'revayatiran'); ?></span>
            </a>
        </div>
    </div>
</div>
