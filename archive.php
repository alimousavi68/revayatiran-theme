<?php
/**
 * تمپلیت نمایش صفحات آرشیو عمومی (Archive Template)
 *
 * @package RevayatIran
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
$has_sidebar = is_active_sidebar( 'public-archive-sidebar' );
?>

<section class="container py-8 md:py-12 rv-public-archive">
    <header class="archive-header rv-archive-hero mb-10">
        <div class="flex items-center gap-2 text-xs font-bold text-primary mb-2">
            <i class="ph ph-folder-notch-open"></i>
            <span><?php esc_html_e('آرشیو محتوا', 'revayatiran'); ?></span>
        </div>
        <h1 class="text-2xl md:text-3xl font-black text-text m-0">
            <?php is_category() ? single_cat_title() : the_archive_title(); ?>
        </h1>
        <?php if (get_the_archive_description()) : ?>
            <div class="archive-description text-sm text-text-muted mt-3 max-w-2xl leading-relaxed">
                <?php the_archive_description(); ?>
            </div>
        <?php endif; ?>
    </header>

    <div class="rv-archive-layout<?php echo $has_sidebar ? ' has-sidebar' : ''; ?>">
    <div class="rv-archive-content">
    <?php if (have_posts()) : ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 rv-archive-grid">
            <?php
            while (have_posts()) :
                the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('rv-archive-card panel-card flex flex-col justify-between p-5 rounded-xl border border-border bg-surface-strong hover:shadow-soft transition-all'); ?>>
                    <div>
                            <div class="aspect-video mb-4 rounded-lg overflow-hidden bg-bg-soft">
                                <a href="<?php the_permalink(); ?>">
                                    <img src="<?php echo esc_url( revayat_get_post_image_url( get_post() ) ); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-cover" width="640" height="360" loading="lazy" decoding="async">
                                </a>
                            </div>

                        <div class="flex items-center gap-2 mb-2 text-xs text-text-muted rv-archive-meta">
                            <?php revayat_posted_on(); ?>
                            <span class="w-1 h-1 rounded-full bg-border"></span>
                            <span><?php echo esc_html(revayat_reading_time()); ?></span>
                        </div>

                        <h2 class="text-lg font-bold text-text mb-3 leading-snug rv-archive-title">
                            <a href="<?php the_permalink(); ?>" class="hover:text-primary transition-colors">
                                <?php the_title(); ?>
                            </a>
                        </h2>

                        <div class="text-sm text-text-muted leading-relaxed line-clamp-3">
                            <?php the_excerpt(); ?>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-border/60 flex items-center justify-between text-xs rv-archive-card-footer">
                        <span class="text-text-muted font-medium"><?php the_author(); ?></span>
                        <a href="<?php the_permalink(); ?>" class="text-primary font-bold inline-flex items-center gap-1 hover:underline">
                            <?php esc_html_e('مشاهده کامل', 'revayatiran'); ?>
                            <i class="ph ph-arrow-left"></i>
                        </a>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>

        <div class="mt-10 flex justify-center">
            <?php
            the_posts_pagination([
                'prev_text' => '<i class="ph ph-caret-right"></i> ' . esc_html__('قبلی', 'revayatiran'),
                'next_text' => esc_html__('بعدی', 'revayatiran') . ' <i class="ph ph-caret-left"></i>',
                'class'     => 'pagination-nav flex items-center gap-2',
            ]);
            ?>
        </div>

    <?php else : ?>
        <div class="text-center py-16 bg-surface-strong rounded-2xl border border-border">
            <i class="ph ph-folder-notch-open text-5xl text-text-muted mb-4 block"></i>
            <h2 class="text-xl font-bold text-text mb-2"><?php esc_html_e('محتوایی در این آرشیو موجود نیست', 'revayatiran'); ?></h2>
            <p class="text-text-muted text-sm max-w-md mx-auto mb-6"><?php esc_html_e('هنوز مطلبی در این دسته‌بندی یا موضوع ثبت نشده است.', 'revayatiran'); ?></p>
            <a href="<?php echo esc_url(home_url('/')); ?>" class="button button--primary inline-flex items-center gap-2">
                <i class="ph ph-house"></i>
                <span><?php esc_html_e('بازگشت به صفحه اصلی', 'revayatiran'); ?></span>
            </a>
        </div>
    <?php endif; ?>
    </div>
    <?php get_template_part( 'template-parts/archive/sidebar' ); ?>
    </div>
</section>

<?php
get_footer();
