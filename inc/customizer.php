<?php
/**
 * Theme Customizer schema, validation and presentation integration.
 * Query execution remains in the companion homepage provider.
 * Dynamic CSS is attached to the enqueued stylesheet because values are user settings.
 *
 * @package RevayatIran
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/** Sections use the existing template identifiers. */
function revayat_home_sections(): array {
    return array(
        'hero' => array( 'ویترین اصلی', '', 4, 'editorial_placement' ),
        'daily-narrative' => array( 'روایت روز', 'برگزیده مهم‌ترین تحلیل‌ها و روایت‌های راهبردی روز', 3, 'editorial_placement' ),
        'situation-room' => array( 'اتاق وضعیت', 'برآوردهای راهبردی و تحلیل‌های پدافندی', 4, 'security_level' ),
        'news-monitoring' => array( 'رصد اخبار', 'پایش مستمر منابع خبری و کشف روایت‌های نوظهور', 8, 'news_source' ),
        'special-dossiers' => array( 'پرونده ویژه', 'واکاوی چندبعدی ریشه‌ها و پیامدهای رویدادهای کلان', 3, 'dossier_topic' ),
        'media-observatory' => array( 'دیده‌بان رسانه', 'تحلیل الگوهای فریم‌بندی، بازخوانی روایت‌ها و خطوط خبری رسانه‌ها', 3, 'observatory_badge' ),
        'analysts-network' => array( 'شبکه تحلیل‌گران', 'ارزیابی تخصصی، رتبه‌بندی مخاطبان و معرفی تحلیل‌گران برتر', 8, 'analyst_field' ),
        'multimedia' => array( 'رسانه‌نگار', 'استوری‌های ویدئویی، تحلیل‌های صوتی برخط و روایت‌های تصویری', 8, 'media_format' ),
    );
}

