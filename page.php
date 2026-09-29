<?php
/**
 * تمپلیت استاندارد نمایش برگه‌های ایستا (Page Template)
 *
 * @package RevayatIran
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div class="container py-8 md:py-12">
    <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('max-w-4xl mx-auto bg-surface-strong p-6 md:p-10 rounded-2xl border border-border shadow-soft'); ?>>
            <header class="entry-header mb-8 pb-4 border-b border-border">
                <h1 class="text-2xl md:text-3xl font-black text-text leading-tight m-0">
                    <?php the_title(); ?>
                </h1>
            </header>

            <?php if (has_post_thumbnail()) : ?>
                <div class="entry-thumbnail mb-8 rounded-xl overflow-hidden shadow-soft">
                    <?php the_post_thumbnail('revayat-hero-large', ['class' => 'w-full h-auto object-cover max-h-[420px]']); ?>
                </div>
            <?php endif; ?>

            <div class="entry-content prose prose-lg max-w-none text-text leading-loose">
                <?php the_content(); ?>
            </div>
        </article>
    <?php endwhile; ?>
</div>

<?php
get_footer();
