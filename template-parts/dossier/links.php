<?php
/** Public, explicitly selected dossier resources. */
if ( ! defined('ABSPATH') ) { exit; }
$links = $args['links'] ?? [];
$labels = ['post' => 'نوشته', 'analyst_post' => 'تحلیل', 'multimedia' => 'چندرسانه‌ای'];
foreach (['documents' => 'اسناد پرونده', 'content' => 'مطالب مرتبط پرونده'] as $kind => $heading) :
    $items = $links[$kind] ?? [];
    if (!$items) { continue; }
?>
<section class="rv-dossier-resources" aria-label="<?php echo esc_attr($heading); ?>">
    <h3><?php echo esc_html($heading); ?> <span class="rv-dossier-count"><?php echo esc_html(function_exists('revayat_en2fa') ? revayat_en2fa(count($items)) : count($items)); ?></span></h3>
    <ul>
        <?php foreach ($items as $item) : ?>
        <?php
        $is_document = 'documents' === $kind;
        $thumbnail = !$is_document ? get_the_post_thumbnail($item['id'], 'medium', ['loading' => 'lazy', 'alt' => '']) : '';
        $excerpt = !$is_document ? wp_trim_words(wp_strip_all_tags(get_the_excerpt($item['id'])), 22) : '';
        $format = $is_document ? strtoupper(pathinfo((string) wp_parse_url($item['url'], PHP_URL_PATH), PATHINFO_EXTENSION)) : ($labels[$item['type']] ?? 'مطلب');
        ?>
        <li class="rv-resource-card">
            <a href="<?php echo esc_url($item['url']); ?>">
                <span class="rv-resource-card__visual" aria-hidden="true"><?php if ($thumbnail) { echo $thumbnail; } else { ?><i class="ph <?php echo $is_document ? 'ph-file-text' : 'ph-article'; ?>"></i><?php } ?></span>
                <span class="rv-resource-card__body">
                    <span class="rv-resource-card__type"><?php echo esc_html($format); ?></span>
                    <strong><?php echo esc_html($item['title'] ?: 'بدون عنوان'); ?></strong>
                    <?php if ($excerpt) : ?><span class="rv-resource-card__excerpt"><?php echo esc_html($excerpt); ?></span><?php endif; ?>
                    <span class="rv-resource-card__action"><?php echo esc_html($is_document ? 'مشاهده سند' : 'مشاهده مطلب'); ?> <i class="ph ph-arrow-left" aria-hidden="true"></i></span>
                </span>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
</section>
<?php endforeach; ?>