/** Only elements that exist in each section are offered. Selectors are section-scoped. */
function revayat_home_components(): array {
    $common = array(
        'heading' => array( 'عنوان و معرفی بخش', '.section-head' ),
        'summary' => array( 'توضیح بخش', '.section-summary' ),
        'archive' => array( 'لینک آرشیو', '.section-link' ),
    );
    return array(
        'hero' => array(
            'lead' => array( 'مطلب اصلی', '.hero-feature' ),
            'side' => array( 'کارت‌های جانبی', '.hero-stack' ),
            'badges' => array( 'برچسب دسته', '.hero-feature > .absolute, .hero-brief-card > .absolute' ),
            'meta' => array( 'تاریخ و برچسب تحریریه', '.meta-row' ),
        ),
        'daily-narrative' => $common + array(
            'lead' => array( 'مطلب اصلی', '.daily-hero-main' ),
            'side' => array( 'مطالب جانبی دسکتاپ و موبایل', '.daily-interactive-thumbs, .daily-mobile-items' ),
            'panel' => array( 'پنل تعاملی دسکتاپ', '.daily-interactive-info' ),
            'author' => array( 'مشخصات نویسنده', '.daily-author__profile, .info-panel__author, .daily-mobile-author' ),
            'meta' => array( 'تاریخ', '.daily-author__stats, .info-panel__date' ),
            'excerpt' => array( 'چکیده', '.daily-featured__excerpt, .info-panel__excerpt' ),
            'badges' => array( 'برچسب دسته', '.daily-featured__tag, .info-panel__tag, .daily-mobile-category' ),
        ),
        'situation-room' => array(
            'heading' => array( 'معرفی اتاق وضعیت', '.sr-sidebar__top, .sr-sidebar__title' ),
            'summary' => array( 'توضیح بخش', '.sr-sidebar__desc' ),
            'meta' => array( 'فراداده بولتن', '.sr-gated-item__meta' ),
            'archive' => array( 'لینک آرشیو برای اعضای مجاز', '.sr-authorized-archive-link' ),
        ),
        'news-monitoring' => $common + array(
            'tabs' => array( 'تب‌ها و انتخاب دسته موبایل', '.tabs, .news-mobile-tabs' ),
            'meta' => array( 'منبع و زمان خبر', '.news-card__meta' ),
        ),
        'special-dossiers' => $common + array(
            'lead' => array( 'پرونده اصلی', '.dossier-hero-card' ),
            'side' => array( 'پرونده‌های تکمیلی', '.dossier-sidebar-list' ),
            'image' => array( 'تصویر پرونده', '.dossier-hero-visual' ),
            'excerpt' => array( 'چکیده پرونده', '.dossier-excerpt' ),
            'stats' => array( 'آمار ضمایم', '.dossier-features-row' ),
            'meta' => array( 'تاریخ و دبیر', '.dossier-top-meta, .dossier-mini-card__meta, .dossier-lead-author' ),
            'live' => array( 'نشان پرونده فعال', '.live-indicator' ),
        ),
        'media-observatory' => $common + array(
            'frames' => array( 'مقایسه فریم‌ها', '.obs-framing-box' ),
            'excerpt' => array( 'جمع‌بندی', '.observatory-card__summary' ),
            'badges' => array( 'گونه تحلیل', '.obs-badge' ),
            'meta' => array( 'تاریخ', '.obs-date' ),
            'read_more' => array( 'لینک واکاوی کامل', '.observatory-card__footer' ),
        ),
        'analysts-network' => $common + array(
            'lead' => array( 'تحلیل پیشنهادی', '.an-hero--editorial' ),
            'side' => array( 'فهرست تحلیل‌ها', '.an-top-analyses--elegant' ),
            'people' => array( 'فهرست تحلیلگران', '.an-showcase--premium' ),
            'cta' => array( 'دعوت به عضویت', '.an-cta--premium' ),
            'excerpt' => array( 'چکیده تحلیل پیشنهادی', '.an-hero__desc' ),
            'author' => array( 'نویسنده تحلیل پیشنهادی', '.an-hero__author' ),
            'meta' => array( 'تاریخ تحلیل پیشنهادی', '.an-hero__date' ),
            'scores' => array( 'امتیاز، رأی و رتبه', '.an-hero__score, .an-analyst-card__rank, .an-analyst-card__right-col, .an-text-card__score, .an-score-badge--amber' ),
            'navigation' => array( 'کنترل‌های اسلایدر', '.analysts-swiper-prev, .analysts-swiper-next, .analysts-swiper-pagination' ),
        ),
        'multimedia' => array(
            'heading' => $common['heading'], 'summary' => $common['summary'],
            'navigation' => array( 'کنترل‌های ریل', '.story-rail-nav' ),
            'badges' => array( 'برچسب رسانه', '.story-chip' ),
            'duration' => array( 'مدت زمان', '.story-duration' ),
            'type' => array( 'نوع رسانه', '.story-type-badge' ),
            'play' => array( 'نشان پخش', '.story-play-badge' ),
        ),
    );
}

function revayat_font_choices(): array {
    return array( 'IRANYekanX' => 'ایران‌یکان (محلی)', 'Tahoma' => 'Tahoma', 'Arial' => 'Arial', 'system-ui' => 'فونت سیستم' );
}

