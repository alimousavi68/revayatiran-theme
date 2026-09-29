<?php
/**
 * لایه داده‌های دمو و آداپتور مهاجرت (Demo Data Adapter / Bridge)
 *
 * هدف:
 * این فایل صرفاً یک Data Provider موقت برای مرحله Migration است (Pass 2B).
 * هیچ‌گونه وابستگی به WP_Query، پست‌های دیتابیس یا CPTها ندارد.
 *
 * طبق ADR-002:
 * این لایه داده‌ها را با ساختار نهایی CPTها تحویل می‌دهد تا در Pass 3
 * فقط توابع این لایه با کوئری‌های واقعی جایگزین شوند، بدون تغییر در Template Parts.
 *
 * طبق ADR-003:
 * تصاویر دمو از نمونه‌های واقعی سورس اولیه (assets/images/demo/) بارگذاری می‌شوند
 * و پلیس‌هولدرهای مدرن هندسی جایگزین لوگوی پایگاه شده‌اند.
 *
 * @package RevayatIran
 * @subpackage Inc
 * @version 2.1.0 (Pass 2B — Media Abstraction Integration)
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * توابع کمکی دریافت تصاویر دمو
 */
if (!function_exists('revayat_demo_post_img')) {
    function revayat_demo_post_img(string $filename): string {
        return esc_url(REVAYAT_THEME_URI . '/assets/images/demo/posts/' . $filename);
    }
}

if (!function_exists('revayat_demo_avatar_img')) {
    function revayat_demo_avatar_img(string $filename): string {
        return esc_url(REVAYAT_THEME_URI . '/assets/images/demo/avatars/' . $filename);
    }
}

/**
 * ۱. داده‌های دمو بخش ویترین اصلی (Hero Section)
 *
 * @return array
 */
function revayat_get_hero_data(): array {
    return [
        'lead' => [
            'id'            => 1,
            'title'         => __('ابعاد راهبردی توافق‌های منطقه‌ای؛ بازخوانی موازنه جدید قدرت در خلیج فارس', 'revayatiran'),
            'url'           => home_url('/'),
            'thumbnail_url' => revayat_demo_post_img('hero______.jpg'),
            'category'      => __('تحلیل راهبردی', 'revayatiran'),
            'tag_label'     => __('تحلیل سردبیر', 'revayatiran'),
            'date'          => __('۱۴ شهریور ۱۴۰۵', 'revayatiran'),
        ],
        'items' => [
            [
                'id'            => 2,
                'title'         => __('سناریوهای ناترازی انرژی و مدیریت تقاضا در صنایع کلان', 'revayatiran'),
                'url'           => home_url('/'),
                'thumbnail_url' => revayat_demo_post_img('hero__________.jpg'),
                'category'      => __('اقتصاد سیاسی', 'revayatiran'),
            ],
            [
                'id'            => 3,
                'title'         => __('پایش تغییرات ژئواکونومیک قفقاز جنوبی و کریدورهای جایگزین', 'revayatiran'),
                'url'           => home_url('/'),
                'thumbnail_url' => revayat_demo_post_img('hero____________.jpg'),
                'category'      => __('روابط بین‌الملل', 'revayatiran'),
            ],
            [
                'id'            => 4,
                'title'         => __('جنگ شناختی و بازمهندسی الگوهای اقناع در شبکه‌های اجتماعی', 'revayatiran'),
                'url'           => home_url('/'),
                'thumbnail_url' => revayat_demo_post_img('hero_____________.jpg'),
                'category'      => __('جنگ ترکیبی', 'revayatiran'),
            ],
            [
                'id'            => 5,
                'title'         => __('تحلیل داده‌محور بازار سرمایه و پیش‌بینی نوسانات نرخ بهره', 'revayatiran'),
                'url'           => home_url('/'),
                'thumbnail_url' => revayat_demo_post_img('hero_________________.jpg'),
                'category'      => __('بازارهای مالی', 'revayatiran'),
            ],
        ],
    ];
}

/**
 * ۲. داده‌های دمو بخش روایت روز (Daily Narrative Section)
 *
 * @return array
 */
