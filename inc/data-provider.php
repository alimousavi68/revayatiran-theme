<?php
/**
 * لایه پل ارتباطی تامین داده‌های پوسته (Theme Data Provider Bridge)
 *
 * چرا این فایل لازم است؟
 * جهت برقراری ارتباط امن میان پوسته و لایه سرویس داده افزونه (Revayat_Data_Service) با تضمین اصل وابستگی اختیاری (Plugin Optional Dependency).
 *
 * چه مسئولیتی دارد؟
 * - ارائه تابع مرکزی `revayat_get_homepage_data($section)` برای تامین یکپارچه داده‌های صفحه اول.
 * - در صورت فعال بودن افزونه: واکشی از متدهای سرویس افزونه (`Revayat_Data_Service`).
 * - در صورت نبود افزونه یا خالی بودن دیتابیس: بازگرداندن آرایه خالی برای empty state پوسته.
 * - در صورت عدم دسترسی به هیچ‌کدام: بازگرداندن آرایه خالی استاندارد بدون بروز خطای کشنده (Zero Fatal Error Guarantee).
 *
 * ارتباط با معماری:
 * تحقق بخشیدن به تصمیمات معماری ثبت‌شده در ADR-002 (تک‌منبعی رندر) و ADR-005 (وابستگی اختیاری افزونه).
 *
 * @package RevayatIran
 * @subpackage Inc
 * @version 1.0.0 (Pass 3A Commit 5)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * تابع مرکزی دریافت داده‌های هر سکشن صفحه اصلی با فال‌بک هوشمند
 *
 * @param string $section نام یا شناسه سکشن مورد نظر.
 * @return array ساختار داده مورد نیاز تمپلیت.
 */
function revayat_get_homepage_data( string $section ): array {
	$has_plugin_service = class_exists( 'Revayat_Data_Service' );
	$data               = array();

	switch ( $section ) {
		case 'hero':
			if ( $has_plugin_service && method_exists( 'Revayat_Data_Service', 'get_hero_data' ) ) {
				$service_data = Revayat_Data_Service::get_hero_data();
				if ( ! empty( $service_data ) && ( ! empty( $service_data['lead'] ) || ! empty( $service_data['side'] ) ) ) {
					$data = array(
						'lead'  => ! empty( $service_data['lead'] ) ? revayat_adapt_hybrid_item( $service_data['lead'] ) : array(),
						'items' => ! empty( $service_data['side'] ) ? array_map( 'revayat_adapt_hybrid_item', $service_data['side'] ) : array(),
					);
				}
			}
			break;

		case 'daily-narrative':
		case 'daily_narrative':
			if ( $has_plugin_service && method_exists( 'Revayat_Data_Service', 'get_daily_narrative_data' ) ) {
				$service_data = Revayat_Data_Service::get_daily_narrative_data();
				if ( ! empty( $service_data ) && ! empty( $service_data['lead'] ) ) {
					$data = array(
						'lead'              => revayat_adapt_hybrid_item( $service_data['lead'] ),
						'interactive_items' => ! empty( $service_data['items'] ) ? array_map( 'revayat_adapt_hybrid_item', $service_data['items'] ) : array(),
					);
				}
			}
			break;

		case 'situation-room':
		case 'situation_room':
			if ( ! current_user_can( 'revayat_read_situation_room' ) && ! current_user_can( 'manage_options' ) ) {
				$data = array();
				break;
			}
			if ( $has_plugin_service && method_exists( 'Revayat_Data_Service', 'get_situation_room_data' ) ) {
				$service_data = Revayat_Data_Service::get_situation_room_data();
				if ( ! empty( $service_data ) ) {
					$bulletins = array_map( 'revayat_adapt_situation_room_bulletin', $service_data );
					$data      = array(
						'bulletins'  => $bulletins,
						'gated_meta' => array(
							'auth_url' => home_url( '/login/' ),
							'help_url' => home_url( '/contact/' ),
						),
					);
				}
			}
			break;

		case 'news-monitoring':
		case 'news_monitoring':
			if ( $has_plugin_service && method_exists( 'Revayat_Data_Service', 'get_news_monitoring_data' ) ) {
				$service_data = Revayat_Data_Service::get_news_monitoring_data();
				if ( ! empty( $service_data ) ) {
					$data = array(
						'tabs'       => array(
							array( 'slug' => 'all', 'label' => __( 'همه', 'revayatiran' ) ),
							array( 'slug' => 'politics', 'label' => __( 'سیاست', 'revayatiran' ) ),
							array( 'slug' => 'economy', 'label' => __( 'اقتصاد', 'revayatiran' ) ),
							array( 'slug' => 'security', 'label' => __( 'امنیت', 'revayatiran' ) ),
						),
						'active_tab' => 'all',
						'items'      => array_map( 'revayat_adapt_hybrid_item', $service_data ),
					);
				}
			}
			break;

		case 'special-dossiers':
		case 'special_dossiers':
			if ( $has_plugin_service && method_exists( 'Revayat_Data_Service', 'get_special_dossiers_data' ) ) {
				$service_data = Revayat_Data_Service::get_special_dossiers_data();
				if ( ! empty( $service_data ) ) {
					$lead_item = array_shift( $service_data );
					$data      = array(
						'lead'    => revayat_adapt_dossier_lead( $lead_item ),
						'sidebar' => array_map( 'revayat_adapt_hybrid_item', $service_data ),
					);
				}
			}
			break;

		case 'media-observatory':
		case 'media_observatory':
			if ( $has_plugin_service && method_exists( 'Revayat_Data_Service', 'get_media_observatory_data' ) ) {
				$service_data = Revayat_Data_Service::get_media_observatory_data();
				if ( ! empty( $service_data ) ) {
					$data = array(
						'items' => array_map( 'revayat_adapt_observatory_card', $service_data ),
					);
				}
			}
			break;

		case 'analysts-network':
		case 'analysts_network':
			if ( $has_plugin_service && method_exists( 'Revayat_Data_Service', 'get_analysts_network_data' ) ) {
				$service_data = Revayat_Data_Service::get_analysts_network_data();
				if ( ! empty( $service_data ) ) {
					$posts        = $service_data['posts'] ?? $service_data;
					$person_items = $service_data['analysts'] ?? array();
					$lead_item    = ! empty( $posts ) ? array_shift( $posts ) : null;
					$top_analysts = array();

					if ( ! empty( $person_items ) ) {
						$rank = 1;
						foreach ( $person_items as $p_item ) {
							$top_analysts[] = revayat_adapt_analyst_person( $p_item, $rank++ );
						}
					}

					$data = array(
						'editorial' => ! empty( $lead_item ) ? revayat_adapt_analyst_editorial( $lead_item ) : array(),
						'analyses'  => ! empty( $posts ) ? array_map( 'revayat_adapt_hybrid_item', $posts ) : array(),
						'analysts'  => $top_analysts,
					);
				}
			}
			break;

		case 'multimedia':
			if ( $has_plugin_service && method_exists( 'Revayat_Data_Service', 'get_multimedia_data' ) ) {
				$service_data = Revayat_Data_Service::get_multimedia_data();
				if ( ! empty( $service_data ) ) {
					$data = array(
						'stories' => array_map( 'revayat_adapt_multimedia_story', $service_data ),
					);
				}
			}
			break;

		default:
			$data = array();
			break;
	}

	return ! empty( $data ) && is_array( $data ) ? $data : array();
}

