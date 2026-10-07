<?php
/**
 * کامپوننت کارت پرونده ویژه (Special Dossier Card Component)
 *
 * طراحی و ساختار کامپوننت منطبق بر استانداردهای بصری و کلاس‌های سورس اصلی (archive-card).
 *
 * @package RevayatIran
 * @subpackage Template_Parts/Cards
 * @version 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$item = $args['item'] ?? ($args['dossier'] ?? []);
if (empty($item)) {
    if (isset($args['post'])) {
        $item = function_exists('revayat_get_single_data') ? revayat_get_single_data($args['post']) : [];
    } else {
        $item = function_exists('revayat_get_single_data') ? revayat_get_single_data(get_the_ID()) : [];
    }
}

if (empty($item)) {
    return;
}

$title        = $item['title'] ?? '';
$url          = $item['permalink'] ?? ($item['url'] ?? get_permalink($item['id'] ?? null));
$thumbnail    = $item['media']['url'] ?? ($item['thumbnail_url'] ?? '');
if (empty($thumbnail) && function_exists('revayat_resolve_image_url')) {
    $thumbnail = revayat_resolve_image_url(null, 'news');
}
$topic        = $item['taxonomies']['dossier_topic'] ?? ($item['category'] ?? esc_html__('پرونده ویژه', 'revayatiran'));
$meta         = $item['meta'] ?? [];
$is_live      = !empty($meta['is_live']);
$dossier_code = $meta['dossier_code'] ?? '';
$editor_lead  = $meta['editor_lead'] ?? '';
$last_update  = $meta['last_update'] ?? ($item['time_ago'] ?? ($item['date'] ?? ''));
$excerpt      = $item['excerpt'] ?? '';
?>

<article class="archive-card group flex flex-col bg-white dark:bg-[#1a1c23] rounded-2xl border border-[var(--border)] overflow-hidden transition-all duration-300 hover:shadow-lg hover:border-primary/30 hover:-translate-y-1">
    <!-- تصویر شاخص و اورلی وضعیت -->
    <a href="<?php echo esc_url($url); ?>" class="relative aspect-video overflow-hidden block bg-gray-100 dark:bg-black/30" aria-label="<?php echo esc_attr($title); ?>">
        <img src="<?php echo esc_url($thumbnail); ?>" 
             alt="<?php echo esc_attr($title); ?>" 
             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" 
             loading="lazy" decoding="async" width="800" height="450">
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>

        <!-- نشانگر زنده در بالای تصویر -->
        <?php if ($is_live) : ?>
            <div class="absolute top-3 right-3 z-10">
                <div class="live-indicator">
                    <span class="live-indicator__dot"></span>
                    <span><?php esc_html_e('در حال پیگیری', 'revayatiran'); ?></span>
                </div>
            </div>
        <?php else : ?>
            <div class="absolute top-3 right-3 z-10">
                <span class="dossier-category-badge"><?php esc_html_e('تکمیل‌شده', 'revayatiran'); ?></span>
            </div>
        <?php endif; ?>

        <!-- بج موضوع و کد پرونده در پایین تصویر -->
        <div class="absolute bottom-3 right-3 left-3 z-10 flex items-center justify-between gap-2 flex-wrap pointer-events-none">
            <?php if (!empty($topic)) : ?>
                <span class="dossier-category-badge text-[11px] font-bold shadow-md">
                    <?php echo esc_html($topic); ?>
                </span>
            <?php endif; ?>

            <?php if (!empty($dossier_code)) : ?>
                <span class="font-mono text-[10px] font-bold text-white/90 bg-black/60 px-2 py-0.5 rounded backdrop-blur-sm">
                    <?php echo esc_html($dossier_code); ?>
                </span>
            <?php endif; ?>
        </div>
    </a>

    <!-- بدنه کارت -->
    <div class="p-5 flex flex-col flex-1">
        <!-- عنوان پرونده -->
        <h3 class="text-base md:text-lg font-bold text-gray-900 dark:text-white leading-snug mb-3">
            <a href="<?php echo esc_url($url); ?>" class="hover:text-primary transition-colors line-clamp-2">
                <?php echo esc_html($title); ?>
            </a>
        </h3>

        <!-- چکیده -->
        <?php if (!empty($excerpt)) : ?>
            <p class="text-[13px] text-gray-600 dark:text-gray-400 line-clamp-2 mb-4 leading-relaxed flex-1">
                <?php echo esc_html($excerpt); ?>
            </p>
        <?php endif; ?>

        <?php get_template_part('template-parts/dossier/counts', null, ['id' => $item['id'] ?? 0]); ?>

        <!-- فوتر کارت: دبیر پرونده و تاریخ بروزرسانی -->
        <div class="flex items-center justify-between pt-4 border-t border-[var(--border)] mt-auto">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-[10px] bg-primary/10 text-primary flex items-center justify-center text-xs border border-black/10 dark:border-white/10 shrink-0">
                    <i class="ph ph-user"></i>
                </div>
                <div class="flex flex-col">
                    <span class="text-xs font-bold text-gray-900 dark:text-white"><?php echo esc_html(!empty($editor_lead) ? $editor_lead : __('تحریریه راهبردی', 'revayatiran')); ?></span>
                    <span class="text-[10px] text-gray-500"><?php esc_html_e('دبیر پرونده', 'revayatiran'); ?></span>
                </div>
            </div>

            <?php if (!empty($last_update)) : ?>
                <div class="text-[11px] text-gray-500 flex items-center gap-1">
                    <i class="ph ph-clock text-xs text-primary"></i>
                    <span><?php echo esc_html(function_exists('revayat_en2fa') ? revayat_en2fa($last_update) : $last_update); ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</article>
