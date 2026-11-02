<?php
/**
 * تمپلیت اختصاصی نمایش پرونده‌های ویژه (Single Special Dossier)
 *
 * انطباق کامل بصری و ساختاری با سورس اصلی (orginal source/single.php و components/special-dossiers.php).
 *
 * @package RevayatIran
 * @subpackage Templates
 * @version 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

while (have_posts()) :
    the_post();

    $post_id         = get_the_ID();
    $dossier         = function_exists('revayat_get_single_data') ? revayat_get_single_data($post_id) : [];
    $title           = $dossier['title'] ?? get_the_title();
    $excerpt         = $dossier['excerpt'] ?? get_the_excerpt();
    $date            = $dossier['date'] ?? get_the_date();
    $thumbnail       = !empty($dossier['media']['url']) ? $dossier['media']['url'] : (has_post_thumbnail() ? get_the_post_thumbnail_url($post_id, 'full') : '');
    $topic_terms     = wp_get_post_terms($post_id, 'dossier_topic', ['fields' => 'names']);
    $topic           = !is_wp_error($topic_terms) ? implode('، ', $topic_terms) : '';
    $meta            = $dossier['meta'] ?? [];
    $is_live         = !empty($meta['is_live']);
    $editor_lead     = !empty($meta['editor_lead']) ? $meta['editor_lead'] : __('تحریریه راهبردی', 'revayatiran');
    $last_update     = $meta['last_update'] ?? '';
    $related         = function_exists('revayat_get_related_posts') ? revayat_get_related_posts($post_id, 3) : [];
    $dossier_links   = class_exists('Revayat_Companion_Dossier_Links') ? Revayat_Companion_Dossier_Links::get($post_id) : [];
    ?>

    <main class="rv-dossier-single bg-gray-50/50 dark:bg-black/20 min-h-screen pt-8 md:pt-12 pb-16">
        <div class="container">

            <!-- ═══ بخش هدر دوتکه (Split Hero - مطابق با orginal source/single.php) ═══ -->
            <article class="bg-white dark:bg-[#1a1c23] rounded-2xl border border-[var(--border)] shadow-sm mb-10 p-[15px]">
                <div class="rv-dossier-hero-layout">

                    <!-- سمت راست: اطلاعات پست -->
                    <div class="rv-dossier-hero-content flex-1 p-6 md:p-8 lg:p-12 flex flex-col justify-center bg-gradient-to-bl from-transparent to-gray-50 dark:to-white/5 rounded-r-2xl">
                        
                        <!-- بردکرامپ -->
                        <div class="mb-6">
                            <nav class="flex text-sm text-gray-500 font-medium" aria-label="<?php esc_attr_e('موقعیت شما', 'revayatiran'); ?>">
                                <ol class="inline-flex items-center space-x-1 space-x-reverse md:space-x-2">
                                    <li class="inline-flex items-center">
                                        <a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-primary transition-colors">
                                            <?php esc_html_e('خانه', 'revayatiran'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="flex items-center">
                                            <i class="ph ph-caret-left text-xs mx-1"></i>
                                            <a href="<?php echo esc_url(home_url('/dossiers/')); ?>" class="hover:text-primary transition-colors">
                                                <?php esc_html_e('پرونده‌های ویژه', 'revayatiran'); ?>
                                            </a>
                                        </div>
                                    </li>
                                    <?php if (!empty($topic)) : ?>
                                        <li>
                                            <div class="flex items-center">
                                                <i class="ph ph-caret-left text-xs mx-1"></i>
                                                <span class="text-gray-400"><?php echo esc_html($topic); ?></span>
                                            </div>
                                        </li>
                                    <?php endif; ?>
                                </ol>
                            </nav>
                        </div>

                        <!-- نشانگر وضعیت زنده، تگ موضوع و کد پرونده -->
                        <div class="flex items-center gap-3 flex-wrap mb-4">
                            <?php if ($is_live) : ?>
                                <div class="live-indicator">
                                    <span class="live-indicator__dot"></span>
                                    <span><?php esc_html_e('در حال پیگیری', 'revayatiran'); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($topic)) : ?>
                                <span class="dossier-category-badge">
                                    <?php echo esc_html($topic); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- تیتر اصلی پرونده -->
                        <h1 class="text-2xl md:text-3xl lg:text-[34px] font-extrabold text-gray-900 dark:text-white leading-[1.6] mb-6 tracking-tight">
                            <?php echo esc_html($title); ?>
                        </h1>

                        <!-- لید و خلاصه اجرایی -->
                        <?php if (!empty($excerpt)) : ?>
                            <p class="text-base md:text-lg text-gray-600 dark:text-gray-300 leading-relaxed mb-8 border-r-4 border-primary/50 pr-4 font-medium">
                                <?php echo esc_html($excerpt); ?>
                            </p>
                        <?php endif; ?>

                        <!-- ردیف متادیتا: دبیر پرونده و آخرین بروزرسانی -->
                        <div class="flex flex-wrap items-center justify-between gap-4 pt-6 border-t border-[var(--border)] mt-auto">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center text-xl border border-[var(--border)]">
                                    <i class="ph ph-user"></i>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white"><?php echo esc_html($editor_lead); ?></span>
                                    <span class="text-xs text-gray-500"><?php esc_html_e('دبیر پرونده', 'revayatiran'); ?></span>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 text-xs text-gray-500 font-medium">
                                <?php if (!empty($last_update)) : ?>
                                    <span class="flex items-center gap-1.5">
                                        <i class="ph ph-clock text-base text-primary"></i>
                                        <?php esc_html_e('آخرین به‌روزرسانی:', 'revayatiran'); ?> <?php echo esc_html(function_exists('revayat_en2fa') ? revayat_en2fa($last_update) : $last_update); ?>
                                    </span>
                                <?php endif; ?>
                                <span class="flex items-center gap-1.5">
                                    <i class="ph ph-calendar-blank text-base"></i>
                                    <?php echo esc_html(function_exists('revayat_en2fa') ? revayat_en2fa($date) : $date); ?>
                                </span>
                            </div>
                        </div>

                    </div>

                    <!-- سمت چپ: تصویر شاخص -->
                    <div class="rv-dossier-hero-image rounded-2xl overflow-hidden bg-gray-900">
                        <?php if (!empty($thumbnail)) : ?>
                            <img src="<?php echo esc_url($thumbnail); ?>"
                                 alt="<?php echo esc_attr($title); ?>"
                                 class="absolute inset-0 w-full h-full object-cover rounded-2xl"
                                 loading="eager">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                        <?php else : ?>
                            <div class="absolute inset-0 flex items-center justify-center text-gray-500">
                                <i class="ph ph-folder-open text-6xl opacity-30"></i>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </article>

            <!-- ═══ گرید اصلی (محتوا + سایدبار ابزارها و پرونده‌های تکمیلی) ═══ -->
            <div class="flex flex-col lg:flex-row gap-10 relative">

                <!-- ستون اصلی: فهرست آرشیوی مطالب مرتبط پرونده (راست - ۷۰٪) -->
                <div class="flex-1 min-w-0 order-1">
                    <?php 
                    $content_items = $dossier_links['content'] ?? [];
                    if (!empty($content_items)) : 
                    ?>
                        <div class="flex items-center justify-between gap-4 mb-6 pb-3 border-b border-[var(--border)]">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center text-lg shrink-0">
                                    <i class="ph ph-article"></i>
                                </div>
                                <div>
                                    <h2 class="text-lg md:text-xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                                        <?php esc_html_e('مطالب و تحلیل‌های پرونده', 'revayatiran'); ?>
                                    </h2>
                                    <p class="text-gray-500 dark:text-gray-400 text-xs mt-0.5">
                                        <?php 
                                        printf(
                                            /* translators: %s: number of items */
                                            esc_html__('%s مطلب مرتبط در این پرونده', 'revayatiran'),
                                            function_exists('revayat_en2fa') ? revayat_en2fa(count($content_items)) : count($content_items)
                                        ); 
                                        ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="dossier-post-list">
                            <?php 
                            foreach ($content_items as $c_item) :
                                $c_id    = (int) ($c_item['id'] ?? 0);
                                $c_post  = $c_id ? get_post($c_id) : null;
                                if (!$c_post) {
                                    continue;
                                }
                                $c_title = !empty($c_item['title']) ? $c_item['title'] : get_the_title($c_id);
                                $c_url   = !empty($c_item['url']) ? $c_item['url'] : get_permalink($c_id);

                                // تصویر شاخص
                                $c_thumb = function_exists('revayat_get_post_image_url') 
                                    ? revayat_get_post_image_url($c_post, 'medium', 'news') 
                                    : get_the_post_thumbnail_url($c_id, 'medium');
                                if (empty($c_thumb) && function_exists('revayat_resolve_image_url')) {
                                    $c_thumb = revayat_resolve_image_url(null, 'news');
                                }

                                // چکیده
                                $c_excerpt = get_the_excerpt($c_post);
                                if (empty($c_excerpt)) {
                                    $c_excerpt = wp_trim_words(wp_strip_all_tags($c_post->post_content), 24);
                                }

                                // تاریخ انتشار
                                $c_date = get_the_date('', $c_id);
                            ?>
                                <article class="dossier-post-card">
                                    <!-- سمت راست: تصویر شاخص -->
                                    <a href="<?php echo esc_url($c_url); ?>" class="dossier-post-card__thumb" aria-label="<?php echo esc_attr($c_title); ?>">
                                        <img src="<?php echo esc_url($c_thumb); ?>" 
                                             alt="<?php echo esc_attr($c_title); ?>" 
                                             loading="lazy" decoding="async">
                                    </a>

                                    <!-- سمت چپ: تاریخ، تیتر، لید و لینک -->
                                    <div class="dossier-post-card__content">
                                        <div>
                                            <!-- ردیف بالای محتوا: تاریخ -->
                                            <?php if (!empty($c_date)) : ?>
                                                <div class="dossier-post-card__header">
                                                    <div class="dossier-post-card__meta">
                                                        <i class="ph ph-calendar-blank"></i>
                                                        <span><?php echo esc_html(function_exists('revayat_en2fa') ? revayat_en2fa($c_date) : $c_date); ?></span>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <!-- تیتر مطلب -->
                                            <h3 class="dossier-post-card__title">
                                                <a href="<?php echo esc_url($c_url); ?>">
                                                    <?php echo esc_html($c_title); ?>
                                                </a>
                                            </h3>

                                            <!-- لید / چکیده مطلب -->
                                            <?php if (!empty($c_excerpt)) : ?>
                                                <p class="dossier-post-card__excerpt">
                                                    <?php echo esc_html($c_excerpt); ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>

                                        <!-- فوتر کارت: دکمه مطالعه -->
                                        <div class="dossier-post-card__footer">
                                            <a href="<?php echo esc_url($c_url); ?>" class="dossier-post-card__link">
                                                <span><?php esc_html_e('مشاهده و مطالعه', 'revayatiran'); ?></span>
                                                <i class="ph ph-arrow-left"></i>
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <!-- وضعیت خالی (Empty State) -->
                        <div class="text-center py-14 px-6 bg-white dark:bg-[#1a1c23] rounded-2xl border border-[var(--border)] shadow-sm">
                            <div class="w-14 h-14 rounded-2xl bg-gray-50 dark:bg-white/5 text-gray-400 flex items-center justify-center text-2xl mx-auto mb-3 border border-[var(--border)]">
                                <i class="ph ph-article"></i>
                            </div>
                            <h3 class="text-base md:text-lg font-bold text-gray-900 dark:text-white mb-2">
                                <?php esc_html_e('هنوز مطلبی به این پرونده متصل نشده است', 'revayatiran'); ?>
                            </h3>
                            <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-5 leading-relaxed">
                                <?php esc_html_e('مطالب و گزارش‌های تحلیلی مرتبط با این پرونده به زودی توسط تحریریه اضافه خواهند شد.', 'revayatiran'); ?>
                            </p>
                            <a href="<?php echo esc_url(home_url('/dossiers/')); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-xs font-bold rounded-xl hover:bg-primary-dark transition-colors">
                                <i class="ph ph-arrow-right"></i>
                                <span><?php esc_html_e('بازگشت به آرشیو پرونده‌ها', 'revayatiran'); ?></span>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ═══ سایدبار (چپ - ۳۰٪ مطابق با orginal source/single.php) ═══ -->
                <aside class="w-full lg:w-[320px] shrink-0 space-y-6 lg:sticky lg:top-28 self-start order-2">

                    <!-- کارت شناسنامه پرونده -->
                    <div class="bg-white dark:bg-[#1a1c23] rounded-2xl p-5 border border-[var(--border)] shadow-sm">
                        <h3 class="text-[15px] font-extrabold text-gray-900 dark:text-white mb-4 flex items-center gap-2 pb-3 border-b border-[var(--border)]">
                            <i class="ph ph-info text-primary text-xl"></i>
                            <?php esc_html_e('شناسنامه پرونده', 'revayatiran'); ?>
                        </h3>
                        <dl class="space-y-3 text-xs">

                            <div class="flex items-center justify-between">
                                <dt class="text-gray-500"><?php esc_html_e('وضعیت بررسی:', 'revayatiran'); ?></dt>
                                <dd class="font-bold <?php echo $is_live ? 'text-red-500' : 'text-primary'; ?>">
                                    <?php echo $is_live ? esc_html__('در حال پیگیری', 'revayatiran') : esc_html__('تکمیل‌شده', 'revayatiran'); ?>
                                </dd>
                            </div>
                            <?php if ($topic) : ?>
                            <div class="flex items-center justify-between">
                                <dt class="text-gray-500"><?php esc_html_e('محور تخصصی:', 'revayatiran'); ?></dt>
                                <dd class="font-bold text-gray-900 dark:text-white"><?php echo esc_html($topic); ?></dd>
                            </div>
                            <?php endif; ?>
                            <div class="flex items-center justify-between">
                                <dt class="text-gray-500"><?php esc_html_e('دبیر پرونده:', 'revayatiran'); ?></dt>
                                <dd class="font-bold text-gray-900 dark:text-white"><?php echo esc_html($editor_lead); ?></dd>
                            </div>
                        </dl>
                    </div>

                    <!-- ═══ ستون پرونده‌های تکمیلی و مرتبط (dossier-sidebar-list مطابق با سورس) ═══ -->
                    <?php if (!empty($related)) : ?>
                        <aside class="dossier-sidebar-list">
                            <div class="dossier-sidebar-header">
                                <i class="ph ph-stack"></i>
                                <h4><?php esc_html_e('سایر پرونده‌های شاخص', 'revayatiran'); ?></h4>
                            </div>

                            <?php foreach ($related as $rel) : 
                                $rel_topic = $rel['taxonomies']['dossier_topic'] ?? ($rel['category'] ?? '');
                                $rel_date  = $rel['time_ago'] ?? ($rel['date'] ?? __('اخیر', 'revayatiran'));
                            ?>
                                <article class="dossier-mini-card">
                                    <?php if ($rel_topic) : ?><div class="dossier-mini-card__tag"><?php echo esc_html($rel_topic); ?></div><?php endif; ?>
                                    <h5 class="dossier-mini-card__title">
                                        <a href="<?php echo esc_url($rel['permalink']); ?>">
                                            <?php echo esc_html($rel['title']); ?>
                                        </a>
                                    </h5>
                                    <div class="dossier-mini-card__meta">
                                        <span><i class="ph ph-calendar-blank"></i> <?php echo esc_html(function_exists('revayat_en2fa') ? revayat_en2fa($rel_date) : $rel_date); ?></span>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </aside>
                    <?php endif; ?>

                    <?php get_template_part('template-parts/single/sidebar', null, ['embedded' => true]); ?>
                </aside>

            </div>

        </div>
    </main>

<?php
endwhile;

get_footer();