function revayat_get_daily_narrative_data(): array {
    return [
        'lead' => [
            'id'            => 10,
            'title'         => __('مذاکرات امنیتی منطقه‌ای؛ سناریوهای مهار و چشم‌انداز بازدارندگی مشترک', 'revayatiran'),
            'url'           => home_url('/'),
            'thumbnail_url' => revayat_demo_post_img('27738882_1_0_799_449_1920x0_80_0_0_5c1b2e4e4887372d25d2ddaca10d8bb0.jpg.webp'),
            'excerpt'       => __('بررسی ابعاد و پیامدهای توافقات دیپلماتیک اخیر بر ساختار امنیتی منطقه و جایگاه ژئوپلیتیک ایران در معادلات موازنه قوا.', 'revayatiran'),
            'date'          => __('۱۴ شهریور ۱۴۰۵', 'revayatiran'),
            'category'      => __('روایت روز', 'revayatiran'),
            'author_name'   => __('دکتر پیمان شریفی', 'revayatiran'),
            'author_avatar' => revayat_demo_avatar_img('رضانصری.webp'),
            'author_role'   => __('پژوهشگر ارشد سیاست خارجی', 'revayatiran'),
        ],
        'interactive_items' => [
            [
                'id'            => 11,
                'title'         => __('صورت‌بندی جدید رقابت‌های ترانزیتی در آسیای مرکزی', 'revayatiran'),
                'url'           => home_url('/'),
                'thumbnail_url' => revayat_demo_post_img('219-os5jc-1-ak32467-800x534.webp'),
                'excerpt'       => __('کالبدشکافی طرح‌های نوین زیرساختی و تاثیر آن بر کریدورهای سنتی ترانزیت کالا در منطقه.', 'revayatiran'),
                'date'          => __('۱۳ شهریور ۱۴۰۵', 'revayatiran'),
                'category'      => __('ژئواکونومی', 'revayatiran'),
                'author_name'   => __('مریم هوشمندی', 'revayatiran'),
                'author_avatar' => revayat_demo_avatar_img('0f1b74871a3e46e7ae950c05c65e6d2d.png'),
            ],
            [
                'id'            => 12,
                'title'         => __('الگوهای نوین مهار تورم در اقتصادهای در حال گذار', 'revayatiran'),
                'url'           => home_url('/'),
                'thumbnail_url' => revayat_demo_post_img('اقتصاد-ایران-ak3539-1024x683.webp'),
                'excerpt'       => __('ارزیابی ابزارهای سیاست پولی و نقش انضباط مالی در بازگرداندن ثبات به بازارهای پیشرو.', 'revayatiran'),
                'date'          => __('۱۲ شهریور ۱۴۰۵', 'revayatiran'),
                'category'      => __('اقتصاد کلان', 'revayatiran'),
                'author_name'   => __('مهندس احسان طاهری', 'revayatiran'),
                'author_avatar' => revayat_demo_avatar_img('14eeb1b8dc3a4bbd905999e407a01725.png'),
            ],
            [
                'id'            => 13,
                'title'         => __('تکنیک‌های جنگ ادراکی در مدیریت هیجانات بازار', 'revayatiran'),
                'url'           => home_url('/'),
                'thumbnail_url' => revayat_demo_post_img('1011664_329.webp'),
                'excerpt'       => __('تحلیل خطوط خبری و شیوه‌های جهت‌دهی به افکار عمومی در بزنگاه‌های تصمیم‌گیری اقتصادی.', 'revayatiran'),
                'date'          => __('۱۱ شهریور ۱۴۰۵', 'revayatiran'),
                'category'      => __('امنیت روانی', 'revayatiran'),
                'author_name'   => __('رضا کمالی', 'revayatiran'),
                'author_avatar' => revayat_demo_avatar_img('56d604c11de44ed4b583e8f8b81626b3.png'),
            ],
        ],
    ];
}

/**
 * ۳. داده‌های دمو بخش اتاق وضعیت (Situation Room Section)
 *
 * @return array
 */