/** One definition drives controls, sanitizers and preview configuration. */
function revayat_customizer_schema(): array {
    static $cached = null;
    if ( null !== $cached ) {
        return $cached;
    }
    $fonts = revayat_font_choices();
    $schema = array(
        'primary' => array( 'label' => 'رنگ اصلی', 'default' => '#B80000', 'type' => 'color', 'section' => 'general', 'group' => 'appearance' ),
        'secondary' => array( 'label' => 'رنگ فرعی', 'default' => '#700000', 'type' => 'color', 'section' => 'general', 'group' => 'appearance' ),
        'header_backdrop' => array( 'label' => 'رنگ لایه بالای هدر', 'default' => '#E2E8F0', 'type' => 'color', 'section' => 'general', 'group' => 'appearance' ),
        'body_font' => array( 'label' => 'فونت متن', 'default' => 'IRANYekanX', 'type' => 'select', 'choices' => $fonts, 'section' => 'general', 'group' => 'appearance' ),
        'heading_font' => array( 'label' => 'فونت عنوان‌ها', 'default' => 'IRANYekanX', 'type' => 'select', 'choices' => $fonts, 'section' => 'general', 'group' => 'appearance' ),
        'container_width' => array( 'label' => 'عرض محتوای سایت (پیکسل)', 'default' => 1280, 'type' => 'number', 'min' => 960, 'max' => 1600, 'section' => 'general', 'group' => 'appearance' ),
    );
    foreach ( revayat_home_sections() as $section => $info ) {
        $catalog = revayat_source_catalog( $section );
        $fields = array(
            'enabled' => array( 'label' => 'نمایش این سکشن', 'default' => true, 'type' => 'checkbox', 'group' => 'display', 'transport' => 'refresh' ),
            'count' => array( 'label' => in_array( $section, array( 'hero', 'daily-narrative' ), true ) ? 'تعداد مطالب جانبی' : 'تعداد مطالب', 'default' => $info[2], 'type' => 'number', 'min' => 1, 'max' => 16, 'group' => 'query', 'transport' => 'refresh' ),
            'orderby' => array( 'label' => 'مرتب‌سازی مطالب', 'default' => 'date', 'type' => 'select', 'choices' => array( 'date' => 'تاریخ انتشار', 'modified' => 'آخرین ویرایش', 'title' => 'عنوان', 'menu_order' => 'ترتیب دستی' ), 'group' => 'query', 'transport' => 'refresh' ),
            'order' => array( 'label' => 'جهت مرتب‌سازی', 'default' => 'DESC', 'type' => 'select', 'choices' => array( 'DESC' => 'نزولی', 'ASC' => 'صعودی' ), 'group' => 'query', 'transport' => 'refresh' ),
            'offset' => array( 'label' => 'تعداد مطالب ابتدای فهرست که رد شوند', 'default' => 0, 'type' => 'number', 'min' => 0, 'max' => 100, 'group' => 'query', 'transport' => 'refresh' ),
            'source_mode' => array( 'label' => 'منبع محتوا', 'default' => 'selected', 'type' => 'select', 'choices' => array( 'selected' => 'نوع‌های محتوای انتخاب‌شده', 'all' => 'همه نوع‌های محتوای مجاز' ), 'group' => 'query', 'transport' => 'refresh' ),
            'post_types' => array( 'label' => 'نوع‌های محتوای مجاز', 'default' => revayat_default_source( $section ), 'type' => 'multicheck', 'choices' => $catalog['types'], 'group' => 'query', 'transport' => 'refresh', 'description' => 'می‌توانید چند نوع را انتخاب کنید. انتخاب خالی یعنی هیچ محتوایی نمایش داده نشود.' ),
            'excluded_post_types' => array( 'label' => 'نوع‌های محتوای مستثنا', 'default' => '', 'type' => 'multicheck', 'choices' => $catalog['types'], 'group' => 'query', 'transport' => 'refresh', 'description' => 'این نوع‌ها حتی اگر در منابع مجاز باشند، نمایش داده نمی‌شوند.' ),
            'tax_filters' => array( 'label' => 'فیلترهای دسته‌بندی و تاکسونومی', 'default' => '[]', 'type' => 'taxonomy_filters', 'group' => 'query', 'transport' => 'refresh', 'description' => 'برای دسته‌بندی، برچسب یا هر تاکسونومی، فیلتر جدا اضافه کنید. موارد مستثنا همیشه اولویت دارند.' ),
            'tax_relation' => array( 'label' => 'رابطه بین فیلترهای مثبت', 'default' => 'AND', 'type' => 'select', 'choices' => array( 'AND' => 'همه فیلترها برقرار باشند', 'OR' => 'حداقل یکی برقرار باشد' ), 'group' => 'query', 'transport' => 'refresh' ),
            'accent' => array( 'label' => 'رنگ تأکید اختصاصی (خالی: رنگ عمومی)', 'default' => '', 'type' => 'color', 'group' => 'appearance' ),
            'background' => array( 'label' => 'پس‌زمینه روشن (خالی: طراحی فعلی)', 'default' => '', 'type' => 'color', 'group' => 'appearance' ),
            'dark_background' => array( 'label' => 'پس‌زمینه تیره (خالی: طراحی فعلی)', 'default' => '', 'type' => 'color', 'group' => 'appearance' ),
            'spacing' => array( 'label' => 'فاصله عمودی', 'default' => 'default', 'type' => 'select', 'choices' => array( 'default' => 'طراحی فعلی', 'compact' => 'فشرده', 'normal' => 'معمولی', 'relaxed' => 'باز' ), 'group' => 'appearance' ),
            'title_size' => array( 'label' => 'اندازه عنوان بخش', 'default' => 'default', 'type' => 'select', 'choices' => array( 'default' => 'طراحی فعلی', 'small' => 'کوچک', 'medium' => 'متوسط', 'large' => 'بزرگ' ), 'group' => 'appearance' ),
            'radius' => array( 'label' => 'گردی کارت‌ها', 'default' => 'default', 'type' => 'select', 'choices' => array( 'default' => 'طراحی فعلی', 'square' => 'بدون گردی', 'soft' => 'ملایم', 'round' => 'گرد' ), 'group' => 'appearance' ),
        );
        if ( 'hero' !== $section ) {
            $fields['title'] = array( 'label' => 'عنوان بخش', 'default' => $info[0], 'type' => 'text', 'group' => 'display', 'transport' => 'refresh' );
            $fields['description'] = array( 'label' => 'توضیح بخش', 'default' => $info[1], 'type' => 'textarea', 'group' => 'display', 'transport' => 'refresh' );
        }
        if ( in_array( $section, array( 'hero', 'daily-narrative' ), true ) ) {
            $prefix = 'hero' === $section ? 'hero' : 'daily';
            $fields['lead_placement'] = array( 'label' => 'جایگاه مطلب اصلی', 'default' => $prefix . '_lead', 'type' => 'select', 'choices' => revayat_placement_choices(), 'group' => 'query', 'transport' => 'refresh' );
            $fields['side_placement'] = array( 'label' => 'جایگاه مطالب جانبی', 'default' => $prefix . '_side', 'type' => 'select', 'choices' => revayat_placement_choices(), 'group' => 'query', 'transport' => 'refresh' );
            $fields['use_default_filter'] = array( 'label' => 'اعمال جایگاه تحریریه بر منابع انتخاب‌شده', 'default' => true, 'type' => 'checkbox', 'group' => 'query', 'transport' => 'refresh', 'description' => 'برای نمایش محتوا بدون محدودیت جایگاه، این گزینه را خاموش کنید.' );
        }
        if ( 'news-monitoring' === $section ) {
            $fields['use_default_filter'] = array( 'label' => 'فقط اخبار دارای منبع خبری', 'default' => true, 'type' => 'checkbox', 'group' => 'query', 'transport' => 'refresh' );
        }
        if ( 'analysts-network' === $section ) {
            $fields['count']['label'] = 'تعداد یادداشت‌ها';
            $fields['source_mode']['label'] = 'منبع محتوای یادداشت‌ها';
            $fields['post_types']['label'] = 'نوع‌های محتوای مجاز برای یادداشت‌ها';
            $fields['tax_filters']['description'] .= ' این فیلترها مربوط به یادداشت‌ها هستند؛ فهرست اشخاص شمارش و ترتیب مستقل دارد.';
            $fields['excluded_post_types']['description'] .= ' مستثنا کردن پروفایل شخص، فهرست اشخاص این سکشن را نیز خالی می‌کند.';
            $fields['people_count'] = array( 'label' => 'تعداد اشخاص', 'default' => 5, 'type' => 'number', 'min' => 1, 'max' => 16, 'group' => 'query', 'transport' => 'refresh' );
            $fields['people_order'] = array( 'label' => 'ترتیب اشخاص بر اساس تاریخ', 'default' => 'ASC', 'type' => 'select', 'choices' => array( 'ASC' => 'صعودی', 'DESC' => 'نزولی' ), 'group' => 'query', 'transport' => 'refresh' );
        }
        foreach ( revayat_home_components()[ $section ] as $key => $component ) {
            $fields[ 'show_' . $key ] = array( 'label' => 'نمایش: ' . $component[0], 'default' => true, 'type' => 'checkbox', 'group' => 'components', 'selector' => $component[1] );
        }
        foreach ( $fields as $key => $field ) {
            $field['section'] = $section;
            $schema[ $section . '_' . $key ] = $field;
        }
    }
    $cached = $schema;
    return $schema;
}

