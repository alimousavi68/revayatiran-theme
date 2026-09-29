<?php
/**
 * مدال و اورلی جستجوی تمام‌صفحه (Search Modal Overlay)
 *
 * @package RevayatIran
 * @subpackage Template_Parts/Header
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="search-overlay group fixed inset-0 z-[1000] flex flex-col items-center justify-center bg-gradient-to-b from-[#080E1A]/95 to-[#0D1626]/95 backdrop-blur-xl opacity-0 invisible transition-all duration-300 [&.is-active]:opacity-100 [&.is-active]:visible" id="search-overlay" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('جستجو در پایگاه تحلیلی روایت ایران', 'revayatiran'); ?>">
    <button class="absolute top-6 left-6 w-11 h-11 rounded-xl border border-white/10 bg-white/5 text-white/50 grid place-items-center cursor-pointer transition-all hover:bg-primary/15 hover:text-white/90 hover:border-primary/30" id="search-close" aria-label="<?php esc_attr_e('بستن جستجو', 'revayatiran'); ?>">
        <i class="ph ph-x text-xl"></i>
    </button>
    <div class="w-[min(calc(100%-40px),760px)] text-center translate-y-6 transition-transform duration-500 group-[.is-active]:translate-y-0">
        <p class="text-white/35 text-xs font-bold tracking-[0.12em] uppercase mb-7"><?php esc_html_e('جستجو در پایگاه تحلیلی روایت ایران', 'revayatiran'); ?></p>
        <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
            <input type="search" name="s" class="w-full bg-transparent border-none border-b-2 border-primary/60 text-white text-[clamp(1.5rem,3vw,2.5rem)] font-extrabold py-4 text-center transition-colors focus:outline-none focus:border-primary placeholder:text-white/15" placeholder="<?php esc_attr_e('عبارت مدنظر خود را بنویسید...', 'revayatiran'); ?>" id="search-input" value="<?php echo get_search_query(); ?>" autocomplete="off">
        </form>
        <div class="flex items-center justify-center gap-2 mt-5 flex-wrap">
            <span class="py-1 px-3.5 rounded-full bg-white/5 border border-white/10 text-white/50 text-xs font-semibold cursor-pointer transition-colors hover:bg-primary/15 hover:border-primary/30 hover:text-white/90"><?php esc_html_e('آخرین اخبار', 'revayatiran'); ?></span>
            <span class="py-1 px-3.5 rounded-full bg-white/5 border border-white/10 text-white/50 text-xs font-semibold cursor-pointer transition-colors hover:bg-primary/15 hover:border-primary/30 hover:text-white/90"><?php esc_html_e('تحلیل راهبردی', 'revayatiran'); ?></span>
            <span class="py-1 px-3.5 rounded-full bg-white/5 border border-white/10 text-white/50 text-xs font-semibold cursor-pointer transition-colors hover:bg-primary/15 hover:border-primary/30 hover:text-white/90"><?php esc_html_e('پرونده ویژه', 'revayatiran'); ?></span>
        </div>
        <p class="text-white/25 text-xs mt-7"><kbd class="inline-block py-0.5 px-2 rounded-md border border-white/15 bg-white/5 font-sans text-[11px]">Esc</kbd> <?php esc_html_e('برای بستن', 'revayatiran'); ?></p>
    </div>
</div>