function revayat_get_situation_room_data(): array {
    return [
        'bulletins' => [
            [
                'id'             => 20,
                'title'          => __('پایش شاخص‌های حساس در بازارهای موازی و زنجیره ارزش', 'revayatiran'),
                'url'            => home_url('/situation-room/'),
                'date'           => __('۱۲ خرداد ۱۴۰۵', 'revayatiran'),
                'category'       => __('اقتصاد سیاسی', 'revayatiran'),
                'security_level' => 'classified',
                'is_locked'      => true,
            ],
            [
                'id'             => 21,
                'title'          => __('تحلیل ابعاد ژئوپلیتیک بحران آب منطقه‌ای و منابع مشترک', 'revayatiran'),
                'url'            => home_url('/situation-room/'),
                'date'           => __('۱۰ خرداد ۱۴۰۵', 'revayatiran'),
                'category'       => __('امنیت منابع', 'revayatiran'),
                'security_level' => 'restricted',
                'is_locked'      => true,
            ],
            [
                'id'             => 22,
                'title'          => __('ارزیابی ریسک زنجیره تأمین صنایع حساس و راهبردی', 'revayatiran'),
                'url'            => home_url('/situation-room/'),
                'date'           => __('۰۹ خرداد ۱۴۰۵', 'revayatiran'),
                'category'       => __('زیرساخت', 'revayatiran'),
                'security_level' => 'classified',
                'is_locked'      => true,
            ],
        ],
        'gated_meta' => [
            'auth_url' => home_url('/auth/'),
            'info_url' => home_url('/situation-room/'),
        ],
    ];
}

/**
 * ۴. داده‌های دمو بخش رصد اخبار (News Monitoring Section)
 *
 * @return array
 */
function revayat_get_news_monitoring_data(): array {
    return [
        'active_tab' => 'all',
        'tabs'       => [
            ['slug' => 'all',      'label' => __('همه', 'revayatiran')],
            ['slug' => 'politics', 'label' => __('سیاست', 'revayatiran')],
            ['slug' => 'economy',  'label' => __('اقتصاد', 'revayatiran')],
            ['slug' => 'security', 'label' => __('امنیت', 'revayatiran')],
        ],
        'items'      => [
            [
                'id'            => 30,
                'title'         => __('تاکید بر توسعه زیرساخت‌های ریلی و ترانزیت ترکیبی در گذرگاه شمال-جنوب', 'revayatiran'),
                'url'           => home_url('/'),
                'thumbnail_url' => revayat_demo_post_img('219-os5jc-1-ak32467-800x534.webp'),
                'category'      => __('سیاست', 'revayatiran'),
                'source_label'  => __('ایرنا', 'revayatiran'),
            ],
            [
                'id'            => 31,
                'title'         => __('بسته جدید سیاست‌های پولی بانک مرکزی برای کنترل نوسانات نرخ ارز', 'revayatiran'),
                'url'           => home_url('/'),
                'thumbnail_url' => revayat_demo_post_img('اقتصادایران-ak1549-ak3280-1200x800-1024x683.webp'),
                'category'      => __('اقتصاد', 'revayatiran'),
                'source_label'  => __('ایسنا', 'revayatiran'),
            ],
            [
                'id'            => 32,
                'title'         => __('رزمایش مشترک پدافند هوایی در منطقه خلیج فارس و دریای عمان', 'revayatiran'),
                'url'           => home_url('/'),
                'thumbnail_url' => revayat_demo_post_img('1404041822335791233405404.jpg'),
                'category'      => __('امنیت', 'revayatiran'),
                'source_label'  => __('تسنیم', 'revayatiran'),
            ],
            [
                'id'            => 33,
                'title'         => __('نشست چهارجانبه وزرای خارجه برای بررسی توافق‌نامه‌های ترانزیتی', 'revayatiran'),
                'url'           => home_url('/'),
                'thumbnail_url' => revayat_demo_post_img('1404092612513063035069134.jpg'),
                'category'      => __('سیاست', 'revayatiran'),
                'source_label'  => __('مهر', 'revayatiran'),
            ],
            [
                'id'            => 34,
                'title'         => __('گزارش تحلیلی از روند رشد تولید ناخالص داخلی و چالش‌های بهره‌وری', 'revayatiran'),
                'url'           => home_url('/'),
                'thumbnail_url' => revayat_demo_post_img('شاخص-بورس-ak5259-ak4687-1200x800-1024x683.webp'),
                'category'      => __('اقتصاد', 'revayatiran'),
                'source_label'  => __('فارس', 'revayatiran'),
            ],
            [
                'id'            => 35,
                'title'         => __('تقویت سامانه‌های پایش الکترونیک و مرزبانی هوشمند در نوار شرقی', 'revayatiran'),
                'url'           => home_url('/'),
                'thumbnail_url' => revayat_demo_post_img('2279480_168.jpg'),
                'category'      => __('امنیت', 'revayatiran'),
                'source_label'  => __('دانشجو', 'revayatiran'),
            ],
        ],
    ];
}

