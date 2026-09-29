<?php
/**
 * تمپلیت آرشیو اختصاصی پرونده‌های ویژه (Archive Special Dossier Template)
 *
 * انطباق ۱۰۰٪ با ساختار و دیزاین سیستم Source اصلی (orginal source/archive.php).
 *
 * @package RevayatIran
 * @subpackage Templates
 * @version 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$paged        = max(1, (int) get_query_var('paged', 1));
$active_search = sanitize_text_field(wp_unslash($_GET['q'] ?? ''));
$active_topic  = sanitize_key(wp_unslash($_GET['topic'] ?? (get_query_var('dossier_topic') ?: 'all')));
$active_sort   = sanitize_key(wp_unslash($_GET['sort'] ?? 'date'));
$archive_data = function_exists('revayat_get_archive_data') ? revayat_get_archive_data('special_dossier', [
    'paged'  => $paged,
    'search' => $active_search,
    'topic'  => $active_topic,
    'sort'   => $active_sort,
]) : [];
$dossiers     = $archive_data['items'] ?? [];
$total_posts  = $archive_data['total_posts'] ?? count($dossiers);
$max_pages    = $archive_data['max_pages'] ?? 1;
$active_search = $archive_data['active_search'] ?? $active_search;
$active_topic  = $archive_data['active_topic'] ?? $active_topic;
$active_sort   = $archive_data['active_sort'] ?? $active_sort;
$archive_url   = get_post_type_archive_link('special_dossier') ?: home_url('/dossiers/');

// استخراج تاکسونومی‌های موضوعی پرونده جهت فیلتر
$topics = taxonomy_exists('dossier_topic') ? get_terms([
    'taxonomy'   => 'dossier_topic',
    'hide_empty' => false,
]) : [];
?>

<div class="rv-archive rv-archive--dossiers bg-gray-50/50 dark:bg-black/20 min-h-screen pb-16">
    <section class="pt-8 md:pt-10" id="archive-content">
        <div class="container">

            <!-- ═══ نوار عنوان صفحه، تعداد مطالب و مرتب‌سازی (مطابق با سورس) ═══ -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-[var(--border)]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white dark:bg-white/10 border border-[var(--border)] shadow-sm text-primary flex items-center justify-center shrink-0">
                        <i class="ph ph-folder-open text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-lg md:text-xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                            <?php esc_html_e('پرونده‌های ویژه', 'revayatiran'); ?>
                        </h1>
                        <p class="text-gray-500 dark:text-gray-400 text-[11px] md:text-xs mt-0.5">
                            <?php 
                            printf(
                                /* translators: 1: count, 2: total */
                                esc_html__('نمایش %1$s از %2$s پرونده تحلیلی و راهبردی', 'revayatiran'),
                                function_exists('revayat_en2fa') ? revayat_en2fa(count($dossiers)) : count($dossiers),
                                function_exists('revayat_en2fa') ? revayat_en2fa($total_posts) : $total_posts
                            );
                            ?>
                        </p>
                    </div>
                </div>

                <!-- فیلد مرتب‌سازی (طراحی سورس) -->
                <form method="get" action="<?php echo esc_url($archive_url); ?>" class="rv-archive-sort flex items-center justify-between sm:justify-start w-full sm:w-auto gap-2 bg-white dark:bg-white/5 px-3.5 py-2 rounded-xl border border-[var(--border)] shadow-sm">
                    <?php if ($active_search) : ?><input type="hidden" name="q" value="<?php echo esc_attr($active_search); ?>"><?php endif; ?>
                    <?php if ('all' !== $active_topic) : ?><input type="hidden" name="topic" value="<?php echo esc_attr($active_topic); ?>"><?php endif; ?>
                    <span class="text-[13px] text-gray-500 shrink-0 flex items-center gap-1.5">
                        <i class="ph ph-arrows-down-up text-primary text-sm"></i>
                        <?php esc_html_e('مرتب‌سازی:', 'revayatiran'); ?>
                    </span>
                    <select name="sort" onchange="this.form.submit()" aria-label="<?php esc_attr_e('مرتب‌سازی پرونده‌ها', 'revayatiran'); ?>" class="bg-transparent text-[13px] font-semibold text-gray-900 dark:text-white border-none outline-none cursor-pointer focus:ring-0">
                        <option value="date" <?php selected($active_sort, 'date'); ?>><?php esc_html_e('جدیدترین', 'revayatiran'); ?></option>
                        <option value="modified" <?php selected($active_sort, 'modified'); ?>><?php esc_html_e('آخرین بروزرسانی', 'revayatiran'); ?></option>
                        <option value="title" <?php selected($active_sort, 'title'); ?>><?php esc_html_e('عنوان الفبایی', 'revayatiran'); ?></option>
                    </select>
                </form>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">

                <!-- ═══ سایدبار (جستجو و فیلترها - اکاردیونی در موبایل، همیشه باز در دسکتاپ) ═══ -->
                <aside class="w-full lg:w-[280px] shrink-0 lg:sticky lg:top-28 self-start">

                    <div class="bg-white dark:bg-white/5 rounded-2xl border border-[var(--border)] shadow-sm overflow-hidden lg:border-0 lg:bg-transparent lg:dark:bg-transparent lg:shadow-none lg:rounded-none lg:overflow-visible">

                        <!-- هدر آکاردئون موبایل -->
                        <button type="button" 
                                id="archive-filter-toggle" 
                                class="w-full lg:hidden flex items-center justify-between p-4 bg-gray-50/80 dark:bg-white/5 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors text-right cursor-pointer"
                                aria-expanded="false" 
                                aria-controls="archive-filter-body">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                                    <i class="ph ph-faders text-lg"></i>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white block"><?php esc_html_e('جستجو و فیلتر پرونده‌ها', 'revayatiran'); ?></span>
                                    <span class="text-[11px] text-gray-500 dark:text-gray-400 block font-normal"><?php esc_html_e('کلیک کنید برای فیلتر نتایج', 'revayatiran'); ?></span>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 text-gray-400">
                                <i class="ph ph-caret-down text-lg transition-transform duration-300" id="archive-filter-icon"></i>
                            </div>
                        </button>

                        <!-- بدنه آکاردئون سایدبار -->
                        <div id="archive-filter-body" class="archive-filter-accordion-content lg:!block">
                            <div class="archive-filter-accordion-inner p-4 lg:p-0 space-y-5 lg:space-y-6">

                                <!-- ویجت جستجو -->
                                <div class="bg-gray-50/60 dark:bg-white/[0.03] lg:bg-white lg:dark:bg-white/5 rounded-2xl p-4 lg:p-5 border border-[var(--border)] lg:shadow-sm">
                                    <h3 class="text-[14px] lg:text-[15px] font-bold text-gray-900 dark:text-white mb-3.5 lg:mb-4 flex items-center gap-2">
                                        <i class="ph ph-magnifying-glass text-primary"></i> <?php esc_html_e('جستجو در پرونده‌ها', 'revayatiran'); ?>
                                    </h3>
                                    <form role="search" method="get" action="<?php echo esc_url($archive_url); ?>" class="relative">
                                        <?php if ('all' !== $active_topic) : ?><input type="hidden" name="topic" value="<?php echo esc_attr($active_topic); ?>"><?php endif; ?>
                                        <input type="hidden" name="sort" value="<?php echo esc_attr($active_sort); ?>">
                                        <label class="screen-reader-text" for="dossier-search"><?php esc_html_e('جستجو در پرونده‌های ویژه', 'revayatiran'); ?></label>
                                        <input id="dossier-search" type="search" name="q" value="<?php echo esc_attr($active_search); ?>" maxlength="100" placeholder="<?php esc_attr_e('عنوان، کلیدواژه یا کد پرونده...', 'revayatiran'); ?>" class="w-full bg-white dark:bg-black/30 lg:bg-gray-50 lg:dark:bg-black/20 border border-[var(--border)] rounded-xl py-2.5 px-4 text-sm text-gray-800 dark:text-gray-200 focus:outline-none focus:border-primary/50 transition-colors">
                                        <button type="submit" aria-label="<?php esc_attr_e('اعمال جستجو', 'revayatiran'); ?>" class="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-primary transition-colors">
                                            <i class="ph ph-arrow-left"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- ویجت فیلتر محورهای موضوعی -->
                                <?php if (!empty($topics) && !is_wp_error($topics)) : ?>
                                    <div class="bg-gray-50/60 dark:bg-white/[0.03] lg:bg-white lg:dark:bg-white/5 rounded-2xl p-4 lg:p-5 border border-[var(--border)] lg:shadow-sm">
                                        <h3 class="text-[14px] lg:text-[15px] font-bold text-gray-900 dark:text-white mb-3.5 lg:mb-4 flex items-center gap-2">
                                            <i class="ph ph-folders text-primary"></i> <?php esc_html_e('محورهای راهبردی', 'revayatiran'); ?>
                                        </h3>
                                        <ul class="space-y-2">
                                            <li>
                                                <a href="<?php echo esc_url($archive_url); ?>" class="flex items-center justify-between p-2.5 rounded-xl text-xs font-bold transition-all <?php echo 'all' === $active_topic ? 'bg-primary text-white shadow-sm' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/5'; ?>">
                                                    <span class="flex items-center gap-2">
                                                        <span class="w-2 h-2 rounded-full <?php echo 'all' === $active_topic ? 'bg-white' : 'bg-primary'; ?>"></span>
                                                        <?php esc_html_e('همه پرونده‌ها', 'revayatiran'); ?>
                                                    </span>
                                                    <span class="text-[10px] <?php echo 'all' === $active_topic ? 'text-white/80' : 'text-gray-400'; ?>"><?php echo esc_html(function_exists('revayat_en2fa') ? revayat_en2fa($total_posts) : $total_posts); ?></span>
                                                </a>
                                            </li>
                                            <?php foreach ($topics as $topic_term) : 
                                                $is_active = ($active_topic === $topic_term->slug);
                                                $topic_url = add_query_arg(array_filter(['topic' => $topic_term->slug, 'q' => $active_search, 'sort' => $active_sort]), $archive_url);
                                            ?>
                                                <li>
                                                    <a href="<?php echo esc_url($topic_url); ?>" class="flex items-center justify-between p-2.5 rounded-xl text-xs font-medium transition-all <?php echo $is_active ? 'bg-primary text-white shadow-sm font-bold' : 'text-gray-600 dark:text-gray-400 hover:text-primary hover:bg-gray-100 dark:hover:bg-white/5'; ?>">
                                                        <span class="flex items-center gap-2 truncate">
                                                            <span class="w-2 h-2 rounded-full <?php echo $is_active ? 'bg-white' : 'bg-gray-300 dark:bg-gray-600'; ?>"></span>
                                                            <span class="truncate"><?php echo esc_html($topic_term->name); ?></span>
                                                        </span>
                                                        <span class="text-[10px] <?php echo $is_active ? 'text-white/80' : 'text-gray-400'; ?> shrink-0">(<?php echo esc_html(function_exists('revayat_en2fa') ? revayat_en2fa($topic_term->count) : $topic_term->count); ?>)</span>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>

                            </div>
                        </div>

                    </div>

                </aside>

                <!-- ═══ ستون اصلی محتوا (Grid ۲ ستونه مطابق با Source) ═══ -->
                <div class="flex-1 min-w-0">

                    <?php if (!empty($dossiers)) : ?>
                        <p class="rv-archive-result-count" aria-live="polite"><?php printf(esc_html__('%s نتیجه یافت شد', 'revayatiran'), esc_html(function_exists('revayat_en2fa') ? revayat_en2fa($total_posts) : $total_posts)); ?></p>
                        <div class="rv-archive-grid rv-archive-grid--dossiers grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                            <?php foreach ($dossiers as $dossier_item) : ?>
                                <?php get_template_part('template-parts/cards/card-dossier', null, ['item' => $dossier_item]); ?>
                            <?php endforeach; ?>
                        </div>

                        <!-- ═══ صفحه‌بندی مدرن مطابق با سورس (Pagination) ═══ -->
                        <?php if ($max_pages > 1) : ?>
                            <div class="flex justify-center items-center gap-1.5 mt-10">
                                <?php
                                $pagination_links = paginate_links([
                                    'base'      => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
                                    'format'    => '?paged=%#%',
                                    'current'   => $paged,
                                    'total'     => $max_pages,
                                    'type'      => 'array',
                                    'prev_text' => '<i class="ph ph-caret-right text-lg"></i>',
                                    'next_text' => '<i class="ph ph-caret-left text-lg"></i>',
                                ]);

                                if (!empty($pagination_links)) {
                                    foreach ($pagination_links as $link) {
                                        // اگر صفحه جاری است
                                        if (strpos($link, 'current') !== false) {
                                            $clean_num = strip_tags($link);
                                            echo '<span class="w-10 h-10 flex items-center justify-center rounded-xl border border-primary bg-primary text-white font-bold shadow-sm shadow-primary/20 transition-all">' . esc_html(function_exists('revayat_en2fa') ? revayat_en2fa($clean_num) : $clean_num) . '</span>';
                                        } elseif (strpos($link, 'dots') !== false) {
                                            echo '<span class="flex items-center justify-center w-8 text-gray-400">...</span>';
                                        } else {
                                            // لینک‌های دیگر
                                            $link = preg_replace('/class="([^"]*)"/', 'class="w-10 h-10 flex items-center justify-center rounded-xl border border-[var(--border)] bg-white dark:bg-white/5 text-gray-700 dark:text-gray-300 hover:text-primary hover:border-primary/50 transition-all shadow-sm $1"', $link);
                                            if (function_exists('revayat_en2fa')) {
                                                $link = preg_replace_callback('/>(\d+)</', function ($m) {
                                                    return '>' . revayat_en2fa($m[1]) . '<';
                                                }, $link);
                                            }
                                            echo wp_kses_post($link);
                                        }
                                    }
                                }
                                ?>
                            </div>
                        <?php endif; ?>

                    <?php else : ?>

                        <!-- وضعیت خالی (Empty State) -->
                        <div class="text-center py-16 px-6 bg-white dark:bg-[#1a1c23] rounded-2xl border border-[var(--border)] shadow-sm max-w-xl mx-auto my-6">
                            <div class="w-16 h-16 rounded-2xl bg-gray-50 dark:bg-white/5 text-gray-400 flex items-center justify-center text-3xl mx-auto mb-4 border border-[var(--border)]">
                                <i class="ph ph-folder-dashed"></i>
                            </div>
                            <h2 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white mb-2">
                                <?php esc_html_e('پرونده‌ای در این بخش یافت نشد', 'revayatiran'); ?>
                            </h2>
                            <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 leading-relaxed mb-6">
                                <?php esc_html_e('در حال حاضر پرونده فعالی در این محور منتشر نشده است. پرونده‌های جدید پس از تکمیل ارزیابی‌های تحریریه اضافه خواهند شد.', 'revayatiran'); ?>
                            </p>
                            <a href="<?php echo esc_url($archive_url); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white text-xs font-bold rounded-xl hover:bg-primary-dark transition-colors shadow-sm shadow-primary/20">
                                <i class="ph ph-house"></i>
                                <span><?php esc_html_e('بازگشت به صفحه اصلی', 'revayatiran'); ?></span>
                            </a>
                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>
    </section>
</div>

<?php
get_footer();
