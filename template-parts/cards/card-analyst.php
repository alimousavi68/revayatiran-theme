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
$author_url    = $item['author_url'] ?? ($item['author']['url'] ?? get_author_posts_url((int) get_post_field('post_author', $id)));
$author_role   = $item['author_role'] ?? ($item['author']['role'] ?? ($args['author_meta'] ?? ''));

$score = $item['score'] ?? ($item['analyst_score'] ?? ($args['score'] ?? '—'));
$score_str = function_exists('revayat_en2fa') ? revayat_en2fa($score) : $score;
$votes_count = absint( $item['votes_count'] ?? ( $item['analyst_votes'] ?? 0 ) );
$comments_count = $id ? (int) get_comments_number( $id ) : 0;

$field_name = $item['field_name'] ?? ($item['taxonomies']['analyst_field'] ?? ($item['analysis_type'] ?? ($args['field_name'] ?? '')));
$field_url  = $item['field_url'] ?? '';
$content_type = $item['analysis_type'] ?? ( $item['meta']['analysis_type'] ?? __( 'یادداشت تحلیلی', 'revayatiran' ) );
$excerpt    = $item['excerpt'] ?? ($item['summary'] ?? ($item['desc'] ?? ($args['desc'] ?? wp_strip_all_tags(get_the_excerpt($id)))));

// تصویر شاخص
$thumbnail = $item['thumbnail_url'] ?? (!empty($item['media']['url']) ? $item['media']['url'] : (has_post_thumbnail($id) ? get_the_post_thumbnail_url($id, 'large') : ''));
if (empty($title)) {
    return;
}

// ═══════════════════════════════════════════════════════════════
// واریانت ۱: کارت شبکه‌ای عمودی (Grid Variant) مخصوص آرشیو و مطالب مرتبط
// ═══════════════════════════════════════════════════════════════
if ($variant === 'grid') : ?>
    <article class="an-card--grid anx-post-card" data-post-id="<?php echo esc_attr( $id ); ?>">
        <div class="anx-post-card__content<?php echo $thumbnail ? '' : ' has-no-media'; ?>">
            <div class="anx-post-card__copy">
                <a class="anx-post-card__author" href="<?php echo esc_url( $author_url ); ?>">
                    <?php if ( ! empty( $author_avatar ) ) : ?>
                        <img src="<?php echo esc_url( $author_avatar ); ?>" alt="<?php echo esc_attr( $author_name ); ?>" loading="lazy" decoding="async" width="44" height="44">
                    <?php else : ?>
                        <span class="anx-post-card__avatar-placeholder" aria-hidden="true"><i class="ph ph-user"></i></span>
                    <?php endif; ?>
                    <span>
                        <strong><?php echo esc_html( $author_name ); ?></strong>
                        <?php if ( $author_role ) : ?><small><?php echo esc_html( $author_role ); ?></small><?php endif; ?>
                        <?php if ( $date ) : ?><time><?php echo esc_html( function_exists( 'revayat_en2fa' ) ? revayat_en2fa( $date ) : $date ); ?></time><?php endif; ?>
                    </span>
                </a>
                <h3><a href="<?php echo esc_url( $url ); ?>"><?php echo wp_kses_post( $title ); ?></a></h3>
                <?php if ( $excerpt ) : ?><p><?php echo esc_html( wp_trim_words( $excerpt, 34, '…' ) ); ?></p><?php endif; ?>
            </div>
            <?php if ( $thumbnail ) : ?>
                <a class="anx-post-card__media" href="<?php echo esc_url( $url ); ?>" tabindex="-1" aria-hidden="true">
                    <img src="<?php echo esc_url( $thumbnail ); ?>" <?php echo revayat_responsive_image_attributes((int) ($item['thumbnail_id'] ?? ($item['media']['id'] ?? get_post_thumbnail_id($id))), '(max-width: 767px) calc(100vw - 32px), 400px'); ?> alt="" loading="lazy" decoding="async" width="800" height="450">
                </a>
            <?php endif; ?>
        </div>

        <footer class="anx-post-card__actions">
            <span title="<?php echo esc_attr( $votes_count ? sprintf( __( '%s رأی', 'revayatiran' ), $votes_count ) : __( 'امتیاز اولیه؛ بدون رأی کاربران', 'revayatiran' ) ); ?>">
                <i class="ph ph-chart-bar"></i>
                <?php echo is_numeric( $score ) && (float) $score > 0 ? esc_html( sprintf( __( '%s از ۵', 'revayatiran' ), $score_str ) ) : esc_html__( 'بدون امتیاز', 'revayatiran' ); ?>
            </span>
            <a href="<?php echo esc_url( $url . '#comments' ); ?>"><i class="ph ph-chat-circle"></i><?php echo esc_html( function_exists( 'revayat_en2fa' ) ? revayat_en2fa( $comments_count ) : $comments_count ); ?> <?php esc_html_e( 'نظر', 'revayatiran' ); ?></a>
            <button type="button" class="anx-post-card__share" data-anx-share data-share-url="<?php echo esc_url( $url ); ?>" data-share-title="<?php echo esc_attr( wp_strip_all_tags( $title ) ); ?>"><i class="ph ph-share-network"></i><span><?php esc_html_e( 'اشتراک', 'revayatiran' ); ?></span></button>
            <a class="anx-post-card__read" href="<?php echo esc_url( $url ); ?>"><?php esc_html_e( 'مطالعه یادداشت', 'revayatiran' ); ?><i class="ph ph-arrow-left"></i></a>
        </footer>
    </article>

