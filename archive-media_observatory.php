<?php
/**
 * تمپلیت آرشیو اختصاصی دیده‌بان رسانه (Archive Media Observatory)
 *
 * انطباق ۱۰۰٪ با دیزاین سیستم اصلی و الگوی کارت‌های رصد و تحلیل فریمینگ.
 * پشتیبانی کامل از فیلتر گونه‌های واکاوی (گفتمان، فریمینگ، کمپین)، صفحه‌بندی بومی و حالت تهی معنایی.
 *
 * @package RevayatIran
 * @subpackage Templates
 * @version 2.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$paged        = max(1, (int) get_query_var('paged', 1));
$active_badge = sanitize_key($_GET['badge'] ?? (get_query_var('observatory_badge') ?: 'all'));

$archive_data = function_exists('revayat_get_archive_data') ? revayat_get_archive_data('media_observatory', [
    'paged' => $paged,
    'badge' => $active_badge,
]) : [];

$items        = $archive_data['items'] ?? [];
$total_posts  = $archive_data['total_posts'] ?? count($items);
$max_pages    = $archive_data['max_pages'] ?? 1;
$active_badge = $archive_data['active_badge'] ?? $active_badge;

$filter_badges = $archive_data['filter_options']['badges'] ?? [
    [
        'slug'      => 'all',
        'label'     => __('همه واکاوی‌ها', 'revayatiran'),
        'css_class' => 'obs-badge--all',
        'count'     => $total_posts,
        'url'       => get_post_type_archive_link('media_observatory') ?: home_url('/observatory/'),
    ],
    [
        'slug'      => 'discourse',
        'label'     => __('تحلیل گفتمان', 'revayatiran'),
        'css_class' => 'obs-badge--discourse',
        'count'     => 0,
        'url'       => add_query_arg('badge', 'discourse', get_post_type_archive_link('media_observatory') ?: home_url('/observatory/')),
    ],
    [
        'slug'      => 'framing',
        'label'     => __('مقایسه فریمینگ', 'revayatiran'),
        'css_class' => 'obs-badge--framing',
        'count'     => 0,
        'url'       => add_query_arg('badge', 'framing', get_post_type_archive_link('media_observatory') ?: home_url('/observatory/')),
    ],
    [
        'slug'      => 'campaign',
        'label'     => __('کمپین‌شناسی', 'revayatiran'),
        'css_class' => 'obs-badge--campaign',
        'count'     => 0,
        'url'       => add_query_arg('badge', 'campaign', get_post_type_archive_link('media_observatory') ?: home_url('/observatory/')),
    ],
];
?>

<main class="bg-gray-50/50 dark:bg-black/20 min-h-screen pb-16">
    <section class="pt-8 md:pt-10" id="archive-media-observatory">
        <div class="container">

            <!-- ═══ نوار هدر آرشیو دیده‌بان رسانه ═══ -->
            <div class="obs-archive-header flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="obs-archive-icon">
                        <i class="ph ph-binoculars"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-lg md:text-xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                                <?php esc_html_e('دیده‌بان رسانه', 'revayatiran'); ?>
                            </h1>
                            <span class="text-xs px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 font-bold">
                                <?php esc_html_e('پایش و واکاوی روایت‌ها', 'revayatiran'); ?>
                            </span>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">
                            <?php 
                            printf(
                                /* translators: 1: shown count, 2: total count */
                                esc_html__('نمایش %1$s از %2$s واکاوی و مقایسه فریم‌های خبری', 'revayatiran'),
                                function_exists('revayat_en2fa') ? revayat_en2fa(count($items)) : count($items),
                                function_exists('revayat_en2fa') ? revayat_en2fa($total_posts) : $total_posts
                            );
                            ?>
                        </p>
                    </div>
                </div>

                <!-- پیوند به رصد زنده و نشانگر پایش -->
                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 bg-white dark:bg-white/5 px-3.5 py-2 rounded-xl border border-[var(--border)] shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span><?php esc_html_e('دیده‌بانی ۲۴ ساعته جریان‌های رسانه‌ای', 'revayatiran'); ?></span>
                </div>
            </div>

            <!-- ═══ تب‌های فیلتر گونه‌های تحلیلی (Badge Filter Tabs) ═══ -->
            <div class="obs-filter-tabs" role="tablist" aria-label="<?php esc_attr_e('فیلتر گونه‌های واکاوی', 'revayatiran'); ?>">
                <?php foreach ($filter_badges as $tab) : 
                    $tab_slug   = $tab['slug'] ?? 'all';
                    $tab_label  = $tab['label'] ?? '';
                    $tab_url    = $tab['url'] ?? '#';
                    $tab_count  = (int) ($tab['count'] ?? 0);
                    $is_active  = ($active_badge === $tab_slug);
                ?>
                    <a href="<?php echo esc_url($tab_url); ?>" 
                       class="obs-filter-tab <?php echo $is_active ? 'is-active' : ''; ?>"
                       role="tab"
                       aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>">
                        <span><?php echo esc_html($tab_label); ?></span>
                        <?php if ($tab_count > 0) : ?>
                            <span class="obs-filter-tab__count">
                                <?php echo esc_html(function_exists('revayat_en2fa') ? revayat_en2fa($tab_count) : $tab_count); ?>
                            </span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- ═══ گرید کارت‌های دیده‌بان رسانه ═══ -->
            <?php if (!empty($items)) : ?>
                <div class="observatory-grid-v2">
                    <?php foreach ($items as $item) : ?>
                        <?php get_template_part('template-parts/cards/card-observatory', null, ['item' => $item]); ?>
                    <?php endforeach; ?>
                </div>

                <!-- صفحه‌بندی بومی وردپرس -->
                <?php if ($max_pages > 1) : ?>
                    <nav class="flex justify-center items-center gap-2 mt-12" aria-label="<?php esc_attr_e('صفحه‌بندی آرشیو دیده‌بان', 'revayatiran'); ?>">
                        <?php
                        $big   = 999999999;
                        $pages = paginate_links([
                            'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                            'format'    => '?paged=%#%',
                            'current'   => $paged,
                            'total'     => $max_pages,
                            'type'      => 'array',
                            'prev_text' => '<i class="ph ph-caret-right text-base"></i>',
                            'next_text' => '<i class="ph ph-caret-left text-base"></i>',
                        ]);

                        if (!empty($pages)) {
                            foreach ($pages as $page) {
                                if (strpos($page, 'current') !== false) {
                                    echo str_replace(
                                        'page-numbers current',
                                        'w-10 h-10 flex items-center justify-center rounded-xl border border-primary bg-primary text-white font-bold shadow-sm shadow-primary/20',
                                        function_exists('revayat_en2fa') ? revayat_en2fa($page) : $page
                                    );
                                } elseif (strpos($page, 'dots') !== false) {
                                    echo '<span class="flex items-center justify-center w-8 text-gray-400">...</span>';
                                } else {
                                    echo str_replace(
                                        'page-numbers',
                                        'w-10 h-10 flex items-center justify-center rounded-xl border border-[var(--border)] bg-white dark:bg-white/5 text-gray-700 dark:text-gray-300 hover:text-primary hover:border-primary/50 transition-all shadow-sm',
                                        function_exists('revayat_en2fa') ? revayat_en2fa($page) : $page
                                    );
                                }
                            }
                        }
                        ?>
                    </nav>
                <?php endif; ?>

            <?php else : ?>
                <!-- حالت تهی معنایی (ADR-002: Semantic Empty State) -->
                <div class="obs-empty-state">
                    <div class="obs-empty-state__icon">
                        <i class="ph ph-binoculars"></i>
                    </div>
                    <h3 class="obs-empty-state__title">
                        <?php esc_html_e('واکاوی یا موردی در این محور یافت نشد', 'revayatiran'); ?>
                    </h3>
                    <p class="obs-empty-state__desc">
                        <?php esc_html_e('هیچ تحلیل و گزارشی با فیلتر انتخابی شما در پایگاه دیده‌بان ثبت نشده است. می‌توانید فیلترها را تغییر داده یا همه موارد را مشاهده کنید.', 'revayatiran'); ?>
                    </p>
                    <a href="<?php echo esc_url(get_post_type_archive_link('media_observatory') ?: home_url('/observatory/')); ?>" 
                       class="obs-filter-tab is-active inline-flex">
                        <i class="ph ph-arrow-clockwise"></i>
                        <span><?php esc_html_e('مشاهده همه واکاوی‌ها', 'revayatiran'); ?></span>
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </section>
</main>

<?php
get_footer();
