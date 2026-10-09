<?php
/**
 * بخش اتاق وضعیت (Situation Room Section)
 *
 * @package RevayatIran
 * @subpackage Template_Parts/Sections
 */

if (!defined('ABSPATH')) {
    exit;
}

$sr_data             = function_exists('revayat_get_homepage_data') ? revayat_get_homepage_data('situation-room') : [];
$bulletins           = $sr_data['bulletins'] ?? [];
$gated_meta          = $sr_data['gated_meta'] ?? [];
$can_access          = class_exists('Revayat_Data_Service') && method_exists('Revayat_Data_Service', 'can_access_situation_room')
    ? Revayat_Data_Service::can_access_situation_room()
    : (current_user_can('revayat_read_situation_room') || current_user_can('manage_options') || current_user_can('edit_others_posts'));
$all_bulletins       = $bulletins;
$first_bulletin      = !empty($bulletins) ? array_shift($bulletins) : null;
$remaining_bulletins = $bulletins;
$gated_target        = is_user_logged_in()
    ? ($gated_meta['upgrade_url'] ?? home_url('/auth/?tab=upgrade'))
    : ($gated_meta['login_url'] ?? home_url('/auth/?tab=login'));
?>
<section class="section-shell section-spacing" id="situation-room">
    <div class="container">
        <div class="sr-layout" data-reveal="up">
            <!-- ستون راست: سایدبار هدر عمودی -->
            <aside class="sr-sidebar sr-sidebar--red">
                <div class="sr-sidebar__top">
                    <div class="sr-sidebar__icon-wrapper">
                        <i class="ph-fill ph-security-camera"></i>
                    </div>
                </div>
                
                <h2 class="sr-sidebar__title"><?php echo esc_html( revayat_home_text( 'situation-room', 'title' ) ); ?></h2>
                <p class="sr-sidebar__desc"><?php echo esc_html( revayat_home_text( 'situation-room', 'description' ) ); ?></p>
                
                <div class="sr-sidebar__footer">
                    <p class="sr-security-notice">
                        <i class="ph-fill <?php echo $can_access ? 'ph-lock-key-open' : 'ph-lock-key'; ?>"></i>
                        <?php echo $can_access ? esc_html__('دسترسی تأییدشده', 'revayatiran') : esc_html__('محتوای طبقه‌بندی شده', 'revayatiran'); ?>
                    </p>
                </div>
            </aside>

            <!-- ستون چپ: محتوای اصلی (نمای گیت‌شده / Soft Paywall) -->
            <div class="sr-content">
                <?php if (empty($all_bulletins)) : ?>
                    <?php revayat_render_home_empty_state(__('هنوز بولتنی در اتاق وضعیت منتشر نشده است.', 'revayatiran')); ?>
                <?php elseif ($can_access) : ?>
                    <div class="sr-authorized-wrapper">
                        <ol class="sr-gated-list sr-authorized-list">
                            <?php foreach ($all_bulletins as $item) : ?>
                                <li class="sr-gated-item sr-authorized-item">
                                    <div class="sr-gated-item__bullet"></div>
                                    <div class="sr-gated-item__body">
                                        <h3 class="sr-gated-item__title">
                                            <a href="<?php echo esc_url($item['permalink'] ?? ($item['url'] ?? '#')); ?>"><?php echo esc_html($item['title'] ?? ''); ?></a>
                                        </h3>
                                        <div class="sr-gated-item__meta">
                                            <?php if (!empty($item['date'])) : ?>
                                                <span><i class="ph ph-calendar-blank"></i> <?php echo esc_html($item['date']); ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($item['date']) && !empty($item['category'])) : ?>
                                                <span class="sr-meta-divider"></span>
                                            <?php endif; ?>
                                            <?php if (!empty($item['category'])) : ?>
                                                <span><i class="ph ph-briefcase"></i> <?php echo esc_html($item['category']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <a class="sr-authorized-item__action" href="<?php echo esc_url($item['permalink'] ?? ($item['url'] ?? '#')); ?>" aria-label="<?php echo esc_attr(sprintf(__('مشاهده %s', 'revayatiran'), $item['title'] ?? '')); ?>">
                                        <i class="ph-bold ph-arrow-up-left"></i>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                        <a class="sr-authorized-archive-link" href="<?php echo esc_url(get_post_type_archive_link('situation_room') ?: home_url('/situation-room/')); ?>">
                            <span><?php esc_html_e('مشاهده همه بولتن‌های اتاق وضعیت', 'revayatiran'); ?></span>
                            <i class="ph-bold ph-arrow-left"></i>
                        </a>
                    </div>
                <?php else : ?>
                <div class="sr-gated-wrapper">
                    <!-- لیست آیتم‌های قفل‌شده -->
                    <ol class="sr-gated-list">
                        <?php if ($first_bulletin) : ?>
                            <li class="sr-gated-item">
                                <div class="sr-gated-item__bullet"></div>
                                <div class="sr-gated-item__body">
                                    <h3 class="sr-gated-item__title">
                                        <a href="<?php echo esc_url($gated_target); ?>"><?php echo esc_html($first_bulletin['title'] ?? ''); ?></a>
                                    </h3>
                                </div>
                                <div class="sr-gated-item__lock">
                                    <i class="ph-fill ph-lock"></i>
                                </div>
                            </li>
                        <?php endif; ?>
                    </ol>

                    <!-- پرده محو شونده و دکمه فراخوان (Curtain + CTA) -->
                    <div class="sr-gated-curtain">
                        <ol class="sr-gated-list sr-gated-list--faded">
                            <?php foreach ($remaining_bulletins as $item) : ?>
                                <li class="sr-gated-item">
                                    <div class="sr-gated-item__bullet"></div>
                                    <div class="sr-gated-item__body">
                                        <h3 class="sr-gated-item__title">
                                            <a href="<?php echo esc_url($gated_target); ?>"><?php echo esc_html($item['title'] ?? ''); ?></a>
                                        </h3>
                                    </div>
                                    <div class="sr-gated-item__lock">
                                        <i class="ph-fill ph-lock"></i>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ol>

                        <div class="sr-gated-cta">
                            <div class="sr-gated-cta__inner">
                                <i class="ph-fill ph-lock-key sr-gated-cta__icon"></i>
                                <p class="sr-gated-cta__text">
                                    <span><?php esc_html_e('برای مشاهده محتوای اتاق وضعیت،', 'revayatiran'); ?></span>
                                    <span><?php esc_html_e('عضویت و احراز هویت الزامی است.', 'revayatiran'); ?></span>
                                </p>
                                <div class="sr-gated-cta__actions">
                                    <a href="<?php echo esc_url($gated_meta['auth_url'] ?? home_url('/auth/?tab=register')); ?>" class="button--compact button--primary button--glow">
                                        <i class="ph-fill ph-user-plus"></i>
                                        <span><?php esc_html_e('ثبت‌نام رایگان', 'revayatiran'); ?></span>
                                    </a>
                                    <a href="<?php echo esc_url($gated_meta['login_url'] ?? home_url('/auth/?tab=login')); ?>" class="button--compact button--soft">
                                        <i class="ph-fill ph-sign-in"></i>
                                        <span><?php esc_html_e('ورود به حساب', 'revayatiran'); ?></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
