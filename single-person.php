<?php
/** Single: person. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
while ( have_posts() ) : the_post();
 $data = revayat_get_single_data( get_the_ID() ); $meta = $data['meta'] ?? array(); $image = $data['media']['url'] ?? '';
 $related = revayat_get_archive_data( 'analyst_post', array( 'contributor' => get_post_field( 'post_name', get_the_ID() ), 'posts_per_page' => 6, 'paged' => 1 ) );
?>
<main class="rv-vertical rv-person-single"><article class="container rv-person-profile">
 <nav class="rv-breadcrumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'خانه', 'revayatiran' ); ?></a><i class="ph ph-caret-left"></i><a href="<?php echo esc_url( get_post_type_archive_link( 'person' ) ); ?>"><?php esc_html_e( 'تحلیلگران', 'revayatiran' ); ?></a></nav>
 <header class="rv-person-profile__hero"><div class="rv-person-profile__avatar"><?php if ( $image ) : ?><img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $data['title'] ?? get_the_title() ); ?>"><?php else : ?><i class="ph ph-user"></i><?php endif; ?></div><div><span class="rv-eyebrow"><?php esc_html_e( 'پروفایل کارشناس', 'revayatiran' ); ?></span><h1><?php echo esc_html( $data['title'] ?? get_the_title() ); ?></h1><p class="rv-person-profile__role"><?php echo esc_html( $meta['role_title'] ?? '' ); ?></p><p><?php echo esc_html( $meta['organization'] ?? '' ); ?></p><div class="rv-person-profile__stats"><span><strong><?php echo esc_html( revayat_en2fa( number_format( (float) ( $meta['person_total_score'] ?? 0 ), 1 ) ) ); ?></strong><?php esc_html_e( 'امتیاز', 'revayatiran' ); ?></span><span><strong><?php echo esc_html( revayat_en2fa( $meta['person_votes'] ?? 0 ) ); ?></strong><?php esc_html_e( 'ارزیابی', 'revayatiran' ); ?></span></div></div></header>
 <div class="rv-person-profile__layout"><section class="rv-editorial-content"><h2><?php esc_html_e( 'درباره کارشناس', 'revayatiran' ); ?></h2><?php the_content(); ?></section><aside><h2><?php esc_html_e( 'حوزه‌های تخصصی', 'revayatiran' ); ?></h2><p><?php echo esc_html( $meta['expertise'] ?? '' ); ?></p></aside></div>
 <?php if ( ! empty( $related['items'] ) ) : ?><section class="rv-related"><h2><?php esc_html_e( 'یادداشت‌های این کارشناس', 'revayatiran' ); ?></h2><div class="an-archive-grid"><?php foreach ( $related['items'] as $item ) { get_template_part( 'template-parts/cards/card-analyst', null, array( 'item' => $item, 'variant' => 'grid' ) ); } ?></div></section><?php endif; ?>
</article></main>
<?php endwhile; get_footer();
