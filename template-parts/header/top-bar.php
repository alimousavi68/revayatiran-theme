<?php
/**
 * نوار بالای هدر (Top Bar)
 *
 * @package RevayatIran
 * @subpackage Template_Parts/Header
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="top-bar w-full hidden md:block" id="top-bar">
    <div class="container flex items-center justify-between h-[42px] px-3">
        <!-- اطلاعات تقویم و ساعت -->
        <div class="flex items-center gap-2 text-text-muted text-xs font-normal whitespace-nowrap shrink-0">
            <i class="ph ph-calendar-blank text-[13px] opacity-75"></i>
            <span id="top-bar-date"><?php echo esc_html(wp_date('l j F Y')); ?></span>
            <span class="w-[1px] h-[14px] bg-border rounded-[1px] mx-1 shrink-0"></span>
            <i class="ph ph-clock text-[13px] opacity-75"></i>
            <span id="top-bar-time"><?php echo esc_html(wp_date('H:i')); ?></span>
        </div>

        <!-- دکمه‌های کنترل سریع (جستجو، تم، ورود) -->
        <div class="flex items-center gap-2 shrink-0">
            <button class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-border bg-bg-elevated text-text-muted text-base cursor-pointer transition-all duration-200 hover:text-primary hover:border-primary hover:bg-primary/5 dark:bg-white/5 dark:border-white/15 dark:text-text-muted dark:hover:text-white" type="button" aria-label="<?php esc_attr_e('جستجو', 'revayatiran'); ?>" id="search-trigger" title="<?php esc_attr_e('جستجو', 'revayatiran'); ?>">
                <i class="ph ph-magnifying-glass"></i>
            </button>
            <button class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-border bg-bg-elevated text-text-muted text-base cursor-pointer transition-all duration-200 hover:text-primary hover:border-primary hover:bg-primary/5 dark:bg-white/5 dark:border-white/15 dark:text-text-muted dark:hover:text-white theme-toggle-btn" id="theme-toggle" type="button" aria-label="<?php esc_attr_e('تغییر پوسته شب/روز', 'revayatiran'); ?>" title="<?php esc_attr_e('تغییر پوسته', 'revayatiran'); ?>">
                <i class="ph ph-sun dark:hidden"></i>
                <i class="ph ph-moon hidden dark:block"></i>
            </button>
            <?php get_template_part( 'template-parts/header/account' ); ?>
        </div>
    </div>
</div>
