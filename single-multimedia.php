<?php
/** Single: multimedia. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
while ( have_posts() ) : the_post();
 $data = revayat_get_single_data( get_the_ID() );
 $meta = $data['meta'] ?? array();
 $type = $data['taxonomies']['media_format'] ?? ( $meta['media_type'] ?? 'video' );
 $labels = array( 'video' => __( 'ویدئو', 'revayatiran' ), 'audio' => __( 'پادکست', 'revayatiran' ), 'gallery' => __( 'گالری تصویری', 'revayatiran' ) );
 $media_url = $meta['video_url'] ?? '';
 $cover = $data['media']['url'] ?? '';
?>
<main class="rv-vertical rv-media-single"><article class="container rv-single">
 <nav class="rv-breadcrumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'خانه', 'revayatiran' ); ?></a><i class="ph ph-caret-left"></i><a href="<?php echo esc_url( get_post_type_archive_link( 'multimedia' ) ); ?>"><?php esc_html_e( 'رسانه‌نگار', 'revayatiran' ); ?></a></nav>
 <header class="rv-single__header"><span class="rv-format-badge"><i class="ph-fill <?php echo 'audio' === $type ? 'ph-headphones' : ( 'gallery' === $type ? 'ph-images' : 'ph-play' ); ?>"></i><?php echo esc_html( $labels[ $type ] ?? $labels['video'] ); ?></span><h1><?php echo esc_html( $data['title'] ?? get_the_title() ); ?></h1><p><?php echo esc_html( $data['excerpt'] ?? get_the_excerpt() ); ?></p><div class="rv-meta"><time><?php echo esc_html( revayat_en2fa( $data['date'] ?? get_the_date() ) ); ?></time><?php if ( ! empty( $meta['duration'] ) ) : ?><span><i class="ph ph-clock"></i><?php echo esc_html( revayat_en2fa( $meta['duration'] ) ); ?></span><?php endif; ?></div></header>
 <section class="rv-player">
 <?php if ( $media_url && 'audio' === $type ) : ?><audio controls preload="metadata" src="<?php echo esc_url( $media_url ); ?>"></audio>
 <?php elseif ( $media_url && 'video' === $type ) : ?><video controls preload="metadata" poster="<?php echo esc_url( $cover ); ?>" src="<?php echo esc_url( $media_url ); ?>"></video>
 <?php elseif ( $cover ) : ?><img src="<?php echo esc_url( $cover ); ?>" alt="<?php echo esc_attr( $data['media']['alt'] ?? get_the_title() ); ?>">
 <?php else : ?><div class="rv-player__placeholder"><i class="ph ph-film-strip"></i><span><?php esc_html_e( 'فایل رسانه هنوز بارگذاری نشده است', 'revayatiran' ); ?></span></div><?php endif; ?>
 </section>
 <section class="rv-editorial-content"><?php the_content(); ?></section>
 <?php $related = revayat_get_related_posts( get_the_ID(), 3 ); if ( $related ) : ?><section class="rv-related"><h2><?php esc_html_e( 'رسانه‌های مرتبط', 'revayatiran' ); ?></h2><div class="rv-media-grid"><?php foreach ( $related as $item ) { get_template_part( 'template-parts/cards/card-media', null, revayat_adapt_multimedia_story( $item ) ); } ?></div></section><?php endif; ?>
</article></main>
<?php endwhile; get_footer();
