<?php
/** Public article: split introduction and a separate reading card/sidebar. */
if (!defined('ABSPATH')) { exit; }
get_header();
while (have_posts()) : the_post();
    $category = revayat_get_primary_category();
    if ($category && (int) $category->term_id === (int) get_option('default_category')) { $category = null; }
?>
<main class="container rv-post-page">
    <article id="post-<?php the_ID(); ?>" <?php post_class('rv-post-shell'); ?>>
        <header class="rv-post-hero<?php echo has_post_thumbnail() && !post_password_required() ? ' has-image' : ''; ?>">
            <div class="rv-post-hero__intro">
                <nav class="rv-breadcrumb" aria-label="<?php esc_attr_e('موقعیت شما', 'revayatiran'); ?>"><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('خانه', 'revayatiran'); ?></a><?php if ($category) : ?><span aria-hidden="true">‹</span><a href="<?php echo esc_url(get_category_link($category->term_id)); ?>"><?php echo esc_html($category->name); ?></a><?php endif; ?></nav>
                <h1><?php the_title(); ?></h1>
                <?php if (has_excerpt() && !post_password_required()) : ?><p class="rv-post-hero__excerpt"><?php echo esc_html(get_the_excerpt()); ?></p><?php endif; ?>
                <div class="rv-post-hero__meta"><span><i class="ph ph-user" aria-hidden="true"></i> <?php the_author(); ?></span><span><?php revayat_posted_on(); ?></span><span><?php echo esc_html(revayat_reading_time()); ?></span></div>
            </div>
            <?php if (has_post_thumbnail() && !post_password_required()) : ?><div class="rv-post-hero__image"><?php the_post_thumbnail('revayat-hero-large', ['loading' => 'eager']); ?></div><?php endif; ?>
        </header>
        <div class="rv-reading-layout">
            <div class="rv-reading-main rv-post-reading-card">

                <div class="entry-content rv-post-body"><?php the_content(); ?></div>
                <?php if (has_tag()) : ?>
                <footer class="rv-post-footer">
                    <div class="post-tags"><?php the_tags('<span>' . esc_html__('برچسب‌ها:', 'revayatiran') . '</span> ', ' ', ''); ?></div>
                </footer>
                <?php endif; ?>
                <?php get_template_part('template-parts/dossier/parents'); ?>
                <?php get_template_part('template-parts/single/engagement'); ?>
            </div>
            <?php get_template_part('template-parts/single/sidebar'); ?>
        </div>
    </article>
</main>
<?php endwhile; get_footer();
