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
 $excerpt = $data['excerpt'] ?? get_the_excerpt();
 $intro = revayat_media_intro_visibility(get_the_content(), $cover, $excerpt);
 $has_player = $media_url && in_array($type, ['audio', 'video'], true);
?>
<main class="rv-vertical rv-media-single"><div class="container rv-reading-layout"><article class="rv-single rv-reading-main">
 <nav class="rv-breadcrumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'خانه', 'revayatiran' ); ?></a><i class="ph ph-caret-left"></i><a href="<?php echo esc_url( get_post_type_archive_link( 'multimedia' ) ); ?>"><?php esc_html_e( 'رسانه‌نگار', 'revayatiran' ); ?></a></nav>
 <header class="rv-single__header"><span class="rv-format-badge"><i class="ph-fill <?php echo 'audio' === $type ? 'ph-headphones' : ( 'gallery' === $type ? 'ph-images' : 'ph-play' ); ?>"></i><?php echo esc_html( $labels[ $type ] ?? $labels['video'] ); ?></span><h1><?php echo esc_html( $data['title'] ?? get_the_title() ); ?></h1><?php if ($intro['excerpt']) : ?><p><?php echo esc_html($excerpt); ?></p><?php endif; ?><div class="rv-meta"><time><?php echo esc_html( revayat_en2fa( $data['date'] ?? get_the_date() ) ); ?></time><?php if ( ! empty( $meta['duration'] ) ) : ?><span><i class="ph ph-clock"></i><?php echo esc_html( revayat_en2fa( $meta['duration'] ) ); ?></span><?php endif; ?></div></header>

 <?php if ($has_player || $intro['cover'] || (!$cover && !get_the_content())) : ?>
 <section class="rv-player">
 <?php if ( $media_url && 'audio' === $type ) : ?><audio controls preload="metadata" src="<?php echo esc_url( $media_url ); ?>"></audio>
 <?php elseif ( $media_url && 'video' === $type ) : ?><video controls preload="metadata" poster="<?php echo esc_url( $cover ); ?>" src="<?php echo esc_url( $media_url ); ?>"></video>
 <?php elseif ( $cover ) : ?><img src="<?php echo esc_url( $cover ); ?>" alt="<?php echo esc_attr( $data['media']['alt'] ?? get_the_title() ); ?>">
 <?php else : ?><div class="rv-player__placeholder"><i class="ph ph-film-strip"></i><span><?php esc_html_e( 'فایل رسانه هنوز بارگذاری نشده است', 'revayatiran' ); ?></span></div><?php endif; ?>
 </section>
 <?php endif; ?>
 <section class="rv-editorial-content"><?php the_content(); ?></section>
 <?php get_template_part('template-parts/dossier/parents'); ?>
                <?php get_template_part('template-parts/single/engagement'); ?>
</article><?php get_template_part('template-parts/single/sidebar'); ?></div></main>
<?php endwhile; get_footer();
