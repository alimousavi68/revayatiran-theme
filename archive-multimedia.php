<?php
/** Archive: multimedia. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
$has_sidebar = is_active_sidebar( 'public-archive-sidebar' );
$paged  = max( 1, (int) get_query_var( 'paged', 1 ) );
$format = isset( $_GET['format'] ) && is_string( $_GET['format'] ) ? sanitize_key( wp_unslash( $_GET['format'] ) ) : 'all';
$data   = revayat_get_archive_data( 'multimedia', array( 'paged' => $paged, 'format' => $format, 'posts_per_page' => 12 ) );
$items  = $data['items'] ?? array();
$filters = $data['filter_options']['formats'] ?? array();
$format_labels = array_column( $filters, 'label', 'slug' );
?>
<section class="rv-vertical rv-media-archive">
 <header class="rv-vertical__hero"><div class="container">
  <span class="rv-eyebrow"><i class="ph-fill ph-play-circle"></i><?php esc_html_e( 'رسانه‌نگار روایت ایران', 'revayatiran' ); ?></span>
  <h1><?php post_type_archive_title(); ?></h1>
  <p><?php esc_html_e( 'مجموعه ویدئوها، پادکست‌ها و روایت‌های تصویری تحریریه', 'revayatiran' ); ?></p>
 </div></header>
 <div class="container rv-vertical__body">
 <div class="rv-archive-layout<?php echo $has_sidebar ? ' has-sidebar' : ''; ?>">
 <div class="rv-archive-content">
  <?php if ( $filters ) : ?><nav class="rv-filter-row" aria-label="<?php esc_attr_e( 'فیلتر قالب رسانه', 'revayatiran' ); ?>">
   <?php foreach ( $filters as $filter ) : $active = ( $format === ( $filter['slug'] ?? 'all' ) ); ?>
    <a class="rv-filter-chip <?php echo $active ? 'is-active' : ''; ?>" href="<?php echo esc_url( $filter['url'] ?? '#' ); ?>"><?php echo esc_html( $filter['label'] ?? '' ); ?><span><?php echo esc_html( revayat_en2fa( $filter['count'] ?? 0 ) ); ?></span></a>
   <?php endforeach; ?>
  </nav><?php endif; ?>
  <?php if ( $items ) : ?><div class="rv-media-grid">
   <?php foreach ( $items as $item ) : ?>
    <article class="rv-media-archive-card" data-post-id="<?php echo esc_attr( $item['id'] ?? 0 ); ?>">
     <?php $story = revayat_adapt_multimedia_story( $item ); $story['duration'] = $item['meta']['duration'] ?? ''; $story['chip_label'] = $format_labels[ $story['media_type'] ] ?? $story['chip_label']; get_template_part( 'template-parts/cards/card-media', null, $story ); ?>
     <div class="rv-media-archive-card__body">
      <time datetime="<?php echo esc_attr( get_post_time( DATE_W3C, false, $item['id'] ?? 0 ) ); ?>"><?php echo esc_html( $item['date'] ?? '' ); ?></time>
      <p><?php echo esc_html( wp_trim_words( $item['excerpt'] ?? '', 35 ) ); ?></p>
     </div>
    </article>
   <?php endforeach; ?>
  </div><?php else : ?><section class="rv-empty"><i class="ph ph-film-strip"></i><h2><?php esc_html_e( 'رسانه‌ای یافت نشد', 'revayatiran' ); ?></h2><p><?php esc_html_e( 'برای این فیلتر هنوز محتوایی منتشر نشده است.', 'revayatiran' ); ?></p></section><?php endif; ?>
  <?php if ( ( $data['max_pages'] ?? 1 ) > 1 ) : ?><nav class="rv-pagination" aria-label="<?php esc_attr_e( 'صفحه‌بندی', 'revayatiran' ); ?>"><?php echo wp_kses_post( paginate_links( array( 'current' => $paged, 'total' => (int) $data['max_pages'], 'mid_size' => 1, 'prev_text' => __( 'قبلی', 'revayatiran' ), 'next_text' => __( 'بعدی', 'revayatiran' ), 'add_args' => 'all' !== $format ? array( 'format' => $format ) : array() ) ) ); ?></nav><?php endif; ?>
 </div>
 <?php get_template_part( 'template-parts/archive/sidebar' ); ?>
 </div>
 </div>
</section>
<?php get_footer();
