<?php
/**
 * سربرگ اصلی پوسته روایت ایران (Site Header Skeleton)
 *
 * @package RevayatIran
 */

if (!defined('ABSPATH')) {
    exit;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?> dir="rtl" data-theme="light">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
    <style>
        body, html { font-feature-settings: "ss02", "numr", "tnum"; }
        .nav-separator:last-of-type, li:last-child .nav-separator { display: none !important; }
    </style>
</head>
<body <?php body_class('bg-bg text-text min-h-screen font-sans leading-relaxed transition-colors duration-300 antialiased selection:bg-accent selection:text-white'); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'پرش به محتوای اصلی', 'revayatiran' ); ?></a>

    <!-- نوار پیشرفت مطالعه صفحه -->
    <div class="fixed top-0 left-0 right-0 h-[3px] z-[9999] pointer-events-none" aria-hidden="true">
        <span id="page-progress-bar" class="block h-full w-0 bg-gradient-to-r from-accent to-accent-strong shadow-[0_0_12px_rgba(var(--accent-rgb),0.5)] transition-all duration-100 ease-linear"></span>
    </div>

    <!-- اورلی و مدال جستجوی سریع -->
    <?php get_template_part('template-parts/header/search-modal'); ?>

    <!-- هدر اصلی سایت -->
    <header class="relative z-50 transition-shadow duration-300 [&.is-scrolled]:shadow-none" id="site-header">
        <!-- لایه ثابت نقره‌ای بالای هدر -->
        <div class="absolute top-0 left-0 w-full h-[82px] bg-[var(--header-backdrop)] header-backdrop-pattern -z-10 pointer-events-none transition-colors duration-300" aria-hidden="true"></div>

        <!-- نوار بالایی هدر -->
        <?php get_template_part('template-parts/header/top-bar'); ?>

        <!-- پوسته و ناوبری اصلی دسکتاپ -->
        <?php get_template_part('template-parts/header/navigation'); ?>

        <!-- پنل دراور منوی موبایل -->
        <?php get_template_part('template-parts/header/mobile-menu'); ?>
    </header>

<main id="primary" class="site-main">