function revayat_sanitize_customizer( $value, $setting ) {
    $key = substr( $setting->id, strlen( 'revayat_' ) );
    $field = revayat_customizer_schema()[ $key ] ?? null;
    if ( ! $field || ! is_scalar( $value ) ) {
        return $field['default'] ?? null;
    }
    switch ( $field['type'] ) {
        case 'taxonomy_filters': return revayat_sanitize_tax_filters( $value, $field['section'] );
        case 'multicheck':
            if ( $value === $field['default'] && ! class_exists( 'Revayat_Companion_Homepage_Query' ) ) { return $value; }
            return implode( ',', array_values( array_intersect( array_unique( explode( ',', (string) $value ) ), array_keys( $field['choices'] ) ) ) );
        case 'checkbox': return in_array( $value, array( true, 1, '1' ), true );
        case 'color':
            $color = '' === $value ? $field['default'] : ( sanitize_hex_color( $value ) ?: $field['default'] );
            return strlen( $color ) === 4 ? '#' . $color[1] . $color[1] . $color[2] . $color[2] . $color[3] . $color[3] : $color;
        case 'number': return max( $field['min'], min( $field['max'], (int) $value ) );
        case 'select': return array_key_exists( (string) $value, $field['choices'] ) ? $value : $field['default'];
        default:
            if ( str_ends_with( $key, '_terms' ) ) {
                return implode( ',', array_unique( array_filter( array_map( 'absint', explode( ',', (string) $value ) ) ) ) );
            }
            if ( str_ends_with( $key, '_placement' ) ) {
                return sanitize_title( $value );
            }
            return 'textarea' === $field['type'] ? sanitize_textarea_field( $value ) : sanitize_text_field( $value );
    }
}

