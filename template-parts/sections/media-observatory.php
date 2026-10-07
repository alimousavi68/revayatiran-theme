<?php
/**
 * بخش دیدبان رسانه (Media Observatory Section)
 *
 * @package RevayatIran
 * @subpackage Template_Parts/Sections
 */

if (!defined('ABSPATH')) {
    exit;
}

$obs_data = function_exists('revayat_get_homepage_data') ? revayat_get_homepage_data('media-observatory') : [];
$items    = $obs_data['items'] ?? [];
?>
<section class="section-shell section-shell--gradient section-spacing" id="media-observatory">
    <div class="container">
        <div class="section-head section-head--etude-b" data-reveal="up">
            <div class="etude-b__leading">
                <div class="etude-b__icon-badge">
                    <i class="ph ph-binoculars"></i>
                </div>
                <h2><?php echo esc_html( revayat_home_text( 'media-observatory', 'title' ) ); ?></h2>
                <p class="section-summary"><?php echo esc_html( revayat_home_text( 'media-observatory', 'description' ) ); ?></p>
            </div>
            <div class="etude-b__actions">
                <?php
                $archive_url = get_post_type_archive_link('media_observatory');
                if (!$archive_url || is_wp_error($archive_url)) {
                    $archive_url = home_url('/observatory/');
                }
                ?>
                <a href="<?php echo esc_url($archive_url); ?>" class="section-link">
                    <span><?php esc_html_e('آرشیو پایش رسانه‌ها', 'revayatiran'); ?></span>
                    <i class="ph ph-arrow-left"></i>
                </a>
            </div>
        </div>

        <?php if (!empty($items)) : ?>
            <div class="observatory-grid-v2" data-reveal="up">
                <?php foreach ($items as $item) : ?>
                    <?php get_template_part('template-parts/cards/card-observatory', null, ['item' => $item]); ?>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <?php revayat_render_home_empty_state(__('هنوز گزارشی در دیده‌بان رسانه منتشر نشده است.', 'revayatiran')); ?>
        <?php endif; ?>
    </div>
</section>