/**
 * تبدیل قرارداد هیبریدی به ساختار عمومی کارت‌های خبری و تحلیلی
 *
 * @param array $item داده‌های خام Hybrid Contract.
 * @return array
 */
function revayat_adapt_hybrid_item( array $item ): array {
	$category = ! empty( $item['taxonomies']['category']['name'] ) ? $item['taxonomies']['category']['name'] : ( $item['taxonomies']['dossier_topic'] ?? '' );
	$score = $item['score'] ?? ( $item['analyst_score'] ?? ( $item['meta']['analyst_score'] ?? '' ) );
	$sections = $item['sections'] ?? ( $item['meta']['timeline_events'] ?? array() );
	$sections_count = is_array( $sections ) ? count( $sections ) : 0;

	return array(
		'id'             => $item['id'] ?? 0,
		'title'          => $item['title'] ?? '',
		'url'            => $item['permalink'] ?? '',
		'thumbnail_url'  => $item['media']['url'] ?? '',
		'category'       => $category,
		'date'           => revayat_en2fa($item['date'] ?? ''),
		'time_ago'       => revayat_en2fa($item['time_ago'] ?? ''),
		'author_name'    => $item['taxonomies']['person_author']['name'] ?? '',
		'author_avatar'  => $item['taxonomies']['person_author']['avatar'] ?? '',
		'author_role'    => $item['taxonomies']['person_author']['role'] ?? '',
		'reading_time'   => revayat_en2fa($item['reading_time'] ?? ''),
		'tag_label'      => $item['taxonomies']['editorial_placement'] ?? '',
		'excerpt'        => $item['excerpt'] ?? '',
		'source_badge'   => $item['taxonomies']['news_source'] ?? '',
		'source_label'   => $item['taxonomies']['news_source'] ?? '',
		'score'          => $score,
		'sections_count' => $sections_count,
	);
}

/**
 * تبدیل قرارداد به ساختار بولتن‌های اتاق وضعیت
 *
 * @param array $item داده‌های خام Hybrid Contract.
 * @return array
 */
function revayat_adapt_situation_room_bulletin( array $item ): array {
	$urgency = $item['meta']['urgency_level'] ?? 'عادی';
	$is_hot  = in_array( $urgency, array( 'فوری', 'آنی', 'بحرانی' ), true );

	return array(
		'id'                  => $item['id'] ?? 0,
		'title'               => $item['title'] ?? '',
		'url'                 => $item['permalink'] ?? '',
		'date'                => $item['date'] ?? ( $item['time_ago'] ?? '' ),
		'category'            => ! empty( $item['taxonomies']['security_level'] ) && 'public' !== $item['taxonomies']['security_level'] ? $item['taxonomies']['security_level'] : ( ! empty( $item['meta']['bulletin_type'] ) ? $item['meta']['bulletin_type'] : 'برآورد راهبردی' ),
		'urgency_level'       => $urgency,
		'security_level'      => $item['taxonomies']['security_level'] ?? 'علنی',
		'verification_status' => $item['meta']['verification_status'] ?? 'تایید شده',
		'update_time'         => $item['meta']['update_time'] ?? ( $item['time_ago'] ?? '' ),
		'is_hot'              => $is_hot,
		'summary'             => $item['excerpt'] ?? '',
		'source'              => $item['taxonomies']['news_source'] ?? '',
	);
}

/**
 * تبدیل قرارداد به ساختار پرونده ویژه اصلی
 *
 * @param array $item داده‌های خام Hybrid Contract.
 * @return array
 */
function revayat_adapt_dossier_lead( array $item ): array {
	$docs_count   = ! empty( $item['meta']['stats']['docs'] ) ? $item['meta']['stats']['docs'] : 0;
	$frames_count = ! empty( $item['meta']['stats']['frames'] ) ? $item['meta']['stats']['frames'] : 0;
	$notes_count  = ! empty( $item['meta']['stats']['notes'] ) ? $item['meta']['stats']['notes'] : 0;

	$stats = array(
		'docs'        => $docs_count ? sprintf( __('%s سند پیوست', 'revayatiran'), function_exists('revayat_en2fa') ? revayat_en2fa($docs_count) : $docs_count ) : '',
		'docs_desc'   => 'بولتن‌های داده‌محور',
		'frames'      => $frames_count ? sprintf( __('%s فریم مستند', 'revayatiran'), function_exists('revayat_en2fa') ? revayat_en2fa($frames_count) : $frames_count ) : '',
		'frames_desc' => 'نمودارها و داده‌نماها',
		'notes'       => $notes_count ? sprintf( __('%s یادداشت تحلیلی', 'revayatiran'), function_exists('revayat_en2fa') ? revayat_en2fa($notes_count) : $notes_count ) : '',
		'notes_desc'  => 'نخبگان و کارشناسان',
	);

	$last_update = ! empty( $item['meta']['last_update'] ) ? $item['meta']['last_update'] : ( ! empty( $item['time_ago'] ) ? $item['time_ago'] : '' );
	if ( $last_update && strpos( $last_update, 'آخرین به‌روزرسانی:' ) === false && strpos( $last_update, 'بروزرسانی:' ) === false ) {
		$last_update = 'آخرین به‌روزرسانی: ' . $last_update;
	}
	if ( function_exists( 'revayat_en2fa' ) ) {
		$last_update = revayat_en2fa( $last_update );
	}

	return array(
		'id'            => $item['id'] ?? 0,
		'title'         => $item['title'] ?? '',
		'url'           => $item['permalink'] ?? '',
		'thumbnail_url' => $item['media']['url'] ?? '',
		'category'      => ! empty( $item['taxonomies']['dossier_topic'] ) ? $item['taxonomies']['dossier_topic'] : 'پرونده ویژه',
		'is_live'       => ! empty( $item['meta']['is_live'] ) ? $item['meta']['is_live'] : false,
		'last_update'   => $last_update,
		'lead_text'     => $item['excerpt'] ?? '',
		'excerpt'       => $item['excerpt'] ?? '',
		'editor_lead'   => ! empty( $item['meta']['editor_lead'] ) ? $item['meta']['editor_lead'] : 'تحریریه راهبردی',
		'stats'         => $stats,
	);
}

/**
 * تبدیل قرارداد به ساختار کارت‌های دیده‌بان رسانه
 *
 * @param array $item داده‌های خام Hybrid Contract.
 * @return array
 */
