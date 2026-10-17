<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'login';
if ( ! in_array( $tab, array( 'login', 'password', 'recover' ), true ) ) { $tab = 'login'; }
$notice = isset( $_GET['notice'] ) ? sanitize_key( wp_unslash( $_GET['notice'] ) ) : '';
$messages = array(
	'login_failed' => array( 'error', __( 'مشخصات ورود صحیح نیست.', 'revayatiran' ) ),
	'account_suspended' => array( 'error', __( 'این حساب موقتاً غیرفعال است. برای پیگیری با مدیریت تماس بگیرید.', 'revayatiran' ) ),
	'invalid_nonce' => array( 'error', __( 'درخواست منقضی شده است؛ دوباره تلاش کنید.', 'revayatiran' ) ),
	'invalid_mobile' => array( 'error', __( 'شماره موبایل باید با ۰۹ شروع شود و ۱۱ رقم داشته باشد.', 'revayatiran' ) ),
	'invalid_otp' => array( 'error', __( 'کد تأیید واردشده صحیح نیست.', 'revayatiran' ) ),
	'otp_expired' => array( 'error', __( 'کد تأیید وجود ندارد یا منقضی شده است.', 'revayatiran' ) ),
	'otp_attempts_exceeded' => array( 'error', __( 'تعداد تلاش ناموفق بیش از حد مجاز است؛ کد تازه درخواست کنید.', 'revayatiran' ) ),
	'otp_rate_limited' => array( 'error', __( 'درخواست کد بیش از حد مجاز است؛ کمی بعد دوباره تلاش کنید.', 'revayatiran' ) ),
	'otp_gateway_unconfigured' => array( 'error', __( 'درگاه پیامک هنوز پیکربندی نشده است.', 'revayatiran' ) ),
	'otp_gateway_rejected' => array( 'error', __( 'ارسال پیامک انجام نشد؛ دوباره تلاش کنید.', 'revayatiran' ) ),
	'otp_disabled' => array( 'error', __( 'ارسال کد یک‌بارمصرف موقتاً غیرفعال است.', 'revayatiran' ) ),
	'otp_send_failed' => array( 'error', __( 'ارتباط با سرویس پیامک برقرار نشد؛ دوباره تلاش کنید.', 'revayatiran' ) ),
	'invalid_otp_gateway' => array( 'error', __( 'تنظیمات درگاه پیامک معتبر نیست.', 'revayatiran' ) ),
	'otp_sent' => array( 'success', __( 'کد تأیید ارسال شد. تا ۲ دقیقه فرصت دارید کد را وارد کنید.', 'revayatiran' ) ),
	'registration_failed' => array( 'error', __( 'ورود و ثبت‌نام انجام نشد؛ دوباره تلاش کنید.', 'revayatiran' ) ),
	'invalid_password' => array( 'error', __( 'رمز تازه باید حداقل ۸ کاراکتر باشد.', 'revayatiran' ) ),
	'mobile_not_found' => array( 'error', __( 'امکان بازیابی حساب با اطلاعات واردشده وجود ندارد.', 'revayatiran' ) ),
	'password_reset' => array( 'success', __( 'رمز عبور تغییر کرد؛ اکنون وارد شوید.', 'revayatiran' ) ),
	'restricted_area' => array( 'error', __( 'برای مشاهده اتاق وضعیت ابتدا وارد شوید؛ حساب شما باید دسترسی ویژه تأییدشده داشته باشد.', 'revayatiran' ) ),
);
$test_otp = class_exists( 'Revayat_Companion_User_Portal' ) ? Revayat_Companion_User_Portal::get_test_otp() : '';
$redirect_to = isset( $_GET['redirect_to'] ) ? wp_validate_redirect( wp_unslash( $_GET['redirect_to'] ), '' ) : '';
$mobile = isset( $_GET['mobile'] ) && class_exists( 'Revayat_Companion_User_Portal' ) ? Revayat_Companion_User_Portal::normalize_mobile( wp_unslash( $_GET['mobile'] ) ) : '';
$code_step = preg_match( '/^09\d{9}$/', $mobile ) && in_array( $notice, array( 'otp_sent', 'invalid_otp', 'otp_expired', 'otp_attempts_exceeded' ), true );
?>
<div class="rv-portal"><div class="container rv-auth-shell">
	<section class="rv-auth-card">
		<nav class="rv-auth-tabs" aria-label="<?php esc_attr_e( 'روش‌های ورود', 'revayatiran' ); ?>"><a class="<?php echo 'login' === $tab ? 'is-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'tab', 'login', get_permalink() ) ); ?>"><?php esc_html_e( 'ورود با کد', 'revayatiran' ); ?></a><a class="<?php echo 'password' === $tab ? 'is-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'tab', 'password', get_permalink() ) ); ?>"><?php esc_html_e( 'ورود با رمز', 'revayatiran' ); ?></a><a class="<?php echo 'recover' === $tab ? 'is-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'tab', 'recover', get_permalink() ) ); ?>"><?php esc_html_e( 'بازیابی رمز', 'revayatiran' ); ?></a></nav>
		<?php if ( isset( $messages[ $notice ] ) ) : ?><div class="rv-notice rv-notice--<?php echo esc_attr( $messages[ $notice ][0] ); ?>" role="status"><?php echo esc_html( $messages[ $notice ][1] ); ?></div><?php endif; ?>
		<?php if ( 'recover' === $tab ) : ?>
			<?php if ( ! $code_step ) : ?>
			<form class="rv-portal-form rv-otp-request" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="revayat_request_otp"><input type="hidden" name="otp_context" value="recover"><?php wp_nonce_field( 'revayat_request_otp', 'revayat_otp_nonce' ); ?><label><?php esc_html_e( 'شماره موبایل حساب', 'revayatiran' ); ?><input required inputmode="numeric"  maxlength="11" name="mobile" value="<?php echo esc_attr( $mobile ); ?>" autocomplete="tel"></label><button type="submit"><?php esc_html_e( 'ارسال کد بازیابی', 'revayatiran' ); ?></button></form>
			<?php endif; ?>
			<?php if ( $code_step ) : ?>
			<form class="rv-portal-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="revayat_recover_account"><?php wp_nonce_field( 'revayat_recover_account', 'revayat_recover_nonce' ); ?><input type="hidden" name="mobile" value="<?php echo esc_attr( $mobile ); ?>"><p class="rv-form-help"><?php printf( esc_html__( 'کد ارسال‌شده به %s را وارد کنید.', 'revayatiran' ), esc_html( $mobile ) ); ?></p><label><?php esc_html_e( 'کد تأیید', 'revayatiran' ); ?><input required inputmode="numeric" pattern="[0-9۰-۹٠-٩]{6}" maxlength="6" name="otp_code" autocomplete="one-time-code"></label><label><?php esc_html_e( 'رمز عبور تازه', 'revayatiran' ); ?><input required minlength="8" type="password" name="password" autocomplete="new-password"></label><button type="submit"><?php esc_html_e( 'تغییر رمز عبور', 'revayatiran' ); ?></button></form>
			<p class="rv-auth-back"><a href="<?php echo esc_url( add_query_arg( 'tab', $tab, get_permalink() ) ); ?>"><?php esc_html_e( 'تغییر شماره یا دریافت کد جدید', 'revayatiran' ); ?></a></p>
			<?php endif; ?>
		<?php elseif ( 'password' === $tab ) : ?>
			<form class="rv-portal-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="revayat_login"><?php if ( $redirect_to ) : ?><input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>"><?php endif; ?><?php wp_nonce_field( 'revayat_login', 'revayat_login_nonce' ); ?><label><?php esc_html_e( 'موبایل، نام کاربری یا ایمیل', 'revayatiran' ); ?><input required name="login" autocomplete="username"></label><label><?php esc_html_e( 'رمز عبور', 'revayatiran' ); ?><input required type="password" name="password" autocomplete="current-password"></label><label class="rv-checkbox"><input type="checkbox" name="remember" value="1"><span><?php esc_html_e( 'مرا به خاطر بسپار', 'revayatiran' ); ?></span></label><button type="submit"><?php esc_html_e( 'ورود به پیشخوان', 'revayatiran' ); ?></button></form>
		<?php else : ?>
			<?php if ( ! $code_step ) : ?>
			<form class="rv-portal-form rv-otp-request" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="revayat_request_otp"><input type="hidden" name="otp_context" value="auth"><?php if ( $redirect_to ) : ?><input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>"><?php endif; ?><?php wp_nonce_field( 'revayat_request_otp', 'revayat_otp_nonce' ); ?><label><?php esc_html_e( 'شماره موبایل', 'revayatiran' ); ?><input required inputmode="numeric"  maxlength="11" name="mobile" value="<?php echo esc_attr( $mobile ); ?>" autocomplete="tel" placeholder="09123456789"></label><button type="submit"><?php esc_html_e( 'دریافت کد ورود', 'revayatiran' ); ?></button></form>
			<?php endif; ?>
			<?php if ( $code_step ) : ?>
			<form class="rv-portal-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="revayat_otp_auth"><?php if ( $redirect_to ) : ?><input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>"><?php endif; ?><?php wp_nonce_field( 'revayat_otp_auth', 'revayat_otp_auth_nonce' ); ?><input type="hidden" name="mobile" value="<?php echo esc_attr( $mobile ); ?>"><p class="rv-form-help"><?php printf( esc_html__( 'کد ارسال‌شده به %s را وارد کنید.', 'revayatiran' ), esc_html( $mobile ) ); ?></p><?php if ( $test_otp ) : ?><div class="rv-test-otp"><i class="ph ph-flask"></i><div><strong><?php esc_html_e( 'حالت آزمایشی OTP', 'revayatiran' ); ?></strong><span><?php esc_html_e( 'کد تأیید ثابت:', 'revayatiran' ); ?> <code><?php echo esc_html( $test_otp ); ?></code></span></div></div><?php endif; ?><label><?php esc_html_e( 'کد تأیید', 'revayatiran' ); ?><input required inputmode="numeric" pattern="[0-9۰-۹٠-٩]{6}" maxlength="6" name="otp_code" autocomplete="one-time-code"></label><label class="rv-checkbox"><input type="checkbox" name="remember" value="1" checked><span><?php esc_html_e( 'مرا به خاطر بسپار', 'revayatiran' ); ?></span></label><button type="submit"><?php esc_html_e( 'تأیید و ورود', 'revayatiran' ); ?></button></form>
			<p class="rv-auth-back"><a href="<?php echo esc_url( add_query_arg( 'tab', $tab, get_permalink() ) ); ?>"><?php esc_html_e( 'تغییر شماره یا دریافت کد جدید', 'revayatiran' ); ?></a></p>
			<?php endif; ?>
		<?php endif; ?>
		<?php
		$support_links = class_exists( 'Revayat_Companion_SMS_Settings' ) ? Revayat_Companion_SMS_Settings::support_links() : array(
			'text'     => 'در صورت عدم دریافت پیامک یا نیاز به راهنمایی، از طریق پیام‌رسان‌ها با ما در ارتباط باشید:',
			'bale'     => 'https://ble.ir/revayatiran',
			'eitaa'    => 'https://eitaa.com/revayatiran',
			'telegram' => 'https://t.me/revayatiran',
		);
		?>
		<aside class="rv-access-support" style="margin-top:24px;padding-top:16px;border-top:1px solid #e2e8f0;text-align:center;">
			<p style="font-size:12px;color:#64748b;margin-bottom:12px;"><i class="ph ph-headset"></i> <?php echo esc_html( $support_links['text'] ); ?></p>
			<div style="display:flex;justify-content:center;gap:10px;flex-wrap:wrap;">
				<?php if ( ! empty( $support_links['bale'] ) ) : ?>
					<a href="<?php echo esc_url( $support_links['bale'] ); ?>" target="_blank" rel="noopener noreferrer" style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:20px;font-size:12px;text-decoration:none;background:#f1f5f9;color:#0f172a;">
						<span>پشتیبانی بله</span>
					</a>
				<?php endif; ?>
				<?php if ( ! empty( $support_links['eitaa'] ) ) : ?>
					<a href="<?php echo esc_url( $support_links['eitaa'] ); ?>" target="_blank" rel="noopener noreferrer" style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:20px;font-size:12px;text-decoration:none;background:#f1f5f9;color:#0f172a;">
						<span>پشتیبانی ایتا</span>
					</a>
				<?php endif; ?>
				<?php if ( ! empty( $support_links['telegram'] ) ) : ?>
					<a href="<?php echo esc_url( $support_links['telegram'] ); ?>" target="_blank" rel="noopener noreferrer" style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:20px;font-size:12px;text-decoration:none;background:#f1f5f9;color:#0f172a;">
						<span>پشتیبانی تلگرام</span>
					</a>
				<?php endif; ?>
			</div>
		</aside>
	</section>
</div></div>
