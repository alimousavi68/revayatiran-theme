<?php
/**
 * بدنه و ستون‌های اصلی فوتر (Main Footer)
 *
 * @package RevayatIran
 * @subpackage Template_Parts/Footer
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<footer class="relative w-full mt-12" id="site-footer" style="min-height:200px;">
    <!-- لایه گرادیان و پس‌زمینه نیمه فوتر -->
	<div class="footer-backdrop-layer absolute bottom-0 left-0 w-full h-[50%] bg-[var(--header-backdrop)] opacity-70 -z-10 pointer-events-none transition-colors duration-300" aria-hidden="true"></div>

    <!-- کانتینر کارت اصلی فوتر -->
    <div class="container relative z-10 bg-primary text-white/90 rounded-t-xl border-t border-x border-white/20 shadow-[0_-12px_36px_rgba(0,0,0,0.18)] dark:bg-primary-dark mx-auto overflow-hidden">
        <div class="h-[1px] bg-gradient-to-r from-transparent via-white/40 to-transparent w-full"></div>

        <div class="rv-footer-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[1.8fr_1fr_1fr] gap-8 md:gap-14 py-8 md:py-10 px-6">
            <!-- ستون برندینگ و معرفی -->
            <div class="rv-footer-brand flex flex-col gap-5 sm:col-span-2 lg:col-span-1">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="inline-flex transition-opacity duration-240 hover:opacity-90 w-fit" rel="home" aria-label="<?php bloginfo('name'); ?>">
                    <img src="<?php echo esc_url(REVAYAT_THEME_URI . '/assets/images/branding/logo-revayat-180-9af21755cde6.webp'); ?>" srcset="<?php echo esc_url(REVAYAT_THEME_URI . '/assets/images/branding/logo-revayat-180-9af21755cde6.webp'); ?> 180w, <?php echo esc_url(REVAYAT_THEME_URI . '/assets/images/branding/logo-revayat-360-3e2fe39acde3.webp'); ?> 360w, <?php echo esc_url(REVAYAT_THEME_URI . '/assets/images/branding/logo-revayat.webp'); ?> 700w" sizes="(max-width: 767px) 200px, 240px"
                         alt="<?php bloginfo('name'); ?>"
                         class="h-[52px] w-auto object-contain"
                         width="700" height="251"
                         loading="lazy">
                </a>
                <p class="text-[12px] leading-[1.8] text-white/85 m-0 max-w-[38ch]">
                    <?php echo esc_html(get_bloginfo('description') ?: __('روایت ایران؛ رصد رویدادها، روایت‌های تحلیلی و پرونده‌های ویژه.', 'revayatiran')); ?>
                </p>

                <!-- پیوندهای شبکه‌های اجتماعی فوتر -->
                <div class="flex items-center gap-2 mt-1 footer-socials">
                    <a href="https://t.me" class="grid place-items-center w-8 h-8 rounded-xl border border-white/25 bg-white/10 text-white text-lg transition-all duration-200 hover:-translate-y-1 no-underline" aria-label="<?php esc_attr_e('تلگرام', 'revayatiran'); ?>" target="_blank" rel="noopener noreferrer">
                        <i class="ph ph-telegram-logo"></i>
                    </a>
                    <a href="https://instagram.com" class="grid place-items-center w-8 h-8 rounded-xl border border-white/25 bg-white/10 text-white text-lg transition-all duration-200 hover:-translate-y-1 no-underline" aria-label="<?php esc_attr_e('اینستاگرام', 'revayatiran'); ?>" target="_blank" rel="noopener noreferrer">
                        <i class="ph ph-instagram-logo"></i>
                    </a>
                    <a href="https://x.com" class="grid place-items-center w-8 h-8 rounded-xl border border-white/25 bg-white/10 text-white text-lg transition-all duration-200 hover:-translate-y-1 no-underline" aria-label="<?php esc_attr_e('توییتر', 'revayatiran'); ?>" target="_blank" rel="noopener noreferrer">
                        <i class="ph ph-x-logo"></i>
                    </a>
                    <a href="https://youtube.com" class="grid place-items-center w-8 h-8 rounded-xl border border-white/25 bg-white/10 text-white text-lg transition-all duration-200 hover:-translate-y-1 no-underline" aria-label="<?php esc_attr_e('یوتیوب', 'revayatiran'); ?>" target="_blank" rel="noopener noreferrer">
                        <i class="ph ph-youtube-logo"></i>
                    </a>
                </div>
            </div>

            <!-- ستون سرویس‌های خبری -->
            <div class="flex flex-col gap-0 footer-accordion">
                <button class="footer-accordion-btn w-full flex justify-between items-center text-right font-sans text-sm font-extrabold text-white uppercase tracking-wider m-0 mb-3 md:mb-5 pb-3 border-b border-white/20 leading-snug md:pointer-events-none cursor-pointer md:cursor-default" type="button">
                    <span><?php esc_html_e('سرویس‌ها', 'revayatiran'); ?></span>
                    <i class="ph ph-caret-down text-lg md:hidden transition-transform duration-300"></i>
                </button>
                <nav class="footer-accordion-content flex flex-col gap-0.5" aria-label="<?php esc_attr_e('سرویس‌های خبری', 'revayatiran'); ?>">
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'footer-col-1',
                        'container'      => false,
                        'menu_class'     => 'flex flex-col gap-0.5 list-none m-0 p-0',
                        'fallback_cb'    => 'revayat_nav_menu_fallback',
                        'walker'         => new Revayat_Nav_Walker(),
                        'depth'          => 1,
                    ]);
                    ?>
                </nav>
            </div>

            <!-- ستون دسترسی و اطلاعات رسانه -->
            <div class="flex flex-col gap-0 footer-accordion">
                <button class="footer-accordion-btn w-full flex justify-between items-center text-right font-sans text-sm font-extrabold text-white uppercase tracking-wider m-0 mb-3 md:mb-5 pb-3 border-b border-white/20 leading-snug md:pointer-events-none cursor-pointer md:cursor-default" type="button">
                    <span><?php esc_html_e('درباره روایت ایران', 'revayatiran'); ?></span>
                    <i class="ph ph-caret-down text-lg md:hidden transition-transform duration-300"></i>
                </button>
                <nav class="footer-accordion-content flex flex-col gap-0.5" aria-label="<?php esc_attr_e('درباره روایت ایران', 'revayatiran'); ?>">
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'footer-col-2',
                        'container'      => false,
                        'menu_class'     => 'flex flex-col gap-0.5 list-none m-0 p-0',
                        'fallback_cb'    => 'revayat_nav_menu_fallback',
                        'walker'         => new Revayat_Nav_Walker(),
                        'depth'          => 1,
                    ]);
                    ?>
                </nav>
            </div>
        </div>

        <!-- نوار پایینی کپی‌رایت -->
        <div class="relative z-10 border-t border-white/20">
            <div class="rv-footer-bottom flex flex-col md:flex-row items-center justify-center md:justify-between gap-4 py-8 px-8 text-center">
                 <span class="footer-copyright text-[12px] text-white/75 font-medium tracking-wide">
                    <?php
                    $year = wp_date('Y');
                    if (function_exists('revayat_en2fa')) {
                        $year = revayat_en2fa($year);
                    } else {
                        $en = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
                        $fa = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
                        $year = str_replace($en, $fa, $year);
                    }
                    printf(
                        /* translators: %s: Current Year */
                        esc_html__('© %s روایت ایران. تمامی حقوق محفوظ است.', 'revayatiran'),
                        esc_html($year)
                    );
                    ?>
                </span>
                <a href="http://ihasht.ir/" class="footer-designer group inline-flex items-center justify-center gap-2 no-underline opacity-85 transition-opacity duration-200 hover:opacity-100" target="_blank" rel="noopener noreferrer">
                     <span class="text-[11px] text-white/80 font-normal whitespace-nowrap"><?php esc_html_e('طراحی و توسعه توسط', 'revayatiran'); ?></span>
                     <span class="relative font-sans text-sm font-extrabold text-white tracking-normal whitespace-nowrap pb-px after:content-[''] after:absolute after:bottom-0 after:left-0 after:right-0 after:h-[1px] after:bg-white/35 after:rounded-[1px] after:scale-x-0 after:origin-right after:transition-transform after:duration-240 group-hover:after:scale-x-100"><?php esc_html_e('هشت بهشت', 'revayatiran'); ?></span>
                </a>
            </div>
        </div>
    </div>
</footer>