function revayat_adapt_observatory_card( array $item ): array {
	$raw_slug  = $item['taxonomies']['observatory_badge'] ?? '';
	$badge_slug_map = [
		'discourse' => 'obs-badge--discourse',
		'framing'   => 'obs-badge--framing',
		'campaign'  => 'obs-badge--campaign',
		'تحلیل گفتمان' => 'obs-badge--discourse',
		'مقایسه فریمینگ' => 'obs-badge--framing',
		'کمپین‌شناسی' => 'obs-badge--campaign',
	];
	
	$badge_label = ! empty( $item['taxonomies']['observatory_badge_label'] ) ? $item['taxonomies']['observatory_badge_label'] : ( $item['taxonomies']['observatory_badge'] ?? 'تحلیل گفتمان' );
	
	$badge_slug = $badge_slug_map[$raw_slug] ?? ( $badge_slug_map[$badge_label] ?? 'obs-badge--discourse' );

	return array(
		'id'                 => $item['id'] ?? 0,
		'title'              => $item['title'] ?? '',
		'url'                => $item['permalink'] ?? ( $item['url'] ?? '' ),
		'category'           => 'دیده‌بان رسانه',
		'badge_slug'         => $badge_slug,
		'badge_class'        => $badge_slug,
		'badge_label'        => $badge_label,
		'date'               => $item['date'] ?? '',
		'frame_a'            => $item['frame_a'] ?? ( $item['meta']['frames']['frame_a'] ?? array( 'source' => '', 'text' => '' ) ),
		'frame_b'            => $item['frame_b'] ?? ( $item['meta']['frames']['frame_b'] ?? array( 'source' => '', 'text' => '' ) ),
		'summary'            => $item['summary'] ?? ( $item['meta']['narrative_summary'] ?? ( $item['excerpt'] ?? '' ) ),
		'narrative_summary'  => $item['narrative_summary'] ?? ( $item['meta']['narrative_summary'] ?? ( $item['excerpt'] ?? '' ) ),
		'monitoring_source'  => $item['monitoring_source'] ?? ( $item['meta']['monitoring_source'] ?? ( $item['taxonomies']['news_source'] ?? '' ) ),
	);
}

/**
 * تبدیل قرارداد هیبریدی به ساختار کارت یادداشت تحلیلی شبکه تحلیل‌گران
 *
 * @param array $item داده‌های خام Hybrid Contract.
 * @return array
 */
function revayat_adapt_analyst_post_card( array $item ): array {
	$raw_score = ! empty( $item['score'] ) ? (float) $item['score'] : (float) ( $item['meta']['analyst_score'] ?? 0 );
	if ( $raw_score > 5.0 ) {
		$raw_score = round( $raw_score / 2, 1 );
	}
	$score_val = $raw_score > 0 ? max( 1.0, min( 5.0, $raw_score ) ) : 0.0;
	$score_str = $score_val > 0 ? number_format( $score_val, 1 ) : '—';

	$author = $item['author'] ?? ( $item['taxonomies']['person_author'] ?? array() );

	return array(
		'id'             => $item['id'] ?? 0,
		'title'          => $item['title'] ?? '',
		'url'            => $item['url'] ?? ( $item['permalink'] ?? '' ),
		'permalink'      => $item['permalink'] ?? ( $item['url'] ?? '' ),
		'thumbnail_url'  => $item['thumbnail_url'] ?? ( $item['media']['url'] ?? '' ),
		'date'           => $item['date'] ?? '',
		'time_ago'       => $item['time_ago'] ?? '',
		'reading_time'   => $item['reading_time'] ?? '۵ دقیقه',
		'score'          => $score_str,
		'analyst_score'  => $score_val,
		'author_name'    => $item['author_name'] ?? ( $author['name'] ?? '' ),
		'author_role'    => $item['author_role'] ?? ( $author['role'] ?? '' ),
		'author_avatar'  => $item['author_avatar'] ?? ( $author['avatar'] ?? '' ),
		'author_url'     => $item['author_url'] ?? ( $author['url'] ?? '' ),
		'author_org'     => $item['author_org'] ?? ( $author['organization'] ?? '' ),
		'field_name'     => $item['field_name'] ?? ( $item['taxonomies']['analyst_field'] ?? '' ),
		'field_slug'     => $item['field_slug'] ?? ( $item['taxonomies']['analyst_field_slug'] ?? '' ),
		'analysis_type'  => $item['analysis_type'] ?? ( $item['meta']['analysis_type'] ?? 'یادداشت تحلیلی' ),
		'featured_quote' => $item['featured_quote'] ?? ( $item['analyst_quote'] ?? ( $item['meta']['featured_quote'] ?? '' ) ),
		'analyst_quote'  => $item['analyst_quote'] ?? ( $item['featured_quote'] ?? ( $item['meta']['analyst_quote'] ?? '' ) ),
		'summary'        => $item['summary'] ?? ( $item['desc'] ?? ( $item['excerpt'] ?? '' ) ),
		'excerpt'        => $item['excerpt'] ?? ( $item['summary'] ?? '' ),
	);
}

if (!function_exists('revayat_en2fa')) {
	function revayat_en2fa($str) {
		$en = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
		$fa = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
		return str_replace($en, $fa, (string)$str);
	}
}

/**
 * تبدیل قرارداد به ساختار یادداشت سرمقاله شبکه تحلیلگران
 *
 * @param array $item داده‌های خام Hybrid Contract.
 * @return array
 */
function revayat_adapt_analyst_editorial( array $item ): array {
	$raw_score = (float) ( $item['analyst_score'] ?? ( $item['meta']['analyst_score'] ?? 0 ) );
	if ( $raw_score > 5.0 ) {
		$raw_score = round( $raw_score / 2, 1 );
	}
	$score_val = $raw_score > 0 ? max( 1.0, min( 5.0, $raw_score ) ) : 0.0;
	$score = $score_val > 0 ? revayat_en2fa( number_format( $score_val, 1 ) ) : '—';
	$votes_num = absint( $item['votes_count'] ?? ( $item['meta']['analyst_votes'] ?? 0 ) );
	$votes = $votes_num ? '(' . revayat_en2fa( $votes_num ) . ' رای)' : '';

	return array(
		'id'             => $item['id'] ?? 0,
		'title'          => $item['title'] ?? '',
		'url'            => $item['permalink'] ?? '',
		'thumbnail_url'  => $item['media']['url'] ?? '',
		'author_name'    => $item['taxonomies']['person_author']['name'] ?? '',
		'author_avatar'  => $item['taxonomies']['person_author']['avatar'] ?? '',
		'author_role'    => $item['taxonomies']['person_author']['role'] ?? '',
		'featured_quote' => $item['meta']['featured_quote'] ?? ( $item['excerpt'] ?? '' ),
		'desc'           => $item['meta']['featured_quote'] ?? ( $item['excerpt'] ?? '' ),
		'analyst_score'  => $score_val,
		'score'          => $score,
		'votes_count'    => $votes,
		'category'       => $item['taxonomies']['analyst_field'] ?? '',
		'date'           => revayat_en2fa($item['date'] ?? ''),
	);
}

/**
 * تبدیل داده‌های کارشناس (CPT Person) به ساختار کارت تحلیلگر رتبه‌بندی‌شده
 *
 * @param array $person داده‌های شیء person در Hybrid Contract.
 * @param int   $rank   رتبه ترتیبی در لیست.
 * @return array
 */
