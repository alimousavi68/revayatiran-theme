<?php
/**
 * صفحه تکی یادداشت تحلیلی - شبکه تحلیلگران
 *
 * بازطراحی تحریریه‌ای فشرده (Compact Editorial Layout)، امتیازدهی ۱-کلیک،
 * تعاملات یکپارچه، تایپوگرافی بهینه و هماهنگ با دیزاین سیستم روایت ایران.
 *
 * @package RevayatIran
 * @version 2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	$post_id      = get_the_ID();
	$data         = function_exists( 'revayat_get_analyst_post_single_data' ) ? revayat_get_analyst_post_single_data( $post_id ) : array();
	$title        = $data['title'] ?? get_the_title();
	$excerpt      = $data['excerpt'] ?? get_the_excerpt();
	$date         = $data['date'] ?? get_the_date();
	$reading_time = $data['reading_time'] ?? ( function_exists( 'revayat_reading_time' ) ? revayat_reading_time( $post_id ) : '' );
	$field_name   = $data['field_name'] ?? '';
	$field_url    = $data['field_url'] ?? '';

	$vote_summary = class_exists( 'Revayat_Companion_User_Portal' )
		? Revayat_Companion_User_Portal::get_analyst_vote_summary( $post_id )
		: array( 'count' => 0, 'average' => 0.0 );
	$score        = $vote_summary['average'] > 0 ? number_format( (float) $vote_summary['average'], 1 ) : '—';

	$author        = $data['author'] ?? ( $data['taxonomies']['person_author'] ?? array() );
	$author_name   = $data['author_name'] ?? ( $author['name'] ?? get_the_author_meta( 'display_name', (int) get_post_field( 'post_author', $post_id ) ) );
	$author_role   = $data['author_role'] ?? ( $author['role'] ?? '' );
	$author_avatar = $data['author_avatar'] ?? ( $author['avatar'] ?? get_avatar_url( (int) get_post_field( 'post_author', $post_id ), array( 'size' => 160 ) ) );
	$author_url    = $data['author_url'] ?? ( $author['url'] ?? '' );
	$author_org    = $data['author_org'] ?? ( $author['organization'] ?? '' );
	$person_id     = absint( $author['person_id'] ?? 0 );
	$full_bio      = $person_id ? wp_strip_all_tags( (string) get_post_field( 'post_content', $person_id ) ) : '';
	$author_score  = (float) ( $author['person_score'] ?? 0 );
	$author_posts  = absint( $author['analyses_count'] ?? 0 );

	$thumbnail_url = ! empty( $data['media']['has_media'] ) ? ( $data['media']['url'] ?? '' ) : ( has_post_thumbnail( $post_id ) ? get_the_post_thumbnail_url( $post_id, 'large' ) : '' );
	$archive_url   = get_post_type_archive_link( 'analyst_post' ) ?: home_url( '/analysts/' );
	$field_terms   = taxonomy_exists( 'analyst_field' ) ? wp_get_object_terms( $post_id, 'analyst_field' ) : array();
	$field_terms   = is_wp_error( $field_terms ) ? array() : $field_terms;

	$can_rate  = class_exists( 'Revayat_Companion_Member_Policy' ) && Revayat_Companion_Member_Policy::can_rate( get_current_user_id(), $post_id );
	$my_rating = $can_rate ? (int) get_user_meta( get_current_user_id(), '_revayat_vote_analyst_' . $post_id, true ) : 0;
	$vote_nonce = wp_create_nonce( 'revayat_vote_analyst_' . $post_id );
?>

<main id="primary" class="anx-page anx-single">
	<article id="post-<?php echo esc_attr( $post_id ); ?>" <?php post_class( 'container anx-single__layout' ); ?>>
		<div class="anx-single__main">
			<!-- ناوبری مسیر راهنما (Breadcrumb) -->
			<nav class="anx-breadcrumb" aria-label="<?php esc_attr_e( 'مسیر راهنما', 'revayatiran' ); ?>">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'خانه', 'revayatiran' ); ?></a>
				<i class="ph ph-caret-left" aria-hidden="true"></i>
				<a href="<?php echo esc_url( $archive_url ); ?>"><?php esc_html_e( 'شبکه تحلیلگران', 'revayatiran' ); ?></a>
				<?php if ( $field_name ) : ?>
					<i class="ph ph-caret-left" aria-hidden="true"></i>
					<a href="<?php echo esc_url( $field_url ?: $archive_url ); ?>"><?php echo esc_html( $field_name ); ?></a>
				<?php endif; ?>
			</nav>

			<!-- هدر تحریریه‌ای فشرده (Compact Editorial Header - شماره ۳) -->
			<header class="anx-editorial-header">
				<div class="anx-editorial-header__body">
					<h1 class="anx-editorial-header__title"><?php echo wp_kses_post( $title ); ?></h1>
					<?php if ( $excerpt ) : ?>
						<p class="anx-editorial-header__lead"><?php echo esc_html( $excerpt ); ?></p>
					<?php endif; ?>

					<div class="anx-editorial-header__meta">
						<a href="<?php echo esc_url( $author_url ); ?>" class="anx-editorial-header__author" title="<?php echo esc_attr( sprintf( __( 'صفحه %s', 'revayatiran' ), $author_name ) ); ?>">
							<?php if ( $author_avatar ) : ?>
								<img src="<?php echo esc_url( $author_avatar ); ?>" alt="<?php echo esc_attr( $author_name ); ?>" width="44" height="44" class="anx-editorial-header__avatar">
							<?php else : ?>
								<span class="anx-editorial-header__avatar-fallback"><i class="ph ph-user"></i></span>
							<?php endif; ?>
							<span class="anx-editorial-header__author-info">
								<strong class="anx-editorial-header__author-name"><?php echo esc_html( $author_name ); ?></strong>
								<?php if ( $author_role ) : ?>
									<span class="anx-editorial-header__author-role"><?php echo esc_html( $author_role ); ?></span>
								<?php endif; ?>
							</span>
						</a>

						<div class="anx-editorial-header__facts">
							<span class="anx-editorial-header__fact"><i class="ph ph-calendar-blank"></i><time><?php echo esc_html( revayat_en2fa( $date ) ); ?></time></span>
							<?php if ( $reading_time ) : ?>
								<span class="anx-editorial-header__fact"><i class="ph ph-clock"></i><?php echo esc_html( revayat_en2fa( $reading_time ) ); ?></span>
							<?php endif; ?>
							<a href="#comments" class="anx-editorial-header__fact anx-editorial-header__comments-link">
								<i class="ph ph-chat-circle"></i>
								<span><?php echo esc_html( revayat_en2fa( get_comments_number() ) ); ?> دیدگاه</span>
							</a>
						</div>
					</div>
				</div>

				<?php if ( $thumbnail_url ) : ?>
					<figure class="anx-editorial-header__media">
						<img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( wp_strip_all_tags( $title ) ); ?>" width="520" height="340" fetchpriority="high">
					</figure>
				<?php else : ?>
					<figure class="anx-editorial-header__media anx-editorial-header__media--fallback">
						<div class="anx-editorial-header__fallback-box">
							<i class="ph ph-newspaper-clipping"></i>
							<span><?php echo esc_html( $field_name ?: __( 'یادداشت تحلیلی', 'revayatiran' ) ); ?></span>
						</div>
					</figure>
				<?php endif; ?>
			</header>

			<!-- محتوای اصلی یادداشت (شماره ۱۰.۱ و ۱۰.۲) -->
			<section class="anx-article-body rv-editorial-content obs-editorial-content">
				<?php the_content(); ?>
			</section>

			<!-- برچسب‌های موضوعی با فرمت هشتگ (شماره ۴) -->
			<?php if ( $field_terms ) : ?>
				<div class="anx-article-hashtags" aria-label="<?php esc_attr_e( 'موضوعات این یادداشت', 'revayatiran' ); ?>">
					<span class="anx-article-hashtags__label"><i class="ph ph-hash"></i><?php esc_html_e( 'موضوعات مرتبط:', 'revayatiran' ); ?></span>
					<div class="anx-article-hashtags__list">
						<?php foreach ( $field_terms as $term ) :
							$term_link = get_term_link( $term );
							if ( ! is_wp_error( $term_link ) ) : ?>
								<a href="<?php echo esc_url( $term_link ); ?>" class="anx-hashtag-chip">
									<span class="anx-hashtag-chip__hash">#</span><?php echo esc_html( $term->name ); ?>
								</a>
						<?php endif; endforeach; ?>
					</div>
				</div>
			<?php endif; ?>

			<!-- کامپوننت تعاملی یکپارچه: امتیازدهی ۱-کلیک و اشتراک‌گذاری (شماره ۶ و ۷) -->
			<section class="anx-unified-engagement" id="engagement-panel" aria-label="<?php esc_attr_e( 'ارزیابی و اشتراک‌گذاری یادداشت', 'revayatiran' ); ?>">
				<div class="anx-engagement-card">
					<!-- امتیازدهی تعاملی ۵ ستاره -->
					<div class="anx-engagement-rating" data-anx-rating-widget
					     data-post-id="<?php echo esc_attr( $post_id ); ?>"
					     data-nonce="<?php echo esc_attr( $vote_nonce ); ?>"
					     data-endpoint="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>"
					     data-can-rate="<?php echo $can_rate ? '1' : '0'; ?>"
					     data-user-rating="<?php echo esc_attr( $my_rating ); ?>"
					     data-is-logged-in="<?php echo is_user_logged_in() ? '1' : '0'; ?>">

						<div class="anx-engagement-rating__header">
							<span class="anx-engagement-rating__icon" aria-hidden="true"><i class="ph ph-chart-bar"></i></span>
							<div class="anx-engagement-rating__meta">
								<h3><?php esc_html_e( 'ارزیابی این تحلیل', 'revayatiran' ); ?></h3>
								<p class="anx-engagement-rating__stats" data-rating-stats>
									<?php if ( $vote_summary['count'] ) : ?>
										<?php printf( esc_html__( 'میانگین %1$s از ۵ (%2$s رأی)', 'revayatiran' ), '<strong data-score-val>' . esc_html( revayat_en2fa( $score ) ) . '</strong>', '<span data-count-val>' . esc_html( revayat_en2fa( $vote_summary['count'] ) ) . '</span>' ); ?>
									<?php elseif ( $vote_summary['average'] > 0 ) : ?>
										<?php printf( esc_html__( 'امتیاز اولیه %s از ۵', 'revayatiran' ), '<strong data-score-val>' . esc_html( revayat_en2fa( $score ) ) . '</strong>' ); ?>
									<?php else : ?>
										<span data-no-votes><?php esc_html_e( 'اولین ارزیابی را ثبت کنید', 'revayatiran' ); ?></span>
									<?php endif; ?>
								</p>
							</div>
						</div>

						<!-- ۵ ستاره تعاملی با کلیک مستقیم -->
						<div class="anx-stars-rating" role="radiogroup" aria-label="<?php esc_attr_e( 'امتیازدهی ۱ تا ۵ ستاره', 'revayatiran' ); ?>">
							<?php for ( $star = 1; $star <= 5; $star++ ) : ?>
								<button type="button" class="anx-star-btn <?php echo ( $my_rating >= $star ) ? 'is-active' : ''; ?>"
								        data-rating-value="<?php echo esc_attr( $star ); ?>"
								        role="radio"
								        aria-checked="<?php echo ( $my_rating === $star ) ? 'true' : 'false'; ?>"
								        aria-label="<?php echo esc_attr( sprintf( __( 'امتیاز %s از ۵', 'revayatiran' ), $star ) ); ?>"
								        title="<?php echo esc_attr( sprintf( __( 'امتیاز %s از ۵', 'revayatiran' ), $star ) ); ?>">
									<i class="ph <?php echo ( $my_rating >= $star ) ? 'ph-star-fill' : 'ph-star'; ?>"></i>
								</button>
							<?php endfor; ?>
						</div>

						<!-- وضعیت بازخورد کاربر -->
						<div class="anx-rating-feedback" role="status" aria-live="polite">
							<?php if ( ! is_user_logged_in() ) : ?>
								<a class="anx-rating-login-hint" href="<?php echo esc_url( add_query_arg( 'redirect_to', get_permalink( $post_id ) . '#engagement-panel', home_url( '/auth/' ) ) ); ?>">
									<i class="ph ph-sign-in"></i>
									<span><?php esc_html_e( 'برای ثبت امتیاز با حساب خود وارد شوید', 'revayatiran' ); ?></span>
								</a>
							<?php elseif ( ! $can_rate ) : ?>
								<span class="anx-rating-notice"><?php echo Revayat_Companion_Member_Policy::owns_note( get_current_user_id(), $post_id ) ? esc_html__( 'ارزیابی یادداشت خودتان مجاز نیست.', 'revayatiran' ) : esc_html__( 'حساب شما دسترسی ارزیابی این یادداشت را ندارد.', 'revayatiran' ); ?></span>
							<?php elseif ( $my_rating > 0 ) : ?>
								<span class="anx-rating-notice is-success"><?php printf( esc_html__( 'امتیاز شما: %s ستاره (برای تغییر دوباره کلیک کنید)', 'revayatiran' ), esc_html( revayat_en2fa( $my_rating ) ) ); ?></span>
							<?php else : ?>
								<span class="anx-rating-notice is-hint"><?php esc_html_e( 'با کلیک روی ستاره‌ها امتیاز خود را ثبت کنید', 'revayatiran' ); ?></span>
							<?php endif; ?>
						</div>

						<!-- فرم پشتیبان برای حالت بدون جاوااسکریپت -->
						<noscript>
							<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="anx-rating-noscript-form">
								<input type="hidden" name="action" value="revayat_vote_analyst_post">
								<input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ); ?>">
								<?php wp_nonce_field( 'revayat_vote_analyst_' . $post_id, 'revayat_vote_nonce' ); ?>
								<select name="rating" aria-label="<?php esc_attr_e( 'انتخاب امتیاز', 'revayatiran' ); ?>">
									<option value="5" <?php selected( $my_rating, 5 ); ?>>۵ ستاره</option>
									<option value="4" <?php selected( $my_rating, 4 ); ?>>۴ ستاره</option>
									<option value="3" <?php selected( $my_rating, 3 ); ?>>۳ ستاره</option>
									<option value="2" <?php selected( $my_rating, 2 ); ?>>۲ ستاره</option>
									<option value="1" <?php selected( $my_rating, 1 ); ?>>۱ ستاره</option>
								</select>
								<button type="submit"><?php esc_html_e( 'ثبت', 'revayatiran' ); ?></button>
							</form>
						</noscript>
					</div>

					<!-- نوار ابزارهای اشتراک‌گذاری -->
					<div class="anx-engagement-tools">
						<div class="anx-engagement-tools__title">
							<i class="ph ph-share-network"></i>
							<span><?php esc_html_e( 'اشتراک‌گذاری:', 'revayatiran' ); ?></span>
						</div>
						<div class="anx-engagement-tools__actions">
							<button type="button" class="anx-tool-btn anx-tool-btn--copy" data-anx-copy-btn data-url="<?php echo esc_url( get_permalink( $post_id ) ); ?>" title="<?php esc_attr_e( 'کپی پیوند یادداشت', 'revayatiran' ); ?>">
								<i class="ph ph-copy" aria-hidden="true"></i>
								<span><?php esc_html_e( 'کپی لینک', 'revayatiran' ); ?></span>
							</button>

							<button type="button" class="anx-tool-btn anx-tool-btn--share" data-anx-native-share data-url="<?php echo esc_url( get_permalink( $post_id ) ); ?>" data-title="<?php echo esc_attr( wp_strip_all_tags( $title ) ); ?>" title="<?php esc_attr_e( 'اشتراک‌گذاری سیستمی', 'revayatiran' ); ?>">
								<i class="ph ph-share" aria-hidden="true"></i>
								<span><?php esc_html_e( 'اشتراک', 'revayatiran' ); ?></span>
							</button>

							<a href="https://t.me/share/url?url=<?php echo rawurlencode( get_permalink( $post_id ) ); ?>&text=<?php echo rawurlencode( $title ); ?>" target="_blank" rel="noopener noreferrer" class="anx-tool-btn anx-tool-btn--telegram" title="<?php esc_attr_e( 'اشتراک در تلگرام', 'revayatiran' ); ?>">
								<i class="ph ph-telegram-logo" aria-hidden="true"></i>
								<span class="screen-reader-text"><?php esc_html_e( 'تلگرام', 'revayatiran' ); ?></span>
							</a>

							<a href="https://twitter.com/intent/tweet?url=<?php echo rawurlencode( get_permalink( $post_id ) ); ?>&text=<?php echo rawurlencode( $title ); ?>" target="_blank" rel="noopener noreferrer" class="anx-tool-btn anx-tool-btn--x" title="<?php esc_attr_e( 'اشتراک در ایکس', 'revayatiran' ); ?>">
								<i class="ph ph-x-logo" aria-hidden="true"></i>
								<span class="screen-reader-text"><?php esc_html_e( 'ایکس', 'revayatiran' ); ?></span>
							</a>

							<a href="#comments" class="anx-tool-btn anx-tool-btn--comments" title="<?php esc_attr_e( 'دیدگاه‌های کاربران', 'revayatiran' ); ?>">
								<i class="ph ph-chat-circle" aria-hidden="true"></i>
								<span><?php echo esc_html( revayat_en2fa( get_comments_number() ) ); ?></span>
							</a>
						</div>
					</div>
				</div>
			</section>

			<!-- مطالب مرتبط و دیدگاه‌ها -->
			<?php get_template_part( 'template-parts/single/engagement' ); ?>
		</div>

		<!-- ستون کناری (Sidebar) -->
		<aside class="anx-single__sidebar" aria-label="<?php esc_attr_e( 'اطلاعات نویسنده و یادداشت', 'revayatiran' ); ?>">
			<!-- کارت نویسنده (شماره ۲) -->
			<section class="anx-author-card">
				<div class="anx-author-card__header">
					<?php if ( $author_avatar ) : ?>
						<img src="<?php echo esc_url( $author_avatar ); ?>" alt="<?php echo esc_attr( $author_name ); ?>" width="84" height="84" class="anx-author-card__avatar">
					<?php else : ?>
						<span class="anx-author-card__placeholder"><i class="ph ph-user"></i></span>
					<?php endif; ?>
					<div class="anx-author-card__identity">
						<h2><?php echo esc_html( $author_name ); ?></h2>
						<?php if ( $author_role ) : ?><p class="anx-author-card__role"><?php echo esc_html( $author_role ); ?></p><?php endif; ?>
						<?php if ( $author_org ) : ?><p class="anx-author-card__org"><?php echo esc_html( $author_org ); ?></p><?php endif; ?>
					</div>
				</div>

				<?php if ( $full_bio ) : ?>
					<div class="anx-author-card__bio-wrap" data-anx-bio-expandable>
						<p class="anx-author-card__bio"><?php echo esc_html( $full_bio ); ?></p>
						<button type="button" class="anx-author-card__bio-toggle" aria-expanded="false" hidden>
							<span><?php esc_html_e( 'مشاهده بیشتر', 'revayatiran' ); ?></span>
							<i class="ph ph-caret-down" aria-hidden="true"></i>
						</button>
					</div>
				<?php endif; ?>

				<div class="anx-author-card__stats-row">
					<div class="anx-author-card__stat-item">
						<span class="anx-author-card__stat-label"><?php esc_html_e( 'میانگین امتیاز', 'revayatiran' ); ?></span>
						<strong class="anx-author-card__stat-val">
							<i class="ph ph-star-fill"></i>
							<?php echo $author_score > 0 ? esc_html( revayat_en2fa( number_format( $author_score, 1 ) ) ) : '—'; ?>
						</strong>
					</div>
					<div class="anx-author-card__stat-item">
						<span class="anx-author-card__stat-label"><?php esc_html_e( 'تعداد یادداشت‌ها', 'revayatiran' ); ?></span>
						<strong class="anx-author-card__stat-val">
							<i class="ph ph-article"></i>
							<?php echo esc_html( revayat_en2fa( $author_posts ) ); ?>
						</strong>
					</div>
				</div>

				<?php if ( $author_url ) : ?>
					<a href="<?php echo esc_url( $author_url ); ?>" class="anx-author-card__profile-link">
						<span><?php esc_html_e( 'مشاهده پروفایل تحلیلگر', 'revayatiran' ); ?></span>
						<i class="ph ph-arrow-left" aria-hidden="true"></i>
					</a>
				<?php endif; ?>
			</section>

			<!-- مشخصات شناسنامه‌ای یادداشت -->
			<section class="anx-side-card">
				<header>
					<i class="ph ph-file-text"></i>
					<h2><?php esc_html_e( 'درباره این یادداشت', 'revayatiran' ); ?></h2>
				</header>
				<dl class="anx-note-facts">
					<div>
						<dt><?php esc_html_e( 'حوزه تخصصی', 'revayatiran' ); ?></dt>
						<dd><?php echo $field_name ? esc_html( $field_name ) : '—'; ?></dd>
					</div>
					<div>
						<dt><?php esc_html_e( 'زمان مطالعه تقریبی', 'revayatiran' ); ?></dt>
						<dd><?php echo $reading_time ? esc_html( revayat_en2fa( $reading_time ) ) : '—'; ?></dd>
					</div>
					<div>
						<dt><?php esc_html_e( 'تاریخ انتشار', 'revayatiran' ); ?></dt>
						<dd><?php echo esc_html( revayat_en2fa( $date ) ); ?></dd>
					</div>
				</dl>
			</section>

			<!-- فهرست مطالب درون‌متنی خودکار -->
			<section class="anx-side-card anx-article-toc" data-anx-toc hidden>
				<header>
					<i class="ph ph-list-numbers"></i>
					<h2><?php esc_html_e( 'در این یادداشت', 'revayatiran' ); ?></h2>
				</header>
				<ol></ol>
			</section>
		</aside>
	</article>
</main>

<?php
endwhile;
get_footer();