function revayat_theme_setting( string $key ) {
    $field = revayat_customizer_schema()[ $key ] ?? null;
    if ( ! $field ) {
        return null;
    }
    // Validate reads too: imported theme_mods must not become arbitrary CSS.
    return revayat_sanitize_customizer( get_theme_mod( 'revayat_' . $key, $field['default'] ), (object) array( 'id' => 'revayat_' . $key ) );
}

function revayat_customize_register( $manager ): void {
    revayat_register_query_control_class();
    $manager->add_panel( 'revayat_theme', array( 'title' => 'تنظیمات قالب روایت ایران', 'priority' => 30 ) );
    $manager->add_panel( 'revayat_home', array( 'title' => 'روایت ایران — صفحه اصلی', 'description' => 'تنظیمات مستقل هشت سکشن صفحه اصلی؛ کوئری‌ها به افزونه مکمل نیاز دارند. مخفی‌سازی اتاق وضعیت مجوز دسترسی آن را تغییر نمی‌دهد.', 'priority' => 31 ) );
    $manager->add_section( 'revayat_general', array( 'title' => 'ظاهر عمومی سایت', 'panel' => 'revayat_theme' ) );
    // A native link section avoids unsupported nested panels.
    $manager->add_section( 'revayat_home_link', array( 'title' => 'مدیریت صفحه اصلی', 'panel' => 'revayat_theme' ) );
    $manager->add_setting( 'revayat_home_link', array( 'sanitize_callback' => 'sanitize_text_field' ) );
    $manager->add_control( 'revayat_home_link', array( 'section' => 'revayat_home_link', 'type' => 'hidden', 'description' => 'از دکمه مدیریت صفحه اصلی برای ورود به تنظیمات هشت سکشن استفاده کنید.' ) );
    foreach ( revayat_home_sections() as $section => $info ) {
        $manager->add_section( 'revayat_' . $section, array( 'title' => $info[0], 'panel' => 'revayat_home' ) );
    }
    foreach ( revayat_customizer_schema() as $key => $field ) {
        $id = 'revayat_' . $key;
        $manager->add_setting( $id, array( 'default' => 'taxonomy_filters' === $field['type'] && class_exists( 'Revayat_Companion_Homepage_Query' ) ? wp_json_encode( Revayat_Companion_Homepage_Query::filters( $field['section'] ) ) : $field['default'], 'type' => 'theme_mod', 'capability' => 'edit_theme_options', 'sanitize_callback' => 'revayat_sanitize_customizer', 'transport' => $field['transport'] ?? 'postMessage', 'validate_callback' => 'taxonomy_filters' === $field['type'] ? 'revayat_validate_tax_filters' : null ) );
        $args = array( 'label' => $field['label'], 'section' => 'revayat_' . $field['section'], 'type' => $field['type'], 'choices' => $field['choices'] ?? array() );
        if ( isset( $field['min'] ) ) {
            $args['input_attrs'] = array( 'min' => $field['min'], 'max' => $field['max'], 'step' => 1 );
        }
        $args['description'] = $field['description'] ?? '';
        if ( str_ends_with( $key, '_placement' ) ) { $args['description'] = 'از جایگاه‌های موجود انتخاب کنید؛ بدون محدودیت، فیلتر جایگاه را حذف می‌کند.'; }
        if ( in_array( $field['type'], array( 'multicheck', 'taxonomy_filters' ), true ) ) {
            $manager->add_control( new Revayat_Query_Control( $manager, $id, $args ) );
            continue;
        }
        if ( 'color' === $field['type'] ) {
            $manager->add_control( new WP_Customize_Color_Control( $manager, $id, $args ) );
        } else {
            $manager->add_control( $id, $args );
        }
    }
}
add_action( 'customize_register', 'revayat_customize_register' );

