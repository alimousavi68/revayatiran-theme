<?php
/** One share panel across templates; restricted documents only expose link copying. */
if (!defined('ABSPATH') || post_password_required()) { return; }
$url = get_permalink(); $title = get_the_title(); $private = !empty($args['private']);
$networks = [
    'تلگرام' => ['https://t.me/share/url?url=' . rawurlencode($url) . '&text=' . rawurlencode($title), 'ph-telegram-logo', 'telegram'],
    'واتساپ' => ['https://api.whatsapp.com/send?text=' . rawurlencode($title . ' ' . $url), 'ph-whatsapp-logo', 'whatsapp'],
    'ایکس' => ['https://twitter.com/intent/tweet?url=' . rawurlencode($url) . '&text=' . rawurlencode($title), 'ph-x-logo', 'x'],
];
?>
<div class="rv-share" data-url="<?php echo esc_url($url); ?>" data-title="<?php echo esc_attr($title); ?>">
    <div class="rv-share__heading"><span class="rv-share__symbol" aria-hidden="true"><i class="ph ph-share-network"></i></span><h2><?php echo esc_html($private ? __('پیوند این سند', 'revayatiran') : __('اشتراک‌گذاری مطلب', 'revayatiran')); ?></h2></div>
    <div class="rv-share__actions">
        <button type="button" class="rv-share__copy" data-share-copy><i class="ph ph-copy" aria-hidden="true"></i><?php esc_html_e('کپی لینک', 'revayatiran'); ?></button>
        <?php if (!$private) : ?>
        <button type="button" data-share-native hidden><i class="ph ph-share" aria-hidden="true"></i><?php esc_html_e('اشتراک‌گذاری', 'revayatiran'); ?></button>
        <?php foreach ($networks as $label => $network) : ?>
            <a class="rv-share__network rv-share__network--<?php echo esc_attr($network[2]); ?>" href="<?php echo esc_url($network[0]); ?>" target="_blank" rel="noopener noreferrer"><i class="ph <?php echo esc_attr($network[1]); ?>" aria-hidden="true"></i><?php echo esc_html($label); ?></a>
        <?php endforeach; endif; ?>
    </div>
    <p class="rv-share__status" role="status" aria-live="polite"></p>
    <input class="rv-share__fallback" type="text" readonly value="<?php echo esc_url($url); ?>" aria-label="<?php esc_attr_e('پیوند مطلب برای کپی دستی', 'revayatiran'); ?>" hidden>
</div>
