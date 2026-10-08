<?php
/** آرشیو شبکه تحلیلگران؛ فید نویسنده‌محور و کشف شبکه. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();

$paged = max( 1, (int) get_query_var( 'paged', 1 ) );
$active_field = sanitize_key( wp_unslash( $_GET['field'] ?? ( get_query_var( 'analyst_field' ) ?: 'all' ) ) );
$active_sort = sanitize_key( wp_unslash( $_GET['sort'] ?? 'date' ) );
$active_search = sanitize_text_field( wp_unslash( $_GET['q'] ?? '' ) );
$archive_url = get_post_type_archive_link( 'analyst_post' ) ?: home_url( '/analysts/' );
$archive_data = function_exists( 'revayat_get_archive_data' ) ? revayat_get_archive_data( 'analyst_post', array( 'paged' => $paged, 'posts_per_page' => 6, 'field' => $active_field, 'sort' => $active_sort, 'search' => $active_search ) ) : array();
$items = $archive_data['items'] ?? array();
$total_posts = absint( $archive_data['total_posts'] ?? count( $items ) );
$max_pages = max( 1, absint( $archive_data['max_pages'] ?? 1 ) );
$active_field = sanitize_key( $archive_data['active_field'] ?? $active_field );
$active_sort = sanitize_key( $archive_data['active_sort'] ?? $active_sort );
$active_search = sanitize_text_field( $archive_data['active_search'] ?? $active_search );
$filter_fields = $archive_data['filter_options']['fields'] ?? array();
$sort_options = $archive_data['filter_options']['sorts'] ?? array();
$published = wp_count_posts( 'analyst_post' );
$people_count = wp_count_posts( 'person' );
$published_all = absint( $published->publish ?? 0 );
$analysts_count = absint( $people_count->publish ?? 0 );
$fields_count = taxonomy_exists( 'analyst_field' ) ? wp_count_terms( array( 'taxonomy' => 'analyst_field', 'hide_empty' => true ) ) : 0;
$fields_count = is_wp_error( $fields_count ) ? 0 : absint( $fields_count );
$member_url = is_user_logged_in() ? home_url( '/dashboard/' ) : home_url( '/auth/?tab=register' );
$next_page_url = $paged < $max_pages ? add_query_arg(
	array_filter(
		array(
			'field' => 'all' !== $active_field ? $active_field : '',
			'sort'  => 'date' !== $active_sort ? $active_sort : '',
			'q'     => $active_search,
		)
	),
	get_pagenum_link( $paged + 1 )
) : '';
$leaderboard = array();
if ( class_exists( 'Revayat_Data_Service' ) && method_exists( 'Revayat_Data_Service', 'get_analyst_leaderboard' ) ) {
	foreach ( Revayat_Data_Service::get_analyst_leaderboard( 4 ) as $index => $person ) {
		$leaderboard[] = function_exists( 'revayat_adapt_analyst_person' ) ? revayat_adapt_analyst_person( $person, $index + 1 ) : $person;
	}
}
$hot_fields = array_values( array_filter( $filter_fields, static function ( $field ) { return 'all' !== ( $field['slug'] ?? 'all' ) && ! empty( $field['count'] ); } ) );
?>
<main id="primary" class="anx-page anx-archive">
	<section class="anx-network-hero" aria-labelledby="anx-network-title"><div class="container anx-network-hero__grid">
		<div class="anx-network-hero__intro"><span class="anx-eyebrow"><i class="ph ph-users-three"></i><?php esc_html_e( 'جامعه تخصصی روایت ایران', 'revayatiran' ); ?></span><h1 id="anx-network-title"><?php esc_html_e( 'شبکه تحلیلگران', 'revayatiran' ); ?></h1><p><?php esc_html_e( 'دیدگاه‌ها و تحلیل‌های جامعه تخصصی روایت ایران', 'revayatiran' ); ?></p></div>
		<div class="anx-network-hero__actions"><div class="anx-network-hero__utility"><form role="search" method="get" action="<?php echo esc_url( $archive_url ); ?>" class="anx-search"><label class="screen-reader-text" for="anx-archive-search"><?php esc_html_e( 'جستجو در شبکه تحلیلگران', 'revayatiran' ); ?></label><i class="ph ph-magnifying-glass" aria-hidden="true"></i><input id="anx-archive-search" type="search" name="q" maxlength="100" value="<?php echo esc_attr( $active_search ); ?>" placeholder="<?php esc_attr_e( 'جستجوی تحلیلگر، موضوع یا یادداشت…', 'revayatiran' ); ?>"><?php if ( 'all' !== $active_field ) : ?><input type="hidden" name="field" value="<?php echo esc_attr( $active_field ); ?>"><?php endif; ?><input type="hidden" name="sort" value="<?php echo esc_attr( $active_sort ); ?>"><button type="submit"><?php esc_html_e( 'جستجو', 'revayatiran' ); ?></button></form><a class="anx-primary-button" href="<?php echo esc_url( $member_url ); ?>"><span><?php esc_html_e( 'پیوستن', 'revayatiran' ); ?></span><i class="ph ph-arrow-left"></i></a></div></div>
	</div></section>

	<div class="container anx-layout">
		<section class="anx-feed" aria-labelledby="anx-feed-title" data-anx-ajax-feed data-endpoint="<?php echo esc_url( rest_url( 'revayat/v1/analyst-posts' ) ); ?>" data-current-page="<?php echo esc_attr( $paged ); ?>" data-max-pages="<?php echo esc_attr( $max_pages ); ?>" data-auto-through="5" data-field="<?php echo esc_attr( $active_field ); ?>" data-sort="<?php echo esc_attr( $active_sort ); ?>" data-search="<?php echo esc_attr( $active_search ); ?>">
			<header class="anx-feed-toolbar anx-feed-toolbar--minimal">
				<nav class="anx-sort" aria-label="<?php esc_attr_e( 'مرتب‌سازی یادداشت‌ها', 'revayatiran' ); ?>"><?php foreach ( $sort_options as $sort ) : $sort_url = add_query_arg( array_filter( array( 'q' => $active_search ) ), $sort['url'] ?? $archive_url ); ?><a class="<?php echo ! empty( $sort['is_active'] ) ? 'is-active' : ''; ?>" href="<?php echo esc_url( $sort_url ); ?>"><i class="ph ph-<?php echo esc_attr( 'score' === ( $sort['slug'] ?? '' ) ? 'chart-bar' : 'clock' ); ?>"></i><?php echo esc_html( $sort['label'] ?? '' ); ?></a><?php endforeach; ?></nav>
			</header>
			<?php if ( $items ) : ?><div class="anx-feed-list" data-anx-feed-list><?php foreach ( $items as $item ) { get_template_part( 'template-parts/cards/card-analyst', null, array( 'item' => $item, 'variant' => 'grid' ) ); } ?></div><?php if ( $max_pages > 1 ) : ?><div class="anx-ajax-pagination" data-anx-ajax-pagination><span class="anx-ajax-pagination__sentinel" data-anx-feed-sentinel aria-hidden="true"></span><p class="anx-ajax-pagination__status" data-anx-feed-status role="status" aria-live="polite"></p><a class="anx-load-more" data-anx-load-more href="<?php echo esc_url( $next_page_url ); ?>"><span><?php esc_html_e( 'نمایش تحلیل‌های بیشتر', 'revayatiran' ); ?></span><i class="ph ph-arrow-down"></i></a></div><nav class="anx-pagination" aria-label="<?php esc_attr_e( 'صفحه‌بندی یادداشت‌ها', 'revayatiran' ); ?>"><?php echo wp_kses_post( paginate_links( array( 'current' => $paged, 'total' => $max_pages, 'prev_text' => __( 'صفحه قبل', 'revayatiran' ) . ' →', 'next_text' => __( 'تحلیل‌های بیشتر', 'revayatiran' ) . ' ←' ) ) ); ?></nav><?php endif; ?>
			<?php else : ?><div class="anx-empty"><i class="ph ph-note-blank"></i><h2><?php esc_html_e( 'یادداشتی پیدا نشد', 'revayatiran' ); ?></h2><p><?php esc_html_e( 'عبارت جستجو یا حوزه انتخابی را تغییر دهید.', 'revayatiran' ); ?></p><a href="<?php echo esc_url( $archive_url ); ?>"><?php esc_html_e( 'پاک‌کردن فیلترها', 'revayatiran' ); ?></a></div><?php endif; ?>
		</section>

		<aside class="anx-sidebar" aria-label="<?php esc_attr_e( 'کشف شبکه تحلیلگران', 'revayatiran' ); ?>">
			<section class="anx-side-card anx-leaderboard"><header><i class="ph ph-trophy"></i><h2><?php esc_html_e( 'برترین تحلیلگران', 'revayatiran' ); ?></h2></header><?php if ( $leaderboard ) : ?><ol><?php foreach ( $leaderboard as $person ) : ?><li><span class="anx-rank"><?php echo esc_html( revayat_en2fa( $person['rank'] ?? 0 ) ); ?></span><a href="<?php echo esc_url( $person['url'] ?? '' ); ?>"><?php if ( ! empty( $person['avatar_url'] ) ) : ?><img src="<?php echo esc_url( $person['avatar_url'] ); ?>" alt="" loading="lazy"><?php else : ?><i class="ph ph-user"></i><?php endif; ?><span><strong><?php echo esc_html( $person['name'] ?? '' ); ?></strong><small><?php echo esc_html( $person['specialty'] ?? '' ); ?></small></span></a><span class="anx-leader-score"><?php echo ! empty( $person['score'] ) ? esc_html( revayat_en2fa( $person['score'] ) ) : '—'; ?></span></li><?php endforeach; ?></ol><?php else : ?><p class="anx-side-empty"><?php esc_html_e( 'با ثبت نخستین رأی معتبر، رتبه‌بندی شکل می‌گیرد.', 'revayatiran' ); ?></p><?php endif; ?></section>
			<section class="anx-side-card"><header><i class="ph ph-fire"></i><h2><?php esc_html_e( 'حوزه‌های داغ', 'revayatiran' ); ?></h2></header><div class="anx-hot-topics"><?php if ( $hot_fields ) : foreach ( array_slice( $hot_fields, 0, 8 ) as $field ) : ?><a href="<?php echo esc_url( $field['url'] ?? $archive_url ); ?>"><span><?php echo esc_html( $field['label'] ?? '' ); ?></span><small><?php echo esc_html( revayat_en2fa( $field['count'] ?? 0 ) ); ?></small></a><?php endforeach; else : ?><p class="anx-side-empty"><?php esc_html_e( 'هنوز حوزه فعالی ثبت نشده است.', 'revayatiran' ); ?></p><?php endif; ?></div></section>
			<section class="anx-side-card anx-join-card"><i class="ph ph-users-three"></i><h2><?php esc_html_e( 'به جمع تحلیلگران بپیوندید', 'revayatiran' ); ?></h2><p><?php esc_html_e( 'تحلیل‌ها و تجربه حرفه‌ای خود را با جامعه تخصصی روایت ایران به اشتراک بگذارید.', 'revayatiran' ); ?></p><a href="<?php echo esc_url( $member_url ); ?>"><?php echo is_user_logged_in() ? esc_html__( 'ورود به پنل شخصی', 'revayatiran' ) : esc_html__( 'درخواست عضویت', 'revayatiran' ); ?><i class="ph ph-arrow-left"></i></a></section>
			<section class="anx-side-card"><header><i class="ph ph-bell"></i><h2><?php esc_html_e( 'تازه در شبکه', 'revayatiran' ); ?></h2></header><?php if ( $items ) : ?><ul class="anx-activity-list"><?php foreach ( array_slice( $items, 0, 4 ) as $item ) : ?><li><i class="ph ph-file-text"></i><p><strong><?php echo esc_html( $item['author_name'] ?? ( $item['author']['name'] ?? __( 'یک تحلیلگر', 'revayatiran' ) ) ); ?></strong> <?php esc_html_e( 'یادداشت تازه‌ای منتشر کرد', 'revayatiran' ); ?><a href="<?php echo esc_url( $item['url'] ?? '' ); ?>"><?php echo wp_kses_post( $item['title'] ?? '' ); ?></a></p></li><?php endforeach; ?></ul><?php else : ?><p class="anx-side-empty"><?php esc_html_e( 'فعالیت تازه‌ای وجود ندارد.', 'revayatiran' ); ?></p><?php endif; ?></section>
		</aside>
	</div>
</main>
<?php get_footer();
