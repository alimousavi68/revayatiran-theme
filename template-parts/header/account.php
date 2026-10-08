<?php
/**
 * Quick user account menu and authentication trigger.
 *
 * @package RevayatIran
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php if ( is_user_logged_in() ) : $account = wp_get_current_user(); ?>
<details class="rv-header-account">
	<summary aria-label="<?php esc_attr_e( 'منوی حساب کاربری', 'revayatiran' ); ?>">
		<?php echo get_avatar( $account->ID, 32 ); ?>
		<span><?php echo esc_html( $account->display_name ); ?></span>
		<i class="ph ph-caret-down" aria-hidden="true"></i>
	</summary>
	<nav aria-label="<?php esc_attr_e( 'دسترسی سریع حساب', 'revayatiran' ); ?>">
		<strong><?php echo esc_html( $account->display_name ); ?></strong>
		<a href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>">
			<i class="ph ph-squares-four" aria-hidden="true"></i>
			<span><?php esc_html_e( 'پنل کاربری', 'revayatiran' ); ?></span>
		</a>
		<a href="<?php echo esc_url( home_url( '/dashboard/?view=profile' ) ); ?>">
			<i class="ph ph-user-gear" aria-hidden="true"></i>
			<span><?php esc_html_e( 'ویرایش اطلاعات و تصویر', 'revayatiran' ); ?></span>
		</a>
		<a href="<?php echo esc_url( home_url( '/dashboard/?view=requests' ) ); ?>">
			<i class="ph ph-files" aria-hidden="true"></i>
			<span><?php esc_html_e( 'درخواست‌های من', 'revayatiran' ); ?></span>
		</a>
		<?php if ( current_user_can( 'revayat_submit_analyst_post' ) ) : ?>
		<a href="<?php echo esc_url( home_url( '/dashboard/?view=note-edit' ) ); ?>">
			<i class="ph ph-pen-nib" aria-hidden="true"></i>
			<span><?php esc_html_e( 'افزودن یادداشت', 'revayatiran' ); ?></span>
		</a>
		<?php endif; ?>
		<a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>">
			<i class="ph ph-sign-out" aria-hidden="true"></i>
			<span><?php esc_html_e( 'خروج از حساب', 'revayatiran' ); ?></span>
		</a>
	</nav>
</details>
<?php else : ?>
<a class="rv-header-login" href="<?php echo esc_url( home_url( '/auth/' ) ); ?>">
	<i class="ph ph-user-circle-plus" aria-hidden="true"></i>
	<span><?php esc_html_e( 'ورود / عضویت', 'revayatiran' ); ?></span>
</a>
<?php endif; ?>