function revayat_adapt_analyst_person( array $person, int $rank ): array {
	$votes          = $person['meta']['person_votes'] ?? 0;
	$raw_score      = (float) ( $person['meta']['person_total_score'] ?? 0 );
	if ( $raw_score > 5.0 ) {
		$raw_score = round( $raw_score / 2, 1 );
	}
	$score_val      = $raw_score > 0 ? max( 1.0, min( 5.0, $raw_score ) ) : 0.0;
	$score          = $score_val > 0 ? revayat_en2fa( number_format( $score_val, 1 ) ) : '—';
	$analyses_count = absint( $person['meta']['analyses_count'] ?? 0 );

	return array(
		'rank'           => revayat_en2fa((string) $rank),
		'name'           => $person['title'] ?? '',
		'specialty'      => ! empty( $person['meta']['expertise'] ) ? $person['meta']['expertise'] : ( ! empty( $person['meta']['role_title'] ) ? $person['meta']['role_title'] : ( $person['taxonomies']['analyst_field'] ?? 'تحلیلگر ارشد' ) ),
		'avatar_url'     => $person['media']['url'] ?? '',
		'url'            => $person['permalink'] ?? ( $person['url'] ?? '' ),
		'score'          => $score,
		'analyses_count' => revayat_en2fa($analyses_count),
		'votes_count'    => revayat_en2fa((string) $votes),
	);
}

/**
 * تبدیل قرارداد به ساختار استوری‌ریل چندرسانه‌ای
 *
 * @param array $item داده‌های خام Hybrid Contract.
 * @return array
 */
function revayat_adapt_multimedia_story( array $item ): array {
	return array(
		'id'            => $item['id'] ?? 0,
		'title'         => $item['title'] ?? '',
		'url'           => $item['permalink'] ?? '',
		'thumbnail_url' => $item['media']['url'] ?? '',
		'media_type'    => $item['taxonomies']['media_format'] ?? ( $item['meta']['media_type'] ?? 'video' ),
		'type'          => $item['taxonomies']['media_format'] ?? ( $item['meta']['media_type'] ?? 'video' ),
		'duration'      => ! empty( $item['meta']['duration'] ) ? $item['meta']['duration'] : '۰۴:۰۰',
		'chip_label'    => ! empty( $item['taxonomies']['category']['name'] ) ? $item['taxonomies']['category']['name'] : 'مستند تصویری',
		'date'          => $item['date'] ?? '',
		'views'         => '1.2k',
	);
}

/**
 * تابع مرکزی دریافت اطلاعات صفحه سینگل با فال‌بک نرم و دفاعی
 *
 * @param WP_Post|int|null $post شیء یا شناسه پست (پیش‌فرض: پست فعلی).
 * @return array داده‌های نرمال‌شده Hybrid Contract.
 */
