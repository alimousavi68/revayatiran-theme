<?php
/** Archive: person. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
$paged = max( 1, (int) get_query_var( 'paged', 1 ) );
$field = isset( $_GET['field'] ) ? sanitize_key( wp_unslash( $_GET['field'] ) ) : 'all';
$data = revayat_get_archive_data( 'person', array( 'paged' => $paged, 'field' => $field, 'posts_per_page' => 12 ) );
?>
<main class="rv-vertical rv-person-archive"><header class="rv-vertical__hero"><div class="container"><span class="rv-eyebrow"><i class="ph ph-users-three"></i><?php esc_html_e( 'شبکه نخبگان', 'revayatiran' ); ?></span><h1><?php post_type_archive_title(); ?></h1><p><?php esc_html_e( 'پروفایل پژوهشگران، تحلیلگران و متخصصان همکار روایت ایران', 'revayatiran' ); ?></p></div></header><div class="container rv-vertical__body">
 <?php $filters = $data['filter_options']['fields'] ?? array(); if ( $filters ) : ?><nav class="rv-filter-row"><?php foreach ( $filters as $filter ) : ?><a class="rv-filter-chip <?php echo $field === ( $filter['slug'] ?? 'all' ) ? 'is-active' : ''; ?>" href="<?php echo esc_url( $filter['url'] ?? '#' ); ?>"><?php echo esc_html( $filter['label'] ?? '' ); ?></a><?php endforeach; ?></nav><?php endif; ?>
 <?php if ( ! empty( $data['items'] ) ) : ?><div class="rv-person-grid"><?php foreach ( $data['items'] as $item ) { get_template_part( 'template-parts/cards/card-person', null, array( 'item' => $item ) ); } ?></div><?php else : ?><section class="rv-empty"><i class="ph ph-users"></i><h2><?php esc_html_e( 'پروفایلی یافت نشد', 'revayatiran' ); ?></h2></section><?php endif; ?>
 <?php if ( ( $data['max_pages'] ?? 1 ) > 1 ) : ?><nav class="rv-pagination"><?php echo wp_kses_post( paginate_links( array( 'current' => $paged, 'total' => (int) $data['max_pages'] ) ) ); ?></nav><?php endif; ?>
</div></main>
<?php get_footer();
