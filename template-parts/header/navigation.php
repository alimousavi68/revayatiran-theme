<?php
/**
 * ناوبری اصلی و پوسته هدر دسکتاپ (Header Shell & Navigation)
 *
 * @package RevayatIran
 * @subpackage Template_Parts/Header
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="container sticky top-[10px] z-[200] min-h-[76px] flex justify-between items-center gap-5 mx-auto my-[2px] mb-4 py-[3px] px-5 border border-white/20 rounded-lg bg-primary shadow-[0_12px_36px_rgba(0,0,0,0.18)] transition-all duration-300 dark:bg-primary-dark dark:border-white/10 dark:shadow-black/40" id="header-shell">
    <!-- نشان و لوگوی پایگاه -->
    <div class="shrink-0 flex items-center">
        <?php if (has_custom_logo()) : ?>
            <?php the_custom_logo(); ?>
        <?php else : ?>
            <a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center gap-3.5 no-underline" rel="home" aria-label="<?php bloginfo('name'); ?>">
                <img src="<?php echo esc_url(REVAYAT_THEME_URI . '/assets/images/branding/logo-revayat.webp'); ?>" alt="<?php bloginfo('name'); ?>" class="h-[64px] w-auto object-contain shrink-0" width="160" height="64">
            </a>
        <?php endif; ?>
    </div>

    <div class="w-[1px] h-8 bg-white/25 opacity-80 shrink-0" aria-hidden="true"></div>

    <!-- منوی اصلی ناوبری دسکتاپ -->
    <nav class="flex-1 hidden xl:flex justify-start items-center gap-1 ms-3 text-sm font-normal font-sans" aria-label="<?php esc_attr_e('فهرست ناوبری اصلی', 'revayatiran'); ?>">
        <?php
        wp_nav_menu([
            'theme_location' => 'primary-menu',
            'container'      => false,
            'menu_class'     => 'flex items-center gap-1 list-none m-0 p-0',
            'fallback_cb'    => 'revayat_nav_menu_fallback',
            'walker'         => new Revayat_Nav_Walker(),
            'depth'          => 2,
        ]);
        ?>
    </nav>

    <!-- دکمه باز کردن منوی موبایل -->
    <div class="flex items-center gap-2 xl:hidden">
        <button class="menu-toggle group" style="color: var(--text);" id="menu-toggle" type="button" aria-expanded="false" aria-controls="mobile-menu" aria-label="<?php esc_attr_e('بازکردن منو', 'revayatiran'); ?>">
            <span class="menu-toggle__line block w-[18px] h-[1.5px] bg-current rounded-full transition-all duration-300 origin-center"></span>
            <span class="menu-toggle__line block w-[18px] h-[1.5px] bg-current rounded-full transition-all duration-300 origin-center"></span>
            <span class="menu-toggle__line block w-[18px] h-[1.5px] bg-current rounded-full transition-all duration-300 origin-center"></span>
        </button>
    </div>
</div>
