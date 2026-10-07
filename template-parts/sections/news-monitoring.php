<?php
/**
 * بخش رصد اخبار (News Monitoring Section)
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

$news_data   = function_exists('revayat_get_homepage_data') ? revayat_get_homepage_data('news-monitoring') : [];
$tabs        = $news_data['tabs'] ?? [
    ['slug' => 'all',      'label' => __('همه', 'revayatiran')],
    ['slug' => 'politics', 'label' => __('سیاست', 'revayatiran')],
    ['slug' => 'economy',  'label' => __('اقتصاد', 'revayatiran')],
    ['slug' => 'security', 'label' => __('امنیت', 'revayatiran')],
];
$active_tab  = $news_data['active_tab'] ?? 'all';
$news_items  = $news_data['items'] ?? [];
?>
<section class="section-shell section-shell--warm section-spacing" id="news-monitoring">
    <div class="container">
        <div class="section-head section-head--etude-b" data-reveal="up">
            <div class="etude-b__leading">
                <div class="etude-b__icon-badge">
                    <i class="ph ph-broadcast"></i>
                </div>
                <h2><?php echo esc_html( revayat_home_text( 'news-monitoring', 'title' ) ); ?></h2>
                <p class="section-summary"><?php echo esc_html( revayat_home_text( 'news-monitoring', 'description' ) ); ?></p>
            </div>
            <div class="etude-b__actions flex items-center gap-2">
                <!-- تب‌های دسکتاپ/تبلت -->
                <div class="tabs hidden sm:flex" role="tablist" aria-label="<?php esc_attr_e('دسته‌بندی اخبار', 'revayatiran'); ?>">
                    <?php foreach ($tabs as $tab) :
                        $is_active = ($tab['slug'] === $active_tab);
                    ?>
                        <button class="tab-button <?php echo $is_active ? 'is-active' : ''; ?>"
                                type="button"
                                role="tab"
                                aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>"
                                data-news-tab="<?php echo esc_attr($tab['slug']); ?>">
                            <?php echo esc_html($tab['label']); ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <!-- منوی کشویی موبایل -->
                <div class="news-mobile-tabs sm:hidden relative inline-block">
                    <select id="news-tab-select"
                            class="bg-surface border border-border text-text text-xs rounded-full px-3 py-1.5 pe-7 appearance-none font-bold cursor-pointer focus:outline-none focus:border-accent"
                            aria-label="<?php esc_attr_e('انتخاب دسته خبر', 'revayatiran'); ?>">
                        <?php foreach ($tabs as $tab) : ?>
                            <option value="<?php echo esc_attr($tab['slug']); ?>" <?php selected($tab['slug'], $active_tab); ?>>
                                <?php echo esc_html($tab['label']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <i class="ph ph-caret-down absolute left-2.5 top-1/2 -translate-y-1/2 text-xs text-text-muted pointer-events-none"></i>
                </div>

                <?php $archive_url = revayat_archive_menu_url( 'news-monitoring' ); if ( $archive_url ) : ?>
                <a href="<?php echo esc_url( $archive_url ); ?>" class="section-link">
                    <span><?php esc_html_e('آرشیو', 'revayatiran'); ?></span>
                    <i class="ph ph-arrow-left"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>

        <?php if (empty($news_items)) : revayat_render_home_empty_state(__('هنوز خبری برای نمایش در رصد اخبار منتشر نشده است.', 'revayatiran')); else : ?>
        <div class="news-layout layout-gap-lg">
            <div class="news-panels">
                <!-- تب همه؛ ساختار برای ارتقای تدریجی به بارگذاری AJAX آماده است. -->
                <div class="news-panel is-active" data-news-panel="all">
                    <div class="swiper news-swiper">
                        <div class="swiper-wrapper">
                            <?php foreach ($news_items as $item) :
                                $card_title     = $item['title'] ?? '';
                                $card_url       = $item['url'] ?? home_url('/');
                                $card_thumbnail = revayat_resolve_image_url($item['thumbnail_url'] ?? null, 'news');
                                $card_category  = $item['category'] ?? '';
                                $card_source    = $item['source_label'] ?? '';
                            ?>
                                <div class="swiper-slide">
                                    <article class="news-card group">
                                        <img src="<?php echo esc_url($card_thumbnail); ?>"
                                             alt="<?php echo esc_attr($card_title); ?>"
                                             class="news-card__img object-cover w-full h-full transition-transform duration-500 group-hover:scale-105"
                                             loading="lazy">
                                        <div class="news-card__overlay">
                                            <div class="news-card__meta">
                                                <?php if (!empty($card_category)) : ?>
                                                    <span class="chip"><?php echo esc_html($card_category); ?></span>
                                                <?php endif; ?>
                                                <?php if (!empty($card_source)) : ?>
                                                    <span class="source-badge"><?php echo esc_html($card_source); ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <h3 class="news-card__title">
                                                <a href="<?php echo esc_url($card_url); ?>" class="hover:text-primary transition-colors">
                                                    <?php echo esc_html($card_title); ?>
                                                </a>
                                            </h3>
                                        </div>
                                    </article>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <!-- کنترل‌های Swiper -->
                        <button type="button" class="swiper-button-prev news-swiper-prev" aria-label="<?php esc_attr_e('قبلی', 'revayatiran'); ?>">
                            <i class="ph-bold ph-caret-right"></i>
                        </button>
                        <button type="button" class="swiper-button-next news-swiper-next" aria-label="<?php esc_attr_e('بعدی', 'revayatiran'); ?>">
                            <i class="ph-bold ph-caret-left"></i>
                        </button>
                        <div class="swiper-pagination news-swiper-pagination"></div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>
