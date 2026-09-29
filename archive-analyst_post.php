<?php
/**
 * تمپلیت آرشیو اختصاصی یادداشت‌های تحلیلی و راهبردی (Archive Analyst Post)
 *
 * انطباق ۱۰۰٪ با دیزاین سیستم اصلی و الگوی شبکه تحلیل‌گران.
 * پشتیبانی کامل از فیلتر حوزه‌های تخصصی (analyst_field)، مرتب‌سازی دوگانه (تاریخ/امتیاز اعتباری)،
 * گرید ۳ ستونه واکنش‌گرا، صفحه‌بندی بومی و حالت تهی معنایی (ADR-002).
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
$active_field = sanitize_key($_GET['field'] ?? (get_query_var('analyst_field') ?: 'all'));
$active_sort  = sanitize_key($_GET['sort'] ?? 'date');
$active_search = sanitize_text_field(wp_unslash($_GET['q'] ?? ''));

$archive_data = function_exists('revayat_get_archive_data') ? revayat_get_archive_data('analyst_post', [
    'paged' => $paged,
    'field' => $active_field,
    'sort'  => $active_sort,
    'search'=> $active_search,
]) : [];

$items        = $archive_data['items'] ?? [];
$total_posts  = $archive_data['total_posts'] ?? count($items);
$max_pages    = $archive_data['max_pages'] ?? 1;
$active_field = $archive_data['active_field'] ?? $active_field;
$active_sort  = $archive_data['active_sort'] ?? $active_sort;
$active_search = $archive_data['active_search'] ?? $active_search;

$archive_url   = get_post_type_archive_link('analyst_post') ?: home_url('/analysts/');
$base_sort_url = ('all' !== $active_field) ? add_query_arg('field', $active_field, $archive_url) : $archive_url;

$filter_fields = $archive_data['filter_options']['fields'] ?? [
    [
        'slug'  => 'all',
        'label' => __('همه حوزه‌ها', 'revayatiran'),
        'count' => $total_posts,
        'url'   => $archive_url,
    ],
    [
        'slug'  => 'geopolitics',
        'label' => __('ژئوپلیتیک', 'revayatiran'),
        'count' => 0,
        'url'   => add_query_arg('field', 'geopolitics', $archive_url),
    ],
    [
        'slug'  => 'political-economy',
        'label' => __('اقتصاد سیاسی', 'revayatiran'),
        'count' => 0,
        'url'   => add_query_arg('field', 'political-economy', $archive_url),
    ],
    [
        'slug'  => 'security',
        'label' => __('امنیت ملی', 'revayatiran'),
        'count' => 0,
        'url'   => add_query_arg('field', 'security', $archive_url),
    ],
    [
        'slug'  => 'media',
        'label' => __('رسانه و روایت', 'revayatiran'),
        'count' => 0,
        'url'   => add_query_arg('field', 'media', $archive_url),
    ],
];

$sort_options = $archive_data['filter_options']['sorts'] ?? [
    [
        'slug'      => 'date',
        'label'     => __('تازه‌ترین‌ها', 'revayatiran'),
        'is_active' => ('date' === $active_sort),
        'url'       => add_query_arg('sort', 'date', $base_sort_url),
    ],
    [
        'slug'      => 'score',
        'label'     => __('برترین امتیاز', 'revayatiran'),
        'is_active' => ('score' === $active_sort),
        'url'       => add_query_arg('sort', 'score', $base_sort_url),
    ],
];
?>

<div class="rv-archive rv-archive--analysts bg-gray-50/50 dark:bg-black/20 min-h-screen pb-16">
    <section class="pt-8 md:pt-10" id="archive-analyst-posts">
        <div class="container">

            <!-- ═══ نوار هدر آرشیو یادداشت‌های تحلیلی ═══ -->
            <div class="an-archive-header flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="an-archive-icon">
                        <i class="ph ph-pen-nib"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-lg md:text-xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                                <?php esc_html_e('یادداشت‌های تحلیلی و راهبردی', 'revayatiran'); ?>
                            </h1>
                            <span class="text-xs px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300 font-bold">
                                <?php esc_html_e('شبکه نخبگان و تحلیل‌گران', 'revayatiran'); ?>
                            </span>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">
                            <?php 
                            printf(
                                /* translators: 1: shown count, 2: total count */
                                esc_html__('نمایش %1$s از %2$s یادداشت پژوهشی و ارزیابی راهبردی', 'revayatiran'),
                                function_exists('revayat_en2fa') ? revayat_en2fa(count($items)) : count($items),
                                function_exists('revayat_en2fa') ? revayat_en2fa($total_posts) : $total_posts
                            );
                            ?>
                        </p>
                    </div>
                </div>

                <!-- پیوند به شبکه و شاخص اعتبار -->
                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 bg-white dark:bg-white/5 px-3.5 py-2 rounded-xl border border-[var(--border)] shadow-sm">
                    <i class="ph-fill ph-check-circle text-emerald-500 text-base"></i>
                    <span><?php esc_html_e('ارزیابی و نمره‌دهی همتا (Peer Review)', 'revayatiran'); ?></span>
                </div>
            </div>

            <!-- ═══ نوار ابزار: تب‌های فیلتر حوزه تخصصی + کنترل مرتب‌سازی ═══ -->
            <div class="an-toolbar">
                <form role="search" method="get" action="<?php echo esc_url($archive_url); ?>" class="rv-archive-search">
                    <?php if ('all' !== $active_field) : ?><input type="hidden" name="field" value="<?php echo esc_attr($active_field); ?>"><?php endif; ?>
                    <input type="hidden" name="sort" value="<?php echo esc_attr($active_sort); ?>">
                    <label class="screen-reader-text" for="analyst-search"><?php esc_html_e('جستجو در یادداشت‌های تحلیلی', 'revayatiran'); ?></label>
                    <i class="ph ph-magnifying-glass" aria-hidden="true"></i>
                    <input id="analyst-search" type="search" name="q" value="<?php echo esc_attr($active_search); ?>" maxlength="100" placeholder="<?php esc_attr_e('جستجو در عنوان و متن تحلیل‌ها...', 'revayatiran'); ?>">
                    <button type="submit"><?php esc_html_e('جستجو', 'revayatiran'); ?></button>
                </form>
                <!-- تب‌های فیلتر حوزه تخصصی (Field Tabs) -->
                <div class="an-filter-tabs" role="tablist" aria-label="<?php esc_attr_e('فیلتر حوزه‌های تخصصی', 'revayatiran'); ?>">
                    <?php foreach ($filter_fields as $tab) : 
                        $tab_slug   = $tab['slug'] ?? 'all';
                        $tab_label  = $tab['label'] ?? '';
                        $tab_url    = add_query_arg(array_filter(['sort' => $active_sort, 'q' => $active_search]), $tab['url'] ?? '#');
                        $tab_count  = (int) ($tab['count'] ?? 0);
                        $is_active  = ($active_field === $tab_slug);
                    ?>
                        <a href="<?php echo esc_url($tab_url); ?>" 
                           class="an-filter-tab <?php echo $is_active ? 'is-active' : ''; ?>"
                           role="tab"
                           aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>">
                            <span><?php echo esc_html($tab_label); ?></span>
                            <?php if ($tab_count > 0) : ?>
                                <span class="an-filter-tab__count">
                                    <?php echo esc_html(function_exists('revayat_en2fa') ? revayat_en2fa($tab_count) : $tab_count); ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <!-- مرتب‌سازی دوگانه: تازه‌ترین‌ها / بالاترین امتیاز -->
                <div class="an-sort-group" role="group" aria-label="<?php esc_attr_e('مرتب‌سازی یادداشت‌ها', 'revayatiran'); ?>">
                    <?php foreach ($sort_options as $sort) : 
                        $sort_slug   = $sort['slug'] ?? 'date';
                        $sort_label  = $sort['label'] ?? '';
                        $sort_url    = add_query_arg(array_filter(['q' => $active_search]), $sort['url'] ?? '#');
                        $is_active   = !empty($sort['is_active']);
                    ?>
                        <a href="<?php echo esc_url($sort_url); ?>" 
                           class="an-sort-btn <?php echo $is_active ? 'is-active' : ''; ?>">
                            <?php if ($sort_slug === 'score') : ?>
                                <i class="ph ph-star"></i>
                            <?php else : ?>
                                <i class="ph ph-clock"></i>
                            <?php endif; ?>
                            <span><?php echo esc_html($sort_label); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- ═══ گرید کارت‌های شبکه تحلیل‌گران ═══ -->
            <?php if (!empty($items)) : ?>
                <p class="rv-archive-result-count" aria-live="polite"><?php printf(esc_html__('%s نتیجه یافت شد', 'revayatiran'), esc_html(function_exists('revayat_en2fa') ? revayat_en2fa($total_posts) : $total_posts)); ?></p>
                <div class="an-archive-grid">
                    <?php foreach ($items as $item) : ?>
                        <?php get_template_part('template-parts/cards/card-analyst', null, [
                            'item'    => $item,
                            'variant' => 'grid',
                        ]); ?>
                    <?php endforeach; ?>
                </div>

                <!-- صفحه‌بندی بومی وردپرس -->
                <?php if ($max_pages > 1) : ?>
                    <nav class="flex justify-center items-center gap-2 mt-12" aria-label="<?php esc_attr_e('صفحه‌بندی یادداشت‌های تحلیلی', 'revayatiran'); ?>">
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
                <div class="an-empty-state">
                    <div class="an-empty-state__icon">
                        <i class="ph ph-pen-nib"></i>
                    </div>
                    <h3 class="an-empty-state__title">
                        <?php esc_html_e('یادداشتی در این حوزه یافت نشد', 'revayatiran'); ?>
                    </h3>
                    <p class="an-empty-state__desc">
                        <?php esc_html_e('هیچ یادداشت یا تحلیلی با فیلتر انتخابی شما در پایگاه ثبت نشده است. می‌توانید فیلترها را تغییر داده یا تمام یادداشت‌ها را مشاهده فرمایید.', 'revayatiran'); ?>
                    </p>
                    <a href="<?php echo esc_url($archive_url); ?>" 
                       class="an-filter-tab is-active inline-flex">
                        <i class="ph ph-arrow-clockwise"></i>
                        <span><?php esc_html_e('مشاهده تمام یادداشت‌ها', 'revayatiran'); ?></span>
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </section>
</div>

<?php
get_footer();
