<?php
/** Shared public sharing, recommendations and native discussion. */
if (!defined('ABSPATH') || post_password_required()) { return; }
$id = get_the_ID();
$url = get_permalink($id);
$title = get_the_title($id);
$items = [];
$heading = __('مطالب پیشنهادی', 'revayatiran');
$is_analyst_post = get_post_type($id) === 'analyst_post';
if ($is_analyst_post) {
    $items = function_exists('revayat_get_related_posts') ? revayat_get_related_posts($id, 3) : [];
    $heading = __('تحلیل‌های مرتبط', 'revayatiran');
} elseif (get_post_type($id) === 'special_dossier') {
    $items = [];
} elseif (class_exists('Revayat_Companion_Contextual_Widget')) {
    if (get_post_type($id) !== 'special_dossier') {
        $items = Revayat_Companion_Contextual_Widget::items(['mode' => 'dossier', 'fallback' => false, 'number' => 3], $id);
    }
    if ($items) { $heading = __('مطالب مرتبط با این پرونده', 'revayatiran'); }
    else {
        $items = Revayat_Companion_Contextual_Widget::items(['mode' => 'related', 'fallback' => false, 'number' => 3], $id);
        if ($items) { $heading = __('مطالب مرتبط', 'revayatiran'); }
        else { $items = Revayat_Companion_Contextual_Widget::items(['mode' => 'recent', 'number' => 3], $id); }
    }
} else {
    $items = get_posts(['post_type' => get_post_type($id), 'post_status' => 'publish', 'has_password' => false, 'post__not_in' => [$id], 'posts_per_page' => 3]);
}
?>
<section class="rv-engagement" aria-label="<?php esc_attr_e('اشتراک‌گذاری و ادامه مطالعه', 'revayatiran'); ?>">
    <?php if ( ! $is_analyst_post ) : ?>
        <?php get_template_part('template-parts/single/share'); ?>
    <?php endif; ?>
    <?php if ($items) : ?>
    <section class="rv-reading-related<?php echo $is_analyst_post ? ' rv-reading-related--analysts' : ''; ?>">
        <?php if ($is_analyst_post) : ?>
            <div class="anx-related-header">
                <div class="anx-related-header__title">
                    <span class="anx-related-header__dot"></span>
                    <h2><?php echo esc_html($heading); ?></h2>
                </div>
                <span class="anx-related-header__hint"><?php esc_html_e('پیشنهاد تحریریه', 'revayatiran'); ?></span>
            </div>
        <?php else : ?>
            <h2><?php echo esc_html($heading); ?></h2>
        <?php endif; ?>
        <div class="rv-reading-related__grid">
        <?php foreach ($items as $item) : ?>
            <?php if ($is_analyst_post) : ?>
                <?php get_template_part('template-parts/cards/card-analyst', null, ['item' => $item, 'variant' => 'related']); ?>
            <?php else : ?>
                <article><a href="<?php echo esc_url(get_permalink($item->ID)); ?>">
                    <?php if (has_post_thumbnail($item->ID)) { echo get_the_post_thumbnail($item->ID, 'medium', ['loading' => 'lazy', 'alt' => '']); } ?>
                    <h3><?php echo wp_kses_post(get_the_title($item->ID)); ?></h3>
                    <time datetime="<?php echo esc_attr(get_the_date(DATE_W3C, $item->ID)); ?>"><?php echo esc_html(revayat_en2fa(get_the_date('', $item->ID))); ?></time>
                </a></article>
            <?php endif; ?>
        <?php endforeach; ?>
    </div></section>
    <?php endif; ?>
</section>
<?php if (comments_open($id) || get_comments_number($id)) { comments_template(); } ?>
