<?php
/**
 * تمپلیت تک‌یادداشت شبکه تحلیل‌گران (Single Analyst Post)
 *
 * انطباق ۱۰۰٪ با دیزاین سیستم اصلی و چک‌لیست وفاداری بصری.
 * شامل هدر تحلیلی، بردکرامپ، باکس برجسته معرفی تحلیل‌گر (با پیوند به پروفایل کارشناس)،
 * باکس فراز کلیدی / تز تحلیلی (Thesis Pull Quote Box)، بدنه تحریریه،
 * باکس اعتبارسنجی و ارزیابی مخاطبان و بخش یادداشت‌های مرتبط.
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
    $data    = function_exists('revayat_get_analyst_post_single_data') ? revayat_get_analyst_post_single_data($post_id) : (function_exists('revayat_get_single_data') ? revayat_get_single_data($post_id) : []);

    $title          = $data['title'] ?? get_the_title();
    $excerpt        = $data['excerpt'] ?? get_the_excerpt();
    $date           = $data['date'] ?? get_the_date();
    $reading_time   = $data['reading_time'] ?? (function_exists('revayat_reading_time') ? revayat_reading_time($post_id) : '۵ دقیقه');
    $field_name     = $data['field_name'] ?? ($data['taxonomies']['analyst_field'] ?? __('ژئوپلیتیک', 'revayatiran'));
    $analysis_type  = $data['analysis_type'] ?? ($data['meta']['analysis_type'] ?? __('یادداشت تحلیلی', 'revayatiran'));
    $featured_quote = $data['featured_quote'] ?? ($data['analyst_quote'] ?? ($data['meta']['featured_quote'] ?? ''));
    $score          = $data['score'] ?? '۴.۸';
    $score_str      = function_exists('revayat_en2fa') ? revayat_en2fa($score) : $score;
    $votes_count    = $data['votes_count'] ?? ($data['meta']['analyst_votes'] ?? 12);
    $votes_str      = function_exists('revayat_en2fa') ? revayat_en2fa($votes_count) : $votes_count;

    // اطلاعات تحلیل‌گر / نویسنده
    $author_name   = $data['author_name'] ?? get_the_author_meta('display_name', get_post_field('post_author', $post_id));
    $author_role   = $data['author_role'] ?? (get_the_author_meta('headline', get_post_field('post_author', $post_id)) ?: __('پژوهشگر ارشد مطالعات راهبردی', 'revayatiran'));
    $author_avatar = $data['author_avatar'] ?? get_avatar_url(get_post_field('post_author', $post_id), ['size' => 96]);
    $author_url    = $data['author_url'] ?? home_url('/person/' . sanitize_title($author_name) . '/');
    $author_org    = $data['author_org'] ?? (get_the_author_meta('organization', get_post_field('post_author', $post_id)) ?: __('مرکز مطالعات روایتی', 'revayatiran'));

    $archive_url = get_post_type_archive_link('analyst_post');
    if (!$archive_url || is_wp_error($archive_url)) {
        $archive_url = home_url('/analysts/');
    }

    // تصویر شاخص
    $thumbnail_url = !empty($data['media']['url']) ? $data['media']['url'] : (has_post_thumbnail() ? get_the_post_thumbnail_url($post_id, 'full') : '');
?>

<main class="bg-gray-50/50 dark:bg-black/20 min-h-screen pb-16">
    <article class="pt-6 md:pt-10" id="post-<?php echo esc_attr($post_id); ?>">
        <div class="container max-w-4xl">

            <!-- ═══ ناوبری و هدر اصلی یادداشت تحلیلی ═══ -->
            <section class="an-single-hero">
                <!-- بردکرامب (Breadcrumbs) -->
                <nav class="obs-breadcrumbs" aria-label="<?php esc_attr_e('مسیر راهنما', 'revayatiran'); ?>">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center gap-1">
                        <i class="ph ph-house"></i>
                        <span><?php esc_html_e('خانه', 'revayatiran'); ?></span>
                    </a>
                    <i class="ph ph-caret-left text-xs opacity-50"></i>
                    <a href="<?php echo esc_url($archive_url); ?>" class="flex items-center gap-1">
                        <i class="ph ph-pen-nib"></i>
                        <span><?php esc_html_e('شبکه تحلیل‌گران', 'revayatiran'); ?></span>
                    </a>
                    <i class="ph ph-caret-left text-xs opacity-50"></i>
                    <span class="text-gray-900 dark:text-white font-medium truncate max-w-xs md:max-w-md">
                        <?php echo esc_html($title); ?>
                    </span>
                </nav>

                <!-- نشان حوزه تحلیلی و گونه یادداشت -->
                <div class="flex items-center gap-2 mb-3 flex-wrap">
                    <span class="text-xs px-2.5 py-0.5 rounded-full bg-primary/10 text-primary font-bold">
                        <?php echo esc_html($field_name); ?>
                    </span>
                    <span class="text-xs px-2.5 py-0.5 rounded-full bg-gray-100 dark:bg-white/10 text-gray-700 dark:text-gray-300 font-semibold">
                        <?php echo esc_html($analysis_type); ?>
                    </span>
                </div>

                <!-- تیتر اصلی یادداشت -->
                <h1 class="an-single-title">
                    <?php echo esc_html($title); ?>
                </h1>

                <?php if (!empty($excerpt)) : ?>
                    <p class="text-gray-600 dark:text-gray-400 text-sm md:text-base leading-relaxed mb-4">
                        <?php echo esc_html($excerpt); ?>
                    </p>
                <?php endif; ?>

                <!-- نوار زمان انتشار و برآورد مطالعه -->
                <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400 pt-3 border-t border-[var(--border)]">
                    <span class="inline-flex items-center gap-1.5">
                        <i class="ph ph-calendar-blank text-primary"></i>
                        <time><?php echo esc_html(function_exists('revayat_en2fa') ? revayat_en2fa($date) : $date); ?></time>
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <i class="ph ph-clock text-primary"></i>
                        <span><?php echo esc_html(function_exists('revayat_en2fa') ? revayat_en2fa($reading_time) : $reading_time); ?></span>
                    </span>
                </div>
            </section>

            <!-- ═══ باکس برجسته معرفی تحلیل‌گر (Prominent Author Box) ═══ -->
            <section class="an-prominent-author-box">
                <div class="an-author-profile">
                    <?php if (!empty($author_avatar)) : ?>
                        <img src="<?php echo esc_url($author_avatar); ?>" 
                             alt="<?php echo esc_attr($author_name); ?>" 
                             class="an-author-profile__avatar">
                    <?php else : ?>
                        <div class="an-author-profile__avatar bg-gray-200 dark:bg-white/10 flex items-center justify-center text-gray-500">
                            <i class="ph ph-user text-2xl"></i>
                        </div>
                    <?php endif; ?>

                    <div class="an-author-profile__details">
                        <h2 class="an-author-profile__name m-0">
                            <a href="<?php echo esc_url($author_url); ?>">
                                <?php echo esc_html($author_name); ?>
                            </a>
                        </h2>
                        <span class="an-author-profile__role"><?php echo esc_html($author_role); ?></span>
                        <?php if (!empty($author_org)) : ?>
                            <span class="an-author-profile__org"><?php echo esc_html($author_org); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="an-score-badge--amber">
                        <span><?php esc_html_e('اعتبار:', 'revayatiran'); ?></span>
                        <strong><?php echo esc_html($score_str); ?></strong>
                        <i class="ph ph-check"></i>
                    </div>

                    <a href="<?php echo esc_url($author_url); ?>" 
                       class="text-xs font-bold text-primary hover:underline flex items-center gap-1">
                        <span><?php esc_html_e('صفحه کارشناس', 'revayatiran'); ?></span>
                        <i class="ph ph-arrow-left"></i>
                    </a>
                </div>
            </section>

            <!-- ═══ باکس نقل‌قول طلایی / تز تحلیلی (Thesis Pull Quote Box) ═══ -->
            <?php if (!empty($featured_quote)) : ?>
                <blockquote class="an-thesis-quote">
                    <div class="an-thesis-quote__label">
                        <i class="ph ph-quotes text-base"></i>
                        <span><?php esc_html_e('فراز کلیدی و تز محوری یادداشت', 'revayatiran'); ?></span>
                    </div>
                    <p class="an-thesis-quote__text">
                        «<?php echo esc_html($featured_quote); ?>»
                    </p>
                </blockquote>
            <?php endif; ?>

            <!-- ═══ تصویر شاخص (در صورت وجود) ═══ -->
            <?php if (!empty($thumbnail_url)) : ?>
                <div class="rounded-2xl overflow-hidden mb-8 border border-[var(--border)] shadow-sm">
                    <img src="<?php echo esc_url($thumbnail_url); ?>" 
                         alt="<?php echo esc_attr($title); ?>" 
                         class="w-full h-auto object-cover max-h-[480px]" 
                         loading="lazy">
                </div>
            <?php endif; ?>

            <!-- ═══ بدنه تحلیلی مقاله (Editorial Body) ═══ -->
            <section class="obs-editorial-content">
                <?php the_content(); ?>
            </section>

            <!-- ═══ باکس اعتبارسنجی و ارزیابی مخاطبان (Credibility Rating Box) ═══ -->
            <section class="an-credibility-box">
                <div class="an-credibility-info">
                    <div class="an-credibility-icon">
                        <i class="ph-bold ph-check"></i>
                    </div>
                    <div>
                        <div class="an-credibility-title">
                            <?php esc_html_e('شاخص اعتبار علمی و ارزیابی تحلیلی', 'revayatiran'); ?>
                        </div>
                        <div class="an-credibility-desc">
                            <?php 
                            printf(
                                /* translators: 1: score, 2: votes count */
                                esc_html__('میانگین نمره: %1$s از ۵ بر پایه ارزیابی همتایان (%2$s رأی ثبت‌شده)', 'revayatiran'),
                                esc_html($score_str),
                                esc_html($votes_str)
                            );
                            ?>
                        </div>
                    </div>
                </div>

                <div class="an-score-badge--amber">
                    <strong><?php echo esc_html($score_str); ?></strong>
                    <i class="ph ph-check"></i>
                </div>
            </section>

            <!-- ═══ یادداشت‌های مرتبط در شبکه تحلیل‌گران ═══ -->
            <?php 
            $related_posts = function_exists('revayat_get_related_posts') ? revayat_get_related_posts($post_id, 3) : [];
            if (!empty($related_posts)) :
            ?>
                <section class="mt-12 pt-8 border-t border-[var(--border)]" id="related-analyses">
                    <div class="flex items-center justify-between gap-4 mb-6">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                                <i class="ph ph-squares-four text-lg"></i>
                            </div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                <?php esc_html_e('یادداشت‌های پژوهشی هم‌راستا', 'revayatiran'); ?>
                            </h3>
                        </div>
                        <a href="<?php echo esc_url($archive_url); ?>" class="text-xs font-bold text-primary flex items-center gap-1 hover:underline">
                            <span><?php esc_html_e('مشاهده تمام تحلیل‌ها', 'revayatiran'); ?></span>
                            <i class="ph ph-arrow-left"></i>
                        </a>
                    </div>

                    <div class="an-archive-grid">
                        <?php foreach ($related_posts as $rel_post) : ?>
                            <?php get_template_part('template-parts/cards/card-analyst', null, [
                                'item'    => $rel_post,
                                'variant' => 'grid',
                            ]); ?>
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
