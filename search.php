<?php
/**
 * تمپلیت نمایش نتایج جستجو (Search Results)
 *
 * @package RevayatIran
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div class="container py-8 md:py-12">
    <header class="search-header mb-10 pb-6 border-b border-border">
        <div class="flex items-center gap-2 text-xs font-bold text-primary mb-2">
            <i class="ph ph-magnifying-glass"></i>
            <span><?php esc_html_e('نتایج جستجو', 'revayatiran'); ?></span>
        </div>
        <h1 class="text-2xl md:text-3xl font-black text-text m-0">
            <?php
            /* translators: %s: Search Query */
            printf(esc_html__('نتایج جستجو برای: «%s»', 'revayatiran'), '<span class="text-primary">' . esc_html(get_search_query()) . '</span>');
            ?>
        </h1>
    </header>

    <?php if (have_posts()) : ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('panel-card flex flex-col justify-between p-5 rounded-xl border border-border bg-surface-strong hover:shadow-soft transition-all'); ?>>
                    <div>
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="aspect-video mb-4 rounded-lg overflow-hidden bg-bg-soft">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('revayat-card-thumb', ['class' => 'w-full h-full object-cover']); ?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="flex items-center gap-2 mb-2 text-xs text-text-muted">
                            <?php revayat_posted_on(); ?>
                            <span class="w-1 h-1 rounded-full bg-border"></span>
                            <span><?php echo esc_html(revayat_reading_time()); ?></span>
                        </div>

                        <h2 class="text-lg font-bold text-text mb-3 leading-snug">
                            <a href="<?php the_permalink(); ?>" class="hover:text-primary transition-colors">
                                <?php the_title(); ?>
                            </a>
                        </h2>

                        <div class="text-sm text-text-muted leading-relaxed line-clamp-3">
                            <?php the_excerpt(); ?>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-border/60 flex items-center justify-between text-xs">
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
            <i class="ph ph-magnifying-glass text-5xl text-text-muted mb-4 block"></i>
            <h2 class="text-xl font-bold text-text mb-2"><?php esc_html_e('هیچ نتیجه‌ای یافت نشد', 'revayatiran'); ?></h2>
            <p class="text-text-muted text-sm max-w-md mx-auto mb-6"><?php esc_html_e('متاسفانه مطلبی متناسب با واژه جستجو شده یافت نشد. لطفاً عبارت دیگری را امتحان کنید.', 'revayatiran'); ?></p>
            <div class="max-w-md mx-auto">
                <?php get_search_form(); ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
get_footer();
