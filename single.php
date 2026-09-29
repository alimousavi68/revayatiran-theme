<?php
/**
 * تمپلیت نمایش تک نوشته‌های استاندارد (Single Post)
 *
 * @package RevayatIran
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div class="container py-8 md:py-12">
    <?php
    while (have_posts()) :
        the_post();
        $primary_cat = revayat_get_primary_category();
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('max-w-4xl mx-auto'); ?>>
            <header class="entry-header mb-8">
                <?php if ($primary_cat) : ?>
                    <div class="mb-3">
                        <a href="<?php echo esc_url(get_category_link($primary_cat->term_id)); ?>" class="chip chip--alert font-bold">
                            <?php echo esc_html($primary_cat->name); ?>
                        </a>
                    </div>
                <?php endif; ?>

                <h1 class="text-2xl md:text-4xl font-black text-text leading-tight mb-4">
                    <?php the_title(); ?>
                </h1>

                <div class="flex items-center flex-wrap gap-4 text-xs text-text-muted pb-4 border-b border-border">
                    <span class="flex items-center gap-1.5 font-medium">
                        <i class="ph ph-user"></i>
                        <?php the_author(); ?>
                    </span>
                    <span class="flex items-center gap-1.5">
                        <i class="ph ph-calendar"></i>
                        <?php revayat_posted_on(); ?>
                    </span>
                    <span class="flex items-center gap-1.5">
                        <i class="ph ph-clock"></i>
                        <?php echo esc_html(revayat_reading_time()); ?>
                    </span>
                </div>
            </header>

            <?php if (has_post_thumbnail()) : ?>
                <div class="entry-thumbnail mb-8 rounded-2xl overflow-hidden shadow-soft border border-border">
                    <?php the_post_thumbnail('revayat-hero-large', ['class' => 'w-full h-auto object-cover max-h-[500px]']); ?>
                </div>
            <?php endif; ?>

            <div class="entry-content prose prose-lg max-w-none text-text leading-loose font-normal">
                <?php the_content(); ?>
            </div>

            <footer class="entry-footer mt-10 pt-6 border-t border-border flex flex-wrap items-center justify-between gap-4">
                <div class="post-tags flex items-center gap-2 flex-wrap">
                    <?php the_tags('<span class="text-xs text-text-muted font-bold">' . esc_html__('برچسب‌ها:', 'revayatiran') . '</span> ', ' ', ''); ?>
                </div>

                <div class="post-navigation-links flex items-center gap-3">
                    <?php
                    previous_post_link('<span class="button button--ghost text-xs">%link</span>', '<i class="ph ph-arrow-right"></i> ' . esc_html__('مطلب قبلی', 'revayatiran'));
                    next_post_link('<span class="button button--ghost text-xs">%link</span>', esc_html__('مطلب بعدی', 'revayatiran') . ' <i class="ph ph-arrow-left"></i>');
                    ?>
                </div>
            </footer>
        </article>
    <?php endwhile; ?>
</div>

<?php
get_footer();