/**
 * ۵. داده‌های دمو بخش پرونده‌های ویژه (Special Dossiers Section)
 *
 * @return array
 */
function revayat_get_special_dossiers_data(): array {
    return [
        'lead' => [
            'id'            => 40,
            'title'         => __('پروژه نفوذ شناختی؛ از بازتولید واژه‌ها تا مهندسی هراس عمومی', 'revayatiran'),
            'url'           => home_url('/dossiers/'),
            'thumbnail_url' => revayat_demo_post_img('troja.jpeg'),
            'excerpt'       => __('این پرونده به‌صورت مرحله‌ای ابعاد جنگ شناختی در بستر رسانه‌های اجتماعی را باز می‌کند؛ از هسته‌های تولید پیام و الگوریتم‌های تقویت محتوا تا سازوکار نرمال‌سازی گزاره‌ها در افکار عمومی.', 'revayatiran'),
            'is_live'       => true,
            'last_update'   => __('آخرین به‌روزرسانی: ۲ ساعت پیش', 'revayatiran'),
            'editor_lead'   => __('تحریریه راهبردی', 'revayatiran'),
            'stats'         => [
                'docs'        => __('۱۲ سند پیوست', 'revayatiran'),
                'docs_desc'   => __('بولتن‌های داده‌محور', 'revayatiran'),
                'frames'      => __('۴۵ فریم مستند', 'revayatiran'),
                'frames_desc' => __('نمودارها و داده‌نماها', 'revayatiran'),
                'notes'       => __('۸ یادداشت تحلیلی', 'revayatiran'),
                'notes_desc'  => __('نخبگان و کارشناسان', 'revayatiran'),
            ],
        ],
        'sidebar' => [
            [
                'id'             => 41,
                'title'          => __('معمای ناترازی انرژی؛ کالبدشکافی سیاست‌های قیمتی و ساختار تولید', 'revayatiran'),
                'url'            => home_url('/dossiers/'),
                'category'       => __('پرونده اقتصادی', 'revayatiran'),
                'sections_count' => __('۶ بخش', 'revayatiran'),
                'date'           => __('مرداد ۱۴۰۵', 'revayatiran'),
            ],
            [
                'id'             => 42,
                'title'          => __('کریدورهای رقیب در خاورمیانه؛ جنگ ژئواکونومیک مسیرهای ترانزیتی', 'revayatiran'),
                'url'            => home_url('/dossiers/'),
                'category'       => __('پرونده منطقه‌ای', 'revayatiran'),
                'sections_count' => __('۸ بخش', 'revayatiran'),
                'date'           => __('تیر ۱۴۰۵', 'revayatiran'),
            ],
        ],
    ];
}

/**
 * ۶. داده‌های دمو بخش دیده‌بان رسانه (Media Observatory Section)
 *
 * @return array
 */
