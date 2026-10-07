<?php
/** پروفایل عمومی تحلیلگر. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
while ( have_posts() ) : the_post();
	$person_id = get_the_ID();
	$data = function_exists( 'revayat_get_single_data' ) ? revayat_get_single_data( $person_id ) : array();
	$meta = $data['meta'] ?? array();
	$name = $data['title'] ?? get_the_title();
	$image = ! empty( $data['media']['has_media'] ) ? ( $data['media']['url'] ?? '' ) : '';
	$role = (string) ( $meta['role_title'] ?? '' );
	$organization = (string) ( $meta['organization'] ?? '' );
	$expertise = (string) ( $meta['expertise'] ?? '' );
	$score = (float) ( $meta['person_total_score'] ?? 0 );
	$votes = absint( $meta['person_votes'] ?? 0 );
	$analyses_count = absint( $meta['analyses_count'] ?? 0 );
	$contributor_slug = class_exists( 'Revayat_Data_Service' ) && method_exists( 'Revayat_Data_Service', 'get_person_contributor_slug' ) ? Revayat_Data_Service::get_person_contributor_slug( $person_id ) : get_post_field( 'post_name', $person_id );
	$related = function_exists( 'revayat_get_archive_data' ) ? revayat_get_archive_data( 'analyst_post', array( 'contributor' => $contributor_slug, 'posts_per_page' => 10, 'paged' => 1 ) ) : array();
	$posts = $related['items'] ?? array();
	$field_terms = taxonomy_exists( 'analyst_field' ) ? wp_get_object_terms( $person_id, 'analyst_field' ) : array();
	$field_terms = is_wp_error( $field_terms ) ? array() : $field_terms;
	$expertise_items = array_values( array_filter( array_map( 'trim', preg_split( '/[,،]/u', $expertise ) ?: array() ) ) );
	$bio_plain = trim( wp_strip_all_tags( get_the_content() ) );
	$topic_labels = array_values( array_unique( array_filter( array_merge( wp_list_pluck( $field_terms, 'name' ), $expertise_items ) ) ) );
	$people_url = get_post_type_archive_link( 'person' ) ?: home_url( '/persons/' );
	$related_people = array();
	if ( class_exists( 'Revayat_Data_Service' ) && method_exists( 'Revayat_Data_Service', 'get_analyst_leaderboard' ) ) {
		foreach ( Revayat_Data_Service::get_analyst_leaderboard( 6 ) as $index => $person ) {
			if ( absint( $person['id'] ?? 0 ) === $person_id ) { continue; }
			$related_people[] = function_exists( 'revayat_adapt_analyst_person' ) ? revayat_adapt_analyst_person( $person, $index + 1 ) : $person;
			if ( count( $related_people ) >= 3 ) { break; }
		}
	}
?>
<main id="primary" class="anx-page anx-person"><article <?php post_class( 'container' ); ?>>
	<nav class="anx-breadcrumb" aria-label="<?php esc_attr_e( 'مسیر راهنما', 'revayatiran' ); ?>"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'خانه', 'revayatiran' ); ?></a><i class="ph ph-caret-left"></i><a href="<?php echo esc_url( $people_url ); ?>"><?php esc_html_e( 'تحلیلگران', 'revayatiran' ); ?></a><i class="ph ph-caret-left"></i><span><?php echo esc_html( $name ); ?></span></nav>
	<header class="anx-person-hero">
		<div class="anx-person-hero__identity"><?php if ( $image ) : ?><img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $name ); ?>" width="180" height="180"><?php else : ?><span class="anx-person-hero__placeholder"><i class="ph ph-user"></i></span><?php endif; ?><div><span class="anx-eyebrow"><i class="ph ph-seal-check"></i><?php esc_html_e( 'پروفایل تحلیلگر', 'revayatiran' ); ?></span><h1><?php echo esc_html( $name ); ?></h1><?php if ( $role ) : ?><p class="anx-person-hero__role"><?php echo esc_html( $role ); ?></p><?php endif; ?><?php if ( $organization ) : ?><p class="anx-person-hero__org"><i class="ph ph-buildings"></i><?php echo esc_html( $organization ); ?></p><?php endif; ?><?php if ( $bio_plain ) : ?><p class="anx-person-hero__bio"><?php echo esc_html( wp_trim_words( $bio_plain, 32, '…' ) ); ?></p><?php endif; ?><div class="anx-person-hero__topics"><?php foreach ( array_slice( $topic_labels, 0, 4 ) as $item ) : ?><span><?php echo esc_html( $item ); ?></span><?php endforeach; ?></div></div></div>
		<div class="anx-person-hero__summary"><dl><div><dt><?php esc_html_e( 'امتیاز تحلیل‌ها', 'revayatiran' ); ?></dt><dd><?php echo $votes ? esc_html( revayat_en2fa( number_format( $score, 1 ) ) ) : '—'; ?></dd></div><div><dt><?php esc_html_e( 'یادداشت منتشرشده', 'revayatiran' ); ?></dt><dd><?php echo esc_html( revayat_en2fa( $analyses_count ) ); ?></dd></div><div><dt><?php esc_html_e( 'مجموع رأی‌ها', 'revayatiran' ); ?></dt><dd><?php echo esc_html( revayat_en2fa( $votes ) ); ?></dd></div></dl><a href="#anx-person-share"><i class="ph ph-share-network"></i><?php esc_html_e( 'اشتراک پروفایل', 'revayatiran' ); ?></a></div>
	</header>
	<nav class="anx-person-tabs" aria-label="<?php esc_attr_e( 'بخش‌های پروفایل', 'revayatiran' ); ?>"><a href="#overview"><?php esc_html_e( 'نمای کلی', 'revayatiran' ); ?></a><a class="is-active" aria-current="page" href="#notes"><?php esc_html_e( 'یادداشت‌ها', 'revayatiran' ); ?></a><a href="#about"><?php esc_html_e( 'درباره و سوابق', 'revayatiran' ); ?></a></nav>

	<div class="anx-layout anx-person-layout">
		<section class="anx-feed" id="notes" aria-labelledby="anx-person-notes-title"><header class="anx-section-heading"><div><span><?php esc_html_e( 'آثار منتشرشده', 'revayatiran' ); ?></span><h2 id="anx-person-notes-title"><?php printf( esc_html__( 'یادداشت‌های %s', 'revayatiran' ), esc_html( $name ) ); ?></h2></div><p><?php printf( esc_html__( '%s یادداشت', 'revayatiran' ), esc_html( revayat_en2fa( $analyses_count ) ) ); ?></p></header><?php if ( $posts ) : ?><div class="anx-feed-list"><?php foreach ( $posts as $item ) { get_template_part( 'template-parts/cards/card-analyst', null, array( 'item' => $item, 'variant' => 'grid' ) ); } ?></div><?php else : ?><div class="anx-empty"><i class="ph ph-note-blank"></i><h2><?php esc_html_e( 'هنوز یادداشتی منتشر نشده است', 'revayatiran' ); ?></h2><p><?php esc_html_e( 'یادداشت‌های منتشرشده این تحلیلگر در این بخش نمایش داده می‌شوند.', 'revayatiran' ); ?></p></div><?php endif; ?></section>

		<aside class="anx-sidebar" id="overview">
			<section class="anx-side-card" id="about"><header><i class="ph ph-user-circle"></i><h2><?php esc_html_e( 'درباره تحلیلگر', 'revayatiran' ); ?></h2></header><div class="anx-person-bio"><?php if ( $bio_plain ) : the_content(); else : ?><p class="anx-side-empty"><?php esc_html_e( 'زندگی‌نامه حرفه‌ای هنوز تکمیل نشده است.', 'revayatiran' ); ?></p><?php endif; ?></div><?php if ( $organization || $role ) : ?><dl class="anx-person-facts"><?php if ( $organization ) : ?><div><dt><i class="ph ph-buildings"></i><?php esc_html_e( 'سازمان', 'revayatiran' ); ?></dt><dd><?php echo esc_html( $organization ); ?></dd></div><?php endif; ?><?php if ( $role ) : ?><div><dt><i class="ph ph-briefcase"></i><?php esc_html_e( 'عنوان حرفه‌ای', 'revayatiran' ); ?></dt><dd><?php echo esc_html( $role ); ?></dd></div><?php endif; ?></dl><?php endif; ?></section>
			<section class="anx-side-card"><header><i class="ph ph-stack"></i><h2><?php esc_html_e( 'حوزه‌های تخصصی', 'revayatiran' ); ?></h2></header><div class="anx-person-expertise"><?php if ( $topic_labels ) : foreach ( $topic_labels as $item ) : ?><span><?php echo esc_html( $item ); ?></span><?php endforeach; else : ?><p class="anx-side-empty"><?php esc_html_e( 'حوزه تخصصی ثبت نشده است.', 'revayatiran' ); ?></p><?php endif; ?></div></section>
			<?php if ( $related_people ) : ?><section class="anx-side-card"><header><i class="ph ph-users"></i><h2><?php esc_html_e( 'تحلیلگران مرتبط', 'revayatiran' ); ?></h2></header><ul class="anx-related-people"><?php foreach ( $related_people as $person ) : ?><li><a href="<?php echo esc_url( $person['url'] ?? '' ); ?>"><?php if ( ! empty( $person['avatar_url'] ) ) : ?><img src="<?php echo esc_url( $person['avatar_url'] ); ?>" alt="" loading="lazy"><?php else : ?><span><i class="ph ph-user"></i></span><?php endif; ?><span><strong><?php echo esc_html( $person['name'] ?? '' ); ?></strong><small><?php echo esc_html( $person['specialty'] ?? '' ); ?></small></span></a></li><?php endforeach; ?></ul></section><?php endif; ?>
			<div id="anx-person-share"><?php get_template_part( 'template-parts/single/share' ); ?></div>
		</aside>
	</div>
</article></main>
<?php endwhile; get_footer();
