<?php
/**
 * آرشیو اختصاصی اتاق وضعیت.
 *
 * @package RevayatIran
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$paged            = max( 1, (int) get_query_var( 'paged', 1 ) );
$current_security = sanitize_key( wp_unslash( $_GET['security_level'] ?? 'all' ) );
$current_type     = sanitize_text_field( wp_unslash( $_GET['bulletin_type'] ?? 'all' ) );
$current_urgency  = sanitize_text_field( wp_unslash( $_GET['urgency_level'] ?? 'all' ) );
$active_search    = sanitize_text_field( wp_unslash( $_GET['q'] ?? '' ) );

$archive_data = function_exists( 'revayat_get_archive_data' ) ? revayat_get_archive_data(
	'situation_room',
	array(
		'paged'          => $paged,
		'posts_per_page' => 16,
		'security_level' => $current_security,
		'bulletin_type'  => $current_type,
		'urgency_level'  => $current_urgency,
		'search'         => $active_search,
	)
) : array();

$bulletins       = $archive_data['items'] ?? array();
$total_posts     = (int) ( $archive_data['total_posts'] ?? count( $bulletins ) );
$max_pages       = max( 1, (int) ( $archive_data['max_pages'] ?? 1 ) );
$active_search   = $archive_data['active_search'] ?? $active_search;
$user_clearance  = $archive_data['user_clearance'] ?? 'guest';
$security_levels = $archive_data['filter_options']['security_levels'] ?? array(
	array( 'slug' => 'all', 'label' => __( 'همه سطوح', 'revayatiran' ) ),
	array( 'slug' => 'public', 'label' => __( 'علنی ـ سطح ۳', 'revayatiran' ) ),
	array( 'slug' => 'restricted', 'label' => __( 'محدود ـ سطح ۲', 'revayatiran' ) ),
	array( 'slug' => 'classified', 'label' => __( 'طبقه‌بندی‌شده ـ سطح ۱', 'revayatiran' ) ),
);

$bulletin_types = array(
	'all'            => __( 'همه گونه‌ها', 'revayatiran' ),
	'بولتن آماری'    => __( 'بولتن آماری', 'revayatiran' ),
	'مستند تصویری'   => __( 'مستند تصویری', 'revayatiran' ),
	'برآورد راهبردی' => __( 'برآورد راهبردی', 'revayatiran' ),
	'سناریوی تقابل'  => __( 'سناریوی تقابل', 'revayatiran' ),
);
$urgency_levels = array(
	'all'    => __( 'همه فوریت‌ها', 'revayatiran' ),
	'عادی'   => __( 'عادی', 'revayatiran' ),
	'فوری'   => __( 'فوری', 'revayatiran' ),
	'بحرانی' => __( 'بحرانی', 'revayatiran' ),
);

$archive_url = get_post_type_archive_link( 'situation_room' );
$filter_url  = static function ( $key, $value ) use ( $archive_url, $current_security, $current_type, $current_urgency, $active_search ) {
	$args = array_filter(
		array(
			'security_level' => 'all' !== $current_security ? $current_security : '',
			'bulletin_type'  => 'all' !== $current_type ? $current_type : '',
			'urgency_level'  => 'all' !== $current_urgency ? $current_urgency : '',
			'q'              => $active_search,
		)
	);
	if ( 'all' === $value ) {
		unset( $args[ $key ] );
	} else {
		$args[ $key ] = $value;
	}
	return empty( $args ) ? $archive_url : add_query_arg( $args, $archive_url );
};

$hero_bulletins  = array_slice( $bulletins, 0, 4 );
$watch_bulletins = array_slice( $bulletins, 0, 5 );
$active_filter_count = ( 'all' !== $current_security ? 1 : 0 ) + ( 'all' !== $current_type ? 1 : 0 ) + ( 'all' !== $current_urgency ? 1 : 0 );
$to_fa           = static function ( $value ) {
	return function_exists( 'revayat_en2fa' ) ? revayat_en2fa( (string) $value ) : (string) $value;
};
?>

<section class="rv-situation-experience situation-room-shell" id="archive-situation-room">
	<div class="container">
		<header class="srx-page-head">
			<div class="srx-page-head__intro">
				<p class="srx-eyebrow"><i class="ph-fill ph-broadcast"></i><?php esc_html_e( 'رصد و تحلیل تحولات', 'revayatiran' ); ?></p>
				<h1><?php esc_html_e( 'اتاق وضعیت', 'revayatiran' ); ?></h1>
				<p><?php esc_html_e( 'بولتن‌های راهبردی، سیگنال‌های فوری و برآوردهای طبقه‌بندی‌شده در یک نمای عملیاتی.', 'revayatiran' ); ?></p>
			</div>
		</header>

		<?php if ( ! empty( $hero_bulletins ) ) : ?>
			<section class="srx-lead-layout" aria-label="<?php esc_attr_e( 'مهم‌ترین بولتن‌ها', 'revayatiran' ); ?>">
				<div class="swiper situation-hero-swiper">
					<div class="swiper-wrapper">
					<?php foreach ( $hero_bulletins as $index => $hero ) :
						$hero_media = $hero['media']['url'] ?? '';
						$hero_alt   = $hero['media']['alt'] ?? ( $hero['title'] ?? '' );
						if ( ! $hero_media && function_exists( 'revayat_get_situation_fallback_media' ) ) {
							$hero_fallback = revayat_get_situation_fallback_media( $hero );
							$hero_media    = $hero_fallback['url'];
							$hero_alt      = $hero_fallback['alt'];
						}
						$hero_meta  = $hero['meta'] ?? array();
						?>
							<article class="swiper-slide srx-lead-slide">
								<a href="<?php echo esc_url( $hero['permalink'] ?? '#' ); ?>" class="srx-lead-slide__visual">
								<img src="<?php echo esc_url( $hero_media ); ?>" alt="<?php echo esc_attr( $hero_alt ); ?>" <?php echo 0 === $index ? 'fetchpriority="high"' : 'loading="lazy"'; ?> decoding="async" width="1400" height="788">
								<span class="srx-lead-slide__shade"></span>
								<span class="srx-lead-slide__content">
									<span class="srx-kicker <?php echo in_array($hero_meta['urgency_level'] ?? '', ['فوری', 'بحرانی', 'آنی'], true) ? 'is-urgent' : 'is-normal'; ?>"><i class="ph-fill <?php echo in_array($hero_meta['urgency_level'] ?? '', ['فوری', 'بحرانی', 'آنی'], true) ? 'ph-warning-circle' : 'ph-files'; ?>"></i><?php echo esc_html( $hero_meta['urgency_level'] ?? __( 'گزارش منتخب', 'revayatiran' ) ); ?></span>
									<strong><?php echo esc_html( $hero['title'] ?? '' ); ?></strong>
									<small><?php echo esc_html( wp_trim_words( $hero['excerpt'] ?? '', 20 ) ); ?></small>
									<span class="srx-hero-cta"><?php echo esc_html(!empty($hero['access']['is_locked']) ? __('مشاهده شرایط دسترسی', 'revayatiran') : __('مشاهده گزارش کامل', 'revayatiran')); ?><i class="ph ph-arrow-left"></i></span>
								</span>
								</a>
							</article>
						<?php endforeach; ?>
					</div>
					<div class="srx-slider-controls">
						<button class="srx-hero-next" type="button" aria-label="<?php esc_attr_e( 'اسلاید بعد', 'revayatiran' ); ?>"><i class="ph ph-arrow-left"></i></button>
						<div class="srx-hero-pagination"></div>
						<button class="srx-hero-prev" type="button" aria-label="<?php esc_attr_e( 'اسلاید قبل', 'revayatiran' ); ?>"><i class="ph ph-arrow-right"></i></button>
					</div>
				</div>

				<aside class="srx-watch">
					<div class="srx-watch__head"><span><i class="ph-fill ph-siren"></i><?php esc_html_e( 'آخرین گزارش‌ها', 'revayatiran' ); ?></span></div>
					<ol>
						<?php foreach ( $watch_bulletins as $position => $watch ) : ?>
							<li><span><?php echo esc_html( $to_fa( $position + 1 ) ); ?></span><a href="<?php echo esc_url( $watch['permalink'] ?? '#' ); ?>"><?php echo esc_html( $watch['title'] ?? '' ); ?></a></li>
						<?php endforeach; ?>
					</ol>
				</aside>
			</section>
		<?php endif; ?>

		<section class="srx-filter-dock" aria-labelledby="srx-filter-title">
			<div class="srx-filter-dock__title"><div><i class="ph ph-faders"></i><span><strong id="srx-filter-title"><?php esc_html_e( 'پالایش بولتن‌ها', 'revayatiran' ); ?></strong><small><?php esc_html_e( 'نتایج را با چند انتخاب سریع محدود کنید', 'revayatiran' ); ?></small></span></div><?php if ( 'all' !== $current_security || 'all' !== $current_type || 'all' !== $current_urgency || $active_search ) : ?><a href="<?php echo esc_url( $archive_url ); ?>"><i class="ph ph-arrow-counter-clockwise"></i><?php esc_html_e( 'پاک‌کردن فیلترها', 'revayatiran' ); ?></a><?php endif; ?></div>
			<form class="srx-search" role="search" method="get" action="<?php echo esc_url( $archive_url ); ?>">
				<label class="screen-reader-text" for="situation-search"><?php esc_html_e( 'جستجو در بولتن‌ها', 'revayatiran' ); ?></label>
				<i class="ph ph-magnifying-glass"></i><input id="situation-search" type="search" name="q" value="<?php echo esc_attr( $active_search ); ?>" maxlength="100" placeholder="<?php esc_attr_e( 'جستجو در بولتن‌ها...', 'revayatiran' ); ?>">
				<?php foreach ( array( 'security_level' => $current_security, 'bulletin_type' => $current_type, 'urgency_level' => $current_urgency ) as $key => $value ) : if ( 'all' !== $value ) : ?><input type="hidden" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $value ); ?>"><?php endif; endforeach; ?>
				<button type="submit"><?php esc_html_e( 'جستجو', 'revayatiran' ); ?></button>
			</form>
			<details class="srx-filter-more" <?php echo $active_filter_count ? 'open' : ''; ?>>
				<summary><span><i class="ph ph-sliders-horizontal"></i><?php esc_html_e( 'فیلترهای بیشتر', 'revayatiran' ); ?></span><?php if ( $active_filter_count ) : ?><small><?php echo esc_html( $to_fa( $active_filter_count ) ); ?></small><?php endif; ?><i class="ph ph-caret-down"></i></summary>
				<div class="srx-filter-groups">
					<div class="srx-filter-group"><span><i class="ph ph-shield"></i><?php esc_html_e( 'سطح دسترسی', 'revayatiran' ); ?></span><div><?php foreach ( $security_levels as $option ) : ?><a class="<?php echo $current_security === $option['slug'] ? 'is-active' : ''; ?>" aria-current="<?php echo $current_security === $option['slug'] ? 'true' : 'false'; ?>" href="<?php echo esc_url( $filter_url( 'security_level', $option['slug'] ) ); ?>"><?php echo esc_html( $option['label'] ); ?></a><?php endforeach; ?></div></div>
					<div class="srx-filter-group"><span><i class="ph ph-tag"></i><?php esc_html_e( 'گونه محتوا', 'revayatiran' ); ?></span><div><?php foreach ( $bulletin_types as $value => $label ) : ?><a class="<?php echo $current_type === $value ? 'is-active' : ''; ?>" aria-current="<?php echo $current_type === $value ? 'true' : 'false'; ?>" href="<?php echo esc_url( $filter_url( 'bulletin_type', $value ) ); ?>"><?php echo esc_html( $label ); ?></a><?php endforeach; ?></div></div>
					<div class="srx-filter-group"><span><i class="ph ph-lightning"></i><?php esc_html_e( 'درجه فوریت', 'revayatiran' ); ?></span><div><?php foreach ( $urgency_levels as $value => $label ) : ?><a class="<?php echo $current_urgency === $value ? 'is-active' : ''; ?>" aria-current="<?php echo $current_urgency === $value ? 'true' : 'false'; ?>" href="<?php echo esc_url( $filter_url( 'urgency_level', $value ) ); ?>"><?php echo esc_html( $label ); ?></a><?php endforeach; ?></div></div>
				</div>
			</details>
		</section>

		<section class="srx-results" aria-labelledby="srx-results-title">
			<div class="srx-results__head"><div><span></span><h2 id="srx-results-title"><?php esc_html_e( 'آخرین بولتن‌ها', 'revayatiran' ); ?></h2></div><p><?php printf( esc_html__( '%s نتیجه', 'revayatiran' ), esc_html( $to_fa( $total_posts ) ) ); ?></p></div>
			<?php if ( $bulletins ) : ?>
				<div class="srx-card-grid"><?php foreach ( $bulletins as $bulletin ) { get_template_part( 'template-parts/cards/card-situation', null, array( 'item' => $bulletin, 'variant' => 'visual', 'archive_card' => true ) ); } ?></div>
				<?php if ( $max_pages > 1 ) : ?><nav class="srx-pagination" aria-label="<?php esc_attr_e( 'صفحه‌بندی بولتن‌ها', 'revayatiran' ); ?>"><?php echo wp_kses_post( paginate_links( array( 'current' => $paged, 'total' => $max_pages, 'prev_text' => '<i class="ph ph-caret-right"></i>', 'next_text' => '<i class="ph ph-caret-left"></i>' ) ) ); ?></nav><?php endif; ?>
			<?php else : ?>
				<div class="srx-empty"><i class="ph ph-broadcast"></i><h2><?php esc_html_e( 'بولتنی با این فیلترها پیدا نشد', 'revayatiran' ); ?></h2><p><?php esc_html_e( 'فیلترها را تغییر دهید یا همه موارد را دوباره نمایش دهید.', 'revayatiran' ); ?></p><a href="<?php echo esc_url( $archive_url ); ?>"><?php esc_html_e( 'نمایش همه بولتن‌ها', 'revayatiran' ); ?></a></div>
			<?php endif; ?>
		</section>
	</div>
</section>

<?php get_footer();
