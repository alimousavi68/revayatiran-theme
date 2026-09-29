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
    $topic           = $dossier['taxonomies']['dossier_topic'] ?? esc_html__('پرونده ویژه', 'revayatiran');
    $meta            = $dossier['meta'] ?? [];
    $is_live         = !empty($meta['is_live']);
    $dossier_code    = $meta['dossier_code'] ?? 'DOS-2026-EUR';
    $editor_lead     = !empty($meta['editor_lead']) ? $meta['editor_lead'] : __('تحریریه راهبردی', 'revayatiran');
    $last_update     = $meta['last_update'] ?? '';
    $stats           = $meta['stats'] ?? [];
    $docs_count      = !empty($stats['docs']) ? $stats['docs'] : 12;
    $frames_count    = !empty($stats['frames']) ? $stats['frames'] : 45;
    $notes_count     = !empty($stats['notes']) ? $stats['notes'] : 8;
    $timeline_events = $meta['timeline_events'] ?? [];
    $related         = function_exists('revayat_get_related_posts') ? revayat_get_related_posts($post_id, 3) : [];
    ?>

    <main class="bg-gray-50/50 dark:bg-black/20 min-h-screen pt-8 md:pt-12 pb-16">
        <div class="container">

            <!-- ═══ بخش هدر دوتکه (Split Hero - مطابق با orginal source/single.php) ═══ -->
            <article class="bg-white dark:bg-[#1a1c23] rounded-2xl border border-[var(--border)] shadow-sm mb-10 p-[15px]">
                <div class="flex flex-col md:flex-row h-full">

                    <!-- سمت راست: اطلاعات پست -->
                    <div class="flex-1 p-6 md:p-8 lg:p-12 flex flex-col justify-center order-2 md:order-1 bg-gradient-to-bl from-transparent to-gray-50 dark:to-white/5 rounded-r-2xl">
                        
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
                                    <span><?php esc_html_e('پرونده فعال و در جریان', 'revayatiran'); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($topic)) : ?>
                                <span class="dossier-category-badge">
                                    <?php echo esc_html($topic); ?>
                                </span>
                            <?php endif; ?>

                            <?php if (!empty($dossier_code)) : ?>
                                <span class="font-mono text-xs font-bold px-2.5 py-1 bg-gray-100 dark:bg-white/10 text-gray-700 dark:text-gray-300 rounded-lg border border-[var(--border)]">
                                    <?php echo esc_html($dossier_code); ?>
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
                    <div class="w-full md:w-[45%] lg:w-[45%] relative min-h-[300px] md:min-h-full order-1 md:order-2 rounded-2xl overflow-hidden bg-gray-900">
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

            <!-- ═══ اقلام داده‌ای و ضمایم پرونده (Stats Bar - مطابق با components/special-dossiers.php) ═══ -->
            <div class="dossier-features-row mb-10">
                <div class="dossier-feature-pill">
                    <i class="ph-fill ph-file-pdf"></i>
                    <div class="pill-info">
                        <strong><?php echo esc_html(function_exists('revayat_en2fa') ? revayat_en2fa($docs_count) : $docs_count); ?> <?php esc_html_e('سند پیوست', 'revayatiran'); ?></strong>
                        <span><?php esc_html_e('بولتن‌های داده‌محور و راهبردی', 'revayatiran'); ?></span>
                    </div>
                </div>
                <div class="dossier-feature-pill">
                    <i class="ph-fill ph-image"></i>
                    <div class="pill-info">
                        <strong><?php echo esc_html(function_exists('revayat_en2fa') ? revayat_en2fa($frames_count) : $frames_count); ?> <?php esc_html_e('فریم مستند', 'revayatiran'); ?></strong>
                        <span><?php esc_html_e('نمودارها و داده‌نماهای تبیینی', 'revayatiran'); ?></span>
                    </div>
                </div>
                <div class="dossier-feature-pill">
                    <i class="ph-fill ph-chat-centered-text"></i>
                    <div class="pill-info">
                        <strong><?php echo esc_html(function_exists('revayat_en2fa') ? revayat_en2fa($notes_count) : $notes_count); ?> <?php esc_html_e('یادداشت تحلیلی', 'revayatiran'); ?></strong>
                        <span><?php esc_html_e('نخبگان و پژوهشگران همکار', 'revayatiran'); ?></span>
                    </div>
                </div>
            </div>

            <!-- ═══ گرید اصلی (محتوا + سایدبار ابزارها و پرونده‌های تکمیلی) ═══ -->
            <div class="flex flex-col lg:flex-row gap-10 relative">

                <!-- ستون اصلی محتوای متنی (راست - ۷۰٪) -->
                <div class="flex-1 min-w-0 order-2 lg:order-1">
                    <div class="bg-white dark:bg-[#1a1c23] rounded-2xl border border-[var(--border)] shadow-sm p-6 md:p-8 lg:p-12 mb-10">

                        <div class="border-b border-[var(--border)] pb-4 mb-8">
                            <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2.5">
                                <i class="ph ph-article text-primary text-2xl"></i>
                                <?php esc_html_e('متن و ارزیابی تفصیلی پرونده', 'revayatiran'); ?>
                            </h2>
                        </div>

                        <!-- متن اصلی مقاله با تایپوگرافی استاندارد سورس -->
                        <div class="text-gray-900 dark:text-white leading-[2.2] text-[16px] md:text-[17px] space-y-7 font-normal entry-content">
                            <?php the_content(); ?>
                        </div>

                        <!-- ═══ گاه‌شمار رخدادها و سیر تحولات پرونده (Timeline) ═══ -->
                        <?php if (!empty($timeline_events) && is_array($timeline_events)) : ?>
                            <div class="mt-12 pt-8 border-t border-[var(--border)]">
                                <h3 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white mb-8 flex items-center gap-2.5 pb-3 border-b border-[var(--border)]">
                                    <i class="ph ph-clock-counter-clockwise text-primary text-2xl"></i>
                                    <?php esc_html_e('گاه‌شمار رخدادها و سیر تحولات پرونده', 'revayatiran'); ?>
                                </h3>
                                <div class="relative border-r-2 border-primary/20 mr-4 pr-6 space-y-8">
                                    <?php foreach ($timeline_events as $event) :
                                        $event_date  = $event['date'] ?? '';
                                        $event_title = $event['title'] ?? '';
                                        $event_desc  = $event['description'] ?? ($event['desc'] ?? '');
                                    ?>
                                        <div class="relative group">
                                            <span class="absolute -right-[31px] top-1 w-5 h-5 rounded-full bg-white dark:bg-[#1a1c23] border-2 border-primary text-primary flex items-center justify-center text-[10px] shadow-sm transition-transform duration-200 group-hover:scale-125">
                                                <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                                            </span>
                                            <?php if (!empty($event_date)) : ?>
                                                <div class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-primary/10 text-primary rounded-md text-xs font-bold mb-2">
                                                    <i class="ph ph-calendar text-xs"></i>
                                                    <?php echo esc_html(function_exists('revayat_en2fa') ? revayat_en2fa($event_date) : $event_date); ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($event_title)) : ?>
                                                <h4 class="text-base font-bold text-gray-900 dark:text-white mb-1.5"><?php echo esc_html($event_title); ?></h4>
                                            <?php endif; ?>
                                            <?php if (!empty($event_desc)) : ?>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed"><?php echo esc_html($event_desc); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- برچسب‌ها و دکمه‌های اشتراک‌گذاری -->
                        <div class="mt-12 pt-6 border-t border-[var(--border)] flex flex-wrap items-center justify-between gap-4">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-gray-900 dark:text-white">
                                    <i class="ph ph-tag text-primary align-middle"></i>
                                    <?php esc_html_e('محور:', 'revayatiran'); ?>
                                </span>
                                <span class="px-3 py-1 bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-gray-300 rounded-lg text-xs font-medium">
                                    <?php echo esc_html($topic); ?>
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-500 font-medium"><?php esc_html_e('اشتراک‌گذاری پرونده:', 'revayatiran'); ?></span>
                                <button type="button" onclick="navigator.clipboard.writeText(window.location.href); alert('<?php echo esc_js(__('پیوند پرونده در حافظه کپی شد.', 'revayatiran')); ?>');" class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-white/5 hover:bg-primary/10 hover:text-primary text-gray-600 dark:text-gray-400 flex items-center justify-center transition-colors text-sm" aria-label="<?php esc_attr_e('کپی لینک', 'revayatiran'); ?>">
                                    <i class="ph ph-link"></i>
                                </button>
                                <a href="https://t.me/share/url?url=<?php echo rawurlencode(get_permalink()); ?>&text=<?php echo rawurlencode($title); ?>" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-white/5 hover:bg-[#24A1DE]/10 hover:text-[#24A1DE] text-gray-600 dark:text-gray-400 flex items-center justify-center transition-colors text-sm" aria-label="<?php esc_attr_e('اشتراک در تلگرام', 'revayatiran'); ?>">
                                    <i class="ph ph-telegram-logo"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?url=<?php echo rawurlencode(get_permalink()); ?>&text=<?php echo rawurlencode($title); ?>" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-white/5 hover:bg-black/10 hover:text-black dark:hover:text-white text-gray-600 dark:text-gray-400 flex items-center justify-center transition-colors text-sm" aria-label="<?php esc_attr_e('اشتراک در توییتر/ایکس', 'revayatiran'); ?>">
                                    <i class="ph ph-x-logo"></i>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- ═══ سایدبار (چپ - ۳۰٪ مطابق با orginal source/single.php) ═══ -->
                <aside class="w-full lg:w-[320px] shrink-0 space-y-6 lg:sticky lg:top-28 self-start order-1 lg:order-2">

                    <!-- کارت شناسنامه پرونده -->
                    <div class="bg-white dark:bg-[#1a1c23] rounded-2xl p-5 border border-[var(--border)] shadow-sm">
                        <h3 class="text-[15px] font-extrabold text-gray-900 dark:text-white mb-4 flex items-center gap-2 pb-3 border-b border-[var(--border)]">
                            <i class="ph ph-info text-primary text-xl"></i>
                            <?php esc_html_e('شناسنامه پرونده', 'revayatiran'); ?>
                        </h3>
                        <dl class="space-y-3 text-xs">
                            <div class="flex items-center justify-between">
                                <dt class="text-gray-500"><?php esc_html_e('کد ثبت:', 'revayatiran'); ?></dt>
                                <dd class="font-mono font-bold text-gray-900 dark:text-white bg-gray-100 dark:bg-white/5 px-2 py-0.5 rounded"><?php echo esc_html($dossier_code); ?></dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-gray-500"><?php esc_html_e('وضعیت بررسی:', 'revayatiran'); ?></dt>
                                <dd class="font-bold <?php echo $is_live ? 'text-red-500' : 'text-primary'; ?>">
                                    <?php echo $is_live ? esc_html__('فعال و در جریان', 'revayatiran') : esc_html__('آرشیو تکمیل‌شده', 'revayatiran'); ?>
                                </dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-gray-500"><?php esc_html_e('محور تخصصی:', 'revayatiran'); ?></dt>
                                <dd class="font-bold text-gray-900 dark:text-white"><?php echo esc_html($topic); ?></dd>
                            </div>
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
                                $rel_topic = $rel['taxonomies']['dossier_topic'] ?? ($rel['category'] ?? __('پرونده اقتصادی', 'revayatiran'));
                                $rel_meta  = $rel['meta'] ?? [];
                                $rel_stats = $rel_meta['stats'] ?? [];
                                $rel_docs  = !empty($rel_stats['docs']) ? $rel_stats['docs'] : (!empty($rel_meta['stats_documents']) ? $rel_meta['stats_documents'] : 6);
                                $rel_date  = $rel['time_ago'] ?? ($rel['date'] ?? __('اخیر', 'revayatiran'));
                            ?>
                                <article class="dossier-mini-card">
                                    <div class="dossier-mini-card__tag"><?php echo esc_html($rel_topic); ?></div>
                                    <h5 class="dossier-mini-card__title">
                                        <a href="<?php echo esc_url($rel['permalink']); ?>">
                                            <?php echo esc_html($rel['title']); ?>
                                        </a>
                                    </h5>
                                    <div class="dossier-mini-card__meta">
                                        <span><i class="ph ph-file-text"></i> <?php echo esc_html(function_exists('revayat_en2fa') ? revayat_en2fa($rel_docs) : $rel_docs); ?> <?php esc_html_e('بخش و سند', 'revayatiran'); ?></span>
                                        <span><i class="ph ph-calendar-blank"></i> <?php echo esc_html(function_exists('revayat_en2fa') ? revayat_en2fa($rel_date) : $rel_date); ?></span>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </aside>
                    <?php endif; ?>

                </aside>

            </div>

        </div>
    </main>

<?php
endwhile;

get_footer();
