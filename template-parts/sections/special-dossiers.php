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

$dossiers_data = function_exists('revayat_get_homepage_data') ? revayat_get_homepage_data('special-dossiers') : (function_exists('revayat_get_special_dossiers_data') ? revayat_get_special_dossiers_data() : []);
$lead          = $dossiers_data['lead'] ?? [];
$sidebar       = $dossiers_data['sidebar'] ?? [];
$stats         = $lead['stats'] ?? [];
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

        <div class="dossier-layout-v2" data-reveal="up">
            <!-- پرونده اصلی و فعال -->
            <?php if (!empty($lead)) : ?>
                <article class="dossier-hero-card">
                    <div class="dossier-hero-visual">
                        <img src="<?php echo esc_url(revayat_resolve_image_url($lead['thumbnail_url'] ?? null, 'news')); ?>" alt="<?php echo esc_attr($lead['title'] ?? ''); ?>" class="w-full h-full object-cover" loading="lazy">
                        <?php if (!empty($lead['is_live'])) : ?>
                            <div class="dossier-hero-visual__overlay">
                                <div class="live-indicator">
                                    <span class="live-indicator__dot"></span>
                                    <span><?php esc_html_e('پرونده فعال و در جریان', 'revayatiran'); ?></span>
                                </div>
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
                        <?php if (!empty($stats)) : ?>
                            <div class="dossier-features-row">
                                <?php if (!empty($stats['docs'])) : ?>
                                    <div class="dossier-feature-pill">
                                        <i class="ph-fill ph-file-pdf"></i>
                                        <div class="pill-info">
                                            <strong><?php echo esc_html($stats['docs']); ?></strong>
                                            <?php if (!empty($stats['docs_desc'])) : ?>
                                                <span><?php echo esc_html($stats['docs_desc']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($stats['frames'])) : ?>
                                    <div class="dossier-feature-pill">
                                        <i class="ph-fill ph-image"></i>
                                        <div class="pill-info">
                                            <strong><?php echo esc_html($stats['frames']); ?></strong>
                                            <?php if (!empty($stats['frames_desc'])) : ?>
                                                <span><?php echo esc_html($stats['frames_desc']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($stats['notes'])) : ?>
                                    <div class="dossier-feature-pill">
                                        <i class="ph-fill ph-chat-centered-text"></i>
                                        <div class="pill-info">
                                            <strong><?php echo esc_html($stats['notes']); ?></strong>
                                            <?php if (!empty($stats['notes_desc'])) : ?>
                                                <span><?php echo esc_html($stats['notes_desc']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

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
                            <?php if (!empty($dossier_item['sections_count'])) : ?>
                                <span><i class="ph ph-file-text"></i> <?php echo esc_html($dossier_item['sections_count']); ?></span>
                            <?php endif; ?>
                            <?php if (!empty($dossier_item['date'])) : ?>
                                <span><i class="ph ph-calendar-blank"></i> <?php echo esc_html($dossier_item['date']); ?></span>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </aside>
        </div>
    </div>
</section>
