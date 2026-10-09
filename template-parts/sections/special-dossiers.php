<?php
/**
 * بخش پرونده‌های ویژه (Special Dossiers Section)
 *
 * @package RevayatIran
 * @subpackage Template_Parts/Sections
 */

if (!defined('ABSPATH')) {
    exit;
}

$dossiers_data = function_exists('revayat_get_homepage_data') ? revayat_get_homepage_data('special-dossiers') : [];
$lead          = $dossiers_data['lead'] ?? [];
$sidebar       = $dossiers_data['sidebar'] ?? [];
?>
<section class="section-shell section-spacing" id="special-dossiers">
    <div class="container">
        <div class="section-head section-head--etude-b" data-reveal="up">
            <div class="etude-b__leading">
                <div class="etude-b__icon-badge">
                    <i class="ph ph-folder-open"></i>
                </div>
                <h2><?php echo esc_html( revayat_home_text( 'special-dossiers', 'title' ) ); ?></h2>
                <p class="section-summary"><?php echo esc_html( revayat_home_text( 'special-dossiers', 'description' ) ); ?></p>
            </div>
            <div class="etude-b__actions">
                <a href="<?php echo esc_url(home_url('/dossiers/')); ?>" class="section-link">
                    <span><?php esc_html_e('آرشیو تمام پرونده‌ها', 'revayatiran'); ?></span>
                    <i class="ph ph-arrow-left"></i>
                </a>
            </div>
        </div>

        <?php if (empty($lead) && empty($sidebar)) : revayat_render_home_empty_state(__('هنوز پرونده‌ای برای نمایش منتشر نشده است.', 'revayatiran')); else : ?>
        <div class="dossier-layout-v2" data-reveal="up">
            <!-- پرونده اصلی و فعال -->
            <?php if (!empty($lead)) : ?>
                <article class="dossier-hero-card">
                    <div class="dossier-hero-visual">
                        <img src="<?php echo esc_url(revayat_resolve_image_url($lead['thumbnail_url'] ?? null, 'news')); ?>" alt="<?php echo esc_attr($lead['title'] ?? ''); ?>" class="w-full h-full object-cover" loading="lazy" decoding="async" <?php echo revayat_responsive_image_attributes((int) ($lead['thumbnail_id'] ?? 0), '(max-width: 767px) calc(100vw - 32px), 600px'); ?>>
                        <?php if (!empty($lead['is_live'])) : ?>
                            <div class="dossier-hero-visual__overlay">
                                <div class="live-indicator">
                                    <span class="live-indicator__dot"></span>
                                    <span><?php esc_html_e('در حال پیگیری', 'revayatiran'); ?></span>
                                </div>
                            </div>
                        <?php else : ?>
                            <div class="dossier-hero-visual__overlay">
                                <span class="dossier-category-badge"><?php esc_html_e('تکمیل‌شده', 'revayatiran'); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="dossier-hero-content">
                        <?php if (!empty($lead['last_update'])) : ?>
                            <div class="dossier-top-meta">
                                <span class="dossier-update-time"><i class="ph ph-clock"></i> <?php echo esc_html($lead['last_update']); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <h3 class="dossier-hero-title">
                            <?php echo esc_html($lead['title'] ?? ''); ?>
                        </h3>
                        
                        <?php if (!empty($lead['excerpt'])) : ?>
                            <p class="dossier-excerpt">
                                <?php echo esc_html($lead['excerpt']); ?>
                            </p>
                        <?php endif; ?>

                        <!-- اقلام داده‌ای و ضمایم پرونده -->
                        <?php get_template_part('template-parts/dossier/counts', null, ['id' => $lead['id'] ?? 0, 'variant' => 'hero']); ?>

                        <div class="dossier-footer">
                            <a href="<?php echo esc_url($lead['url'] ?? home_url('/dossiers/')); ?>" class="btn-dossier-enter">
                                <span><?php esc_html_e('ورود به پرونده جامع', 'revayatiran'); ?></span>
                                <i class="ph-bold ph-arrow-left"></i>
                            </a>
                            <?php if (!empty($lead['editor_lead'])) : ?>
                                <span class="dossier-lead-author">
                                    <i class="ph ph-user"></i> <?php esc_html_e('دبیر پرونده:', 'revayatiran'); ?> <strong><?php echo esc_html($lead['editor_lead']); ?></strong>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endif; ?>

            <!-- ستون پرونده‌های تکمیلی و مرتبط -->
            <aside class="dossier-sidebar-list">
                <div class="dossier-sidebar-header">
                    <i class="ph ph-stack"></i>
                    <h4><?php esc_html_e('سایر پرونده‌های شاخص', 'revayatiran'); ?></h4>
                </div>

                <?php foreach ($sidebar as $dossier_item) : ?>
                    <article class="dossier-mini-card">
                        <?php if (!empty($dossier_item['category'])) : ?>
                            <div class="dossier-mini-card__tag"><?php echo esc_html($dossier_item['category']); ?></div>
                        <?php endif; ?>
                        <h5 class="dossier-mini-card__title">
                            <a href="<?php echo esc_url($dossier_item['url'] ?? home_url('/dossiers/')); ?>"><?php echo esc_html($dossier_item['title'] ?? ''); ?></a>
                        </h5>
                        <div class="dossier-mini-card__meta">
                            <?php if (!empty($dossier_item['date'])) : ?>
                                <span><i class="ph ph-calendar-blank"></i> <?php echo esc_html($dossier_item['date']); ?></span>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </aside>
        </div>
        <?php endif; ?>
    </div>
</section>