function revayat_get_single_data( $post = null ): array {
	$has_plugin_service = class_exists( 'Revayat_Data_Service' );

	if ( $has_plugin_service && method_exists( 'Revayat_Data_Service', 'get_single_data' ) ) {
		$data = Revayat_Data_Service::get_single_data( $post );
		if ( ! empty( $data ) && is_array( $data ) ) {
			return $data;
		}
	}

	// فال‌بک بومی با توابع استاندارد وردپرس (بدون Fatal Error در غیاب افزونه)
	$post_obj = get_post( $post );
	if ( ! $post_obj instanceof WP_Post ) {
		return array();
	}

	$id        = (int) $post_obj->ID;
	$post_type = $post_obj->post_type;

	// تشخیص سطح دسترسی امنیتی کاربر
	$user_clearance = revayat_get_user_clearance();

	// استخراج تاکسونومی سطح امنیتی
	$sec_terms      = taxonomy_exists( 'security_level' ) ? wp_get_object_terms( $id, 'security_level', array( 'fields' => 'slugs' ) ) : array();
	$security_level = ( ! empty( $sec_terms ) && ! is_wp_error( $sec_terms ) ) ? $sec_terms[0] : 'public';

	// نرمال‌سازی اسلاگ سطح امنیتی به مقدار کانونیکال
	if ( in_array( $security_level, array( 'classified', 'high', 'secret' ), true ) ) {
		$canonical_sec = 'classified';
	} elseif ( in_array( $security_level, array( 'restricted', 'normal', 'member' ), true ) ) {
		$canonical_sec = 'restricted';
	} else {
		$canonical_sec = 'public';
	}

	$is_sr_locked   = (bool) get_post_meta( $id, '_revayat_sr_is_locked', true );
	$custom_auth_url= (string) get_post_meta( $id, '_revayat_sr_auth_url', true );
	$auth_url       = $custom_auth_url ?: home_url( '/auth/?tab=login' );

	// ارزیابی دسترسی بومی در صورت نبود افزونه
	$can_view = true;
	if ( 'classified' === $canonical_sec && 'high' !== $user_clearance ) {
		$can_view = false;
	} elseif ( 'restricted' === $canonical_sec && 'guest' === $user_clearance ) {
		$can_view = false;
	} elseif ( $is_sr_locked && 'guest' === $user_clearance ) {
		$can_view = false;
	}

	$access = array(
		'can_view_content'         => $can_view,
		'can_download_attachments' => $can_view,
		'can_view_gallery'         => $can_view,
		'user_clearance'           => $user_clearance,
		'required_level'           => ( 'classified' === $canonical_sec ? 'high' : ( 'restricted' === $canonical_sec ? 'normal' : 'public' ) ),
		'security_level'           => $canonical_sec,
		'is_locked'                => ! $can_view,
		'lock_reason'              => ! $can_view ? __( 'مشاهده این بخش نیازمند عضویت و احراز هویت است.', 'revayatiran' ) : '',
		'auth_url'                 => $auth_url,
		'login_url'                => home_url( '/auth/?tab=login' ),
		'register_url'             => home_url( '/auth/?tab=register' ),
		'upgrade_url'              => home_url( '/auth/?tab=upgrade' ),
	);

	// متاداده دیده‌بان رسانه (Media Observatory Fallback)
	$obs_badge_slug = 'discourse';
	$obs_badge_terms = taxonomy_exists( 'observatory_badge' ) ? wp_get_object_terms( $id, 'observatory_badge', array( 'fields' => 'slugs' ) ) : array();
	if ( ! empty( $obs_badge_terms ) && ! is_wp_error( $obs_badge_terms ) ) {
		$obs_badge_slug = $obs_badge_terms[0];
	} else {
		$meta_slug = (string) get_post_meta( $id, '_revayat_obs_badge_slug', true );
		if ( $meta_slug ) {
			$obs_badge_slug = $meta_slug;
		}
	}

	$badge_labels = array(
		'discourse' => __( 'تحلیل گفتمان', 'revayatiran' ),
		'framing'   => __( 'مقایسه فریمینگ', 'revayatiran' ),
		'campaign'  => __( 'کمپین‌شناسی', 'revayatiran' ),
	);
	$badge_classes = array(
		'discourse' => 'obs-badge--discourse',
		'framing'   => 'obs-badge--framing',
		'campaign'  => 'obs-badge--campaign',
	);

	$obs_badge_label = $badge_labels[ $obs_badge_slug ] ?? __( 'تحلیل گفتمان', 'revayatiran' );
	$obs_badge_class = $badge_classes[ $obs_badge_slug ] ?? 'obs-badge--discourse';

	$frame_a = array(
		'source' => (string) get_post_meta( $id, '_revayat_obs_frame_a_source', true ),
		'text'   => (string) get_post_meta( $id, '_revayat_obs_frame_a_text', true ),
	);
	$frame_b = array(
		'source' => (string) get_post_meta( $id, '_revayat_obs_frame_b_source', true ),
		'text'   => (string) get_post_meta( $id, '_revayat_obs_frame_b_text', true ),
	);

	$narrative_summary = (string) get_post_meta( $id, '_revayat_obs_narrative_summary', true ) ?: (string) wp_strip_all_tags( get_the_excerpt( $post_obj ) );
	$monitoring_source = (string) get_post_meta( $id, '_revayat_obs_monitoring_source', true ) ?: ( (string) get_post_meta( $id, '_revayat_news_source', true ) ?: __( 'دیده‌بان رصد و پایش روایت', 'revayatiran' ) );

	// متاداده شبکه تحلیل‌گران (Analyst Post Fallback)
	$vote_summary = 'analyst_post' === $post_obj->post_type && class_exists( 'Revayat_Companion_User_Portal' ) ? Revayat_Companion_User_Portal::get_analyst_vote_summary( $id ) : array( 'count' => 0, 'average' => 0.0 );
	$analyst_score_val = $vote_summary['average'];
	$analyst_score_str = $vote_summary['count'] ? number_format( $analyst_score_val, 1 ) : '—';

	$featured_quote = (string) get_post_meta( $id, '_revayat_analyst_quote', true ) ?: ( (string) get_post_meta( $id, '_revayat_featured_quote', true ) ?: (string) wp_strip_all_tags( get_the_excerpt( $post_obj ) ) );
	$analysis_type  = (string) get_post_meta( $id, '_revayat_analysis_type', true ) ?: __( 'یادداشت تحلیلی', 'revayatiran' );
	$reading_depth  = (string) get_post_meta( $id, '_revayat_reading_depth', true ) ?: __( 'تحلیل راهبردی', 'revayatiran' );
	$key_takeaways  = (string) get_post_meta( $id, '_revayat_key_takeaways', true );
	$references     = (string) get_post_meta( $id, '_revayat_references', true );
	$analyst_votes  = $vote_summary['count'];

	$analyst_fields = taxonomy_exists( 'analyst_field' ) ? wp_get_object_terms( $id, 'analyst_field', array( 'fields' => 'all' ) ) : array();
	$field_slug = ( ! empty( $analyst_fields ) && ! is_wp_error( $analyst_fields ) ) ? $analyst_fields[0]->slug : 'geopolitics';
	$field_name = ( ! empty( $analyst_fields ) && ! is_wp_error( $analyst_fields ) ) ? $analyst_fields[0]->name : __( 'ژئوپلیتیک', 'revayatiran' );

	$author_person_terms = taxonomy_exists( 'person_author' ) ? wp_get_object_terms( $id, 'person_author', array( 'fields' => 'all' ) ) : array();
	$person_name = ( ! empty( $author_person_terms ) && ! is_wp_error( $author_person_terms ) ) ? $author_person_terms[0]->name : ( get_the_author_meta( 'display_name', $post_obj->post_author ) ?: __( 'کارشناس تحلیلی', 'revayatiran' ) );
	$person_slug = ( ! empty( $author_person_terms ) && ! is_wp_error( $author_person_terms ) ) ? $author_person_terms[0]->slug : sanitize_title( $person_name );

	return array(
		'id'             => $id,
		'entity'         => $post_obj,
		'title'          => (string) get_the_title( $post_obj ),
		'excerpt'        => (string) wp_strip_all_tags( get_the_excerpt( $post_obj ) ),
		'content'        => (string) $post_obj->post_content,
		'raw_content'    => (string) $post_obj->post_content,
		'permalink'      => (string) get_permalink( $post_obj ),
		'url'            => (string) get_permalink( $post_obj ),
		'date'           => (string) get_the_date( '', $post_obj ),
		'time_ago'       => (string) human_time_diff( get_the_time( 'U', $post_obj ), current_time( 'timestamp' ) ),
		'reading_time'   => function_exists( 'revayat_reading_time' ) ? revayat_reading_time( $id ) : '۵ دقیقه',
		'media'          => array(
			'id'        => (int) get_post_thumbnail_id( $post_obj ),
			'url'       => function_exists( 'revayat_get_post_image_url' ) ? revayat_get_post_image_url( $post_obj, 'revayat-hero-large', 'news' ) : get_the_post_thumbnail_url( $post_obj, 'large' ),
			'alt'       => (string) get_the_title( $post_obj ),
			'has_media' => has_post_thumbnail( $post_obj ),
		),
		'taxonomies'     => array(
			'dossier_topic'           => taxonomy_exists( 'dossier_topic' ) && ! is_wp_error( wp_get_object_terms( $id, 'dossier_topic', array( 'fields' => 'names' ) ) ) ? ( wp_get_object_terms( $id, 'dossier_topic', array( 'fields' => 'names' ) )[0] ?? 'پرونده ویژه' ) : 'پرونده ویژه',
			'security_level'          => $canonical_sec,
			'observatory_badge'       => $obs_badge_slug,
			'observatory_badge_label' => $obs_badge_label,
			'analyst_field'           => $field_name,
			'analyst_field_slug'      => $field_slug,
			'person_author'           => array(
				'name'         => $person_name,
				'role'         => (string) ( get_the_author_meta( 'headline', $post_obj->post_author ) ?: __( 'پژوهشگر ارشد مطالعات راهبردی', 'revayatiran' ) ),
				'avatar'       => get_avatar_url( $post_obj->post_author, array( 'size' => 96 ) ),
				'url'          => home_url( '/person/' . $person_slug . '/' ),
				'organization' => (string) ( get_the_author_meta( 'organization', $post_obj->post_author ) ?: __( 'مرکز مطالعات روایتی', 'revayatiran' ) ),
			),
		),
		'meta'           => array(
			'dossier_code'       => (string) get_post_meta( $id, '_revayat_dossier_code', true ),
			'lead_status'        => (string) get_post_meta( $id, '_revayat_lead_status', true ),
			'is_live'            => (bool) get_post_meta( $id, '_revayat_dossier_is_live', true ),
			'last_update'        => (string) get_post_meta( $id, '_revayat_dossier_last_update', true ),
			'editor_lead'        => (string) get_post_meta( $id, '_revayat_dossier_editor_lead', true ),
			'bulletin_type'      => (string) ( get_post_meta( $id, '_revayat_bulletin_type', true ) ?: 'برآورد راهبردی' ),
			'urgency_level'      => (string) ( get_post_meta( $id, '_revayat_urgency_level', true ) ?: 'عادی' ),
			'verification_status'=> (string) ( get_post_meta( $id, '_revayat_verification_status', true ) ?: 'تایید شده' ),
			'update_time'        => (string) ( get_post_meta( $id, '_revayat_update_time', true ) ?: human_time_diff( get_the_time( 'U', $post_obj ), current_time( 'timestamp' ) ) ),
			'is_locked'          => ! $can_view,
			'auth_url'           => $auth_url,
			'attachments'        => array(),
			'sensor_gallery'     => array( 'total_count' => 0, 'is_locked' => ! $can_view, 'items' => array() ),
			'stats'              => array(
				'docs'   => (int) get_post_meta( $id, '_revayat_stats_documents', true ),
				'frames' => (int) get_post_meta( $id, '_revayat_stats_frames', true ),
				'notes'  => (int) get_post_meta( $id, '_revayat_stats_notes', true ),
			),
			'timeline_events'    => (array) ( get_post_meta( $id, '_revayat_timeline_events', true ) ?: array() ),
			'frames'             => array( 'frame_a' => $frame_a, 'frame_b' => $frame_b ),
			'narrative_summary'  => $narrative_summary,
			'monitoring_source'  => $monitoring_source,
			'badge_slug'         => $obs_badge_slug,
			'badge_label'        => $obs_badge_label,
			'badge_class'        => $obs_badge_class,
			'analyst_score'      => $analyst_score_val,
			'analyst_quote'      => $featured_quote,
			'featured_quote'     => $featured_quote,
			'analysis_type'      => $analysis_type,
			'reading_depth'      => $reading_depth,
			'key_takeaways'      => $key_takeaways,
			'references'         => $references,
			'analyst_votes'      => $analyst_votes,
		),
		'access'             => $access,
		'attachments'        => array(),
		'sensor_gallery'     => array( 'total_count' => 0, 'is_locked' => ! $can_view, 'items' => array() ),
		'frame_a'            => $frame_a,
		'frame_b'            => $frame_b,
		'frames'             => array( 'frame_a' => $frame_a, 'frame_b' => $frame_b ),
		'summary'            => $narrative_summary,
		'narrative_summary'  => $narrative_summary,
		'monitoring_source'  => $monitoring_source,
		'badge_slug'         => $obs_badge_slug,
		'badge_label'        => $obs_badge_label,
		'badge_class'        => $obs_badge_class,
		'badge_css'          => $obs_badge_class,
		'score'              => $analyst_score_str,
		'analyst_score'      => $analyst_score_val,
		'featured_quote'     => $featured_quote,
		'analyst_quote'      => $featured_quote,
		'desc'               => $featured_quote,
		'analysis_type'      => $analysis_type,
		'reading_depth'      => $reading_depth,
		'key_takeaways'      => $key_takeaways,
		'references'         => $references,
		'analyst_votes'      => $analyst_votes,
		'votes_count'        => $analyst_votes,
		'field_slug'         => $field_slug,
		'field_name'         => $field_name,
		'field_label'        => $field_name,
		'author_name'        => $person_name,
		'author_role'        => (string) ( get_the_author_meta( 'headline', $post_obj->post_author ) ?: __( 'پژوهشگر ارشد مطالعات راهبردی', 'revayatiran' ) ),
		'author_avatar'      => get_avatar_url( $post_obj->post_author, array( 'size' => 96 ) ),
		'author_url'         => home_url( '/person/' . $person_slug . '/' ),
		'author_org'         => (string) ( get_the_author_meta( 'organization', $post_obj->post_author ) ?: __( 'مرکز مطالعات روایتی', 'revayatiran' ) ),
	);
}