function revayat_get_media_observatory_data(): array {
    return [
        'items' => [
            [
                'id'          => 50,
                'title'       => __('چطور واژه «ثبات» در چهار رسانه، چهار معنای کاملاً متضاد پیدا کرد؟', 'revayatiran'),
                'url'         => home_url('/observatory/'),
                'badge_slug'  => 'obs-badge--discourse',
                'badge_label' => __('تحلیل گفتمان', 'revayatiran'),
                'frame_a'     => [
                    'source' => __('رسانه‌های رسمی:', 'revayatiran'),
                    'text'   => __('«ثبات به‌مثابه آرامش بازار و مهار نوسانات کوتاه‌مدت»', 'revayatiran'),
                ],
                'frame_b'     => [
                    'source' => __('رسانه‌های منتقد:', 'revayatiran'),
                    'text'   => __('«ثبات به‌مثابه تعمیق رکود و تاخیر در تخلیه تورم»', 'revayatiran'),
                ],
                'summary'     => __('واکاوی تکنیک تقلیل مفاهیم اقتصادی کلان به برداشت‌های جناحی و نحوه بهره‌برداری از ابهام زبانی در جهت‌دهی به انتظارات تورمی افکار عمومی.', 'revayatiran'),
            ],
            [
                'id'          => 51,
                'title'       => __('یک رویداد، دو جهان موازی؛ واکاوی صورت‌بندی رسانه‌ای تحولات مرزی', 'revayatiran'),
                'url'         => home_url('/observatory/'),
                'badge_slug'  => 'obs-badge--framing',
                'badge_label' => __('مقایسه فریمینگ', 'revayatiran'),
                'frame_a'     => [
                    'source' => __('رسانه‌های منطقه‌ای:', 'revayatiran'),
                    'text'   => __('«آرایش بازدارنده در پاسخ به تحرکات مشکوک خارجی»', 'revayatiran'),
                ],
                'frame_b'     => [
                    'source' => __('خبرگزاری‌های بین‌المللی:', 'revayatiran'),
                    'text'   => __('«تشدید تنش و سیگنال‌های ناپایداری امنیتی در مرز»', 'revayatiran'),
                ],
                'summary'     => __('بررسی شیوه‌های برجسته‌سازی و چینش واژگان حامل بار عاطفی برای تغییر ادراک مخاطب از یک مانور روتین پدافندی.', 'revayatiran'),
            ],
            [
                'id'          => 52,
                'title'       => __('موتیف‌های هماهنگ؛ ردپای یک کمپین شبکه‌ای در اخبار حوزه انرژی', 'revayatiran'),
                'url'         => home_url('/observatory/'),
                'badge_slug'  => 'obs-badge--campaign',
                'badge_label' => __('کمپین‌شناسی', 'revayatiran'),
                'frame_a'     => [
                    'source' => __('کلیدواژه محوری:', 'revayatiran'),
                    'text'   => __('«شمارش معکوس برای بحران حتمی سوخت در پاییز»', 'revayatiran'),
                ],
                'frame_b'     => [
                    'source' => __('داده‌های میدانی:', 'revayatiran'),
                    'text'   => __('«رشد ۸٪ ذخایر استراتژیک و مدیریت تقاضای صنعتی»', 'revayatiran'),
                ],
                'summary'     => __('شناسایی الگوی تزریق همزمان ۱۲ گزاره خبری با ساختار نحوی همسان در شبکه‌های اجتماعی برای ایجاد احساس اضطرار کاذب در بازار.', 'revayatiran'),
            ],
        ],
    ];
}

/**
 * ۷. داده‌های دمو بخش شبکه تحلیل‌گران (Analysts Network Section)
 *
 * @return array
 */
