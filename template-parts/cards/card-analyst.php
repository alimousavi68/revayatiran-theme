<?php
/**
 * کارت کارشناس و یادداشت تحلیلی (Card Analyst - Dual Variant)
 *
 * منبع اصلی حقیقت (Single Source of Truth) برای رندر کارت‌های یادداشت تحلیلی در شبکه تحلیل‌گران، آرشیو و مطالب مرتبط.
 * پشتیبانی از دو واریانت:
 *   ۱. 'grid': کارت عمودی ۳ ستونه استاندارد آرشیو با تصویر شاخص، بج حوزه تخصصی، لید، نویسنده و نشان نمره طلایی.
 *   ۲. 'compact': کارت متنی افقی فشرده برای اسلایدر صفحه اول (تضمین عدم رگرسیون در صفحه اصلی).
 *
 * پشتیبانی از قرارداد داده‌ای هیبریدی ($args['item']) یا آبجکت بومی WP_Post ($args['post'] یا Loop وردپرس).
 *
 * @package RevayatIran
 * @subpackage Template_Parts/Cards
 * @version 2.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$variant = $args['variant'] ?? ($args['layout'] ?? 'compact');

// استخراج داده‌ها از $args['item'] یا $args['post'] یا پارامترهای مسطح
$item = $args['item'] ?? [];
if (empty($item) && isset($args['post'])) {
    $item = function_exists('revayat_get_analyst_post_single_data') ? revayat_get_analyst_post_single_data($args['post']) : (function_exists('revayat_get_single_data') ? revayat_get_single_data($args['post']) : []);
} elseif (empty($item) && empty($args['title']) && get_the_ID()) {
    $item = function_exists('revayat_get_analyst_post_single_data') ? revayat_get_analyst_post_single_data(get_the_ID()) : (function_exists('revayat_get_single_data') ? revayat_get_single_data(get_the_ID()) : []);
}

$id    = $item['id'] ?? (get_the_ID() ?: 0);
$title = $item['title'] ?? ($args['title'] ?? get_the_title($id));
$url   = $item['url'] ?? ($item['permalink'] ?? ($args['url'] ?? get_permalink($id)));
$date  = $item['date'] ?? ($args['date'] ?? get_the_date('', $id));

$author_name   = $item['author_name'] ?? ($item['author']['name'] ?? ($args['author_name'] ?? ($args['author'] ?? get_the_author_meta('display_name', get_post_field('post_author', $id)))));
$author_avatar = $item['author_avatar'] ?? ($item['author']['avatar'] ?? ($args['author_avatar'] ?? get_avatar_url(get_post_field('post_author', $id), ['size' => 64])));
$author_url    = $item['author_url'] ?? ($item['author']['url'] ?? home_url('/person/' . sanitize_title($author_name) . '/'));
$author_role   = $item['author_role'] ?? ($item['author']['role'] ?? ($args['author_meta'] ?? ''));

$score = $item['score'] ?? ($item['analyst_score'] ?? ($args['score'] ?? '۴.۸'));
$score_str = function_exists('revayat_en2fa') ? revayat_en2fa($score) : $score;

$field_name = $item['field_name'] ?? ($item['taxonomies']['analyst_field'] ?? ($item['analysis_type'] ?? ($args['field_name'] ?? '')));
$excerpt    = $item['excerpt'] ?? ($item['summary'] ?? ($item['desc'] ?? ($args['desc'] ?? wp_strip_all_tags(get_the_excerpt($id)))));

// تصویر شاخص
$thumbnail = $item['thumbnail_url'] ?? (!empty($item['media']['url']) ? $item['media']['url'] : (has_post_thumbnail($id) ? get_the_post_thumbnail_url($id, 'large') : ''));
if (empty($thumbnail) && function_exists('revayat_resolve_image_url')) {
    $thumbnail = revayat_resolve_image_url(null, 'news');
}

if (empty($title)) {
    return;
}

// ═══════════════════════════════════════════════════════════════
// واریانت ۱: کارت شبکه‌ای عمودی (Grid Variant) مخصوص آرشیو و مطالب مرتبط
// ═══════════════════════════════════════════════════════════════
if ($variant === 'grid') : ?>
    <article class="an-card--grid">
        <a href="<?php echo esc_url($url); ?>" class="an-card--grid__thumb-link">
            <?php if (!empty($thumbnail)) : ?>
                <img src="<?php echo esc_url($thumbnail); ?>"
                     alt="<?php echo esc_attr($title); ?>"
                     class="an-card--grid__thumb"
                     loading="lazy" decoding="async" width="800" height="450">
            <?php endif; ?>

            <?php if (!empty($field_name)) : ?>
                <span class="an-card--grid__field-badge">
                    <?php echo esc_html($field_name); ?>
                </span>
            <?php endif; ?>
        </a>

        <div class="an-card--grid__body">
            <h3 class="an-card--grid__title">
                <a href="<?php echo esc_url($url); ?>">
                    <?php echo esc_html($title); ?>
                </a>
            </h3>

            <?php if (!empty($excerpt)) : ?>
                <p class="an-card--grid__excerpt">
                    <?php echo esc_html($excerpt); ?>
                </p>
            <?php endif; ?>

            <div class="an-card--grid__footer">
                <div class="an-card--grid__author">
                    <?php if (!empty($author_avatar)) : ?>
                        <img src="<?php echo esc_url($author_avatar); ?>"
                             alt="<?php echo esc_attr($author_name); ?>"
                             class="an-card--grid__avatar"
                             loading="lazy" decoding="async" width="64" height="64">
                    <?php endif; ?>
                    <div class="an-card--grid__author-info">
                        <span class="an-card--grid__author-name"><?php echo esc_html($author_name); ?></span>
                        <?php if (!empty($date)) : ?>
                            <span class="an-card--grid__date"><?php echo esc_html(function_exists('revayat_en2fa') ? revayat_en2fa($date) : $date); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="an-score-badge--amber">
                    <strong><?php echo esc_html($score_str); ?></strong>
                    <i class="ph ph-check"></i>
                </div>
            </div>
        </div>
    </article>

<?php
// ═══════════════════════════════════════════════════════════════
// واریانت ۲: کارت فشرده افقی (Compact Variant) مخصوص اسلایدر صفحه نخست
// ═══════════════════════════════════════════════════════════════
else : ?>
    <article class="an-text-card group items-center">
        <div class="an-text-card__body">
            <h5 class="an-text-card__title">
                <a href="<?php echo esc_url($url); ?>" class="hover:text-primary transition-colors line-clamp-2">
                    <?php echo esc_html($title); ?>
                </a>
            </h5>
            <div class="an-text-card__meta">
                <span class="an-author"><?php echo esc_html($author_name); ?></span>
                <?php if ($score !== '—' && !empty($score)) : ?>
                    <div class="an-text-card__score">
                        <strong><?php echo esc_html($score_str); ?></strong>
                        <i class="ph ph-check"></i>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="an-text-card__avatar-wrap">
            <img src="<?php echo esc_url($author_avatar); ?>"
                 alt="<?php echo esc_attr($author_name); ?>"
                 class="w-full h-full object-cover object-center"
                 loading="lazy">
        </div>
    </article>
<?php endif;
