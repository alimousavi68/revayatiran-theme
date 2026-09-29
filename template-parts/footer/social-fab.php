<?php
/**
 * پنل شناور شبکه‌های اجتماعی (Floating Social FAB)
 *
 * @package RevayatIran
 * @subpackage Template_Parts/Footer
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<aside class="social-fab" id="social-fab" aria-label="<?php esc_attr_e('کانال‌های ارتباطی و شبکه‌های اجتماعی', 'revayatiran'); ?>">
    <div class="social-fab__panel" id="social-fab-panel" role="dialog" aria-hidden="true" aria-label="<?php esc_attr_e('شبکه‌های اجتماعی روایت ایران', 'revayatiran'); ?>">
        <div class="social-fab__header">
            <div class="social-fab__title-group">
                <span class="social-fab__title"><?php esc_html_e('روایت ایران در شبکه‌ها', 'revayatiran'); ?></span>
                <span class="social-fab__subtitle"><?php esc_html_e('عضویت در کانال‌های رسمی', 'revayatiran'); ?></span>
            </div>
            <button type="button" class="social-fab__close" id="social-fab-close" aria-label="<?php esc_attr_e('بستن پنجره', 'revayatiran'); ?>">
                <i class="ph ph-x"></i>
            </button>
        </div>
        <ul class="social-fab__list">
            <li>
                <a href="https://t.me/revayatiran" target="_blank" rel="noopener noreferrer" class="social-fab__link social-fab__link--telegram">
                    <span class="social-fab__icon-box"><i class="ph ph-telegram-logo"></i></span>
                    <div class="social-fab__info">
                        <span class="social-fab__name"><?php esc_html_e('کانال تلگرام', 'revayatiran'); ?></span>
                        <span class="social-fab__handle">@revayatiran</span>
                    </div>
                    <i class="ph ph-arrow-left social-fab__arrow"></i>
                </a>
            </li>
            <li>
                <a href="https://eitaa.com/revayatiran" target="_blank" rel="noopener noreferrer" class="social-fab__link social-fab__link--eitaa">
                    <span class="social-fab__icon-box"><i class="ph ph-chats-circle"></i></span>
                    <div class="social-fab__info">
                        <span class="social-fab__name"><?php esc_html_e('کانال ایتا', 'revayatiran'); ?></span>
                        <span class="social-fab__handle">eitaa.com/revayatiran</span>
                    </div>
                    <i class="ph ph-arrow-left social-fab__arrow"></i>
                </a>
            </li>
            <li>
                <a href="https://ble.ir/revayatiran" target="_blank" rel="noopener noreferrer" class="social-fab__link social-fab__link--bale">
                    <span class="social-fab__icon-box"><i class="ph ph-chat-centered-dots"></i></span>
                    <div class="social-fab__info">
                        <span class="social-fab__name"><?php esc_html_e('کانال بله', 'revayatiran'); ?></span>
                        <span class="social-fab__handle">ble.ir/revayatiran</span>
                    </div>
                    <i class="ph ph-arrow-left social-fab__arrow"></i>
                </a>
            </li>
            <li>
                <a href="https://instagram.com/revayatiran" target="_blank" rel="noopener noreferrer" class="social-fab__link social-fab__link--instagram">
                    <span class="social-fab__icon-box"><i class="ph ph-instagram-logo"></i></span>
                    <div class="social-fab__info">
                        <span class="social-fab__name"><?php esc_html_e('اینستاگرام', 'revayatiran'); ?></span>
                        <span class="social-fab__handle">@revayatiran</span>
                    </div>
                    <i class="ph ph-arrow-left social-fab__arrow"></i>
                </a>
            </li>
            <li>
                <a href="https://x.com/revayatiran" target="_blank" rel="noopener noreferrer" class="social-fab__link social-fab__link--x">
                    <span class="social-fab__icon-box"><i class="ph ph-x-logo"></i></span>
                    <div class="social-fab__info">
                        <span class="social-fab__name"><?php esc_html_e('ایکس (توییتر)', 'revayatiran'); ?></span>
                        <span class="social-fab__handle">@revayatiran</span>
                    </div>
                    <i class="ph ph-arrow-left social-fab__arrow"></i>
                </a>
            </li>
            <li>
                <a href="https://youtube.com/@revayatiran" target="_blank" rel="noopener noreferrer" class="social-fab__link social-fab__link--youtube">
                    <span class="social-fab__icon-box"><i class="ph ph-youtube-logo"></i></span>
                    <div class="social-fab__info">
                        <span class="social-fab__name"><?php esc_html_e('یوتیوب', 'revayatiran'); ?></span>
                        <span class="social-fab__handle">youtube.com/@revayatiran</span>
                    </div>
                    <i class="ph ph-arrow-left social-fab__arrow"></i>
                </a>
            </li>
            <li>
                <a href="<?php bloginfo('rss2_url'); ?>" class="social-fab__link social-fab__link--rss">
                    <span class="social-fab__icon-box"><i class="ph ph-rss"></i></span>
                    <div class="social-fab__info">
                        <span class="social-fab__name"><?php esc_html_e('خبرخوان (RSS)', 'revayatiran'); ?></span>
                        <span class="social-fab__handle"><?php esc_html_e('روایت‌های زنده', 'revayatiran'); ?></span>
                    </div>
                    <i class="ph ph-arrow-left social-fab__arrow"></i>
                </a>
            </li>
        </ul>
    </div>

    <button type="button" class="social-fab__trigger" id="social-fab-toggle" aria-expanded="false" aria-controls="social-fab-panel" aria-label="<?php esc_attr_e('مشاهده شبکه‌های اجتماعی', 'revayatiran'); ?>" title="<?php esc_attr_e('شبکه‌های اجتماعی روایت ایران', 'revayatiran'); ?>">
        <span class="social-fab__badge-pulse" aria-hidden="true"></span>
        <i class="ph ph-share-network social-fab__trigger-icon-open"></i>
        <i class="ph ph-x social-fab__trigger-icon-close"></i>
    </button>
</aside>