<?php
// ═══════════════════════════════════════════════════════════════
// واریانت ۳: کارت افقی فشرده مطالب مرتبط (Compact Related Editorial Card)
// ═══════════════════════════════════════════════════════════════
elseif ($variant === 'related') : ?>
    <article class="anx-related-card" data-post-id="<?php echo esc_attr( $id ); ?>">
        <a href="<?php echo esc_url( $url ); ?>" class="anx-related-card__link">
            <?php if ( $thumbnail ) : ?>
                <div class="anx-related-card__media">
                    <img src="<?php echo esc_url( $thumbnail ); ?>" <?php echo revayat_responsive_image_attributes((int) ($item['thumbnail_id'] ?? ($item['media']['id'] ?? get_post_thumbnail_id($id))), '(max-width: 767px) calc(100vw - 32px), 400px'); ?> alt="" loading="lazy" decoding="async" width="160" height="106">
                </div>
            <?php else : ?>
                <div class="anx-related-card__media anx-related-card__media--placeholder">
                    <i class="ph ph-newspaper"></i>
                </div>
            <?php endif; ?>
            <div class="anx-related-card__content">
                <div class="anx-related-card__author">
                    <?php if ( ! empty( $author_avatar ) ) : ?>
                        <img src="<?php echo esc_url( $author_avatar ); ?>" alt="" width="20" height="20">
                    <?php endif; ?>
                    <span><?php echo esc_html( $author_name ); ?></span>
                </div>
                <h3 class="anx-related-card__title"><?php echo wp_kses_post( $title ); ?></h3>
                <div class="anx-related-card__meta">
                    <?php if ( $date ) : ?>
                        <time><i class="ph ph-calendar-blank"></i><?php echo esc_html( function_exists( 'revayat_en2fa' ) ? revayat_en2fa( $date ) : $date ); ?></time>
                    <?php endif; ?>
                    <?php if ( is_numeric( $score ) && (float) $score > 0 ) : ?>
                        <span class="anx-related-card__score" title="<?php esc_attr_e( 'امتیاز ارزیابی', 'revayatiran' ); ?>">
                            <i class="ph-fill ph-star"></i>
                            <?php echo esc_html( $score_str ); ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </a>
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
                        <i class="ph ph-chart-bar"></i>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="an-text-card__avatar-wrap">
            <?php if ( $author_avatar ) : ?><img src="<?php echo esc_url($author_avatar); ?>"
                 alt="<?php echo esc_attr($author_name); ?>"
                 class="w-full h-full object-cover object-center"
                 loading="lazy"><?php else : ?><i class="ph ph-user" aria-hidden="true"></i><?php endif; ?>
        </div>
    </article>
<?php endif;