/**
 * دریافت تخصصی اطلاعات بولتن تک اتاق وضعیت با تضمین ساختار کلیدها
 *
 * @param WP_Post|int|null $post شیء یا شناسه پست.
 * @return array داده‌های ساختاریافته بولتن.
 */
function revayat_get_situation_room_single_data( $post = null ): array {
	$data = revayat_get_single_data( $post );
	if ( empty( $data ) ) {
		return array();
	}

	// تضمین وجود کلیدهای اختصاصی اتاق وضعیت
	if ( ! isset( $data['access'] ) || ! is_array( $data['access'] ) ) {
		$data['access'] = array(
			'can_view_content'         => true,
			'can_download_attachments' => true,
			'can_view_gallery'         => true,
			'user_clearance'           => revayat_get_user_clearance(),
			'required_level'           => 'public',
			'security_level'           => 'public',
			'is_locked'                => false,
			'lock_reason'              => '',
			'auth_url'                 => home_url( '/auth/?tab=login' ),
			'login_url'                => home_url( '/auth/?tab=login' ),
			'register_url'             => home_url( '/auth/?tab=register' ),
			'upgrade_url'              => home_url( '/auth/?tab=upgrade' ),
		);
	}

	if ( ! isset( $data['attachments'] ) || ! is_array( $data['attachments'] ) ) {
		$data['attachments'] = $data['meta']['attachments'] ?? array();
	}

	if ( ! isset( $data['sensor_gallery'] ) || ! is_array( $data['sensor_gallery'] ) ) {
		$data['sensor_gallery'] = $data['meta']['sensor_gallery'] ?? array( 'total_count' => 0, 'is_locked' => false, 'items' => array() );
	}

	return $data;
}

/**
 * دریافت تخصصی اطلاعات تک‌دیده‌بان رسانه با تضمین ساختار کلیدها و فریم‌های تقابلی
 *
 * @param WP_Post|int|null $post شیء یا شناسه پست.
 * @return array داده‌های ساختاریافته دیده‌بان رسانه.
 */
