<?php
/**
 * توابع کمکی نمایش و رندرینگ تمپلیت‌ها (Template Tags)
 *
 * @package RevayatIran
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('revayat_posted_on')) {
    /**
     * رندر تاریخ انتشار به فرمت انسانی یا استاندارد
     */
    function revayat_posted_on($format = '') {
        $time_string = sprintf(
            '<time class="entry-date published updated" datetime="%1$s">%2$s</time>',
            esc_attr(get_the_date(DATE_W3C)),
            esc_html(get_the_date($format))
        );
        echo $time_string; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}

if (!function_exists('revayat_reading_time')) {
    /**
     * محاسبه تخمینی زمان مطالعه محتوا بر اساس کلمات
     */
    function revayat_reading_time($post_id = null) {
        $post = get_post($post_id);
        if (!$post) {
            return '۳ دقیقه';
        }

        $word_count = mb_strlen(strip_tags($post->post_content), 'UTF-8') / 5; // تخمین واژگان فارسی
        $reading_time = ceil($word_count / 200); // میانگین ۲۰۰ کلمه در دقیقه
        $reading_time = max(1, $reading_time);

        return sprintf(esc_html__('%s دقیقه مطالعه', 'revayatiran'), number_format_i18n($reading_time));
    }
}

if (!function_exists('revayat_get_primary_category')) {
    /**
     * دریافت دسته‌بندی اولیه نوشته
     */
    function revayat_get_primary_category($post_id = null) {
        $categories = get_the_category($post_id);
        if (!empty($categories) && !is_wp_error($categories)) {
            return $categories[0];
        }
        return null;
    }
}

if ( ! function_exists( 'revayat_render_home_empty_state' ) ) {
	/** Render the shared, production-safe empty state for homepage sections. */
	function revayat_render_home_empty_state( $message = '' ) {
		$message = $message ?: __( 'هنوز محتوایی برای نمایش در این بخش منتشر نشده است.', 'revayatiran' );
		?>
		<div class="rv-home-empty" role="status">
			<i class="ph ph-file-dashed" aria-hidden="true"></i>
			<p><?php echo esc_html( $message ); ?></p>
		</div>
		<?php
	}
}

