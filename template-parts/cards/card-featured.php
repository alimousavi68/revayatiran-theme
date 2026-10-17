<?php
/**
 * کارت شاخص و لید ویژه ویترین (Card Featured / Lead)
 *
 * برای استفاده در Hero section — خبر اول و لید اصلی.
 * پشتیبانی کامل از هر دو حالت:
 *   ۱. ارسال آبجکت WP_Post در Loop وردپرس
 *   ۲. ارسال آرایه قرارداد داده (Data Contract)
 *
 * طبق ADR-003:
 * اتصال به Media Abstraction Layer و حذف کامل وابستگی به لوگوی سایت.
 *
 * پارامترهای $args:
 *   - 'post'          (WP_Post) — آبجکت پست (اختیاری)
 *   - 'title'         (string)  — عنوان پست
 *   - 'url'           (string)  — پیوند
 *   - 'thumbnail_url' (string)  — نشانی تصویر شاخص
 *   - 'category'      (string)  — برچسب دسته
 *   - 'tag_label'     (string)  — متن بج اضافی (پیش‌فرض: "تحلیل سردبیر")
 *   - 'date'          (string)  — تاریخ انتشار
 *
 * @package RevayatIran
 * @subpackage Template_Parts/Cards
 * @version 2.1.0 (Pass 2B — Media Abstraction Integration)
 */

if (!defined('ABSPATH')) {
    exit;
}

$card_post = $args['post'] ?? null;

if ($card_post instanceof WP_Post) {
    $card_title     = get_the_title($card_post);
    $card_url       = get_the_permalink($card_post);
    $card_thumbnail = revayat_get_post_image_url($card_post, 'revayat-hero-large', 'hero');
    $card_date      = get_the_date('', $card_post);
    $card_category  = $args['category'] ?? '';

    if (empty($card_category)) {
        $primary_cat   = revayat_get_primary_category($card_post->ID);
        $card_category = $primary_cat ? $primary_cat->name : esc_html__('رصد ویژه', 'revayatiran');
    }
} else {
    $card_title     = $args['title'] ?? '';
    $card_url       = $args['url'] ?? home_url('/');
    $card_thumbnail = revayat_resolve_image_url($args['thumbnail_url'] ?? null, 'hero');
    $card_date      = $args['date'] ?? '';
    $card_category  = $args['category'] ?? esc_html__('تحلیل راهبردی', 'revayatiran');
}

if (empty($card_title)) {
    return;
}
?>
<article class="hero-feature group">
    <div class="hero-feature__media">
        <img src="<?php echo esc_url($card_thumbnail); ?>"
             alt="<?php echo esc_attr($card_title); ?>"
             loading="eager">
    </div>

    <?php if (!empty($card_category)) : ?>
        <div class="absolute top-5 right-5 z-[5]">
            <span class="bg-primary text-white text-[11px] font-bold px-3 py-1.5 rounded-full shadow-lg border border-white/10">
                <?php echo esc_html($card_category); ?>
            </span>
        </div>
    <?php endif; ?>

    <div class="hero-feature__content">
        <h2 class="hero-feature-title">
            <a href="<?php echo esc_url($card_url); ?>" class="hover:text-white transition-colors">
                <?php echo esc_html($card_title); ?>
            </a>
        </h2>
        <div class="w-10 h-[2px] bg-primary rounded-full mt-3 mb-2 opacity-0 group-hover:opacity-100 transition-all duration-500 transform translate-y-2 group-hover:translate-y-0"></div>
        <div class="meta-row mt-2">
            <?php if (!empty($card_date)) : ?>
                <span><i class="ph ph-clock align-middle text-sm"></i> <?php echo esc_html($card_date); ?></span>
            <?php endif; ?>
        </div>
    </div>
</article>
