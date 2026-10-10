<?php
/** Template Name: پیشخوان کاربری */
if ( ! defined( 'ABSPATH' ) ) { exit; }
if ( ! is_user_logged_in() ) { wp_safe_redirect( add_query_arg( 'redirect_to', home_url( '/dashboard/' ), home_url( '/auth/' ) ) ); exit; }
if ( ! defined( 'DONOTCACHEPAGE' ) ) { define( 'DONOTCACHEPAGE', true ); }
do_action( 'litespeed_control_set_nocache', 'Private member dashboard' );
nocache_headers();
if ( ! class_exists( 'Revayat_Companion_Member_Profile' ) ) { get_header(); echo '<div class="container">پنل کاربری موقتاً در دسترس نیست.</div>'; get_footer(); return; }
$user = wp_get_current_user();
$views = array( 'overview' => array( 'نمای کلی', 'house' ), 'profile' => array( 'اطلاعات و تصویر من', 'user-circle' ), 'requests' => array( 'درخواست‌های من', 'clipboard-text' ), 'notifications' => array( 'اعلان‌ها', 'bell' ), 'security' => array( 'امنیت حساب', 'shield-check' ) );
if ( current_user_can( 'revayat_submit_analyst_post' ) ) { $views['notes'] = array( 'یادداشت‌های من', 'files' ); $views['note-edit'] = array( 'یادداشت تازه', 'note-pencil' ); }
if ( current_user_can( 'revayat_review_notes' ) || current_user_can( 'edit_others_posts' ) ) { $views['review'] = array( 'کارتابل بررسی', 'list-checks' ); }
$allowed = array_merge( array_keys( $views ), array( 'analyst-request', 'situation-access' ) );
$view = sanitize_key( $_GET['view'] ?? ( isset( $_GET['edit_submission'] ) ? 'note-edit' : 'overview' ) );
if ( ! in_array( $view, $allowed, true ) ) { $view = 'overview'; }
$title = $views[ $view ][0] ?? ( 'analyst-request' === $view ? 'درخواست تحلیلگری' : 'درخواست اتاق وضعیت' );
$flash = get_transient( 'rv_portal_flash_' . $user->ID ); delete_transient( 'rv_portal_flash_' . $user->ID );
get_header(); ?>
<div class="rv-member-page"><div class="container rv-member-layout">
    <aside class="rv-member-nav" aria-label="منوی پنل">
        <div class="rv-member-identity"><?php echo get_avatar($user->ID, 72); ?><strong><?php echo esc_html($user->display_name); ?></strong><span><?php echo current_user_can('revayat_submit_analyst_post') ? 'تحلیلگر' : 'عضو روایت ایران'; ?></span></div>
        <nav class="rv-member-menu" aria-label="بخش‌های حساب">
            <div class="rv-member-menu__primary">
                <?php foreach (array('overview' => 'نمای کلی', 'profile' => 'پروفایل', 'notifications' => 'اعلان‌ها') as $key => $short_label) : $item = $views[$key]; ?>
                    <a href="<?php echo esc_url(home_url('/dashboard/?view=' . $key)); ?>" <?php if ($view === $key) { echo 'aria-current="page"'; } ?> aria-label="<?php echo esc_attr($item[0]); ?>">
                        <i class="ph ph-<?php echo esc_attr($item[1]); ?>" aria-hidden="true"></i>
                        <span class="rv-member-menu__full-label"><?php echo esc_html($item[0]); ?></span><span class="rv-member-menu__short-label"><?php echo esc_html($short_label); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
            <details open class="rv-member-menu__more<?php echo !in_array($view, array('overview', 'profile', 'notifications'), true) ? ' is-current' : ''; ?>">
                <summary aria-label="<?php echo esc_attr(!in_array($view, array('overview', 'profile', 'notifications'), true) ? 'بخش‌های بیشتر؛ بخش فعلی: ' . $title : 'بخش‌های بیشتر حساب'); ?>"><span class="rv-member-menu__more-icon" aria-hidden="true">⋯</span><span>بیشتر</span></summary>
                <div class="rv-member-menu__secondary">
                    <?php foreach (array('notes', 'note-edit', 'requests', 'review', 'security') as $key) : if (!isset($views[$key])) { continue; } $item = $views[$key]; ?>
                        <a href="<?php echo esc_url(home_url('/dashboard/?view=' . $key)); ?>" <?php if ($view === $key) { echo 'aria-current="page"'; } ?>><i class="ph ph-<?php echo esc_attr($item[1]); ?>" aria-hidden="true"></i><span><?php echo esc_html($item[0]); ?></span></a>
                    <?php endforeach; ?>
                    <a class="rv-member-menu__logout" href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>"><i class="ph ph-sign-out" aria-hidden="true"></i><span>خروج از حساب</span></a>
                </div>
            </details>
        </nav>

    </aside>
    <section class="rv-member-main"><header class="rv-member-heading"><span>پنل شخصی</span><h1><?php echo esc_html( $title ); ?></h1><p><?php echo esc_html( 'profile' === $view ? 'اطلاعات حساب و نحوه معرفی خود را مدیریت کنید.' : 'از اینجا وضعیت و اقدام بعدی خود را دنبال کنید.' ); ?></p></header>
    <?php if ( $flash ) : ?><div class="rv-member-notice" role="status"><?php echo esc_html( $flash['message'] ); ?></div><?php endif; ?>
    <?php if ( isset( $_GET['notice'] ) ) : ?><p class="rv-member-notice" role="status"><?php echo esc_html( array( 'password_updated' => 'رمز عبور ذخیره شد.', 'invalid_password' => 'رمز و تکرار آن معتبر نیست.', 'notifications_read' => 'اعلان‌ها خوانده شدند.' )[ sanitize_key( $_GET['notice'] ) ] ?? 'وضعیت حساب به‌روزرسانی شد.' ); ?></p><?php endif; ?>
    <?php get_template_part( 'template-parts/member/' . ( in_array( $view, array( 'analyst-request', 'situation-access' ), true ) ? 'application' : $view ), null, array( 'user' => $user, 'view' => $view ) ); ?>
    </section>
</div></div>
<?php get_footer();
