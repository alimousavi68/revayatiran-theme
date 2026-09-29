<?php
/**
 * کامپوننت کارت دیده‌بان رسانه (Media Observatory Card)
 *
 * انطباق ۱۰۰٪ با دیزاین سیستم اصلی و الگوی کارت‌های تقابل و تحلیل فریمینگ.
 * قابل استفاده مجدد در گرید صفحه اصلی، آرشیو دیده‌بان و بخش مطالب مرتبط.
 *
 * @package RevayatIran
 * @subpackage Template_Parts/Cards
 * @version 2.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$item = $args['item'] ?? ($args['observatory'] ?? []);
if (empty($item)) {
    if (isset($args['post'])) {
        $item = function_exists('revayat_get_observatory_single_data') ? revayat_get_observatory_single_data($args['post']) : (function_exists('revayat_get_single_data') ? revayat_get_single_data($args['post']) : []);
    } else {
        $item = function_exists('revayat_get_observatory_single_data') ? revayat_get_observatory_single_data(get_the_ID()) : (function_exists('revayat_get_single_data') ? revayat_get_single_data(get_the_ID()) : []);
    }
}

if (empty($item)) {
    return;
}

$id    = $item['id'] ?? (get_the_ID() ?: 0);
$title = $item['title'] ?? get_the_title($id);
$url   = $item['url'] ?? ($item['permalink'] ?? get_permalink($id));
$date  = $item['date'] ?? get_the_date('', $id);

// نشان دسته‌بندی و تحلیل دیده‌بان
$raw_badge   = $item['badge_slug'] ?? ($item['taxonomies']['observatory_badge'] ?? 'discourse');
$badge_label = $item['badge_label'] ?? ($item['taxonomies']['observatory_badge_label'] ?? ($item['taxonomies']['observatory_badge'] ?? __('تحلیل گفتمان', 'revayatiran')));

$badge_slug_map = [
    'discourse'      => 'obs-badge--discourse',
    'framing'        => 'obs-badge--framing',
    'campaign'       => 'obs-badge--campaign',
    'تحلیل گفتمان'   => 'obs-badge--discourse',
    'مقایسه فریمینگ' => 'obs-badge--framing',
    'کمپین‌شناسی'    => 'obs-badge--campaign',
];

$badge_class = $badge_slug_map[$raw_badge] ?? ($item['badge_class'] ?? ($item['badge_css'] ?? 'obs-badge--discourse'));

// فریم‌های تقابلی A و B
$frames  = $item['frames'] ?? ($item['meta']['frames'] ?? []);
$frame_a = $item['frame_a'] ?? ($frames['frame_a'] ?? []);
$frame_b = $item['frame_b'] ?? ($frames['frame_b'] ?? []);

// چکیده یا جمع‌بندی تحلیلی
$summary = $item['summary'] ?? ($item['narrative_summary'] ?? ($item['meta']['narrative_summary'] ?? ($item['excerpt'] ?? '')));
?>

<article class="observatory-card">
    <div class="observatory-card__header">
        <?php if (!empty($badge_label)) : ?>
            <span class="obs-badge <?php echo esc_attr($badge_class); ?>">
                <?php echo esc_html($badge_label); ?>
            </span>
        <?php endif; ?>

        <?php if (!empty($date)) : ?>
            <span class="obs-date">
                <i class="ph ph-calendar-blank"></i>
                <time><?php echo esc_html(function_exists('revayat_en2fa') ? revayat_en2fa($date) : $date); ?></time>
            </span>
        <?php endif; ?>
    </div>

    <h3 class="observatory-card__title">
        <a href="<?php echo esc_url($url); ?>">
            <?php echo esc_html($title); ?>
        </a>
    </h3>

    <?php if (!empty($frame_a['text']) || !empty($frame_b['text']) || !empty($frame_a['source']) || !empty($frame_b['source'])) : ?>
        <!-- بلوک تقابل فریم‌های رسانه‌ای -->
        <div class="obs-framing-box">
            <?php if (!empty($frame_a['text']) || !empty($frame_a['source'])) : ?>
                <div class="obs-frame-item obs-frame-item--a">
                    <?php if (!empty($frame_a['source'])) : ?>
                        <span class="obs-frame-source"><?php echo esc_html($frame_a['source']); ?></span>
                    <?php endif; ?>
                    <?php if (!empty($frame_a['text'])) : ?>
                        <p class="obs-frame-text"><?php echo esc_html($frame_a['text']); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($frame_b['text']) || !empty($frame_b['source'])) : ?>
                <div class="obs-frame-item obs-frame-item--b">
                    <?php if (!empty($frame_b['source'])) : ?>
                        <span class="obs-frame-source"><?php echo esc_html($frame_b['source']); ?></span>
                    <?php endif; ?>
                    <?php if (!empty($frame_b['text'])) : ?>
                        <p class="obs-frame-text"><?php echo esc_html($frame_b['text']); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($summary)) : ?>
        <p class="observatory-card__summary">
            <?php echo esc_html($summary); ?>
        </p>
    <?php endif; ?>

    <div class="observatory-card__footer">
        <a href="<?php echo esc_url($url); ?>" class="obs-read-more">
            <span><?php esc_html_e('واکاوی کامل روایت', 'revayatiran'); ?></span>
            <i class="ph ph-arrow-left"></i>
        </a>
    </div>
</article>
