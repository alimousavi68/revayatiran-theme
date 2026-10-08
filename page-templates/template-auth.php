<?php
/** Template Name: ورود و عضویت */
if ( ! defined( 'ABSPATH' ) ) { exit; }
if ( is_user_logged_in() ) { wp_safe_redirect( home_url( '/dashboard/' ) ); exit; }
get_header(); ?>
<div class="rv-access-page"><div class="rv-access-shell"><?php get_template_part( 'template-parts/auth/form' ); ?></div></div>
<noscript><?php get_template_part( 'template-parts/auth/legacy' ); ?></noscript>
<?php get_footer();
