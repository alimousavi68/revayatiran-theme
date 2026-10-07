<?php
/**
 * بخش رسانه‌نگار / چندرسانه‌ای (Multimedia Section)
 *
 * نمایش ریل اسلایدی استوری‌ها در صفحه اصلی با مصرف داده از قرارداد داده (Data Contract).
 * رندر کارت‌ها به صورت یکپارچه از طریق کامپوننت card-media.php انجام می‌شود.
 * ریل اسلایدی توسط ماژول جاوااسکریپتی story-rail در assets/js/main.js کنترل می‌گردد.
 *
 * @package RevayatIran
 * @subpackage Template_Parts/Sections
 * @version 2.0.0 (Pass 2B — Single Source of Truth)
 */

if (!defined('ABSPATH')) {
    exit;
}

$multimedia_data = function_exists('revayat_get_homepage_data') ? revayat_get_homepage_data('multimedia') : [];
$stories         = $multimedia_data['stories'] ?? [];
?>
<section class="section-shell section-spacing" id="multimedia">
    <div class="container">

        <!-- هدر بخش رسانه‌نگار -->
        <div class="section-head section-head--etude-b" data-reveal="up">
            <div class="etude-b__leading">
                <div class="etude-b__icon-badge">
                    <i class="ph ph-circle-dashed"></i>
                </div>
                <h2><?php echo esc_html( revayat_home_text( 'multimedia', 'title' ) ); ?></h2>
                <p class="section-summary"><?php echo esc_html( revayat_home_text( 'multimedia', 'description' ) ); ?></p>
            </div>
            <div class="etude-b__actions">
                <?php $archive_url = revayat_archive_menu_url( 'multimedia' ); if ( $archive_url ) : ?>
                <a href="<?php echo esc_url( $archive_url ); ?>" class="section-link"><span><?php esc_html_e( 'آرشیو رسانه‌نگار', 'revayatiran' ); ?></span><i class="ph ph-arrow-left"></i></a>
                <?php endif; ?>
                <div class="story-rail-nav">
                    <button class="button--compact button--soft story-rail-btn" id="story-rail-prev" aria-label="<?php esc_attr_e('قبلی', 'revayatiran'); ?>">
                        <i class="ph ph-arrow-right"></i>
                    </button>
                    <button class="button--compact button--soft story-rail-btn" id="story-rail-next" aria-label="<?php esc_attr_e('بعدی', 'revayatiran'); ?>">
                        <i class="ph ph-arrow-left"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- ریل اسلایدی استوری‌ها -->
        <?php if (empty($stories)) : revayat_render_home_empty_state(__('هنوز محتوای چندرسانه‌ای منتشر نشده است.', 'revayatiran')); else : ?>
        <div class="story-rail-container" data-reveal="up">
            <div class="story-rail" id="story-rail">
                <?php
                foreach ($stories as $story) {
                    get_template_part('template-parts/cards/card-media', null, $story);
                }
                ?>
            </div><!-- /#story-rail -->
        </div><!-- /.story-rail-container -->
        <?php endif; ?>

    </div><!-- /.container -->
</section>
