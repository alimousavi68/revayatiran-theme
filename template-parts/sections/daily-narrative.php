<?php
/**
 * بخش روایت روز (Daily Narrative Section)
 *
 * اتصال به لایه قرارداد داده (Data Contract) بدون وابستگی به WP_Query مستقیم.
 * پایداری تضمین‌شده رندر بخش در DOM.
 *
 * @package RevayatIran
 * @subpackage Template_Parts/Sections
 * @version 2.0.0 (Pass 2B Commit 4)
 */

if (!defined('ABSPATH')) {
    exit;
}

$daily_data        = function_exists('revayat_get_homepage_data') ? revayat_get_homepage_data('daily-narrative') : (function_exists('revayat_get_daily_narrative_data') ? revayat_get_daily_narrative_data() : []);
$lead              = $daily_data['lead'] ?? [];
$interactive_items = $daily_data['interactive_items'] ?? [];
$first_thumb       = !empty($interactive_items) ? $interactive_items[0] : [];

// متغیرهای کارت اصلی
$main_title   = $lead['title'] ?? '';
$main_url     = $lead['url'] ?? home_url('/');
$main_img     = revayat_resolve_image_url($lead['thumbnail_url'] ?? null, 'news');
$main_excerpt = $lead['excerpt'] ?? '';
$main_date    = $lead['date'] ?? '';
$main_author  = $lead['author_name'] ?? '';
$main_avatar  = revayat_resolve_image_url($lead['author_avatar'] ?? null, 'avatar');
$main_role    = $lead['author_role'] ?? '';
$main_cat     = $lead['category'] ?? esc_html__('روایت روز', 'revayatiran');

