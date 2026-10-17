<?php
/**
 * بخش ویترین و هیرو صفحه اصلی (Hero Section)
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

$hero_data  = function_exists('revayat_get_homepage_data') ? revayat_get_homepage_data('hero') : [];
$lead       = $hero_data['lead'] ?? [];
$side_items = $hero_data['items'] ?? [];

// اطلاعات لید اصلی
$lead_title     = $lead['title'] ?? '';
$lead_url       = $lead['url'] ?? home_url('/');
$lead_thumbnail = revayat_resolve_image_url($lead['thumbnail_url'] ?? null, 'hero');
$lead_origin    = ! empty( $lead['origin_label'] ) ? $lead['origin_label'] : ( function_exists('revayat_get_post_origin_label') ? revayat_get_post_origin_label($lead) : 'روایت روز' );
$lead_date      = $lead['date'] ?? '';
?>
<section class="hero-section" id="hero">
    <div class="hero-grid container">
        <?php if (empty($lead) && empty($side_items)) : revayat_render_home_empty_state(__('هنوز مطلبی برای ویترین اصلی انتخاب نشده است.', 'revayatiran')); else : ?>
        <div class="hero-stage" data-reveal="up">

            <!-- هیرو اصلی (Lead Feature) -->
            <?php if (!empty($lead)) : ?>
                <article class="hero-feature group">
                    <div class="hero-feature__media">
                        <img src="<?php echo esc_url($lead_thumbnail); ?>" <?php echo revayat_responsive_image_attributes((int) ($lead['thumbnail_id'] ?? 0), '(max-width: 1200px) calc(100vw - 32px), 790px'); ?>
                             alt="<?php echo esc_attr($lead_title); ?>"
                             loading="eager"
                             fetchpriority="high"
                             decoding="async">
                    </div>

                    <?php if (!empty($lead_origin)) : ?>
                        <div class="absolute top-5 right-5 z-[5]">
                            <span class="bg-primary text-white text-[11px] font-bold px-3 py-1.5 rounded-full shadow-lg border border-white/10">
                                <?php echo esc_html($lead_origin); ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <div class="hero-feature__content">
                        <h2 class="hero-feature-title">
                            <a href="<?php echo esc_url($lead_url); ?>" class="hover:text-white transition-colors">
                                <?php echo esc_html($lead_title); ?>
                            </a>
                        </h2>
                        <div class="w-10 h-[2px] bg-primary rounded-full mt-3 mb-2 opacity-0 group-hover:opacity-100 transition-all duration-500 transform translate-y-2 group-hover:translate-y-0"></div>
                        <div class="meta-row mt-2">
                            <?php if (!empty($lead_date)) : ?>
                                <span><i class="ph ph-clock align-middle text-sm"></i> <?php echo esc_html($lead_date); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endif; ?>

            <!-- ستون کارت‌های جانبی -->
            <div class="hero-stack" data-stagger>
                <?php foreach ($side_items as $item) :
                    $item_title  = $item['title'] ?? '';
                    $item_url    = $item['url'] ?? home_url('/');
                    $item_origin = ! empty( $item['origin_label'] ) ? $item['origin_label'] : ( function_exists('revayat_get_post_origin_label') ? revayat_get_post_origin_label($item) : 'روایت روز' );
                    $item_thumb  = revayat_resolve_image_url($item['thumbnail_url'] ?? null, 'hero');
                ?>
                    <article class="hero-brief-card group">
                        <div class="skeleton-wrapper">
                            <img src="<?php echo esc_url($item_thumb); ?>" <?php echo revayat_responsive_image_attributes((int) ($item['thumbnail_id'] ?? 0), '(max-width: 767px) 50vw, 320px'); ?>
                                 alt="<?php echo esc_attr($item_title); ?>"
                                 loading="lazy"
                                 decoding="async">
                        </div>
                        <?php if (!empty($item_origin)) : ?>
                            <div class="absolute top-3 right-3 z-[5]">
                                <span class="bg-primary text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow border border-white/10">
                                    <?php echo esc_html($item_origin); ?>
                                </span>
                            </div>
                        <?php endif; ?>
                        <div class="hero-brief-card__content">
                            <h3 class="card-brief-title">
                                <a href="<?php echo esc_url($item_url); ?>"
                                   class="hover:text-white transition-colors before:absolute before:inset-0">
                                    <?php echo esc_html($item_title); ?>
                                </a>
                            </h3>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div><!-- /.hero-stack -->

        </div><!-- /.hero-stage -->
        <?php endif; ?>
    </div><!-- /.hero-grid -->
</section>
