<?php
/**
 * کارت استوری و چندرسانه‌ای (Card Media / Story Card)
 *
 * منبع اصلی حقیقت (Single Source of Truth) برای رندر کارت‌های چندرسانه‌ای در story rail و آرشیو.
 * پشتیبانی از هر دو حالت:
 *   ۱. ارسال آبجکت WP_Post در Loop وردپرس
 *   ۲. ارسال آرایه ساختاریافته بر اساس قرارداد داده (Data Contract)
 *
 * پارامترهای $args پشتیبانی‌شده:
 *   - 'post'          (WP_Post) — آبجکت پست (اختیاری)
 *   - 'id'            (int|string) — شناسه استوری
 *   - 'title'         (string)  — عنوان رسانه
 *   - 'url'           (string)  — پیوند رسانه
 *   - 'thumbnail_url' (string)  — نشانی تصویر بندانگشتی
 *   - 'media_type'    (string)  — video | audio | gallery (پیش‌فرض: video)
 *   - 'chip_label'    (string)  — برچسب دسته‌بندی استوری
 *   - 'duration'      (string)  — زمان یا تعداد فریم
 *
 * @package RevayatIran
 * @subpackage Template_Parts/Cards
 * @version 2.0.0 (Pass 2B — Single Source of Truth)
 */

if (!defined('ABSPATH')) {
    exit;
}

$card_post = $args['post'] ?? null;

if ($card_post instanceof WP_Post) {
    $story_id       = $card_post->ID;
    $card_title     = get_the_title($card_post);
    $card_url       = get_the_permalink($card_post);
    $card_thumbnail = get_the_post_thumbnail_url($card_post, 'revayat-card-thumb');
    $media_type     = $args['media_type'] ?? 'video';
    $duration       = $args['duration']   ?? '';
    $chip_label     = $args['chip_label'] ?? esc_html__('مستند تصویری', 'revayatiran');
} else {
    $story_id       = $args['id']            ?? 0;
    $card_title     = $args['title']         ?? '';
    $card_url       = $args['url']           ?? home_url('/');
    $card_thumbnail = $args['thumbnail_url'] ?? '';
    $media_type     = $args['media_type']    ?? ($args['type'] ?? 'video');
    $duration       = $args['duration']      ?? '';
    $chip_label     = $args['chip_label']    ?? ($args['category'] ?? esc_html__('مستند تصویری', 'revayatiran'));
}

if (empty($card_title)) {
    return;
}

// تنظیمات آیکون، برچسب و کلاس‌های تغییردهنده بر اساس نوع رسانه
$type_configs = [
    'video'   => [
        'badge'      => 'ph-fill ph-play',
        'badge_mod'  => '',
        'type_icon'  => 'ph-fill ph-video-camera',
        'type_label' => esc_html__('ویدئو', 'revayatiran'),
        'chip_mod'   => '',
    ],
    'audio'   => [
        'badge'      => 'ph-fill ph-headphones',
        'badge_mod'  => 'story-play-badge--audio',
        'type_icon'  => 'ph-fill ph-broadcast',
        'type_label' => esc_html__('پادکست', 'revayatiran'),
        'chip_mod'   => 'story-chip--audio',
    ],
    'gallery' => [
        'badge'      => 'ph-fill ph-images',
        'badge_mod'  => 'story-play-badge--gallery',
        'type_icon'  => 'ph-fill ph-images',
        'type_label' => esc_html__('گالری', 'revayatiran'),
        'chip_mod'   => 'story-chip--gallery',
    ],
];

$config = $type_configs[$media_type] ?? $type_configs['video'];

// fallback تصویر پیش‌فرض طبق ADR-003
if (empty($card_thumbnail)) {
    $card_thumbnail = revayat_get_placeholder_url('media');
}
?>
<a href="<?php echo esc_url($card_url); ?>"
   class="story-card block group"
   data-story-id="<?php echo esc_attr($story_id); ?>"
   data-title="<?php echo esc_attr($card_title); ?>"
   data-author="<?php echo esc_attr($args['author_name'] ?? ''); ?>"
   data-img="<?php echo esc_url($card_thumbnail); ?>"
   data-category="<?php echo esc_attr($chip_label); ?>"
   data-duration="<?php echo esc_attr($duration); ?>"
   data-type="<?php echo esc_attr($media_type); ?>">
    <div class="story-card__cover">
        <img src="<?php echo esc_url($card_thumbnail); ?>"
             alt="<?php echo esc_attr($card_title); ?>"
             class="story-card__img transition-transform duration-500 group-hover:scale-105"
             loading="lazy">
        <div class="story-card__overlay"></div>

        <!-- دکمه اکشن مرکزی -->
        <div class="story-play-badge <?php echo esc_attr($config['badge_mod']); ?>">
            <i class="<?php echo esc_attr($config['badge']); ?>"></i>
        </div>

        <!-- اطلاعات پایین استوری -->
        <div class="story-card__footer">
            <span class="story-chip <?php echo esc_attr($config['chip_mod']); ?>">
                <?php echo esc_html($chip_label); ?>
            </span>
            <h3 class="story-title"><?php echo esc_html($card_title); ?></h3>
            <div class="story-meta">
                <?php if (!empty($duration)) : ?>
                <span class="story-duration">
                    <?php if ($media_type === 'gallery') : ?>
                        <i class="ph ph-image"></i>
                    <?php else : ?>
                        <i class="ph ph-clock"></i>
                    <?php endif; ?>
                    <?php echo esc_html($duration); ?>
                </span>
                <?php endif; ?>
                <span class="story-type-badge">
                    <i class="<?php echo esc_attr($config['type_icon']); ?>"></i>
                    <?php echo esc_html($config['type_label']); ?>
                </span>
            </div>
        </div>
    </div>
</a>
