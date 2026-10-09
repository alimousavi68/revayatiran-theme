<?php
/**
 * کامپوننت کارت تاکتیکال اتاق وضعیت (Situation Room Tactical Card)
 *
 * انطباق ۱۰۰٪ با ساختار و دیزاین سیستم Source اصلی (scratch_situation_room.php و components/situation-room.php).
 * قابل استفاده مجدد در گرید آرشیو، بخش بولتن‌های مرتبط و سکشن صفحه اصلی.
 *
 * @package RevayatIran
 * @subpackage Template_Parts/Cards
 * @version 2.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$item = $args['item'] ?? ($args['bulletin'] ?? []);
if (empty($item)) {
    if (isset($args['post'])) {
        $item = function_exists('revayat_get_situation_room_single_data') ? revayat_get_situation_room_single_data($args['post']) : (function_exists('revayat_get_single_data') ? revayat_get_single_data($args['post']) : []);
    } else {
        $item = function_exists('revayat_get_situation_room_single_data') ? revayat_get_situation_room_single_data(get_the_ID()) : (function_exists('revayat_get_single_data') ? revayat_get_single_data(get_the_ID()) : []);
    }
}

if (empty($item)) {
    return;
}

$id            = $item['id'] ?? 0;
$title         = $item['title'] ?? '';
$url           = $item['permalink'] ?? ($item['url'] ?? get_permalink($id));
$excerpt       = $item['excerpt'] ?? ($item['summary'] ?? '');
$meta          = $item['meta'] ?? [];
$taxonomies    = $item['taxonomies'] ?? [];
$access        = $item['access'] ?? [];
$attachments   = $item['attachments'] ?? ($meta['attachments'] ?? []);
$gallery       = $item['sensor_gallery'] ?? ($meta['sensor_gallery'] ?? []);
$media         = $item['media'] ?? [];
$media_url     = $media['url'] ?? ($item['thumbnail_url'] ?? '');
$media_alt     = $media['alt'] ?? $title;
$media_srcset  = $media['srcset'] ?? '';
$variant       = sanitize_key($args['variant'] ?? 'default');
$uses_fallback = false;

if (empty($media_url) && function_exists('revayat_get_situation_fallback_media')) {
    $fallback_media = revayat_get_situation_fallback_media($item);
    $media_url      = $fallback_media['url'];
    $media_alt      = $fallback_media['alt'];
    $uses_fallback  = true;
}

// مقادیر تاکتیکال و امنیتی
$bulletin_type = !empty($meta['bulletin_type']) ? $meta['bulletin_type'] : ($item['category'] ?? __('برآورد راهبردی', 'revayatiran'));
$urgency       = $meta['urgency_level'] ?? ($item['urgency_level'] ?? 'عادی');
$is_hot        = in_array($urgency, ['فوری', 'آنی', 'بحرانی'], true) || !empty($item['is_hot']);
$update_time   = !empty($meta['update_time']) ? $meta['update_time'] : ($item['time_ago'] ?? ($item['date'] ?? ''));

// بررسی سطح امنیت
$sec_raw = $taxonomies['security_level'] ?? ($item['security_level'] ?? 'public');
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

// وضعیت قفل و دسترسی
$is_locked   = !empty($access['is_locked']) || (!empty($meta['is_locked']) && !is_user_logged_in());
$lock_reason = !empty($access['lock_reason']) ? $access['lock_reason'] : __('دسترسی به این بولتن نیازمند احراز هویت است.', 'revayatiran');
$auth_url    = !empty($access['auth_url']) ? $access['auth_url'] : home_url('/auth/?tab=login');
$action_text = (isset($access['user_clearance']) && 'normal' === $access['user_clearance']) ? __('درخواست ارتقای سطح دسترسی', 'revayatiran') : __('ورود به حساب / ثبت‌نام', 'revayatiran');
?>

<article class="situation-item panel-card--dark p-6 <?php echo $is_locked ? 'item--locked ' : ''; ?><?php echo 'visual' === $variant ? 'situation-item--visual' : ''; ?>" data-reveal="up">
    <?php if ('visual' === $variant) : ?>
        <a class="sr-card-media <?php echo $uses_fallback ? 'is-editorial-fallback' : 'has-image'; ?>" href="<?php echo esc_url($url); ?>" aria-label="<?php echo esc_attr(sprintf(__('مشاهده بولتن: %s', 'revayatiran'), $title)); ?>">
            <?php if ($media_url) : ?>
                <img src="<?php echo esc_url($media_url); ?>"
                     <?php if ($media_srcset) : ?>srcset="<?php echo esc_attr($media_srcset); ?>" sizes="(max-width: 760px) 100vw, 50vw"<?php endif; ?>
                     alt="<?php echo esc_attr($media_alt); ?>" loading="lazy" decoding="async" width="800" height="450">
            <?php endif; ?>
            <span class="sr-card-media__shade" aria-hidden="true"></span>
            <span class="sr-card-media__fallback-label"><?php echo esc_html($bulletin_type); ?></span>
        </a>
    <?php endif; ?>
    <!-- ═══ نوار وضعیت ترمینال و نشانگر قفل ═══ -->
    <div class="card-status-bar">
        <div class="terminal-dots">
            <span></span><span></span><span></span>
        </div>

        <div class="flex items-center gap-2">
            <?php if ($is_hot) : ?>
                <span class="sr-urgency-badge sr-urgency-badge--hot">
                    <?php echo esc_html($urgency); ?>
                </span>
            <?php endif; ?>

            <span class="security-tag tag-<?php echo esc_attr($canonical_sec); ?>">
                <?php echo esc_html($sec_label); ?>
            </span>

            <?php if ($is_locked) : ?>
                <?php if ('classified' === $canonical_sec) : ?>
                    <span class="lock-indicator alert-lock">
                        <i class="ph-fill ph-shield-warning"></i>
                        <?php esc_html_e('غیرمجاز', 'revayatiran'); ?>
                    </span>
                <?php else : ?>
                    <span class="lock-indicator">
                        <i class="ph-fill ph-lock"></i>
                        <?php esc_html_e('محدود شده', 'revayatiran'); ?>
                    </span>
                <?php endif; ?>
            <?php else : ?>
                <span class="lock-indicator unlock">
                    <i class="ph-fill ph-lock-key-open"></i>
                    <?php esc_html_e('تایید شده', 'revayatiran'); ?>
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- ═══ هدر آیتم: برچسب تاکتیکال و زمان ═══ -->
    <div class="item-header">
        <span class="chip chip--tactical <?php echo ('classified' === $canonical_sec) ? 'chip--danger' : ''; ?>">
            <?php echo esc_html($bulletin_type); ?>
        </span>

        <?php if (!empty($update_time)) : ?>
            <span class="item-date">
                <i class="ph ph-clock text-xs"></i>
                <?php echo esc_html(function_exists('revayat_en2fa') ? revayat_en2fa($update_time) : $update_time); ?>
            </span>
        <?php endif; ?>
    </div>

    <!-- ═══ بدنه آیتم: عنوان و توضیحات تاکتیکال ═══ -->
    <div class="item-body">
        <h3>
            <a href="<?php echo esc_url($url); ?>">
                <?php echo esc_html($title); ?>
            </a>
        </h3>

        <?php if ($is_locked) : ?>
            <!-- حالت قفل شده -->
            <p class="tactical-desc sr-gated-item__excerpt--blurred">
                <?php echo esc_html($excerpt); ?>
            </p>

            <div class="tactical-lock-block <?php echo ('classified' === $canonical_sec) ? 'level-1-lock' : ''; ?>">
                <p class="lock-msg">
                    <i class="ph-fill ph-lock-key text-red-400"></i>
                    <?php echo esc_html($lock_reason); ?>
                </p>
                <a href="<?php echo esc_url($auth_url); ?>" class="upgrade-action-btn <?php echo ('classified' === $canonical_sec) ? 'danger-upgrade' : ''; ?>">
                    <i class="ph <?php echo ('classified' === $canonical_sec) ? 'ph-fingerprint' : 'ph-arrows-clockwise'; ?>"></i>
                    <span><?php echo esc_html($action_text); ?></span>
                </a>
            </div>
        <?php else : ?>
            <!-- حالت باز شده -->
            <p class="tactical-desc">
                <?php echo esc_html($excerpt); ?>
            </p>

            <!-- اسنیپت پیوست‌های سند (در صورت وجود) -->
            <?php if (!empty($attachments) && is_array($attachments)) : 
                $first_att = reset($attachments);
                if (!empty($first_att) && is_array($first_att)) :
                    $att_title     = $first_att['file_name'] ?? ($first_att['title'] ?? __('سند پیوست', 'revayatiran'));
                    $att_size      = $first_att['file_size'] ?? 'PDF';
                    $att_url       = $first_att['url'] ?? '';
                    $is_download   = !empty($first_att['is_downloadable']) && !empty($att_url);
                    $is_view_only  = !empty($first_att['is_view_only']);
                    $sec_note      = $first_att['security_note'] ?? ($is_view_only ? __('فقط مشاهده برخط (واترمارک شده)', 'revayatiran') : '');
            ?>
                <div class="attachments-list">
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
                            <span class="view-only-badge" title="<?php esc_attr_e('مشاهده برخط با مهر امنیتی', 'revayatiran'); ?>">
                                <i class="ph-bold ph-eye"></i>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; endif; ?>

            <!-- اسنیپت گالری سنسورهای ماهواره‌ای و حرارتی (در صورت وجود) -->
            <?php if (!empty($gallery['items']) && is_array($gallery['items'])) : 
                $gallery_items = array_slice($gallery['items'], 0, 3);
                $more_count    = max(0, ($gallery['total_count'] ?? count($gallery['items'])) - 3);
            ?>
                <div class="tactical-gallery">
                    <div class="gallery-wrapper">
                        <?php foreach ($gallery_items as $g_item) : 
                            $g_thumb = $g_item['thumb_url'] ?? ($g_item['url'] ?? '');
                            $g_label = $g_item['sensor_label'] ?? 'SENSOR';
                            if (!empty($g_thumb)) :
                        ?>
                            <div class="gallery-item-tactical">
                                <img src="<?php echo esc_url($g_thumb); ?>" alt="<?php echo esc_attr($g_item['caption'] ?? $title); ?>" loading="lazy">
                                <span class="img-sensor"><?php echo esc_html($g_label); ?></span>
                            </div>
                        <?php endif; endforeach; ?>

                        <?php if ($more_count > 0) : ?>
                            <div class="gallery-more-tactical">
                                <span class="more-number">+<?php echo esc_html(function_exists('revayat_en2fa') ? revayat_en2fa($more_count) : $more_count); ?></span>
                                <span class="more-label"><?php esc_html_e('تصویر دیگر', 'revayatiran'); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>
    <?php if ('visual' === $variant && !$is_locked) : ?>
        <a class="sr-card-action" href="<?php echo esc_url($url); ?>">
            <span><?php esc_html_e('مشاهده بولتن', 'revayatiran'); ?></span>
            <i class="ph ph-arrow-left"></i>
        </a>
    <?php endif; ?>
</article>
