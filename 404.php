<?php
/**
 * تمپلیت صفحه خطای ۴۰۴ (Not Found)
 *
 * @package RevayatIran
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div class="container py-16 md:py-24 text-center">
    <div class="max-w-xl mx-auto bg-surface-strong p-8 md:p-12 rounded-3xl border border-border shadow-soft">
        <span class="text-7xl md:text-8xl font-black text-primary block mb-4 tracking-tighter">۴۰۴</span>
        <h1 class="text-xl md:text-2xl font-bold text-text mb-3">
            <?php esc_html_e('صفحه مورد نظر شما یافت نشد!', 'revayatiran'); ?>
        </h1>
        <p class="text-text-muted text-sm leading-relaxed mb-8">
            <?php esc_html_e('نشانی وارد شده تغییر کرده، حذف شده یا وجود ندارد. می‌توانید از طریق نوار زیر جستجو کنید یا به صفحه اصلی بازگردید.', 'revayatiran'); ?>
        </p>

        <div class="flex items-center justify-center gap-4 flex-wrap">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="button button--primary inline-flex items-center gap-2">
                <i class="ph ph-house"></i>
                <span><?php esc_html_e('بازگشت به صفحه اصلی', 'revayatiran'); ?></span>
            </a>
            <button type="button" class="button button--ghost inline-flex items-center gap-2" onclick="document.getElementById('search-overlay').classList.add('is-active');">
                <i class="ph ph-magnifying-glass"></i>
                <span><?php esc_html_e('جستجوی پیشرفته', 'revayatiran'); ?></span>
            </button>
        </div>
    </div>
</div>

<?php
get_footer();