function revayat_customizer_assets(): void {
    wp_enqueue_style( 'revayat-customizer', REVAYAT_THEME_URI . '/assets/css/customizer-controls.css', array(), filemtime( REVAYAT_THEME_DIR . '/assets/css/customizer-controls.css' ) );
    wp_enqueue_script( 'revayat-customizer', REVAYAT_THEME_URI . '/assets/js/customizer-controls.js', array( 'customize-controls' ), REVAYAT_THEME_VERSION, true );
    wp_enqueue_script( 'revayat-customizer-query', REVAYAT_THEME_URI . '/assets/js/customizer-query.js', array( 'customize-controls' ), filemtime( REVAYAT_THEME_DIR . '/assets/js/customizer-query.js' ), true );
    $catalogs = array();
    foreach ( revayat_home_sections() as $section => $info ) { $catalogs[ $section ] = revayat_source_catalog( $section ); }
    wp_localize_script( 'revayat-customizer-query', 'revayatQueryControls', array( 'catalogs' => $catalogs, 'ajaxUrl' => admin_url( 'admin-ajax.php' ), 'nonce' => wp_create_nonce( 'revayat_customizer_terms' ) ) );
    wp_localize_script( 'revayat-customizer', 'revayatCustomizerControls', array( 'schema' => revayat_customizer_schema(), 'groups' => array( 'display' => 'نمایش و معرفی', 'components' => 'اجزای داخلی', 'query' => 'محتوا و کوئری', 'appearance' => 'ظاهر و فاصله‌ها' ) ) );
}
add_action( 'customize_controls_enqueue_scripts', 'revayat_customizer_assets' );

function revayat_customizer_preview(): void {
    wp_enqueue_script( 'revayat-customizer-preview', REVAYAT_THEME_URI . '/assets/js/customizer-preview.js', array( 'customize-preview' ), REVAYAT_THEME_VERSION, true );
    wp_localize_script( 'revayat-customizer-preview', 'revayatCustomizerPreview', array( 'schema' => revayat_customizer_schema(), 'layout' => revayat_home_layout_rules() ) );
}
add_action( 'customize_preview_init', 'revayat_customizer_preview' );