function revayat_get_analysts_network_data(): array {
    return [
        'editorial' => [
            'id'            => 60,
            'title'         => __('ابعاد ژئوپلیتیک ناترازی انرژی؛ سناریوهای بازدارندگی و دیپلماسی سوخت', 'revayatiran'),
            'desc'          => __('تحلیلی جامع بر ارزیابی متغیرهای اقتصادی و امنیتی ناترازی انرژی و نقش دیپلماسی ترانزیت در خلیج فارس.', 'revayatiran'),
            'thumbnail_url' => revayat_demo_post_img('muCE4NJA7dFf.jpg'),
            'author_name'   => __('دکتر پیمان شریفی', 'revayatiran'),
            'author_role'   => __('پژوهشگر ارشد سیاست عمومی', 'revayatiran'),
            'author_avatar' => revayat_demo_avatar_img('رضانصری.webp'),
            'score'         => '۴.۹',
            'votes_count'   => __('۱۲۵۰ رای', 'revayatiran'),
            'date'          => __('۲۴ تیر ۱۴۰۳', 'revayatiran'),
            'url'           => home_url('/analysts/'),
        ],
        'analyses' => [
            [
                'id'            => 61,
                'title'         => __('چرا افکار عمومی دیگر با تیترهای خطی قانع نمی‌شود؟', 'revayatiran'),
                'url'           => home_url('/analysts/'),
                'author_name'   => __('بابک صادقی', 'revayatiran'),
                'author_avatar' => revayat_demo_avatar_img('0f1b74871a3e46e7ae950c05c65e6d2d.png'),
                'score'         => '۴.۸',
            ],
            [
                'id'            => 62,
                'title'         => __('آیا اتحادهای موقت منطقه‌ای به بازدارندگی تبدیل می‌شوند؟', 'revayatiran'),
                'url'           => home_url('/analysts/'),
                'author_name'   => __('مریم هوشمندی', 'revayatiran'),
                'author_avatar' => revayat_demo_avatar_img('14eeb1b8dc3a4bbd905999e407a01725.png'),
                'score'         => '۴.۷',
            ],
            [
                'id'            => 63,
                'title'         => __('تنش‌های ارزی و سناریوهای بازارهای مالی در فصل جدید', 'revayatiran'),
                'url'           => home_url('/analysts/'),
                'author_name'   => __('احسان طاهری', 'revayatiran'),
                'author_avatar' => revayat_demo_avatar_img('56d604c11de44ed4b583e8f8b81626b3.png'),
                'score'         => '۴.۶',
            ],
            [
                'id'            => 64,
                'title'         => __('تحولات بازار بورس و تاثیر سیاست‌های پولی بر شاخص‌ها', 'revayatiran'),
                'url'           => home_url('/analysts/'),
                'author_name'   => __('الهام شریفی', 'revayatiran'),
                'author_avatar' => revayat_demo_avatar_img('634eb20869b740fdbc5b7c8427a8f674.png'),
                'score'         => '۴.۵',
            ],
            [
                'id'            => 65,
                'title'         => __('پیامدهای رزمایش‌های مشترک در تعادل قدرت منطقه‌ای', 'revayatiran'),
                'url'           => home_url('/analysts/'),
                'author_name'   => __('نرگس احمدی', 'revayatiran'),
                'author_avatar' => revayat_demo_avatar_img('8fc8b30f66b4489aaeb92b686a386cdc.png'),
                'score'         => '۴.۴',
            ],
            [
                'id'            => 66,
                'title'         => __('تحلیل الگوهای انتخاباتی و پیش‌بینی روند قانون‌گذاری', 'revayatiran'),
                'url'           => home_url('/analysts/'),
                'author_name'   => __('پیمان شریفی', 'revayatiran'),
                'author_avatar' => revayat_demo_avatar_img('a9d7f2cbb32944f1bcd60800dda1d236.png'),
                'score'         => '۴.۳',
            ],
        ],
        'analysts' => [
            [
                'rank'           => '۱',
                'name'           => __('نرگس احمدی', 'revayatiran'),
                'specialty'      => __('متخصص ژئوپلیتیک', 'revayatiran'),
                'avatar_url'     => revayat_demo_avatar_img('رضانصری.webp'),
                'score'          => '۴.۹',
                'analyses_count' => '۱۴',
                'votes_count'    => '۶۲۰',
            ],
            [
                'rank'           => '۲',
                'name'           => __('مهندس احسان طاهری', 'revayatiran'),
                'specialty'      => __('اقتصاد سیاسی و فناوری', 'revayatiran'),
                'avatar_url'     => revayat_demo_avatar_img('0f1b74871a3e46e7ae950c05c65e6d2d.png'),
                'score'          => '۴.۸',
                'analyses_count' => '۱۰',
                'votes_count'    => '۴۸۰',
            ],
            [
                'rank'           => '۳',
                'name'           => __('دکتر الهام شریفی', 'revayatiran'),
                'specialty'      => __('جامعه‌شناسی سیاسی', 'revayatiran'),
                'avatar_url'     => revayat_demo_avatar_img('14eeb1b8dc3a4bbd905999e407a01725.png'),
                'score'          => '۴.۷',
                'analyses_count' => '۸',
                'votes_count'    => '۳۹۰',
            ],
            [
                'rank'           => '۴',
                'name'           => __('رضا کمالی', 'revayatiran'),
                'specialty'      => __('پژوهشگر رسانه', 'revayatiran'),
                'avatar_url'     => revayat_demo_avatar_img('56d604c11de44ed4b583e8f8b81626b3.png'),
                'score'          => '۴.۶',
                'analyses_count' => '۷',
                'votes_count'    => '۲۸۰',
            ],
            [
                'rank'           => '۵',
                'name'           => __('مریم هوشمندی', 'revayatiran'),
                'specialty'      => __('امنیت ملی و دیپلماسی', 'revayatiran'),
                'avatar_url'     => revayat_demo_avatar_img('634eb20869b740fdbc5b7c8427a8f674.png'),
                'score'          => '۴.۵',
                'analyses_count' => '۶',
                'votes_count'    => '۲۴۰',
            ],
        ],
    ];
}

