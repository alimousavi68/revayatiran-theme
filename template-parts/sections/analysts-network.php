<?php
/**
 * بخش شبکه تحلیل‌گران (Analysts Network Section)
 *
 * نمایش یادداشت‌های تحلیلی، اسلایدر یادداشت‌های برتر و رده‌بندی تحلیل‌گران.
 * کارت‌های تحلیلی اسلایدر منحصراً از کامپوننت card-analyst.php رندر می‌شوند.
 *
 * @package RevayatIran
 * @subpackage Template_Parts/Sections
 * @version 2.0.0 (Pass 2B — Single Source of Truth)
 */

if (!defined('ABSPATH')) {
    exit;
}

$network_data = function_exists('revayat_get_homepage_data') ? revayat_get_homepage_data('analysts-network') : [];
$editorial    = $network_data['editorial'] ?? [];
$analyses     = $network_data['analyses']  ?? [];
$top_analysts = $network_data['analysts']  ?? [];
?>
<section class="section-shell section-spacing" id="analysts-network">
    <div class="container">

        <!-- هدر بخش شبکه تحلیل‌گران -->
        <div class="section-head section-head--etude-b" data-reveal="up">
            <div class="etude-b__leading">
                <div class="etude-b__icon-badge">
                    <i class="ph ph-users-three"></i>
                </div>
                <h2><?php echo esc_html( revayat_home_text( 'analysts-network', 'title' ) ); ?></h2>
                <p class="section-summary"><?php echo esc_html( revayat_home_text( 'analysts-network', 'description' ) ); ?></p>
            </div>
            <div class="etude-b__actions">
                <a href="<?php echo esc_url(home_url('/analysts/')); ?>" class="section-link">
                    <span><?php esc_html_e('مشاهده تمام تحلیل‌گران', 'revayatiran'); ?></span>
                    <i class="ph ph-arrow-left"></i>
                </a>
            </div>
        </div>

        <?php if (empty($editorial) && empty($analyses) && empty($top_analysts)) : revayat_render_home_empty_state(__('هنوز یادداشت یا پروفایل عمومی تحلیلگری منتشر نشده است.', 'revayatiran')); else : ?>
        <div class="an-grid-layout layout-gap-lg" data-reveal="up">

            <!-- ═══ ستون اصلی (محتوا - ۶۵٪) ═══ -->
            <div class="an-main-column">

                <!-- تحلیل پیشنهادی هفته -->
                <div class="an-hero--editorial">
                    <img src="<?php echo esc_url(revayat_resolve_image_url($editorial['thumbnail_url'] ?? null, 'news')); ?>" alt="<?php esc_attr_e('تحلیل پیشنهادی هفته', 'revayatiran'); ?>" class="an-hero__bg-img">
                    <div class="an-hero__overlay-gradient"></div>

                    <div class="an-hero__content-panel panel-pad">
                        <div class="an-hero__badge--accent">
                            <i class="ph ph-chart-bar"></i>
                            <span><?php esc_html_e('تحلیل ویژه هفته', 'revayatiran'); ?></span>
                        </div>

                        <h3 class="an-hero__title">
                            <a href="<?php echo esc_url($editorial['url'] ?? home_url('/')); ?>">
                                <?php echo esc_html($editorial['title'] ?? ''); ?>
                            </a>
                        </h3>
                        <p class="an-hero__desc">
                            <?php echo esc_html($editorial['desc'] ?? ''); ?>
                        </p>

                        <div class="an-hero__author-row">
                            <div class="an-hero__author">
                                <img src="<?php echo esc_url(revayat_resolve_image_url($editorial['author_avatar'] ?? null, 'avatar')); ?>" alt="<?php echo esc_attr($editorial['author_name'] ?? ''); ?>" class="an-hero__avatar">
                                <div class="an-hero__author-info">
                                    <strong><?php echo esc_html($editorial['author_name'] ?? ''); ?></strong>
                                    <span><?php echo esc_html($editorial['author_role'] ?? ''); ?></span>
                                </div>
                            </div>

                            <div class="an-hero__meta-group">
                                <div class="an-hero__score">
                                    <span><?php esc_html_e('اعتبار: ', 'revayatiran'); ?></span>
									<strong><?php echo esc_html($editorial['score'] ?? '—'); ?></strong>
                                    <i class="ph ph-chart-bar"></i>
                                    <span class="an-votes"><?php echo esc_html($editorial['votes_count'] ?? ''); ?></span>
                                </div>
                                <div class="an-hero__date"><?php echo esc_html($editorial['date'] ?? ''); ?></div>
                                <a href="<?php echo esc_url($editorial['url'] ?? home_url('/')); ?>" class="an-hero__read-btn">
                                    <?php esc_html_e('مطالعه', 'revayatiran'); ?> <i class="ph ph-arrow-left"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- لیست تحلیل‌های برتر (Swiper Carousel) -->
                <div class="an-top-analyses--elegant relative">
                    <div class="an-sub-header flex items-center justify-between mb-3">
						<h4 class="m-0"><i class="ph ph-trend-up"></i> <?php echo ! empty( $analyses ) ? esc_html__( 'یادداشت‌های شبکه تحلیلگران', 'revayatiran' ) : esc_html__( 'هنوز یادداشتی منتشر نشده است', 'revayatiran' ); ?></h4>
                        <a href="<?php echo esc_url(home_url('/analyses/')); ?>" class="an-sub-header__link"><?php esc_html_e('آرشیو تحلیل‌ها', 'revayatiran'); ?> <i class="ph ph-caret-left"></i></a>
                    </div>

                    <div class="swiper analysts-top-swiper relative pb-10">
                        <div class="swiper-wrapper">
                            <?php foreach ($analyses as $item) : ?>
                            <div class="swiper-slide">
                                <?php get_template_part('template-parts/cards/card-analyst', null, $item); ?>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- کنترل‌های کروسل -->
                        <button type="button" class="swiper-button-prev analysts-swiper-prev" aria-label="<?php esc_attr_e('قبلی', 'revayatiran'); ?>">
                            <i class="ph-bold ph-caret-right"></i>
                        </button>
                        <button type="button" class="swiper-button-next analysts-swiper-next" aria-label="<?php esc_attr_e('بعدی', 'revayatiran'); ?>">
                            <i class="ph-bold ph-caret-left"></i>
                        </button>
                        <div class="swiper-pagination analysts-swiper-pagination"></div>
                    </div>
                </div>

            </div><!-- /.an-main-column -->

            <!-- ═══ ستون کناری (افراد و شبکه - ۳۵٪) ═══ -->
            <div class="an-sidebar-column">

                <!-- برترین تحلیل‌گران -->
                <div class="an-showcase--premium panel-pad">
                    <div class="an-sub-header">
                        <h4><i class="ph ph-medal"></i> <?php esc_html_e('برترین تحلیل‌گران', 'revayatiran'); ?></h4>
                        <a href="<?php echo esc_url(home_url('/analysts/')); ?>" class="an-sub-header__link"><?php esc_html_e('همه', 'revayatiran'); ?> <i class="ph ph-caret-left"></i></a>
                    </div>

                    <div class="an-showcase__list">
						<?php foreach ($top_analysts as $analyst) : ?>
						<a href="<?php echo esc_url( $analyst['url'] ?? '#' ); ?>" class="an-analyst-card an-analyst-card--ranked">
                            <div class="an-analyst-card__rank"><?php echo esc_html($analyst['rank']); ?></div>
                            <div class="an-analyst-card__avatar-wrap an-analyst-card__avatar-wrap--authority">
                                <img src="<?php echo esc_url(revayat_resolve_image_url($analyst['avatar_url'] ?? null, 'avatar')); ?>" alt="<?php echo esc_attr($analyst['name']); ?>">
                            </div>
                            <div class="an-analyst-card__info">
                                <strong class="an-analyst-card__name"><?php echo esc_html($analyst['name']); ?></strong>
                                <span class="an-analyst-card__spec"><?php echo esc_html($analyst['specialty']); ?></span>
                            </div>
                            <div class="an-analyst-card__right-col">
                                <div class="an-analyst-card__score-badge">
                                    <strong><?php echo esc_html($analyst['score']); ?></strong>
                                    <i class="ph ph-chart-bar"></i>
                                </div>
                                <div class="an-analyst-card__stats-minimal">
                                    <?php
                                    printf(
                                        /* translators: 1: count of analyses, 2: count of votes */
                                        esc_html__('%1$s تحلیل %2$s %3$s رای', 'revayatiran'),
                                        esc_html($analyst['analyses_count'] ?? ''),
                                        '<span class="dot"></span>',
                                        esc_html($analyst['votes_count'] ?? '')
                                    );
                                    ?>
                                </div>
                            </div>
                        </a>
						<?php endforeach; ?>
						<?php if ( empty( $top_analysts ) ) : ?><p class="an-empty-state"><?php esc_html_e( 'پس از ثبت رأی‌های معتبر، رتبه‌بندی تحلیلگران در این بخش نمایش داده می‌شود.', 'revayatiran' ); ?></p><?php endif; ?>
                    </div>
                </div>

                <!-- درخواست عضویت (CTA) -->
                <div class="an-cta--premium">
                    <div class="an-cta-premium__info">
                        <div class="an-cta-header flex items-center gap-2">
                            <i class="ph-fill ph-users-three text-2xl text-white/90"></i>
                            <h4 class="m-0 text-base text-white font-bold"><?php esc_html_e('عضویت در شبکه تحلیل‌گران', 'revayatiran'); ?></h4>
                        </div>
                        <p class="an-cta-desc text-white/85 text-xs sm:text-sm leading-relaxed m-0">
							<?php esc_html_e('با پیوستن به شبکه تحلیلگران روایت ایران، یادداشت‌های راهبردی خود را برای بررسی و انتشار تحریریه ارسال کنید.', 'revayatiran'); ?>
                        </p>
                    </div>
                    <div class="an-cta-actions">
                        <a href="<?php echo esc_url(home_url('/auth/?tab=register')); ?>" class="an-cta-btn--primary">
                            <i class="ph ph-user-plus"></i>
                            <span><?php esc_html_e('ثبت نام', 'revayatiran'); ?></span>
                        </a>
                        <a href="<?php echo esc_url(home_url('/auth/?tab=login')); ?>" class="an-cta-btn--secondary">
                            <i class="ph ph-sign-in"></i>
                            <span><?php esc_html_e('ورود', 'revayatiran'); ?></span>
                        </a>
                    </div>
                </div>

            </div><!-- /.an-sidebar-column -->

        </div><!-- /.an-grid-layout -->
        <?php endif; ?>

    </div><!-- /.container -->
</section>