// متغیرهای اینفوپنل پیش‌فرض (اولین آیتم تعاملی)
$fp_title   = $first_thumb['title'] ?? '';
$fp_url     = $first_thumb['url'] ?? home_url('/');
$fp_excerpt = $first_thumb['excerpt'] ?? '';
$fp_author  = $first_thumb['author_name'] ?? '';
$fp_avatar  = revayat_resolve_image_url($first_thumb['author_avatar'] ?? null, 'avatar');
$fp_date    = $first_thumb['date'] ?? '';
$fp_cat     = $first_thumb['category'] ?? '';
?>
<section class="section-shell section-shell--offwhite section-spacing-top" id="daily-narrative">
    <div class="container">
        <!-- هدر بخش روایت روز -->
        <div class="section-head section-head--etude-b">
            <div class="etude-b__leading">
                <div class="etude-b__icon-badge">
                    <i class="ph ph-newspaper"></i>
                </div>
                <h2><?php echo esc_html( revayat_home_text( 'daily-narrative', 'title' ) ); ?></h2>
                <p class="section-summary"><?php echo esc_html( revayat_home_text( 'daily-narrative', 'description' ) ); ?></p>
            </div>
            <div class="etude-b__actions">
                <?php $archive_url = revayat_archive_menu_url( 'daily-narrative' ); if ( $archive_url ) : ?>
                <a href="<?php echo esc_url( $archive_url ); ?>" class="section-link">
                    <span><?php esc_html_e('آرشیو روایت‌ها', 'revayatiran'); ?></span>
                    <i class="ph ph-arrow-left"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- چیدمان اصلی: ستون هیرو + thumbnails تعاملی + info panel -->
        <div class="daily-hero-layout layout-gap-lg">

            <!-- ستون راست (۷۰٪): کارت اصلی -->
            <div class="daily-hero-main">
                <article class="daily-featured-card" id="daily-main-card">
                    <a href="<?php echo esc_url($main_url); ?>" class="daily-featured__visual block group">
                        <img src="<?php echo esc_url($main_img); ?>"
                             alt="<?php echo esc_attr($main_title); ?>"
                             class="daily-featured__img transition-transform duration-500 group-hover:scale-105"
                             id="daily-main-img">
                        <div class="daily-featured__overlay"></div>
                        <span class="daily-featured__tag" id="daily-main-tag"><?php echo esc_html($main_cat); ?></span>
                    </a>

                    <div class="daily-featured__content">
                        <div class="daily-featured__author-row">
                            <div class="daily-author__profile">
                                <img src="<?php echo esc_url($main_avatar); ?>"
                                     alt="<?php echo esc_attr($main_author); ?>"
                                     class="daily-author__avatar"
                                     id="daily-main-avatar">
                                <div class="daily-author__info">
                                    <strong id="daily-main-author-name"><?php echo esc_html($main_author); ?></strong>
                                    <?php if (!empty($main_role)) : ?>
                                        <span class="daily-author__role" id="daily-main-author-role"><?php echo esc_html($main_role); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="daily-author__stats">
                                <span class="daily-featured__date" id="daily-main-date"><?php echo esc_html($main_date); ?></span>
                            </div>
                        </div>

                        <h2 class="daily-featured__title">
                            <a href="<?php echo esc_url($main_url); ?>" class="text-inherit hover:text-primary transition-colors">
                                <?php echo esc_html($main_title); ?>
                            </a>
                        </h2>

                        <p class="daily-featured__excerpt" id="daily-main-excerpt">
                            <?php echo esc_html($main_excerpt); ?>
                        </p>
                    </div>
                </article>
            </div>

            <!-- ستون وسط: Thumbnails تعاملی (JS‌محور) -->
            <div class="daily-interactive-thumbs hidden lg:block">
                <div class="interactive-thumb-list layout-gap-md" id="interactive-thumb-list">
                    <?php foreach ($interactive_items as $index => $item) :
                        $t_title   = $item['title'] ?? '';
                        $t_url     = $item['url'] ?? home_url('/');
                        $t_img     = revayat_resolve_image_url($item['thumbnail_url'] ?? null, 'news');
                        $t_excerpt = $item['excerpt'] ?? '';
                        $t_date    = $item['date'] ?? '';
                        $t_author  = $item['author_name'] ?? '';
                        $t_avatar  = revayat_resolve_image_url($item['author_avatar'] ?? null, 'avatar');
                        $t_cat     = $item['category'] ?? '';
                    ?>
                        <div class="interactive-thumb-item <?php echo $index === 0 ? 'is-active' : ''; ?>"
                             data-index="<?php echo esc_attr($index); ?>"
                             data-img="<?php echo esc_url($t_img); ?>"
                             data-title="<?php echo esc_attr($t_title); ?>"
                             data-excerpt="<?php echo esc_attr($t_excerpt); ?>"
                             data-author="<?php echo esc_attr($t_author); ?>"
                             data-avatar="<?php echo esc_url($t_avatar); ?>"
                             data-tag="<?php echo esc_attr($t_cat); ?>"
                             data-date="<?php echo esc_attr($t_date); ?>"
                             data-link="<?php echo esc_url($t_url); ?>">
                            <img src="<?php echo esc_url($t_img); ?>" alt="<?php echo esc_attr($t_title); ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- ستون چپ: info panel تعاملی -->
            <div class="daily-interactive-info hidden lg:block" id="interactive-info-panel">
                <div class="info-panel__inner panel-pad">
                    <span class="info-panel__tag" id="info-panel-tag"><?php echo esc_html($fp_cat); ?></span>
                    <h3 class="info-panel__title" id="info-panel-title"><?php echo esc_html($fp_title); ?></h3>
                    <p class="info-panel__excerpt" id="info-panel-excerpt"><?php echo esc_html($fp_excerpt); ?></p>

                    <div class="info-panel__meta">
                        <div class="info-panel__author">
                            <img src="<?php echo esc_url($fp_avatar); ?>" id="info-panel-avatar" alt="<?php echo esc_attr($fp_author); ?>">
                            <span id="info-panel-author"><?php echo esc_html($fp_author); ?></span>
                        </div>
                        <span class="info-panel__date" id="info-panel-date"><?php echo esc_html($fp_date); ?></span>
                    </div>

                    <a href="<?php echo esc_url($fp_url); ?>"
                       class="button--compact button--accent info-panel__link"
                       id="info-panel-link">
                        <span><?php esc_html_e('ادامه تحلیل', 'revayatiran'); ?></span>
                        <i class="ph ph-arrow-left"></i>
                    </a>
                </div>
            </div>

            <!-- نمایش موبایل: لیست ساده -->
            <?php if (!empty($interactive_items)) : ?>
                <div class="daily-mobile-items flex flex-col gap-4 lg:hidden w-full col-span-1 md:col-span-2 mt-4 md:mt-0">
                    <?php foreach ($interactive_items as $m_item) :
                        $m_title   = $m_item['title'] ?? '';
                        $m_url     = $m_item['url'] ?? home_url('/');
                        $m_img     = revayat_resolve_image_url($m_item['thumbnail_url'] ?? null, 'news');
                        $m_author  = $m_item['author_name'] ?? '';
                        $m_cat     = $m_item['category'] ?? '';
                    ?>
                        <a href="<?php echo esc_url($m_url); ?>"
                           class="flex gap-4 p-3 bg-white dark:bg-[#1a1c23] border border-[var(--border)] rounded-[20px] shadow-sm hover:border-primary/50 transition-colors">
                            <img src="<?php echo esc_url($m_img); ?>"
                                 alt="<?php echo esc_attr($m_title); ?>"
                                 class="w-24 h-24 md:w-28 md:h-28 rounded-xl object-cover shrink-0"
                                 loading="lazy">
                            <div class="flex flex-col justify-center min-w-0">
                                <?php if (!empty($m_cat)) : ?>
                                    <span class="daily-mobile-category text-[10px] font-bold text-primary mb-1"><?php echo esc_html($m_cat); ?></span>
                                <?php endif; ?>
                                <h4 class="text-[13px] md:text-sm font-bold text-gray-900 dark:text-white line-clamp-2 mb-2 leading-[1.6]">
                                    <?php echo esc_html($m_title); ?>
                                </h4>
                                <?php if (!empty($m_author)) : ?>
                                    <span class="daily-mobile-author text-[11px] text-gray-500 font-medium flex items-center gap-1">
                                        <i class="ph-fill ph-user-circle"></i> <?php echo esc_html($m_author); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div><!-- /.daily-hero-layout -->
    </div><!-- /.container -->
</section>