function revayat_get_observatory_single_data( $post = null ): array {
	$data = revayat_get_single_data( $post );
	if ( empty( $data ) ) {
		return array();
	}

	// استخراج و تضمین فیلدهای اختصاصی دیده‌بان رسانه
	$id = ! empty( $data['id'] ) ? (int) $data['id'] : ( get_the_ID() ?: 0 );

	// تضمین تاکسونومی نشان دیده‌بان
	$raw_slug = $data['taxonomies']['observatory_badge'] ?? ( $data['badge_slug'] ?? '' );
	if ( empty( $raw_slug ) && $id ) {
		$terms = taxonomy_exists( 'observatory_badge' ) ? wp_get_object_terms( $id, 'observatory_badge', array( 'fields' => 'slugs' ) ) : array();
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			$raw_slug = $terms[0];
		}
	}
	if ( empty( $raw_slug ) ) {
		$raw_slug = 'discourse';
	}

	$badge_slug_map = array(
		'discourse'      => 'obs-badge--discourse',
		'framing'        => 'obs-badge--framing',
		'campaign'       => 'obs-badge--campaign',
		'تحلیل گفتمان'   => 'obs-badge--discourse',
		'مقایسه فریمینگ' => 'obs-badge--framing',
		'کمپین‌شناسی'    => 'obs-badge--campaign',
	);

	$badge_label_map = array(
		'discourse' => __( 'تحلیل گفتمان', 'revayatiran' ),
		'framing'   => __( 'مقایسه فریمینگ', 'revayatiran' ),
		'campaign'  => __( 'کمپین‌شناسی', 'revayatiran' ),
	);

	$badge_label = $data['taxonomies']['observatory_badge_label'] ?? ( $data['badge_label'] ?? ( $badge_label_map[ $raw_slug ] ?? __( 'تحلیل گفتمان', 'revayatiran' ) ) );
	$badge_class = $badge_slug_map[ $raw_slug ] ?? ( $badge_slug_map[ $badge_label ] ?? 'obs-badge--discourse' );

	// تضمین فریم‌ها
	$frames  = $data['meta']['frames'] ?? ( $data['frames'] ?? array() );
	$frame_a = $frames['frame_a'] ?? ( $data['frame_a'] ?? array( 'source' => '', 'text' => '' ) );
	$frame_b = $frames['frame_b'] ?? ( $data['frame_b'] ?? array( 'source' => '', 'text' => '' ) );

	if ( empty( $frame_a['text'] ) && $id ) {
		$fa_src = (string) get_post_meta( $id, '_revayat_obs_frame_a_source', true );
		$fa_txt = (string) get_post_meta( $id, '_revayat_obs_frame_a_text', true );
		if ( $fa_src || $fa_txt ) {
			$frame_a = array( 'source' => $fa_src, 'text' => $fa_txt );
		}
	}
	if ( empty( $frame_b['text'] ) && $id ) {
		$fb_src = (string) get_post_meta( $id, '_revayat_obs_frame_b_source', true );
		$fb_txt = (string) get_post_meta( $id, '_revayat_obs_frame_b_text', true );
		if ( $fb_src || $fb_txt ) {
			$frame_b = array( 'source' => $fb_src, 'text' => $fb_txt );
		}
	}

	$frames['frame_a'] = $frame_a;
	$frames['frame_b'] = $frame_b;

	// چکیده تحلیلی و منبع رصد
	$narrative_summary = $data['meta']['narrative_summary'] ?? ( $data['narrative_summary'] ?? ( $data['summary'] ?? ( $data['excerpt'] ?? '' ) ) );
	if ( empty( $narrative_summary ) && $id ) {
		$narrative_summary = (string) get_post_meta( $id, '_revayat_obs_narrative_summary', true ) ?: ( $data['excerpt'] ?? '' );
	}

	$monitoring_source = $data['meta']['monitoring_source'] ?? ( $data['monitoring_source'] ?? ( $data['taxonomies']['news_source'] ?? '' ) );
	if ( empty( $monitoring_source ) && $id ) {
		$monitoring_source = (string) get_post_meta( $id, '_revayat_obs_monitoring_source', true ) ?: ( (string) get_post_meta( $id, '_revayat_news_source', true ) ?: __( 'دیده‌بان رصد و پایش روایت', 'revayatiran' ) );
	}

	// اطلاعات تحلیل‌گر / نویسنده
	$author_data = $data['taxonomies']['person_author'] ?? array();
	if ( empty( $author_data['name'] ) && $id ) {
		$post_obj = get_post( $id );
		if ( $post_obj ) {
			$author_id   = $post_obj->post_author;
			$author_data = array(
				'name'   => get_the_author_meta( 'display_name', $author_id ) ?: __( 'تحریریه دیده‌بان', 'revayatiran' ),
				'role'   => __( 'تحلیل‌گر ارشد ارتباطات و رسانه', 'revayatiran' ),
				'avatar' => get_avatar_url( $author_id, array( 'size' => 80 ) ),
				'url'    => get_author_posts_url( $author_id ),
			);
		}
	}

	// یکپارچه‌سازی در آرایه خروجی
	$data['frames']            = $frames;
	$data['frame_a']           = $frame_a;
	$data['frame_b']           = $frame_b;
	$data['narrative_summary'] = $narrative_summary;
	$data['summary']           = $narrative_summary;
	$data['monitoring_source'] = $monitoring_source;
	$data['badge_slug']        = $raw_slug;
	$data['badge_label']       = $badge_label;
	$data['badge_class']       = $badge_class;
	$data['badge_css']         = $badge_class;

	$data['meta']['frames']            = $frames;
	$data['meta']['narrative_summary'] = $narrative_summary;
	$data['meta']['monitoring_source'] = $monitoring_source;

	$data['taxonomies']['observatory_badge']       = $raw_slug;
	$data['taxonomies']['observatory_badge_label'] = $badge_label;
	$data['taxonomies']['person_author']           = $author_data;

	return $data;
}

/**
 * دریافت تخصصی اطلاعات یادداشت تک شبکه تحلیل‌گران با تضمین ساختار کلیدها
 *
 * @param WP_Post|int|null $post شیء یا شناسه پست.
 * @return array داده‌های ساختاریافته یادداشت تحلیلی.
 */
function revayat_get_analyst_post_single_data( $post = null ): array {
	$data = revayat_get_single_data( $post );
	if ( empty( $data ) ) {
		return array();
	}

	$id = ! empty( $data['id'] ) ? (int) $data['id'] : ( get_the_ID() ?: 0 );

	// تضمین فیلدهای تخصصی
	if ( empty( $data['author_name'] ) && $id ) {
		$author_id = get_post_field( 'post_author', $id );
		$data['author_name']   = get_the_author_meta( 'display_name', $author_id ) ?: __( 'کارشناس تحلیلی', 'revayatiran' );
		$data['author_role']   = (string) ( get_the_author_meta( 'headline', $author_id ) ?: __( 'پژوهشگر ارشد مطالعات راهبردی', 'revayatiran' ) );
		$data['author_avatar'] = get_avatar_url( $author_id, array( 'size' => 96 ) );
		$data['author_url']    = get_author_posts_url( $author_id );
		$data['author_org']    = (string) ( get_the_author_meta( 'organization', $author_id ) ?: __( 'مرکز مطالعات روایتی', 'revayatiran' ) );
	}

	if ( empty( $data['score'] ) ) {
		$data['score'] = '—';
	}

	return $data;
}

/**
 * تشخیص امن سطح صلاحیت کاربر جاری بدون وابستگی به افزونه
 *
 * @param int|WP_User|null $user شیء یا شناسه کاربر.
 * @return string 'guest' | 'normal' | 'high'
 */
function revayat_get_user_clearance( $user = null ): string {
	$has_plugin_service = class_exists( 'Revayat_Data_Service' );

	if ( $has_plugin_service && method_exists( 'Revayat_Data_Service', 'get_user_clearance' ) ) {
		return Revayat_Data_Service::get_user_clearance( $user );
	}

	if ( null === $user ) {
		if ( ! is_user_logged_in() ) {
			return 'guest';
		}
		$user_obj = wp_get_current_user();
	} elseif ( is_numeric( $user ) ) {
		$user_obj = get_user_by( 'id', (int) $user );
	} elseif ( $user instanceof WP_User ) {
		$user_obj = $user;
	} else {
		return 'guest';
	}

	if ( ! $user_obj || ! $user_obj->exists() ) {
		return 'guest';
	}

	if (
		user_can( $user_obj, 'administrator' ) ||
		user_can( $user_obj, 'manage_options' ) ||
		in_array( 'vip_subscriber', (array) $user_obj->roles, true )
	) {
		return 'high';
	}

	return 'normal';
}

/**
 * تابع ایمن دریافت مطالب یا پرونده‌های مرتبط از طریق لایه سرویس
 *
 * @param int $post_id شناسه پست مبدا.
 * @param int $count   تعداد پست‌های درخواستی.
 * @return array
 */
function revayat_get_related_posts( $post_id = 0, int $count = 3 ): array {
	$has_plugin_service = class_exists( 'Revayat_Data_Service' );

	if ( $has_plugin_service && method_exists( 'Revayat_Data_Service', 'get_related_posts' ) ) {
		$related = Revayat_Data_Service::get_related_posts( $post_id, $count );
		if ( ! empty( $related ) && is_array( $related ) ) {
			return $related;
		}
	}

	// فال‌بک بومی برای مطالب مرتبط
	$post_id   = (int) ( $post_id ?: get_the_ID() );
	$post_type = get_post_type( $post_id ) ?: 'post';

	$query = new WP_Query( array(
		'post_type'      => $post_type,
		'posts_per_page' => $count,
		'post__not_in'   => array( $post_id ),
		'post_status'    => 'publish',
		'no_found_rows'  => true,
	) );

	$results = array();
	if ( ! empty( $query->posts ) ) {
		foreach ( $query->posts as $p ) {
			$results[] = revayat_get_single_data( $p );
		}
	}

	return $results;
}