/** Build CSS with validated values and constant selectors only. */
function revayat_customizer_css(): string {
    $primary = revayat_theme_setting( 'primary' );
    $parts = array_map( 'hexdec', str_split( substr( $primary, 1 ), 2 ) );
    $rgb = implode( ', ', $parts );
    $primary_dark = sprintf( '#%02x%02x%02x', ...array_map( static fn( $part ) => (int) round( $part * 0.75 ), $parts ) );
    $css = ':root{--primary:' . $primary . ';--primary-dark:' . $primary_dark . ';--accent:' . $primary . ';--accent-rgb:' . $rgb . ';--accent-strong:' . revayat_theme_setting( 'secondary' ) . ';--header-backdrop:' . revayat_theme_setting( 'header_backdrop' ) . ';--container:' . revayat_theme_setting( 'container_width' ) . 'px;}';
    $css .= 'body,button,input,select,textarea,.main-nav{font-family:"' . revayat_theme_setting( 'body_font' ) . '",sans-serif!important;}h1,h2,h3,h4,h5,h6{font-family:"' . revayat_theme_setting( 'heading_font' ) . '",sans-serif!important;}';
    $spacing = array( 'compact' => 16, 'normal' => 32, 'relaxed' => 56 );
    $size = array( 'small' => 20, 'medium' => 26, 'large' => 32 );
    $radius = array( 'square' => 0, 'soft' => 8, 'round' => 20 );
    foreach ( revayat_home_sections() as $section => $info ) {
        $scope = '#homepage-content #' . $section;
        foreach ( revayat_home_components()[ $section ] as $key => $component ) {
            if ( ! revayat_theme_setting( $section . '_show_' . $key ) ) {
                $selectors = array_map( static fn( $selector ) => $scope . ' ' . trim( $selector ), explode( ',', $component[1] ) );
                $css .= implode( ',', $selectors ) . '{display:none!important;}';
            }
        }
        $accent = revayat_theme_setting( $section . '_accent' );
        if ( $accent ) {
            $rgb = implode( ', ', array_map( 'hexdec', str_split( substr( $accent, 1 ), 2 ) ) );
            $css .= $scope . '{--primary:' . $accent . ';--accent:' . $accent . ';--accent-rgb:' . $rgb . ';--accent-gradient:linear-gradient(135deg,var(--primary) 0%,var(--accent-strong) 100%);}';
        }
        foreach ( array( 'background' => 'html:not([data-theme="dark"]) ', 'dark_background' => 'html[data-theme="dark"] ' ) as $key => $mode ) {
            $color = revayat_theme_setting( $section . '_' . $key );
            if ( $color ) {
                $css .= $mode . $scope . '{background:' . $color . '!important;}';
            }
        }
        $space = revayat_theme_setting( $section . '_spacing' );
        if ( isset( $spacing[ $space ] ) ) {
            $css .= $scope . '{padding-block:' . $spacing[ $space ] . 'px!important;}';
        }
        $title_size = revayat_theme_setting( $section . '_title_size' );
        if ( isset( $size[ $title_size ] ) ) {
            $css .= $scope . ' .section-head h2,' . $scope . ' .sr-sidebar__title,' . $scope . ' .hero-feature-title{font-size:' . $size[ $title_size ] . 'px!important;}';
        }
        $round = revayat_theme_setting( $section . '_radius' );
        if ( isset( $radius[ $round ] ) ) {
            $css .= $scope . ' article,' . $scope . ' .story-card,' . $scope . ' .an-hero--editorial{border-radius:' . $radius[ $round ] . 'px!important;}';
        }
    }
    return $css . revayat_home_layout_css();
}
add_action( 'wp_enqueue_scripts', static function () {
    wp_add_inline_style( 'revayat-theme-root', revayat_customizer_css() );
}, 30 );

function revayat_home_text( string $section, string $key ): string {
    return (string) revayat_theme_setting( $section . '_' . $key );
}

/** Query choices should never silently fall back to unrelated demo stories. */
function revayat_home_query_is_default( string $section ): bool {
    $section = str_replace( '_', '-', $section );
    if ( class_exists( 'Revayat_Companion_Homepage_Query' ) && Revayat_Companion_Homepage_Query::filters( $section ) ) { return false; }
    foreach ( revayat_customizer_schema() as $key => $field ) {
        if ( $section === $field['section'] && 'query' === $field['group'] && revayat_theme_setting( $key ) !== $field['default'] ) {
            return false;
        }
    }
    return true;
}

