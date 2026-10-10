<?php
/**
 * تمپلیت تک‌بولتن اتاق وضعیت (Single Situation Room Bulletin)
 *
 * انطباق ۱۰۰٪ با ساختار و دیزاین سیستم Source اصلی (scratch_situation_room.php و single-special_dossier.php).
 * پشتیبانی کامل از دو وضعیت Gated (Soft Paywall) و Authenticated (دسترسی کامل، پیوست‌ها، گالری سنسور حرارتی).
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

    $post_id   = get_the_ID();
    $bulletin  = function_exists('revayat_get_situation_room_single_data') ? revayat_get_situation_room_single_data($post_id) : (function_exists('revayat_get_single_data') ? revayat_get_single_data($post_id) : []);

    $title        = $bulletin['title'] ?? get_the_title();
    $excerpt      = $bulletin['excerpt'] ?? get_the_excerpt();
    $date         = $bulletin['date'] ?? get_the_date();
    $thumbnail    = !empty($bulletin['media']['url']) ? $bulletin['media']['url'] : (has_post_thumbnail() ? get_the_post_thumbnail_url($post_id, 'full') : '');
    $meta         = $bulletin['meta'] ?? [];
    $taxonomies   = $bulletin['taxonomies'] ?? [];
    $access       = $bulletin['access'] ?? [];
    $attachments  = $bulletin['attachments'] ?? ($meta['attachments'] ?? []);
    $gallery      = $bulletin['sensor_gallery'] ?? ($meta['sensor_gallery'] ?? []);

    // وضعیت‌های دسترسی و صلاحیت
    $can_view       = !empty($access['can_view_content']);
    $can_download   = !empty($access['can_download_attachments']);
    $can_gallery    = !empty($access['can_view_gallery']);
    $user_clearance = $access['user_clearance'] ?? (function_exists('revayat_get_user_clearance') ? revayat_get_user_clearance() : 'guest');

    // مقادیر متاداده تاکتیکال
    $bulletin_type       = !empty($meta['bulletin_type']) ? $meta['bulletin_type'] : __('برآورد راهبردی', 'revayatiran');
    $urgency             = $meta['urgency_level'] ?? 'عادی';
    $is_hot              = in_array($urgency, ['فوری', 'آنی', 'بحرانی'], true);
    $verification_status = $meta['verification_status'] ?? __('تایید شده', 'revayatiran');
    $update_time         = !empty($meta['update_time']) ? $meta['update_time'] : ($bulletin['time_ago'] ?? '');
    $dossier_code        = !empty($meta['dossier_code']) ? $meta['dossier_code'] : 'SIT-' . date('Y') . '-' . str_pad($post_id, 3, '0', STR_PAD_LEFT);
    $source              = !empty($taxonomies['news_source']) ? $taxonomies['news_source'] : get_bloginfo('name');

    // نگاشت کانونیکال سطح امنیتی
    $sec_raw = $access['security_level'] ?? ($taxonomies['security_level'] ?? 'public');
    if (in_array($sec_raw, ['classified', 'high', 'secret', 'سطح ۱', 'سطح ۱ (عالی)'], true)) {
        $canonical_sec = 'classified';
        $sec_label     = __('سطح ۱ (عالی)', 'revayatiran');
    } elseif (in_array($sec_raw, ['restricted', 'normal', 'member', 'سطح ۲', 'سطح ۲ (متوسط)'], true)) {
        $canonical_sec = 'restricted';
        $sec_label     = __('سطح ۲ (متوسط)', 'revayatiran');
    } else {
        $canonical_sec = 'public';
        $sec_label     = __('سطح ۳ (پایه)', 'revayatiran');
    }

    $lock_reason = !empty($access['lock_reason']) ? $access['lock_reason'] : __('مشاهده محتوای این بولتن منوط به احراز هویت و ارتقای سطح دسترسی است.', 'revayatiran');
    $auth_url    = !empty($access['auth_url']) ? $access['auth_url'] : home_url('/auth/?tab=login');
    $register_url= !empty($access['register_url']) ? $access['register_url'] : home_url('/auth/?tab=register');
    $login_url   = !empty($access['login_url']) ? $access['login_url'] : home_url('/auth/?tab=login');
    $upgrade_url = !empty($access['upgrade_url']) ? $access['upgrade_url'] : home_url('/auth/?tab=upgrade');

    // بولتن‌های مرتبط
    $related = function_exists('revayat_get_related_posts') ? revayat_get_related_posts($post_id, 2) : [];
    ?>

    <section id="single-situation-room" class="bg-gray-50/50 dark:bg-black/20 min-h-screen pt-8 md:pt-12 pb-16">
        <div class="container">

            <!-- ═══ هدر اتاق فرمان (Tactical Command Hero) ═══ -->
            <article class="sr-bulletin-hero bg-[#0B1120] text-white rounded-2xl border border-[var(--border)] shadow-xl mb-10 p-6 md:p-10 relative overflow-hidden">
                <div class="absolute -right-20 -top-20 w-80 h-80 bg-red-900/10 rounded-full blur-3xl pointer-events-none"></div>

                <!-- نوار ناوبری بردکرامپ -->
                <div class="mb-6 relative z-10">
                    <nav class="flex text-xs md:text-sm text-gray-400 font-medium" aria-label="<?php esc_attr_e('موقعیت شما', 'revayatiran'); ?>">
                        <ol class="inline-flex items-center space-x-1 space-x-reverse md:space-x-2">
                            <li class="inline-flex items-center">
                                <a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-primary transition-colors">
                                    <?php esc_html_e('خانه', 'revayatiran'); ?>
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <i class="ph ph-caret-left text-xs mx-1"></i>
                                    <a href="<?php echo esc_url(get_post_type_archive_link('situation_room')); ?>" class="hover:text-primary transition-colors">
                                        <?php esc_html_e('اتاق وضعیت', 'revayatiran'); ?>
                                    </a>
                                </div>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <i class="ph ph-caret-left text-xs mx-1"></i>
                                    <span class="text-gray-300"><?php echo esc_html($bulletin_type); ?></span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                </div>

                <!-- تیتر اصلی بولتن -->
                <h1 class="text-2xl md:text-3xl lg:text-[32px] font-extrabold text-white leading-[1.6] mb-6 tracking-tight relative z-10">
                    <?php echo esc_html($title); ?>
                </h1>

                <!-- لید و چکیده راهبردی -->
                <?php if (!empty($excerpt)) : ?>
                    <p class="text-base md:text-lg text-gray-300 leading-relaxed mb-6 border-r-4 border-red-500/80 pr-4 font-medium relative z-10 <?php echo !$can_view ? 'sr-gated-item__excerpt--blurred' : ''; ?>">
                        <?php echo esc_html($excerpt); ?>
                    </p>
                <?php endif; ?>

                <!-- ردیف متاداده تکمیلی هدر -->
                <div class="flex flex-wrap items-center justify-between gap-4 pt-4 border-t border-white/10 text-xs text-gray-400 font-medium relative z-10">
                    <div class="flex items-center gap-2">
                        <i class="ph ph-buildings text-primary text-base"></i>
                        <span><?php esc_html_e('ناشر:', 'revayatiran'); ?></span>
                        <strong class="text-white"><?php echo esc_html($source); ?></strong>
                    </div>

                    <div class="flex items-center gap-4">
                        <?php if (!empty($update_time)) : ?>
                            <span class="flex items-center gap-1.5">
                                <i class="ph ph-clock text-primary"></i>
                                <?php esc_html_e('انتشار:', 'revayatiran'); ?>
                                <span class="text-gray-300"><?php echo esc_html(function_exists('revayat_en2fa') ? revayat_en2fa($update_time) : $update_time); ?></span>
                            </span>
                        <?php endif; ?>
                        <span class="flex items-center gap-1.5">
                            <i class="ph ph-calendar-blank"></i>
                            <span class="text-gray-300"><?php echo esc_html(function_exists('revayat_en2fa') ? revayat_en2fa($date) : $date); ?></span>
                        </span>
                    </div>
                </div>
                <?php if ($can_view) : ?>
                    <?php $cover = $bulletin['media'] ?? [];
                    if (empty($thumbnail) && function_exists('revayat_get_situation_fallback_media')) {
                        $cover = revayat_get_situation_fallback_media($bulletin);
                        $thumbnail = $cover['url'] ?? '';
                    }
                    if ($thumbnail) : ?>
                        <figure class="sr-bulletin-cover">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('revayat-hero-large', ['loading' => 'eager', 'fetchpriority' => 'high', 'sizes' => '(max-width: 767px) calc(100vw - 72px), (max-width: 1180px) calc(100vw - 100px), 1116px']); ?>
                            <?php else : ?>
                                <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($cover['alt'] ?? $title); ?>" width="1400" height="788" loading="eager" fetchpriority="high" decoding="async">
                            <?php endif; ?>
                        </figure>
                    <?php endif; ?>
                <?php endif; ?>
            </article>

            <!-- ═══ گرید اصلی (ستون محتوا + سایدبار شناسنامه بولتن) ═══ -->
            <div class="sr-bulletin-layout flex flex-col lg:flex-row gap-8 relative">

                <!-- ستون اصلی محتوا (راست - ۷۰٪) -->
                <div class="sr-bulletin-main flex-1 min-w-0 order-1">

                    <?php if ($can_view) : ?>
                        <!-- ═════ حالت دسترسی مجاز (Authenticated State) ═════ -->
                        <div class="sr-bulletin-body bg-white dark:bg-[#1a1c23] rounded-2xl border border-[var(--border)] shadow-sm p-6 md:p-8 lg:p-10 mb-8">
                            <div class="border-b border-[var(--border)] pb-4 mb-8 flex items-center justify-between">
                                <h2 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2.5">
                                    <i class="ph ph-article text-primary text-2xl"></i>
                                    <?php esc_html_e('متن بولتن', 'revayatiran'); ?>
                                </h2>
                            </div>

                            <!-- متن کامل بولتن -->
                            <div class="text-gray-900 dark:text-white leading-[2.2] text-[16px] md:text-[17px] space-y-6 font-normal entry-content sr-single-content">
                                <?php the_content(); ?>
                            </div>

                            <!-- ═══ بخش گالری سنسورهای ماهواره‌ای و رصد حرارتی ═══ -->
                            <?php if (!empty($gallery['items']) && is_array($gallery['items'])) : ?>
                                <div class="mt-12 pt-8 border-t border-[var(--border)]">
                                    <div class="flex items-center justify-between mb-6">
                                        <h3 class="text-base md:text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                            <i class="ph ph-crosshair text-primary text-xl"></i>
                                            <?php esc_html_e('رصد میدانی و تصویربرداری حرارتی ماهواره‌ای', 'revayatiran'); ?>
                                        </h3>
                                        <span class="text-xs px-2.5 py-1 rounded bg-black/5 dark:bg-white/5 text-gray-500 font-mono">
                                            <?php echo esc_html(function_exists('revayat_en2fa') ? revayat_en2fa(count($gallery['items'])) : count($gallery['items'])); ?> <?php esc_html_e('فریم', 'revayatiran'); ?>
                                        </span>
                                    </div>

                                    <?php if ($can_gallery) : ?>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                            <?php foreach ($gallery['items'] as $g_item) : 
                                                $g_url   = $g_item['url'] ?? ($g_item['thumb_url'] ?? '');
                                                $g_label = $g_item['sensor_label'] ?? 'SENSOR';
                                                $g_cap   = $g_item['caption'] ?? '';
                                                if (!empty($g_url)) :
                                            ?>
                                                <figure class="relative rounded-xl overflow-hidden border border-[var(--border)] bg-black group">
                                                    <img src="<?php echo esc_url($g_url); ?>" alt="<?php echo esc_attr($g_cap); ?>" class="w-full aspect-[4/3] object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
                                                    <span class="img-sensor"><?php echo esc_html($g_label); ?></span>
                                                    <?php if (!empty($g_cap)) : ?>
                                                        <figcaption class="p-2.5 bg-black/80 text-[11px] text-gray-300 line-clamp-1">
                                                            <?php echo esc_html($g_cap); ?>
                                                        </figcaption>
                                                    <?php endif; ?>
                                                </figure>
                                            <?php endif; endforeach; ?>
                                        </div>
                                    <?php else : ?>
                                        <div class="tactical-lock-block level-1-lock">
                                            <p class="lock-msg">
                                                <i class="ph-fill ph-shield-warning text-red-500"></i>
                                                <?php esc_html_e('مشاهده فریم‌های با تفکیک‌پذیری بالا و تصاویر حرارتی ماهواره‌ای نیازمند ارتقای صلاحیت سطح ۱ است.', 'revayatiran'); ?>
                                            </p>
                                            <a href="<?php echo esc_url($upgrade_url); ?>" class="upgrade-action-btn danger-upgrade">
                                                <i class="ph ph-fingerprint"></i>
                                                <span><?php esc_html_e('درخواست دسترسی تصاویر ماهواره‌ای', 'revayatiran'); ?></span>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <!-- ═══ بخش اسناد و پیوست‌های دانلودی / برخط ═══ -->
                            <?php if (!empty($attachments) && is_array($attachments)) : ?>
                                <div class="mt-12 pt-8 border-t border-[var(--border)]">
                                    <div class="flex items-center justify-between mb-6">
                                        <h3 class="text-base md:text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                            <i class="ph ph-file-text text-primary text-xl"></i>
                                            <?php esc_html_e('اسناد و مدارک راهبردی پیوست', 'revayatiran'); ?>
                                        </h3>
                                        <span class="text-xs text-gray-500">
                                            <?php echo esc_html(function_exists('revayat_en2fa') ? revayat_en2fa(count($attachments)) : count($attachments)); ?> <?php esc_html_e('سند ثبت‌شده', 'revayatiran'); ?>
                                        </span>
                                    </div>

                                    <div class="attachments-list">
                                        <?php foreach ($attachments as $att) : 
                                            $att_title     = $att['file_name'] ?? ($att['title'] ?? __('سند راهبردی', 'revayatiran'));
                                            $att_size      = $att['file_size'] ?? 'PDF';
                                            $att_url       = $att['url'] ?? '';
                                            $is_download   = !empty($att['is_downloadable']) && !empty($att_url) && $can_download;
                                            $is_view_only  = !empty($att['is_view_only']);
                                            $sec_note      = $att['security_note'] ?? ($is_view_only ? __('فقط مشاهده برخط (واترمارک شده)', 'revayatiran') : '');
                                        ?>
                                            <div class="attachment-file <?php echo $is_download ? 'file-downloadable' : 'file-view-only'; ?>">
                                                <div class="file-info">
                                                    <div class="file-icon <?php echo $is_view_only ? 'warning-type' : 'pdf-type'; ?>">
                                                        <i class="ph-fill <?php echo $is_view_only ? 'ph-shield-warning' : 'ph-file-pdf'; ?>"></i>
                                                    </div>
                                                    <div class="file-text">
                                                        <span class="file-name"><?php echo esc_html($att_title); ?></span>
                                                        <span class="<?php echo $is_view_only ? 'file-security-note' : 'file-size'; ?>">
                                                            <?php echo esc_html(function_exists('revayat_en2fa') ? revayat_en2fa($is_view_only ? $sec_note : $att_size) : ($is_view_only ? $sec_note : $att_size)); ?>
                                                        </span>
                                                    </div>
                                                </div>

                                                <?php if ($is_download) : ?>
                                                    <a href="<?php echo esc_url($att_url); ?>" class="download-btn-modern" title="<?php esc_attr_e('دانلود مستقیم سند', 'revayatiran'); ?>" download>
                                                        <i class="ph-bold ph-download-simple"></i>
                                                    </a>
                                                <?php else : ?>
                                                    <span class="view-only-badge" title="<?php esc_attr_e('دانلود به دلایل امنیتی غیرفعال است (مشاهده با مهر دیجیتال)', 'revayatiran'); ?>">
                                                        <i class="ph-bold ph-eye"></i>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>


                        </div>

                    <?php else : ?>

                        <!-- ═════ حالت قفل‌شده (Gated State / Soft Paywall) ═════ -->
                        <div class="sr-gated-wrapper p-6 md:p-10 mb-8" data-reveal="up">
                            <div class="sr-gated-curtain">
                                <!-- محتوای محو پیشرونده -->
                                <div class="space-y-4 filter blur-[2px] opacity-60 select-none pointer-events-none mb-8">
                                    <p class="leading-loose text-gray-300">
                                        <?php esc_html_e('این سند راهبردی حاوی ارزیابی‌های پدافندی، تحلیل سناریوهای تقابل در امنیت ملی، داده‌های حساس و پیوست‌های ایمن‌سازی شبکه‌های حیاتی است که بر اساس آیین‌نامه‌های صیانتی طبقه‌بندی شده‌اند.', 'revayatiran'); ?>
                                    </p>
                                    <p class="leading-loose text-gray-300">
                                        <?php esc_html_e('بررسی الگوهای تغییر در رفتارهای بازیگران پیرامونی و شبیه‌سازی مراحل بحران در زیرساخت‌های انرژی و سایبری...', 'revayatiran'); ?>
                                    </p>
                                </div>

                                <!-- کادر CTA شیشه‌ای پی‌وال -->
                                <div class="sr-gated-cta">
                                    <div class="sr-gated-cta__inner">
                                        <i class="ph-fill ph-lock-key sr-gated-cta__icon"></i>
                                        <h3 class="text-lg md:text-xl font-extrabold text-white">
                                            <?php esc_html_e('محتوای طبقه‌بندی شده اتاق وضعیت', 'revayatiran'); ?>
                                        </h3>
                                        <p class="sr-gated-cta__text">
                                            <?php echo esc_html($lock_reason); ?>
                                        </p>

                                        <div class="sr-gated-cta__actions">
                                            <?php if ($user_clearance === 'guest') : ?>
                                                <a href="<?php echo esc_url($register_url); ?>" class="button--compact button--primary button--glow">
                                                    <i class="ph-fill ph-user-plus"></i>
                                                    <span><?php esc_html_e('ثبت‌نام رایگان', 'revayatiran'); ?></span>
                                                </a>
                                                <a href="<?php echo esc_url($login_url); ?>" class="button--compact button--soft">
                                                    <i class="ph-fill ph-sign-in"></i>
                                                    <span><?php esc_html_e('ورود به حساب کاربری', 'revayatiran'); ?></span>
                                                </a>
                                            <?php else : ?>
                                                <a href="<?php echo esc_url($upgrade_url); ?>" class="upgrade-action-btn danger-upgrade">
                                                    <i class="ph ph-fingerprint"></i>
                                                    <span><?php esc_html_e('احراز هویت دو مرحله‌ای و ارتقای سطح', 'revayatiran'); ?></span>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    <?php endif; ?>

                </div>

                <!-- ═══ سایدبار شناسنامه بولتن و موارد مرتبط (چپ - ۳۰٪) ═══ -->
                <aside class="sr-bulletin-sidebar w-full lg:w-[320px] shrink-0 space-y-6 lg:sticky lg:top-28 self-start order-2">

                    <!-- کارت شناسنامه امنیتی سند -->
                    <div class="bg-white dark:bg-[#1a1c23] rounded-2xl p-5 border border-[var(--border)] shadow-sm">
                        <h3 class="text-[15px] font-extrabold text-gray-900 dark:text-white mb-4 flex items-center gap-2 pb-3 border-b border-[var(--border)]">
                            <i class="ph ph-shield-check text-primary text-xl"></i>
                            <?php esc_html_e('شناسنامه سند راهبردی', 'revayatiran'); ?>
                        </h3>

                        <dl class="space-y-3 text-xs">
                            <div class="flex items-center justify-between">
                                <dt class="text-gray-500"><?php esc_html_e('کد سند:', 'revayatiran'); ?></dt>
                                <dd class="font-mono font-bold text-gray-900 dark:text-white bg-gray-100 dark:bg-white/5 px-2 py-0.5 rounded"><?php echo esc_html($dossier_code); ?></dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-gray-500"><?php esc_html_e('سطح طبقه‌بندی:', 'revayatiran'); ?></dt>
                                <dd class="font-bold <?php echo ('classified' === $canonical_sec) ? 'text-red-500' : 'text-amber-500'; ?>"><?php echo esc_html($sec_label); ?></dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-gray-500"><?php esc_html_e('گونه سند:', 'revayatiran'); ?></dt>
                                <dd class="font-bold text-gray-900 dark:text-white"><?php echo esc_html($bulletin_type); ?></dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-gray-500"><?php esc_html_e('درجه فوریت:', 'revayatiran'); ?></dt>
                                <dd class="font-bold <?php echo $is_hot ? 'text-red-500' : 'text-gray-700 dark:text-gray-300'; ?>"><?php echo esc_html($urgency); ?></dd>
                            </div>
                            <?php if ($verification_status) : ?>
                            <div class="flex items-center justify-between">
                                <dt class="text-gray-500"><?php esc_html_e('وضعیت بررسی:', 'revayatiran'); ?></dt>
                                <dd class="font-bold text-emerald-500"><?php echo esc_html($verification_status); ?></dd>
                            </div><?php endif; ?>
                        </dl>
                    </div>

                    <!-- بولتن‌های مرتبط -->
                    <?php if (!empty($related)) : ?>
                        <div class="bg-white dark:bg-[#1a1c23] rounded-2xl p-5 border border-[var(--border)] shadow-sm">
                            <h4 class="text-[14px] font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2 pb-3 border-b border-[var(--border)]">
                                <i class="ph ph-broadcast text-primary"></i>
                                <?php esc_html_e('بولتن‌های مرتبط', 'revayatiran'); ?>
                            </h4>

                            <div class="space-y-4">
                                <?php foreach ($related as $rel) : 
                                    $rel_url  = $rel['permalink'] ?? get_permalink($rel['id'] ?? null);
                                    $rel_type = $rel['meta']['bulletin_type'] ?? ($rel['category'] ?? __('برآورد', 'revayatiran'));
                                ?>
                                    <article class="p-3 rounded-xl bg-gray-50 dark:bg-white/[0.03] border border-[var(--border)] hover:border-primary/40 transition-colors">
                                        <span class="chip chip--tactical text-[10px] mb-2 inline-block"><?php echo esc_html($rel_type); ?></span>
                                        <h5 class="text-xs font-bold leading-relaxed text-gray-900 dark:text-white mb-2">
                                            <a href="<?php echo esc_url($rel_url); ?>" class="hover:text-primary transition-colors">
                                                <?php echo esc_html($rel['title']); ?>
                                            </a>
                                        </h5>
                                        <span class="text-[10px] text-gray-400 flex items-center gap-1">
                                            <i class="ph ph-clock"></i>
                                            <?php echo esc_html(function_exists('revayat_en2fa') ? revayat_en2fa($rel['time_ago'] ?? '') : ($rel['time_ago'] ?? '')); ?>
                                        </span>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                </aside>

            </div>

        </div>
    </section>

<?php
endwhile;

get_footer();
