<?php
/** پروفایل عمومی تحلیلگر. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
while ( have_posts() ) : the_post();
	$person_id = get_the_ID();
	$data = function_exists( 'revayat_get_single_data' ) ? revayat_get_single_data( $person_id ) : array();
	$meta = $data['meta'] ?? array();
	$name = $data['title'] ?? get_the_title();
	$image = is_callable( array( 'Revayat_Data_Service', 'get_person_avatar_url' ) ) ? Revayat_Data_Service::get_person_avatar_url( $person_id ) : ( $data['media']['url'] ?? '' );
	$role = trim( (string) ( $meta['role_title'] ?? '' ) );
	$organization = trim( (string) ( $meta['organization'] ?? '' ) );
	$expertise = (string) ( $meta['expertise'] ?? '' );
	$score = (float) ( $meta['person_total_score'] ?? 0 );
	$votes = absint( $meta['person_votes'] ?? 0 );
	$page = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
	$related = is_callable( array( 'Revayat_Data_Service', 'get_person_posts' ) ) ? Revayat_Data_Service::get_person_posts( $person_id, $page ) : array();
	$analyses_count = (int) ( $related['total_posts'] ?? 0 );
	$posts = $related['items'] ?? array();
	$field_terms = taxonomy_exists( 'analyst_field' ) ? wp_get_object_terms( $person_id, 'analyst_field' ) : array();
	$field_terms = is_wp_error( $field_terms ) ? array() : $field_terms;
	$expertise_items = array_values( array_filter( array_map( 'trim', preg_split( '/[,،]/u', $expertise ) ?: array() ) ) );
	$bio_plain = trim( wp_strip_all_tags( get_the_content() ) );
	$topic_labels = array_values( array_unique( array_filter( array_merge( wp_list_pluck( $field_terms, 'name' ), $expertise_items ) ) ) );
	$topic_labels = array_values( array_filter( $topic_labels, static function ( $label ) use ( $role ) { return trim( $label ) !== trim( $role ); } ) );
	$people_url = get_post_type_archive_link( 'person' ) ?: home_url( '/persons/' );
	$related_people = array();
	if ( class_exists( 'Revayat_Data_Service' ) && method_exists( 'Revayat_Data_Service', 'get_analyst_leaderboard' ) ) {
		foreach ( Revayat_Data_Service::get_analyst_leaderboard( 6 ) as $index => $person ) {
			$related_people[] = function_exists( 'revayat_adapt_analyst_person' ) ? revayat_adapt_analyst_person( $person, $index + 1 ) : $person;
			if ( count( $related_people ) >= 3 ) { break; }
		}
	}
?>
<main id="primary" class="anx-page anx-person"><article <?php post_class( 'container' ); ?>>
	<nav class="anx-breadcrumb" aria-label="<?php esc_attr_e( 'مسیر راهنما', 'revayatiran' ); ?>"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'خانه', 'revayatiran' ); ?></a><i class="ph ph-caret-left"></i><a href="<?php echo esc_url( $people_url ); ?>"><?php esc_html_e( 'تحلیلگران', 'revayatiran' ); ?></a><i class="ph ph-caret-left"></i><span><?php echo esc_html( $name ); ?></span></nav>
	<header class="anx-person-hero">
		<div class="anx-person-hero__identity"><?php if ( $image ) : ?><img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $name ); ?>" width="180" height="180"><?php else : ?><span class="anx-person-hero__placeholder"><i class="ph ph-user"></i></span><?php endif; ?><div><span class="anx-eyebrow"><i class="ph ph-seal-check"></i><?php esc_html_e( 'پروفایل تحلیلگر', 'revayatiran' ); ?></span><h1><?php echo esc_html( $name ); ?></h1><?php if ( $role ) : ?><p class="anx-person-hero__role"><?php echo esc_html( $role ); ?></p><?php endif; ?><?php if ( $organization ) : ?><p class="anx-person-hero__org"><i class="ph ph-buildings"></i><?php echo esc_html( $organization ); ?></p><?php endif; ?><?php if ( $bio_plain ) : ?><p class="anx-person-hero__bio"><?php echo esc_html( wp_trim_words( $bio_plain, 32, '…' ) ); ?></p><?php endif; ?><?php if ( $topic_labels ) : ?><div class="anx-person-hero__topics"><?php foreach ( array_slice( $topic_labels, 0, 4 ) as $item ) : ?><span><?php echo esc_html( $item ); ?></span><?php endforeach; ?></div><?php endif; ?></div></div>
		<div class="anx-person-hero__summary"><dl><div><dt><?php esc_html_e( 'امتیاز تحلیل‌ها', 'revayatiran' ); ?></dt><dd><?php echo $score > 0 ? esc_html( revayat_en2fa( number_format( $score, 1 ) ) ) : '—'; ?></dd></div><div><dt><?php esc_html_e( 'یادداشت منتشرشده', 'revayatiran' ); ?></dt><dd><?php echo esc_html( revayat_en2fa( $analyses_count ) ); ?></dd></div><div><dt><?php esc_html_e( 'مجموع رأی‌ها', 'revayatiran' ); ?></dt><dd><?php echo esc_html( revayat_en2fa( $votes ) ); ?></dd></div></dl><a href="#anx-person-share"><i class="ph ph-share-network"></i><?php esc_html_e( 'اشتراک پروفایل', 'revayatiran' ); ?></a></div>
	</header>

	<div class="anx-layout anx-person-layout">
		<section class="anx-feed" id="notes" aria-label="<?php esc_attr_e( 'یادداشت‌ها', 'revayatiran' ); ?>"><?php if ( $posts ) : ?><div class="anx-feed-list"><?php foreach ( $posts as $item ) { get_template_part( 'template-parts/cards/card-analyst', null, array( 'item' => $item, 'variant' => 'grid' ) ); } ?></div><?php else : ?><div class="anx-empty"><i class="ph ph-note-blank"></i><h2><?php esc_html_e( 'هنوز یادداشتی منتشر نشده است', 'revayatiran' ); ?></h2><p><?php esc_html_e( 'یادداشت‌های منتشرشده این تحلیلگر در این بخش نمایش داده می‌شوند.', 'revayatiran' ); ?></p></div><?php endif; ?><?php if ( ( $related['max_pages'] ?? 0 ) > 1 ) : ?><nav class="anx-pagination" aria-label="<?php esc_attr_e( 'صفحه‌بندی یادداشت‌ها', 'revayatiran' ); ?>"><?php echo wp_kses_post( paginate_links( array( 'total' => $related['max_pages'], 'current' => $page ) ) ); ?></nav><?php endif; ?></section>

		<aside class="anx-sidebar" id="overview">
			<?php if ( $related_people ) : ?><section class="anx-side-card"><header><i class="ph ph-users"></i><h2><?php esc_html_e( 'برترین تحلیلگران', 'revayatiran' ); ?></h2></header><ul class="anx-related-people"><?php foreach ( $related_people as $person ) : ?><li><a href="<?php echo esc_url( $person['url'] ?? '' ); ?>"><?php if ( ! empty( $person['avatar_url'] ) ) : ?><img src="<?php echo esc_url( $person['avatar_url'] ); ?>" alt="" loading="lazy"><?php else : ?><span><i class="ph ph-user"></i></span><?php endif; ?><span><strong><?php echo esc_html( $person['name'] ?? '' ); ?></strong><small><?php echo esc_html( $person['specialty'] ?? '' ); ?></small><small><i class="ph ph-chart-bar"></i> <?php echo esc_html( $person['score'] ?? '—' ); ?></small></span></a></li><?php endforeach; ?></ul></section><?php endif; ?>
			<div id="anx-person-share"><?php get_template_part( 'template-parts/single/share' ); ?></div>
		</aside>
	</div>
</article></main>
<?php endwhile; get_footer();