/**
 * تابع ایمن دریافت داده‌های صفحات آرشیو از طریق لایه سرویس افزونه با فال‌بک بومی
 *
 * @param string $post_type نوع پست‌تایپ.
 * @param array  $args      آرگومان‌های صفحه‌بندی یا فیلتر.
 * @return array
 */
function revayat_get_archive_data( string $post_type, array $args = array() ): array {
	if ( 'situation_room' === $post_type && ! current_user_can( 'revayat_read_situation_room' ) && ! current_user_can( 'manage_options' ) ) {
		return array( 'items' => array(), 'total_posts' => 0, 'max_pages' => 0, 'current_page' => 1, 'access_denied' => true );
	}
	$has_plugin_service = class_exists( 'Revayat_Data_Service' );

	if ( $has_plugin_service && method_exists( 'Revayat_Data_Service', 'get_archive_data' ) ) {
		$data = Revayat_Data_Service::get_archive_data( $post_type, $args );
		if ( ! empty( $data ) && is_array( $data ) ) {
			return $data;
		}
	}

	// فال‌بک نرم و ایمن در صورت غیرفعال بودن افزونه
	global $wp_query;
	$items = array();
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			$items[] = revayat_get_single_data( get_the_ID() );
		}
	}

	$paged = isset( $args['paged'] ) ? max( 1, (int) $args['paged'] ) : max( 1, (int) get_query_var( 'paged', 1 ) );

	$result = array(
		'items'        => $items,
		'total_posts'  => $wp_query ? (int) $wp_query->found_posts : count( $items ),
		'max_pages'    => $wp_query ? (int) $wp_query->max_num_pages : 1,
		'current_page' => $paged,
	);

	if ( 'situation_room' === $post_type ) {
		$result['user_clearance'] = revayat_get_user_clearance();
		$result['filter_options'] = array(
			'security_levels' => array(
				array( 'slug' => 'all',        'label' => __( 'همه سطوح', 'revayatiran' ) ),
				array( 'slug' => 'public',     'label' => __( 'علنی (سطح ۳)', 'revayatiran' ) ),
				array( 'slug' => 'restricted', 'label' => __( 'محدود (سطح ۲)', 'revayatiran' ) ),
				array( 'slug' => 'classified', 'label' => __( 'طبقه‌بندی‌شده (سطح ۱)', 'revayatiran' ) ),
			),
		);
		$result['gated_meta']     = array(
			'auth_url'    => home_url( '/auth/?tab=register' ),
			'login_url'   => home_url( '/auth/?tab=login' ),
			'upgrade_url' => home_url( '/auth/?tab=upgrade' ),
			'notice'      => __( 'مشاهده بولتن‌های راهبردی و پدافندی منوط به عضویت و احراز هویت است.', 'revayatiran' ),
		);
	}

	if ( 'media_observatory' === $post_type ) {
		$active_badge = sanitize_key( $args['badge'] ?? ( $args['observatory_badge'] ?? ( get_query_var( 'observatory_badge' ) ?: ( $_GET['badge'] ?? 'all' ) ) ) );
		$archive_url  = get_post_type_archive_link( 'media_observatory' ) ?: home_url( '/observatory/' );

		$result['active_badge']   = $active_badge;
		$result['filter_options'] = array(
			'badges' => array(
				array(
					'slug'      => 'all',
					'label'     => __( 'همه واکاوی‌ها', 'revayatiran' ),
					'css_class' => 'obs-badge--all',
					'count'     => $result['total_posts'],
					'url'       => $archive_url,
				),
				array(
					'slug'      => 'discourse',
					'label'     => __( 'تحلیل گفتمان', 'revayatiran' ),
					'css_class' => 'obs-badge--discourse',
					'count'     => 0,
					'url'       => add_query_arg( 'badge', 'discourse', $archive_url ),
				),
				array(
					'slug'      => 'framing',
					'label'     => __( 'مقایسه فریمینگ', 'revayatiran' ),
					'css_class' => 'obs-badge--framing',
					'count'     => 0,
					'url'       => add_query_arg( 'badge', 'framing', $archive_url ),
				),
				array(
					'slug'      => 'campaign',
					'label'     => __( 'کمپین‌شناسی', 'revayatiran' ),
					'css_class' => 'obs-badge--campaign',
					'count'     => 0,
					'url'       => add_query_arg( 'badge', 'campaign', $archive_url ),
				),
			),
		);
	}

	if ( 'analyst_post' === $post_type ) {
		$active_field  = sanitize_key( $args['field'] ?? ( $args['analyst_field'] ?? ( get_query_var( 'analyst_field' ) ?: ( $_GET['field'] ?? 'all' ) ) ) );
		$active_sort   = sanitize_key( $args['sort'] ?? ( $_GET['sort'] ?? 'date' ) );
		$archive_url   = get_post_type_archive_link( 'analyst_post' ) ?: home_url( '/analysts/' );
		$base_sort_url = ( 'all' !== $active_field ) ? add_query_arg( 'field', $active_field, $archive_url ) : $archive_url;

		$result['active_field']   = $active_field;
		$result['active_sort']    = $active_sort;
		$result['filter_options'] = array(
			'fields' => array(
				array(
					'slug'  => 'all',
					'label' => __( 'همه حوزه‌ها', 'revayatiran' ),
					'count' => $result['total_posts'],
					'url'   => $archive_url,
				),
				array(
					'slug'  => 'geopolitics',
					'label' => __( 'ژئوپلیتیک', 'revayatiran' ),
					'count' => 0,
					'url'   => add_query_arg( 'field', 'geopolitics', $archive_url ),
				),
				array(
					'slug'  => 'political-economy',
					'label' => __( 'اقتصاد سیاسی', 'revayatiran' ),
					'count' => 0,
					'url'   => add_query_arg( 'field', 'political-economy', $archive_url ),
				),
				array(
					'slug'  => 'security',
					'label' => __( 'امنیت ملی', 'revayatiran' ),
					'count' => 0,
					'url'   => add_query_arg( 'field', 'security', $archive_url ),
				),
				array(
					'slug'  => 'media',
					'label' => __( 'رسانه و روایت', 'revayatiran' ),
					'count' => 0,
					'url'   => add_query_arg( 'field', 'media', $archive_url ),
				),
			),
			'sorts'  => array(
				array(
					'slug'      => 'date',
					'label'     => __( 'تازه‌ترین‌ها', 'revayatiran' ),
					'is_active' => ( 'date' === $active_sort ),
					'url'       => add_query_arg( 'sort', 'date', $base_sort_url ),
				),
				array(
					'slug'      => 'score',
					'label'     => __( 'برترین امتیاز', 'revayatiran' ),
					'is_active' => ( 'score' === $active_sort ),
					'url'       => add_query_arg( 'sort', 'score', $base_sort_url ),
				),
			),
		);
	}

	return $result;
}
