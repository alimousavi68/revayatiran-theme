<?php
/** صفحه تکی یادداشت شبکه تحلیلگران. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
while ( have_posts() ) : the_post();
	$post_id = get_the_ID();
	$data = function_exists( 'revayat_get_analyst_post_single_data' ) ? revayat_get_analyst_post_single_data( $post_id ) : array();
	$title = $data['title'] ?? get_the_title();
	$excerpt = $data['excerpt'] ?? get_the_excerpt();
	$date = $data['date'] ?? get_the_date();
	$reading_time = $data['reading_time'] ?? ( function_exists( 'revayat_reading_time' ) ? revayat_reading_time( $post_id ) : '' );
	$field_name = $data['field_name'] ?? '';
	$field_url = $data['field_url'] ?? '';
	$analysis_type = $data['analysis_type'] ?? __( 'یادداشت تحلیلی', 'revayatiran' );
	$featured_quote = $data['featured_quote'] ?? '';
	$vote_summary = class_exists( 'Revayat_Companion_User_Portal' ) ? Revayat_Companion_User_Portal::get_analyst_vote_summary( $post_id ) : array( 'count' => 0, 'average' => 0.0 );
	$score = $vote_summary['count'] ? number_format( (float) $vote_summary['average'], 1 ) : '—';
	$author = $data['author'] ?? ( $data['taxonomies']['person_author'] ?? array() );
	$author_name = $data['author_name'] ?? ( $author['name'] ?? get_the_author_meta( 'display_name', (int) get_post_field( 'post_author', $post_id ) ) );
	$author_role = $data['author_role'] ?? ( $author['role'] ?? '' );
	$author_avatar = $data['author_avatar'] ?? ( $author['avatar'] ?? get_avatar_url( (int) get_post_field( 'post_author', $post_id ), array( 'size' => 160 ) ) );
	$author_url = $data['author_url'] ?? ( $author['url'] ?? '' );
	$author_org = $data['author_org'] ?? ( $author['organization'] ?? '' );
	$person_id = absint( $author['person_id'] ?? 0 );
	$author_bio = $person_id ? wp_trim_words( wp_strip_all_tags( (string) get_post_field( 'post_content', $person_id ) ), 40, '…' ) : '';
	$author_score = (float) ( $author['person_score'] ?? 0 );
	$author_votes = absint( $author['person_votes'] ?? 0 );
	$author_posts = absint( $author['analyses_count'] ?? 0 );
	$thumbnail_url = ! empty( $data['media']['has_media'] ) ? ( $data['media']['url'] ?? '' ) : '';
	$archive_url = get_post_type_archive_link( 'analyst_post' ) ?: home_url( '/analysts/' );
	$field_terms = taxonomy_exists( 'analyst_field' ) ? wp_get_object_terms( $post_id, 'analyst_field' ) : array();
	$field_terms = is_wp_error( $field_terms ) ? array() : $field_terms;
	$can_rate = current_user_can( 'revayat_rate_analyst_post' ) && (int) get_post_field( 'post_author', $post_id ) !== get_current_user_id();
	$my_rating = $can_rate ? (int) get_user_meta( get_current_user_id(), '_revayat_vote_analyst_' . $post_id, true ) : 0;
?>
<main id="primary" class="anx-page anx-single"><article id="post-<?php echo esc_attr( $post_id ); ?>" <?php post_class( 'container anx-single__layout' ); ?>>
	<div class="anx-single__main">
		<nav class="anx-breadcrumb" aria-label="<?php esc_attr_e( 'مسیر راهنما', 'revayatiran' ); ?>"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'خانه', 'revayatiran' ); ?></a><i class="ph ph-caret-left"></i><a href="<?php echo esc_url( $archive_url ); ?>"><?php esc_html_e( 'شبکه تحلیلگران', 'revayatiran' ); ?></a><?php if ( $field_name ) : ?><i class="ph ph-caret-left"></i><a href="<?php echo esc_url( $field_url ?: $archive_url ); ?>"><?php echo esc_html( $field_name ); ?></a><?php endif; ?></nav>
		<header class="anx-article-head"><span class="anx-content-badge"><?php echo esc_html( $analysis_type ); ?></span><h1><?php echo wp_kses_post( $title ); ?></h1><?php if ( $excerpt ) : ?><p class="anx-article-lead"><?php echo esc_html( $excerpt ); ?></p><?php endif; ?>
			<div class="anx-article-author"><a href="<?php echo esc_url( $author_url ); ?>" class="anx-article-author__identity"><?php if ( $author_avatar ) : ?><img src="<?php echo esc_url( $author_avatar ); ?>" alt="" width="72" height="72"><?php else : ?><span><i class="ph ph-user"></i></span><?php endif; ?><span><strong><?php echo esc_html( $author_name ); ?></strong><?php if ( $author_role ) : ?><small><?php echo esc_html( $author_role ); ?></small><?php endif; ?><small><time><?php echo esc_html( revayat_en2fa( $date ) ); ?></time><?php if ( $reading_time ) : ?> · <?php echo esc_html( revayat_en2fa( $reading_time ) ); ?><?php endif; ?></small></span></a><div class="anx-article-author__actions"><a href="#anx-share"><i class="ph ph-share-network"></i><?php esc_html_e( 'اشتراک‌گذاری', 'revayatiran' ); ?></a><a href="#comments"><i class="ph ph-chat-circle"></i><?php echo esc_html( revayat_en2fa( get_comments_number() ) ); ?> <?php esc_html_e( 'دیدگاه', 'revayatiran' ); ?></a></div></div>
		</header>
		<?php if ( $thumbnail_url ) : ?><figure class="anx-article-cover"><img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( wp_strip_all_tags( $title ) ); ?>" width="1200" height="675"></figure><?php endif; ?>
		<?php if ( $featured_quote && $featured_quote !== $excerpt ) : ?><blockquote class="anx-thesis"><i class="ph ph-quotes"></i><p><?php echo esc_html( $featured_quote ); ?></p></blockquote><?php endif; ?>
		<section class="anx-article-body rv-editorial-content obs-editorial-content"><?php the_content(); ?></section>
		<?php if ( $field_terms ) : ?><footer class="anx-article-topics"><strong><?php esc_html_e( 'موضوعات:', 'revayatiran' ); ?></strong><?php foreach ( $field_terms as $term ) : $term_link = get_term_link( $term ); if ( ! is_wp_error( $term_link ) ) : ?><a href="<?php echo esc_url( $term_link ); ?>"><?php echo esc_html( $term->name ); ?></a><?php endif; endforeach; ?></footer><?php endif; ?>

		<section class="anx-rating" id="analyst-vote"><div class="anx-rating__summary"><span><i class="ph ph-chart-bar"></i></span><div><h2><?php esc_html_e( 'به این تحلیل امتیاز دهید', 'revayatiran' ); ?></h2><?php if ( $vote_summary['count'] ) : ?><p><?php printf( esc_html__( 'میانگین %1$s از ۵، بر پایه %2$s رأی معتبر', 'revayatiran' ), esc_html( revayat_en2fa( $score ) ), esc_html( revayat_en2fa( $vote_summary['count'] ) ) ); ?></p><?php else : ?><p><?php esc_html_e( 'هنوز رأیی ثبت نشده است.', 'revayatiran' ); ?></p><?php endif; ?></div></div>
			<?php if ( $can_rate ) : ?><form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="revayat_vote_analyst_post"><input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ); ?>"><?php wp_nonce_field( 'revayat_vote_analyst_' . $post_id, 'revayat_vote_nonce' ); ?><fieldset><legend class="screen-reader-text"><?php esc_html_e( 'انتخاب امتیاز از یک تا پنج', 'revayatiran' ); ?></legend><?php for ( $rating = 5; $rating >= 1; --$rating ) : ?><label><input type="radio" name="rating" value="<?php echo esc_attr( $rating ); ?>" <?php checked( $my_rating, $rating ); ?> required><span><?php echo esc_html( revayat_en2fa( $rating ) ); ?><i class="ph ph-star"></i></span></label><?php endfor; ?></fieldset><button type="submit"><?php echo esc_html( $my_rating ? __( 'تغییر امتیاز', 'revayatiran' ) : __( 'ثبت امتیاز', 'revayatiran' ) ); ?></button></form><?php elseif ( ! is_user_logged_in() ) : ?><a class="anx-rating__login" href="<?php echo esc_url( home_url( '/auth/?tab=login' ) ); ?>"><?php esc_html_e( 'برای ارزیابی وارد شوید', 'revayatiran' ); ?></a><?php else : ?><p class="anx-rating__unavailable"><?php echo (int) get_post_field( 'post_author', $post_id ) === get_current_user_id() ? esc_html__( 'امتیازدهی به یادداشت خودتان امکان‌پذیر نیست.', 'revayatiran' ) : esc_html__( 'حساب شما دسترسی ارزیابی این یادداشت را ندارد.', 'revayatiran' ); ?></p><?php endif; ?>
			<?php if ( isset( $_GET['vote'] ) && 'saved' === sanitize_key( wp_unslash( $_GET['vote'] ) ) ) : ?><p class="anx-inline-notice" role="status"><?php esc_html_e( 'امتیاز شما ثبت شد.', 'revayatiran' ); ?></p><?php endif; ?>
		</section>
		<div id="anx-share"><?php get_template_part( 'template-parts/dossier/parents' ); get_template_part( 'template-parts/single/engagement' ); ?></div>
	</div>

	<aside class="anx-single__sidebar" aria-label="<?php esc_attr_e( 'اطلاعات نویسنده و یادداشت', 'revayatiran' ); ?>">
		<section class="anx-author-card"><?php if ( $author_avatar ) : ?><img src="<?php echo esc_url( $author_avatar ); ?>" alt="<?php echo esc_attr( $author_name ); ?>" width="160" height="160"><?php else : ?><span class="anx-author-card__placeholder"><i class="ph ph-user"></i></span><?php endif; ?><h2><?php echo esc_html( $author_name ); ?></h2><?php if ( $author_role ) : ?><p class="anx-author-card__role"><?php echo esc_html( $author_role ); ?></p><?php endif; ?><?php if ( $author_org ) : ?><p class="anx-author-card__org"><?php echo esc_html( $author_org ); ?></p><?php endif; ?><?php if ( $author_bio ) : ?><p class="anx-author-card__bio"><?php echo esc_html( $author_bio ); ?></p><?php endif; ?><dl><div><dt><?php esc_html_e( 'امتیاز', 'revayatiran' ); ?></dt><dd><?php echo $author_votes ? esc_html( revayat_en2fa( number_format( $author_score, 1 ) ) ) : '—'; ?></dd></div><div><dt><?php esc_html_e( 'یادداشت', 'revayatiran' ); ?></dt><dd><?php echo esc_html( revayat_en2fa( $author_posts ) ); ?></dd></div></dl><?php if ( $author_url ) : ?><a href="<?php echo esc_url( $author_url ); ?>"><?php esc_html_e( 'مشاهده پروفایل', 'revayatiran' ); ?><i class="ph ph-arrow-left"></i></a><?php endif; ?></section>
		<section class="anx-side-card"><header><i class="ph ph-file-text"></i><h2><?php esc_html_e( 'درباره این یادداشت', 'revayatiran' ); ?></h2></header><dl class="anx-note-facts"><div><dt><?php esc_html_e( 'حوزه', 'revayatiran' ); ?></dt><dd><?php echo $field_name ? esc_html( $field_name ) : '—'; ?></dd></div><div><dt><?php esc_html_e( 'زمان مطالعه', 'revayatiran' ); ?></dt><dd><?php echo $reading_time ? esc_html( revayat_en2fa( $reading_time ) ) : '—'; ?></dd></div><div><dt><?php esc_html_e( 'تاریخ انتشار', 'revayatiran' ); ?></dt><dd><?php echo esc_html( revayat_en2fa( $date ) ); ?></dd></div></dl></section>
		<section class="anx-side-card anx-article-toc" data-anx-toc hidden><header><i class="ph ph-list-numbers"></i><h2><?php esc_html_e( 'در این یادداشت', 'revayatiran' ); ?></h2></header><ol></ol></section>
		<?php if ( $field_terms ) : ?><section class="anx-side-card"><header><i class="ph ph-tag"></i><h2><?php esc_html_e( 'موضوعات یادداشت', 'revayatiran' ); ?></h2></header><div class="anx-person-expertise"><?php foreach ( $field_terms as $term ) : $term_link = get_term_link( $term ); if ( ! is_wp_error( $term_link ) ) : ?><a href="<?php echo esc_url( $term_link ); ?>"><?php echo esc_html( $term->name ); ?></a><?php endif; endforeach; ?></div></section><?php endif; ?>
	</aside>
</article></main>
<?php endwhile; get_footer();
