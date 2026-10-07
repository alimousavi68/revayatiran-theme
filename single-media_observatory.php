<?php
/**
 * تمپلیت تک‌واکاوی دیده‌بان رسانه (Single Media Observatory)
 *
 * انطباق ۱۰۰٪ با دیزاین سیستم اصلی و الگوی تقابل و تحلیل فریمینگ رسانه‌ها.
 * شامل هدر تحلیلی، ویترین مقایسه دو دیدگاه (Frame A vs Frame B)، چکیده یافته‌های کلیدی،
 * بدنه تحریریه و بخش واکاوی‌های مرتبط.
 *
 * @package RevayatIran
 * @subpackage Templates
 * @version 2.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

while (have_posts()) :
    the_post();

    $post_id = get_the_ID();
    $data    = function_exists('revayat_get_observatory_single_data') ? revayat_get_observatory_single_data($post_id) : (function_exists('revayat_get_single_data') ? revayat_get_single_data($post_id) : []);

    $title             = $data['title'] ?? get_the_title();
    $excerpt           = $data['excerpt'] ?? get_the_excerpt();
    $date              = $data['date'] ?? get_the_date();
    $reading_time      = $data['reading_time'] ?? (function_exists('revayat_reading_time') ? revayat_reading_time($post_id) : '۵ دقیقه');
    $badge_label       = $data['badge_label'] ?? ($data['taxonomies']['observatory_badge_label'] ?? __('تحلیل گفتمان', 'revayatiran'));
    $badge_class       = $data['badge_class'] ?? ($data['badge_css'] ?? 'obs-badge--discourse');
    $frame_a           = $data['frame_a'] ?? ($data['frames']['frame_a'] ?? []);
    $frame_b           = $data['frame_b'] ?? ($data['frames']['frame_b'] ?? []);
    $narrative_summary = $data['narrative_summary'] ?? ($data['summary'] ?? '');
    $monitoring_source = $data['monitoring_source'] ?? __('دیده‌بان رصد و پایش روایت', 'revayatiran');
    $author            = $data['taxonomies']['person_author'] ?? [];

    $archive_url = get_post_type_archive_link('media_observatory');
    if (!$archive_url || is_wp_error($archive_url)) {
        $archive_url = home_url('/observatory/');
    }

    // تصویر یا اینفوگرافیک شاخص
    $thumbnail_url = !empty($data['media']['url']) ? $data['media']['url'] : (has_post_thumbnail() ? get_the_post_thumbnail_url($post_id, 'full') : '');
    $infographic   = $data['infographic'] ?? ($data['meta']['infographic'] ?? []);
    $featured_img  = !empty($infographic['url']) ? $infographic['url'] : $thumbnail_url;
?>

<main class="bg-gray-50/50 dark:bg-black/20 min-h-screen pb-16">
    <article class="pt-6 md:pt-10" id="post-<?php echo esc_attr($post_id); ?>">
        <div class="container max-w-5xl">

            <!-- ═══ ناوبری و هدر اصلی واکاوی ═══ -->
            <section class="obs-single-hero">
                <!-- بردکرامب (Breadcrumbs) -->
                <nav class="obs-breadcrumbs" aria-label="<?php esc_attr_e('مسیر راهنما', 'revayatiran'); ?>">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center gap-1">
                        <i class="ph ph-house"></i>
                        <span><?php esc_html_e('خانه', 'revayatiran'); ?></span>
                    </a>
                    <i class="ph ph-caret-left text-xs opacity-50"></i>
                    <a href="<?php echo esc_url($archive_url); ?>" class="flex items-center gap-1">
                        <i class="ph ph-binoculars"></i>
                        <span><?php esc_html_e('دیده‌بان رسانه', 'revayatiran'); ?></span>
                    </a>
                    <i class="ph ph-caret-left text-xs opacity-50"></i>
                    <span class="text-gray-900 dark:text-white font-medium truncate max-w-xs md:max-w-md">
                        <?php echo esc_html($title); ?>
                    </span>
                </nav>

                <!-- نشان گونه واکاوی -->
                <div class="mb-3">
                    <span class="obs-badge <?php echo esc_attr($badge_class); ?>">
                        <?php echo esc_html($badge_label); ?>
                    </span>
                </div>

                <!-- تیتر اصلی واکاوی -->
                <h1 class="obs-single-title">
                    <?php echo esc_html($title); ?>
                </h1>

                <?php if (!empty($excerpt)) : ?>
                    <p class="text-gray-600 dark:text-gray-400 text-sm md:text-base leading-relaxed mb-4">
                        <?php echo esc_html($excerpt); ?>
                    </p>
                <?php endif; ?>

                <!-- نوار متاداده، تحلیل‌گر و زمان رصد -->
                <div class="obs-meta-bar">
                    <!-- کارت تحلیل‌گر / نویسنده -->
                    <div class="obs-author-card">
                        <?php if (!empty($author['avatar'])) : ?>
                            <img src="<?php echo esc_url($author['avatar']); ?>" 
                                 alt="<?php echo esc_attr($author['name'] ?? ''); ?>" 
                                 class="obs-author-avatar" />
                        <?php else : ?>
                            <div class="obs-author-avatar bg-gray-200 dark:bg-white/10 flex items-center justify-center text-gray-500">
                                <i class="ph ph-user"></i>
                            </div>
                        <?php endif; ?>
                        <div class="obs-author-info">
                            <span class="obs-author-name"><?php echo esc_html($author['name'] ?? __('تحریریه دیده‌بان', 'revayatiran')); ?></span>
                            <span class="obs-author-role"><?php echo esc_html($author['role'] ?? __('تحلیل‌گر ارشد رسانه', 'revayatiran')); ?></span>
                        </div>
                    </div>

                    <!-- جزئیات متادیتای انتشار -->
                    <div class="obs-meta-details">
                        <span class="obs-meta-item">
                            <i class="ph ph-calendar-blank text-primary"></i>
                            <time><?php echo esc_html(function_exists('revayat_en2fa') ? revayat_en2fa($date) : $date); ?></time>
                        </span>

                        <span class="obs-meta-item">
                            <i class="ph ph-clock text-primary"></i>
                            <span><?php echo esc_html(function_exists('revayat_en2fa') ? revayat_en2fa($reading_time) : $reading_time); ?></span>
                        </span>

                        <?php if (!empty($monitoring_source)) : ?>
                            <span class="obs-meta-item">
                                <i class="ph ph-broadcast text-primary"></i>
                                <span><?php echo esc_html($monitoring_source); ?></span>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <!-- ═══ ویترین بزرگ مقایسه فریمینگ (Comparative Showcase: Frame A vs Frame B) ═══ -->
            <?php if (!empty($frame_a['text']) || !empty($frame_b['text']) || !empty($frame_a['source']) || !empty($frame_b['source'])) : ?>
                <section class="obs-comparative-showcase">
                    <div class="obs-showcase-header">
                        <h2 class="obs-showcase-header__title">
                            <i class="ph ph-scales text-primary text-xl"></i>
                            <span><?php esc_html_e('تقابل زوایای روایی و فریمینگ رسانه‌ای', 'revayatiran'); ?></span>
                        </h2>
                        <span class="text-xs text-gray-400 font-medium">
                            <?php esc_html_e('واکاوی چینش واژگان و زاویه دید', 'revayatiran'); ?>
                        </span>
                    </div>

                    <div class="obs-comparative-grid">
                        <!-- فریم A (زاویه دید اول) -->
                        <div class="obs-frame-card obs-frame-card--a">
                            <div class="obs-frame-card__source">
                                <i class="ph ph-newspaper"></i>
                                <span><?php echo esc_html(!empty($frame_a['source']) ? $frame_a['source'] : __('رسانه اول / جریان رسمی', 'revayatiran')); ?></span>
                            </div>
                            <?php if (!empty($frame_a['text'])) : ?>
                                <blockquote class="obs-frame-card__quote">
                                    «<?php echo esc_html($frame_a['text']); ?>»
                                </blockquote>
                            <?php endif; ?>
                        </div>

                        <!-- نشان مرکزی تقابل (VS) -->
                        <div class="obs-vs-badge" aria-hidden="true">
                            <span><?php esc_html_e('در برابر', 'revayatiran'); ?></span>
                        </div>

                        <!-- فریم B (زاویه دید دوم) -->
                        <div class="obs-frame-card obs-frame-card--b">
                            <div class="obs-frame-card__source">
                                <i class="ph ph-globe"></i>
                                <span><?php echo esc_html(!empty($frame_b['source']) ? $frame_b['source'] : __('رسانه دوم / جریان منتقد یا بین‌المللی', 'revayatiran')); ?></span>
                            </div>
                            <?php if (!empty($frame_b['text'])) : ?>
                                <blockquote class="obs-frame-card__quote">
                                    «<?php echo esc_html($frame_b['text']); ?>»
                                </blockquote>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <!-- ═══ باکس یافته‌های کلیدی و جمع‌بندی دیده‌بان ═══ -->
            <?php if (!empty($narrative_summary)) : ?>
                <section class="obs-takeaways-box">
                    <h3 class="obs-takeaways-box__title">
                        <i class="ph ph-lightbulb text-xl"></i>
                        <span><?php esc_html_e('یافته‌های کلیدی و جمع‌بندی تحلیلی دیده‌بان', 'revayatiran'); ?></span>
                    </h3>
                    <p class="obs-takeaways-box__body">
                        <?php echo esc_html($narrative_summary); ?>
                    </p>
                </section>
            <?php endif; ?>

            <!-- ═══ اینفوگرافیک یا تصویر شاخص دارایی بصری ═══ -->
            <?php if (!empty($featured_img)) : ?>
                <section class="obs-infographic-box">
                    <img src="<?php echo esc_url($featured_img); ?>" 
                         alt="<?php echo esc_attr($title); ?>" 
                         loading="lazy" />
                    <div class="obs-infographic-box__caption">
                        <i class="ph ph-image"></i>
                        <span><?php echo esc_html(sprintf(__('پایش تصویری و نمودار تحلیل محتوای روایت: %s', 'revayatiran'), $title)); ?></span>
                    </div>
                </section>
            <?php endif; ?>

            <!-- ═══ بدنه تحلیلی تحریریه (Editorial Body) ═══ -->
            <section class="obs-editorial-content">
                <?php the_content(); ?>
            </section>

            <!-- ═══ واکاوی‌های مرتبط در دیده‌بان رسانه ═══ -->
            <?php get_template_part('template-parts/single/share'); ?>
            <?php 
            $related_posts = function_exists('revayat_get_related_posts') ? revayat_get_related_posts($post_id, 3) : [];
            if (!empty($related_posts)) :
            ?>
                <section class="mt-12 pt-8 border-t border-[var(--border)]" id="related-observatory">
                    <div class="flex items-center justify-between gap-4 mb-6">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                                <i class="ph ph-squares-four text-lg"></i>
                            </div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                <?php esc_html_e('واکاوی‌ها و روایت‌های هم‌راستا', 'revayatiran'); ?>
                            </h3>
                        </div>
                        <a href="<?php echo esc_url($archive_url); ?>" class="text-xs font-bold text-primary flex items-center gap-1 hover:underline">
                            <span><?php esc_html_e('مشاهده همه رصدها', 'revayatiran'); ?></span>
                            <i class="ph ph-arrow-left"></i>
                        </a>
                    </div>

                    <div class="observatory-grid-v2">
                        <?php foreach ($related_posts as $rel_post) : ?>
                            <?php get_template_part('template-parts/cards/card-observatory', null, ['item' => $rel_post]); ?>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

        </div>
    </article>
</main>

<?php
endwhile;

get_footer();