/** Shared conditional layout rules prevent empty columns after hiding components. */
function revayat_home_layout_rules(): array {
    return array(
        array( 'section' => 'hero', 'hidden' => array( 'lead' ), 'css' => ' .hero-stage{grid-template-columns:1fr!important;}' ),
        array( 'section' => 'hero', 'hidden' => array( 'side' ), 'css' => ' .hero-stage{grid-template-columns:1fr!important;}' ),
        array( 'section' => 'special-dossiers', 'hidden' => array( 'lead' ), 'css' => ' .dossier-layout-v2{grid-template-columns:1fr!important;}' ),
        array( 'section' => 'special-dossiers', 'hidden' => array( 'side' ), 'css' => ' .dossier-layout-v2{grid-template-columns:1fr!important;}' ),
        array( 'section' => 'special-dossiers', 'hidden' => array( 'image' ), 'css' => ' .dossier-hero-card{grid-template-columns:1fr!important;}' ),
        array( 'section' => 'analysts-network', 'hidden' => array( 'people', 'cta' ), 'css' => ' .an-sidebar-column{display:none!important;}| .an-grid-layout{grid-template-columns:1fr!important;}' ),
        array( 'section' => 'analysts-network', 'hidden' => array( 'lead', 'side' ), 'css' => ' .an-main-column{display:none!important;}| .an-grid-layout{grid-template-columns:1fr!important;}' ),
        array( 'section' => 'daily-narrative', 'hidden' => array( 'lead' ), 'css' => ' .daily-hero-layout{grid-template-columns:repeat(auto-fit,minmax(220px,1fr))!important;}| .daily-mobile-items{grid-column:1/-1!important;}', 'media' => '(min-width:1025px)' ),
        array( 'section' => 'daily-narrative', 'hidden' => array( 'panel' ), 'css' => ' .daily-hero-layout{grid-template-columns:repeat(auto-fit,minmax(220px,1fr))!important;}| .daily-mobile-items{grid-column:1/-1!important;}', 'media' => '(min-width:1025px)' ),
        array( 'section' => 'daily-narrative', 'hidden' => array( 'side' ), 'css' => ' .daily-hero-layout{grid-template-columns:repeat(auto-fit,minmax(220px,1fr))!important;}', 'media' => '(min-width:1025px)' ),
        array( 'section' => 'daily-narrative', 'hidden' => array( 'side', 'panel' ), 'css' => ' .daily-hero-layout{grid-template-columns:1fr!important;}| .daily-hero-main{grid-column:auto!important;}| .daily-hero-main::after{display:none!important;}' ),
    );
}

function revayat_home_layout_css(): string {
    $css = '';
    foreach ( revayat_home_layout_rules() as $rule ) {
        $apply = true;
        foreach ( $rule['hidden'] as $key ) {
            $apply = $apply && ! revayat_theme_setting( $rule['section'] . '_show_' . $key );
        }
        if ( $apply ) {
            $scope = '#homepage-content #' . $rule['section'];
            $body = implode( '', array_map( static fn( $part ) => $scope . $part, explode( '|', $rule['css'] ) ) );
            $css .= isset( $rule['media'] ) ? '@media ' . $rule['media'] . '{' . $body . '}' : $body;
        }
    }
    return $css;
}

function revayat_placement_choices(): array {
    $choices = array( '' => 'بدون محدودیت جایگاه' );
    $terms = get_terms( array( 'taxonomy' => 'editorial_placement', 'hide_empty' => false ) );
    if ( ! is_wp_error( $terms ) ) {
        foreach ( $terms as $term ) { $choices[ $term->slug ] = $term->name; }
    }
    foreach ( array( 'hero_lead' => 'لید ویترین', 'hero_side' => 'مطالب جانبی ویترین', 'daily_lead' => 'لید روایت روز', 'daily_side' => 'مطالب جانبی روایت روز' ) as $slug => $label ) {
        if ( ! isset( $choices[ $slug ] ) ) { $choices[ $slug ] = $label . ' (جایگاه پیش‌فرض)' ; }
    }
    return $choices;
}
