<?php
/**
 * Template Name: ورود و عضویت
 * Template Post Type: page
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
if ( is_user_logged_in() ) { wp_safe_redirect( home_url( '/dashboard/' ) ); exit; }
get_header();
$tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'login';
$notice = isset( $_GET['notice'] ) ? sanitize_key( wp_unslash( $_GET['notice'] ) ) : '';
$messages = array(
 'login_failed' => array( 'error', __( 'نام کاربری یا رمز عبور صحیح نیست.', 'revayatiran' ) ),
 'invalid_nonce' => array( 'error', __( 'درخواست منقضی شده است؛ دوباره تلاش کنید.', 'revayatiran' ) ),
 'invalid_registration' => array( 'error', __( 'نام، ایمیل معتبر و رمز حداقل ۸ کاراکتری لازم است.', 'revayatiran' ) ),
 'invalid_mobile' => array( 'error', __( 'شماره موبایل باید با ۰۹ شروع شود و ۱۱ رقم داشته باشد.', 'revayatiran' ) ),
 'invalid_otp' => array( 'error', __( 'کد تأیید واردشده صحیح نیست.', 'revayatiran' ) ),
 'otp_unavailable' => array( 'error', __( 'ثبت‌نام تا اتصال سرویس OTP در محیط عملیاتی غیرفعال است.', 'revayatiran' ) ),
 'email_exists' => array( 'error', __( 'قبلاً حسابی با این ایمیل ساخته شده است.', 'revayatiran' ) ),
 'mobile_exists' => array( 'error', __( 'قبلاً حسابی با این شماره موبایل ساخته شده است.', 'revayatiran' ) ),
 'registration_failed' => array( 'error', __( 'ساخت حساب انجام نشد؛ دوباره تلاش کنید.', 'revayatiran' ) ),
 'registration_pending' => array( 'success', __( 'درخواست عضویت ثبت شد و پس از بررسی مدیر فعال می‌شود.', 'revayatiran' ) ),
 'restricted_area' => array( 'error', __( 'برای مشاهده اتاق وضعیت ابتدا وارد شوید؛ حساب شما باید دسترسی ویژه تأییدشده داشته باشد.', 'revayatiran' ) ),
);
$test_otp = class_exists( 'Revayat_Companion_User_Portal' ) ? Revayat_Companion_User_Portal::get_test_otp() : '';
$redirect_to = isset( $_GET['redirect_to'] ) ? wp_validate_redirect( wp_unslash( $_GET['redirect_to'] ), '' ) : '';
?>
<div class="rv-portal"><div class="container rv-auth-shell">
 <section class="rv-auth-intro"><span class="rv-eyebrow"><i class="ph ph-shield-check"></i><?php esc_html_e( 'درگاه امن روایت ایران', 'revayatiran' ); ?></span><h1><?php esc_html_e( 'ورود به شبکه تحلیلگران', 'revayatiran' ); ?></h1><p><?php esc_html_e( 'تحلیلگران پس از تأیید حساب می‌توانند یادداشت‌های خود را برای بررسی تحریریه ارسال و وضعیت آن‌ها را دنبال کنند.', 'revayatiran' ); ?></p><ul><li><i class="ph ph-check-circle"></i><?php esc_html_e( 'ارسال امن یادداشت', 'revayatiran' ); ?></li><li><i class="ph ph-check-circle"></i><?php esc_html_e( 'پیگیری وضعیت بررسی', 'revayatiran' ); ?></li><li><i class="ph ph-check-circle"></i><?php esc_html_e( 'مدیریت پروفایل حرفه‌ای', 'revayatiran' ); ?></li></ul></section>
 <section class="rv-auth-card">
  <nav class="rv-auth-tabs"><a class="<?php echo 'register' !== $tab ? 'is-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'tab', 'login', get_permalink() ) ); ?>"><?php esc_html_e( 'ورود', 'revayatiran' ); ?></a><a class="<?php echo 'register' === $tab ? 'is-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'tab', 'register', get_permalink() ) ); ?>"><?php esc_html_e( 'درخواست عضویت', 'revayatiran' ); ?></a></nav>
  <?php if ( isset( $messages[ $notice ] ) ) : ?><div class="rv-notice rv-notice--<?php echo esc_attr( $messages[ $notice ][0] ); ?>"><?php echo esc_html( $messages[ $notice ][1] ); ?></div><?php endif; ?>
  <?php if ( 'register' === $tab ) : ?>
   <form class="rv-portal-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="revayat_register"><?php wp_nonce_field( 'revayat_register', 'revayat_register_nonce' ); ?><label><?php esc_html_e( 'نام و نام خانوادگی', 'revayatiran' ); ?><input required name="display_name" autocomplete="name"></label><label><?php esc_html_e( 'ایمیل', 'revayatiran' ); ?><input required type="email" name="email" autocomplete="email"></label><label><?php esc_html_e( 'شماره موبایل', 'revayatiran' ); ?><input required inputmode="numeric" pattern="09[0-9]{9}" maxlength="11" name="mobile" autocomplete="tel" placeholder="09123456789"></label><?php if ( $test_otp ) : ?><div class="rv-test-otp"><i class="ph ph-flask"></i><div><strong><?php esc_html_e( 'حالت آزمایشی OTP', 'revayatiran' ); ?></strong><span><?php esc_html_e( 'کد تأیید ثابت:', 'revayatiran' ); ?> <code><?php echo esc_html( $test_otp ); ?></code></span></div></div><?php endif; ?><label><?php esc_html_e( 'کد تأیید', 'revayatiran' ); ?><input required inputmode="numeric" pattern="[0-9]{6}" maxlength="6" name="otp_code" autocomplete="one-time-code"></label><label><?php esc_html_e( 'رمز عبور', 'revayatiran' ); ?><input required minlength="8" type="password" name="password" autocomplete="new-password"></label><button type="submit"><?php esc_html_e( 'ثبت درخواست تحلیلگری', 'revayatiran' ); ?></button><p class="rv-form-help"><?php esc_html_e( 'حساب ابتدا در وضعیت انتظار بررسی ساخته می‌شود.', 'revayatiran' ); ?></p></form>
  <?php else : ?>
   <form class="rv-portal-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="revayat_login"><?php if ( $redirect_to ) : ?><input type="hidden" name="redirect_to" value="<?php echo esc_url( $redirect_to ); ?>"><?php endif; ?><?php wp_nonce_field( 'revayat_login', 'revayat_login_nonce' ); ?><label><?php esc_html_e( 'نام کاربری یا ایمیل', 'revayatiran' ); ?><input required name="login" autocomplete="username"></label><label><?php esc_html_e( 'رمز عبور', 'revayatiran' ); ?><input required type="password" name="password" autocomplete="current-password"></label><label class="rv-checkbox"><input type="checkbox" name="remember" value="1"><span><?php esc_html_e( 'مرا به خاطر بسپار', 'revayatiran' ); ?></span></label><button type="submit"><?php esc_html_e( 'ورود به پیشخوان', 'revayatiran' ); ?></button></form>
  <?php endif; ?>
 </section>
</div></div>
<?php get_footer();
