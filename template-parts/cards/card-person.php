<?php
/** Person card. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$item = $args['item'] ?? $args;
if ( empty( $item['title'] ) ) { return; }
$meta = $item['meta'] ?? array();
$image = $item['media']['url'] ?? '';
?>
<article class="rv-person-card">
 <a class="rv-person-card__avatar" href="<?php echo esc_url( $item['permalink'] ?? '#' ); ?>"><?php if ( $image ) : ?><img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" loading="lazy"><?php else : ?><i class="ph ph-user"></i><?php endif; ?></a>
 <div><h2><a href="<?php echo esc_url( $item['permalink'] ?? '#' ); ?>"><?php echo esc_html( $item['title'] ); ?></a></h2><p class="rv-person-card__role"><?php echo esc_html( $meta['role_title'] ?? '' ); ?></p><p><?php echo esc_html( $meta['expertise'] ?? ( $item['excerpt'] ?? '' ) ); ?></p><div class="rv-person-card__meta"><?php if ( ! empty( $meta['organization'] ) ) : ?><span><i class="ph ph-buildings"></i><?php echo esc_html( $meta['organization'] ); ?></span><?php endif; ?><span><i class="ph-fill ph-star"></i><?php echo esc_html( revayat_en2fa( number_format( (float) ( $meta['person_total_score'] ?? 0 ), 1 ) ) ); ?></span></div></div>
</article>