/**
 * ۸. داده‌های دمو بخش چندرسانه‌ای و استوری‌ریل (Multimedia Section)
 *
 * @return array
 */
function revayat_get_multimedia_data(): array {
    return [
        'stories' => [
            [
                'id'            => 70,
                'title'         => __('بندر گوادر؛ گلوگاه ژئوپلیتیک دریای عمان', 'revayatiran'),
                'url'           => home_url('/multimedia/?id=70&type=video'),
                'thumbnail_url' => revayat_demo_post_img('27738882_1_0_799_449_1920x0_80_0_0_5c1b2e4e4887372d25d2ddaca10d8bb0.jpg.webp'),
                'media_type'    => 'video',
                'chip_label'    => __('مستند تصویری', 'revayatiran'),
                'duration'      => '۰۴:۱۵',
            ],
            [
                'id'            => 71,
                'title'         => __('تحلیل سناریوهای ناترازی انرژی کشور', 'revayatiran'),
                'url'           => home_url('/multimedia/?id=71&type=audio'),
                'thumbnail_url' => revayat_demo_post_img('اقتصادآمریکا-ak1634-1200x800-1024x683.webp'),
                'media_type'    => 'audio',
                'chip_label'    => __('پادکست اختصاصی', 'revayatiran'),
                'duration'      => '۱۸:۴۰',
            ],
            [
                'id'            => 72,
                'title'         => __('نقش کریدورهای جدید آسیای مرکزی', 'revayatiran'),
                'url'           => home_url('/multimedia/?id=72&type=gallery'),
                'thumbnail_url' => revayat_demo_post_img('1404041822335791233405404.jpg'),
                'media_type'    => 'gallery',
                'chip_label'    => __('گالری تصاویر', 'revayatiran'),
                'duration'      => __('۶ تصویر', 'revayatiran'),
            ],
            [
                'id'            => 73,
                'title'         => __('هوشمندسازی و پایش مرزهای شرقی', 'revayatiran'),
                'url'           => home_url('/multimedia/?id=73&type=video'),
                'thumbnail_url' => revayat_demo_post_img('1011664_329.webp'),
                'media_type'    => 'video',
                'chip_label'    => __('رصد میدانی', 'revayatiran'),
                'duration'      => '۰۸:۵۰',
            ],
            [
                'id'            => 74,
                'title'         => __('موتیف‌های کلیدی کمپین‌های هفته', 'revayatiran'),
                'url'           => home_url('/multimedia/?id=74&type=video'),
                'thumbnail_url' => revayat_demo_post_img('بورس-1-ak6185-1024x683.webp'),
                'media_type'    => 'video',
                'chip_label'    => __('ویدئوکست', 'revayatiran'),
                'duration'      => '۰۵:۳۰',
            ],
            [
                'id'            => 75,
                'title'         => __('بررسی کریدورهای مالی همسایگان', 'revayatiran'),
                'url'           => home_url('/multimedia/?id=75&type=audio'),
                'thumbnail_url' => revayat_demo_post_img('2279480_168.jpg'),
                'media_type'    => 'audio',
                'chip_label'    => __('پادکست تحلیل', 'revayatiran'),
                'duration'      => '۱۴:۲۰',
            ],
            [
                'id'            => 76,
                'title'         => __('رصد هوایی زیرساخت‌های خلیج فارس', 'revayatiran'),
                'url'           => home_url('/multimedia/?id=76&type=gallery'),
                'thumbnail_url' => revayat_demo_post_img('صندوق_هایترکیبیcopy-ak8765-ak6517-1200x800-1024x683.webp'),
                'media_type'    => 'gallery',
                'chip_label'    => __('گزارش تصویری', 'revayatiran'),
                'duration'      => __('۸ تصویر', 'revayatiran'),
            ],
            [
                'id'            => 77,
                'title'         => __('بازبازنمایی رویدادها در رسانه‌های غربی', 'revayatiran'),
                'url'           => home_url('/multimedia/?id=77&type=video'),
                'thumbnail_url' => revayat_demo_post_img('1404092612513063035069134.jpg'),
                'media_type'    => 'video',
                'chip_label'    => __('تحلیل گفتمان', 'revayatiran'),
                'duration'      => '۰۹:۱۵',
            ],
        ],
    ];
}
