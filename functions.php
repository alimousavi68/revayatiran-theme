<?php
/**
 * هاب اصلی تنظیمات و بارگذاری ماژول‌های پوسته روایت ایران
 *
 * @package RevayatIran
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // امنیت: جلوگیری از دسترسی مستقیم
}

// ۱. تعریف ثابت‌های اساسی پوسته
define('REVAYAT_THEME_VERSION', '1.0.0');
define('REVAYAT_THEME_DIR', get_template_directory());
define('REVAYAT_THEME_URI', get_template_directory_uri());

// ۲. بارگذاری ماژول‌های تخصصی معماری
$revayat_inc_files = [
    '/inc/setup.php',          // پیکربندی و add_theme_support
    '/inc/customizer-query-controls.php', // کنترل‌های انتخاب منابع و فیلترها
    '/inc/customizer.php',     // تنظیمات عمومی و هشت سکشن صفحه اصلی
    '/inc/enqueue.php',        // مدیریت و صف‌بندی دارایی‌های CSS و JS
    '/inc/frontend-quality.php', // Conditional form delivery and homepage metadata.
    '/inc/navigation.php',     // موقعیت‌های منو و ناوبری
    '/inc/nav-walker.php',     // واکر استاندارد ناوبری وردپرس (Pass 2B Commit 5)
    '/inc/template-tags.php',  // توابع کمکی رندرینگ تمپلیت‌ها
    '/inc/single-sidebar.php', // Editable reading-page sidebars.
    '/inc/dossier-editor.php', // Native dossier writing controls.
    '/inc/media.php',          // لایه انتزاعی رسانه و تصاویر (ADR-003)
    '/inc/data-provider.php',  // پل ارتباطی تامین داده‌های واقعی افزونه
];

foreach ($revayat_inc_files as $file) {
    $filepath = REVAYAT_THEME_DIR . $file;
    if (file_exists($filepath)) {
        require_once $filepath;
    }
}