if ( ! function_exists( 'revayat_get_post_origin_label' ) ) {
	/**
	 * تشخیص و دریافت برچسب بخش مبدأ نوشته
	 * (روایت روز، پرونده ویژه، شبکه تحلیلگران، اتاق وضعیت، رصد روز، دیدبان رسانه)
	 *
	 * @param mixed $item شیء پست، شناسه عددی یا آرایه هیبریدی.
	 * @return string برچسب بخش مبدأ.
	 */
	function revayat_get_post_origin_label( $item = null ): string {
		$post_id      = 0;
		$post_type    = '';
		$raw_category = '';
		$placement    = '';
		$news_source  = '';

		if ( is_array( $item ) ) {
			$post_id      = ! empty( $item['id'] ) ? absint( $item['id'] ) : 0;
			$raw_category = $item['category'] ?? '';
			$placement    = $item['tag_label'] ?? ( $item['taxonomies']['editorial_placement'] ?? '' );
			$news_source  = $item['source_badge'] ?? ( $item['source_label'] ?? ( $item['taxonomies']['news_source'] ?? '' ) );
			if ( isset( $item['entity'] ) && $item['entity'] instanceof WP_Post ) {
				$post_type = $item['entity']->post_type;
			} elseif ( ! empty( $item['post_type'] ) ) {
				$post_type = $item['post_type'];
			}
		} elseif ( $item instanceof WP_Post ) {
			$post_id   = $item->ID;
			$post_type = $item->post_type;
		} elseif ( is_numeric( $item ) && (int) $item > 0 ) {
			$post_id = absint( $item );
		} else {
			$post = get_post();
			if ( $post instanceof WP_Post ) {
				$post_id   = $post->ID;
				$post_type = $post->post_type;
			}
		}

		if ( $post_id > 0 && empty( $post_type ) ) {
			$post_type = (string) get_post_type( $post_id );
		}

		// ۱. نگاشت بر اساس Post Type اختصاصی
		$post_type_map = array(
			'special_dossier'   => 'پرونده ویژه',
			'analyst_post'      => 'شبکه تحلیلگران',
			'situation_room'    => 'اتاق وضعیت',
			'media_observatory' => 'دیدبان رسانه',
			'multimedia'        => 'رسانه‌نگار',
		);

		if ( ! empty( $post_type ) && isset( $post_type_map[ $post_type ] ) ) {
			return $post_type_map[ $post_type ];
		}

		// ۲. ارزیابی تاکسونومی‌های اختصاصی
		if ( $post_id > 0 ) {
			if ( taxonomy_exists( 'dossier_topic' ) && has_term( '', 'dossier_topic', $post_id ) ) {
				return 'پرونده ویژه';
			}
			if ( taxonomy_exists( 'analyst_field' ) && has_term( '', 'analyst_field', $post_id ) ) {
				return 'شبکه تحلیلگران';
			}
			if ( taxonomy_exists( 'security_level' ) && has_term( '', 'security_level', $post_id ) ) {
				return 'اتاق وضعیت';
			}
			if ( taxonomy_exists( 'observatory_badge' ) && has_term( '', 'observatory_badge', $post_id ) ) {
				return 'دیدبان رسانه';
			}
			if ( taxonomy_exists( 'media_format' ) && has_term( '', 'media_format', $post_id ) ) {
				return 'رسانه‌نگار';
			}
			if ( taxonomy_exists( 'news_source' ) && has_term( '', 'news_source', $post_id ) ) {
				return 'رصد روز';
			}
			if ( taxonomy_exists( 'editorial_placement' ) ) {
				if ( has_term( array( 'daily_lead', 'daily_side' ), 'editorial_placement', $post_id ) ) {
					return 'روایت روز';
				}
			}
		}

		// ۳. بررسی فیلدهای نرمال‌شده قرارداد
		if ( ! empty( $news_source ) ) {
			return 'رصد روز';
		}

		if ( in_array( $placement, array( 'daily_lead', 'daily_side' ), true ) ) {
			return 'روایت روز';
		}

		// ۴. ارزیابی هوشمند نام دسته‌بندی موضوعی
		$check_string = mb_strtolower( (string) $raw_category );
		if ( $post_id > 0 && empty( $check_string ) ) {
			$cats = get_the_category( $post_id );
			if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) {
				$check_string = mb_strtolower( $cats[0]->name . ' ' . $cats[0]->slug );
			}
		}

		if ( str_contains( $check_string, 'پرونده' ) || str_contains( $check_string, 'dossier' ) ) {
			return 'پرونده ویژه';
		}
		if ( str_contains( $check_string, 'دیدبان' ) || str_contains( $check_string, 'دیده‌بان' ) || str_contains( $check_string, 'observatory' ) ) {
			return 'دیدبان رسانه';
		}
		if ( str_contains( $check_string, 'تحلیلگر' ) || str_contains( $check_string, 'analyst' ) ) {
			return 'شبکه تحلیلگران';
		}
		if ( str_contains( $check_string, 'وضعیت' ) || str_contains( $check_string, 'situation' ) ) {
			return 'اتاق وضعیت';
		}
		if ( str_contains( $check_string, 'رصد' ) || str_contains( $check_string, 'news' ) || str_contains( $check_string, 'خبر' ) ) {
			return 'رصد روز';
		}
		if ( str_contains( $check_string, 'رسانه' ) || str_contains( $check_string, 'multimedia' ) ) {
			return 'رسانه‌نگار';
		}

		// ۵. پیش‌فرض برای مطالب تحریریه
		return 'روایت روز';
	}
}

/** Decide whether multimedia introduction is already present in authored content. */
function revayat_media_intro_visibility(string $content, string $cover, string $excerpt): array {
    $normalize = static function ($text) {
        return trim(preg_replace('/\s+/u', ' ', html_entity_decode(wp_strip_all_tags($text), ENT_QUOTES, 'UTF-8')));
    };
    $photo_key = static function ($url) {
        $path = wp_parse_url(html_entity_decode($url, ENT_QUOTES, 'UTF-8'), PHP_URL_PATH) ?: '';
        $host = strtolower(wp_parse_url($url, PHP_URL_HOST) ?: wp_parse_url(home_url(), PHP_URL_HOST));
        return $host . preg_replace('/-\d+x\d+(?=\.[a-z0-9]+$)/i', '', $path);
    };
    $show_cover = (bool) $cover;
    if ($cover && preg_match_all('/<img\b[^>]*\bsrc\s*=\s*([\'\"])(.*?)\1/is', $content, $images)) {
        foreach ($images[2] as $src) {
            if ($photo_key($src) === $photo_key($cover)) { $show_cover = false; break; }
        }
    }
    $show_excerpt = (bool) $normalize($excerpt);
    if (preg_match('/<p\b[^>]*>(.*?)<\/p>/is', $content, $paragraph)) {
        if ($normalize($paragraph[1]) === $normalize($excerpt)) { $show_excerpt = false; }
    }
    return ['cover' => $show_cover, 'excerpt' => $show_excerpt];
}

/** Keep public reading-page forms compact while preserving native validation/cookies. */
add_filter('comment_form_default_fields', function ($fields) {
    if (is_singular(['post', 'analyst_post', 'multimedia', 'special_dossier'])) { unset($fields['url'], $fields['cookies']); }
    return $fields;
});
