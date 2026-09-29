<?php
/**
 * واکر اختصاصی ناوبری پوسته روایت ایران (Revayat Navigation Walker)
 *
 * یکپارچه‌سازی کامل ساختار منوهای کلاسیک وردپرس با سیستم طراحی Tailwind و ماژول‌های CSS قالب.
 * پشتیبانی استاندارد از:
 *   - کلاس‌های هسته وردپرس: menu-item, current-menu-item, current-menu-parent, sub-menu
 *   - موقعیت‌های گوناگون: primary-menu, mobile-menu, footer-col-1, footer-col-2, footer-legal
 *   - زیرمنوهای چندسطحی (Dropdown) با انیمیشن‌های CSS و Phosphor Icons
 *   - حذف ۱۰۰٪ فال‌بک‌های استاتیک طبق ADR-002 (No Static HTML Fallback)
 *
 * @package RevayatIran
 * @subpackage Inc
 * @version 1.0.0 (Pass 2B Commit 5)
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Revayat_Nav_Walker')) {
    /**
     * کلاس واکر ناوبری پوسته
     */
    class Revayat_Nav_Walker extends Walker_Nav_Menu {

        /**
         * شروع کانتینر زیرمنو (Sub-menu Wrapper)
         *
         * @param string   $output خروجی رشته HTML مرجع
         * @param int      $depth  عمق سطح در درخت منو
         * @param stdClass $args   تنظیمات ارسال‌شده به wp_nav_menu
         */
        public function start_lvl(&$output, $depth = 0, $args = null) {
            $location = $args->theme_location ?? '';
            $indent   = str_repeat("\t", $depth);

            if ($location === 'primary-menu') {
                // دراپ‌داون پیشرفته هدر دسکتاپ
                $classes = 'sub-menu dropdown-menu absolute top-[calc(100%+12px)] right-1/2 translate-x-1/2 translate-y-2.5 bg-white border border-border rounded-xl p-2 min-w-[170px] flex flex-col gap-0.5 z-[250] opacity-0 invisible pointer-events-none transition-all duration-300 group-hover:opacity-100 group-hover:visible group-hover:pointer-events-auto group-hover:translate-y-0 shadow-xl dark:bg-[#162136]/95 dark:backdrop-blur-md dark:border-white/10 dark:shadow-black/30 before:absolute before:top-[-14px] before:left-0 before:right-0 before:h-[14px] before:bg-transparent list-none m-0';
                $output .= "\n{$indent}<ul class=\"{$classes}\">\n";
            } elseif ($location === 'mobile-menu') {
                // زیرمنوی کشویی موبایل
                $classes = 'sub-menu mobile-sub-menu ps-4 my-1 flex flex-col gap-1 border-r-2 border-border/50 list-none m-0';
                $output .= "\n{$indent}<ul class=\"{$classes}\">\n";
            } else {
                // زیرمنوی پیش‌فرض استاندارد
                $output .= "\n{$indent}<ul class=\"sub-menu list-none m-0 p-0\">\n";
            }
        }

        /**
         * پایان کانتینر زیرمنو
         */
        public function end_lvl(&$output, $depth = 0, $args = null) {
            $indent  = str_repeat("\t", $depth);
            $output .= "{$indent}</ul>\n";
        }

        /**
         * شروع المان منو (LI و تگ پیوند A)
         *
         * @param string   $output            خروجی رشته HTML
         * @param WP_Post  $data_object       آبجکت داده آیتم منو
         * @param int      $depth             عمق منو
         * @param stdClass $args              آرگومان‌های wp_nav_menu
         * @param int      $current_object_id شناسه آبجکت کنونی
         */
        public function start_el(&$output, $data_object, $depth = 0, $args = null, $current_object_id = 0) {
            $menu_item = $data_object;
            $location  = $args->theme_location ?? '';
            $indent    = $depth ? str_repeat("\t", $depth) : '';

            // ۱. ارزیابی وجود فرزند (has_children)
            $has_children = !empty($args->walker->has_children);

            // ۲. جمع‌آوری و استانداردسازی کلاس‌های LI هسته وردپرس
            $classes   = empty($menu_item->classes) ? [] : (array) $menu_item->classes;
            $classes[] = 'menu-item';
            $classes[] = 'menu-item-' . $menu_item->ID;

            // بررسی وضعیت فعال (Active State)
            $is_active = $menu_item->current ||
                         $menu_item->current_item_ancestor ||
                         in_array('current-menu-item', $classes, true) ||
                         in_array('current-menu-parent', $classes, true) ||
                         in_array('current_page_item', $classes, true);

            if ($is_active) {
                $classes[] = 'current-menu-item';
            }

            // ۳. الصاق کلاس‌های کانتینر بسته به موقعیت (theme_location)
            if ($location === 'primary-menu') {
                if ($depth === 0) {
                    if ($has_children) {
                        $classes[] = 'relative flex items-center gap-1 group nav-item-dropdown';
                    } else {
                        $classes[] = 'relative flex items-center gap-1';
                    }
                } else {
                    $classes[] = 'w-full';
                }
            } elseif ($location === 'mobile-menu') {
                $classes[] = 'w-full';
            } elseif ($location === 'footer-col-1' || $location === 'footer-col-2') {
                $classes[] = 'w-full';
            }

            $class_names = implode(' ', array_filter(array_unique($classes)));
            $class_attr  = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

            $id_attr = ' id="menu-item-' . esc_attr($menu_item->ID) . '"';

            $output .= $indent . '<li' . $id_attr . $class_attr . '>';

            // ۴. ساخت صفات تگ <a>
            $atts           = [];
            $atts['title']  = !empty($menu_item->attr_title) ? $menu_item->attr_title : '';
            $atts['target'] = !empty($menu_item->target)     ? $menu_item->target     : '';
            $atts['rel']    = !empty($menu_item->xfn)        ? $menu_item->xfn        : '';
            $atts['href']   = !empty($menu_item->url)        ? $menu_item->url        : '#';

            if ($has_children && $depth === 0 && $location === 'primary-menu') {
                $atts['aria-haspopup'] = 'true';
                $atts['aria-expanded'] = 'false';
            }

            // ۵. استایل‌دهی تگ <a> بر اساس سطح و موقعیت
            $active_class = $is_active ? ' is-active' : '';

            if ($location === 'primary-menu') {
                if ($depth === 0) {
                    if ($has_children) {
                        $atts['class'] = 'inline-flex items-center gap-1.5 no-underline cursor-pointer relative py-1.5 px-2 text-white/90 hover:text-white transition-colors duration-300 dropdown-trigger' . $active_class;
                    } else {
                        $atts['class'] = 'relative py-1.5 px-2 text-white/90 hover:text-white transition-colors duration-300 before:absolute before:bottom-1 before:left-1/2 before:-translate-x-1/2 before:w-0 before:h-[2px] before:bg-white/80 before:transition-all before:duration-300 hover:before:w-[70%]' . $active_class;
                        if ($is_active) {
                            $atts['class'] .= ' before:w-[70%]';
                        }
                    }
                } else {
                    $atts['class'] = 'dropdown-item flex items-center gap-2 py-2 px-3 text-text-soft text-[13px] font-medium no-underline rounded-lg transition-all duration-200 hover:bg-primary/10 hover:text-primary hover:-translate-x-1 rtl:hover:translate-x-1' . $active_class;
                }
            } elseif ($location === 'mobile-menu') {
                if ($depth === 0) {
                    $atts['class'] = 'mobile-nav-item' . $active_class;
                } else {
                    $atts['class'] = 'flex items-center gap-2 py-2 px-3 text-text-muted hover:text-primary text-sm font-medium transition-colors' . $active_class;
                }
            } elseif ($location === 'footer-col-1' || $location === 'footer-col-2') {
                $atts['class'] = 'group inline-flex items-center gap-2.5 py-1.5 px-2.5 rounded-lg text-sm font-semibold text-white/85 no-underline transition-all duration-200 -ms-2.5 hover:text-white hover:bg-white/15' . $active_class;
            } else {
                $atts['class'] = 'menu-link' . $active_class;
            }

            $atts = apply_filters('nav_menu_link_attributes', $atts, $menu_item, $args, $depth);

            $attributes = '';
            foreach ($atts as $attr => $value) {
                if (!empty($value)) {
                    $value       = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                    $attributes .= ' ' . $attr . '="' . $value . '"';
                }
            }

            $title = apply_filters('the_title', $menu_item->title, $menu_item->ID);
            $title = apply_filters('nav_menu_item_title', $title, $menu_item, $args, $depth);

            // ۶. واکشی و تعیین آیکون Phosphor برای آیتم
            $icon = $this->resolve_item_icon($menu_item, $location, $depth);

            // ۷. قالب‌بندی بدنه پیوند
            $item_output = $args->before ?? '';
            $item_output .= '<a' . $attributes . '>';

            if ($location === 'primary-menu') {
                if ($depth === 0) {
                    if ($has_children) {
                        $item_output .= '<span>' . esc_html($title) . '</span>';
                        $item_output .= '<i class="ph ph-caret-down text-[11px] opacity-75 transition-transform duration-300 group-hover:rotate-180 group-hover:text-white dropdown-caret" aria-hidden="true"></i>';
                    } else {
                        $item_output .= esc_html($title);
                    }
                } else {
                    $item_output .= '<i class="ph ' . esc_attr($icon) . ' text-[14px] opacity-65" aria-hidden="true"></i>';
                    $item_output .= '<span>' . esc_html($title) . '</span>';
                }
            } elseif ($location === 'mobile-menu') {
                if ($depth === 0) {
                    $item_output .= '<span class="mobile-nav-item__icon"><i class="ph ' . esc_attr($icon) . '" aria-hidden="true"></i></span>';
                    $item_output .= '<span>' . esc_html($title) . '</span>';
                } else {
                    $item_output .= '<i class="ph ' . esc_attr($icon) . ' text-xs opacity-60" aria-hidden="true"></i>';
                    $item_output .= '<span>' . esc_html($title) . '</span>';
                }
            } elseif ($location === 'footer-col-1' || $location === 'footer-col-2') {
                $item_output .= '<i class="ph ' . esc_attr($icon) . ' text-base opacity-90 transition-all duration-200 shrink-0" aria-hidden="true"></i>';
                $item_output .= '<span>' . esc_html($title) . '</span>';
            } else {
                $item_output .= esc_html($title);
            }

            $item_output .= '</a>';
            
            // اضافه کردن جداکننده بین آیتم‌های منوی اصلی دسکتاپ
            if ($location === 'primary-menu' && $depth === 0) {
                $item_output .= '<span class="w-[1px] h-[10px] bg-white/25 mx-0.5 shrink-0 nav-separator" aria-hidden="true"></span>';
            }
            
            $item_output .= $args->after ?? '';

            $output .= apply_filters('walker_nav_menu_start_el', $item_output, $menu_item, $depth, $args);
        }

        /**
         * پایان المان منو (بستن LI)
         */
        public function end_el(&$output, $data_object, $depth = 0, $args = null) {
            $output .= "</li>\n";
        }

        /**
         * تعیین هوشمند آیکون متناسب با برچسب یا تنظیمات کلاس در پنل مدیریت
         *
         * @param WP_Post $menu_item شیء آیتم منو
         * @param string  $location  موقعیت فهرست
         * @param int     $depth     عمق ساختار
         * @return string نام کلاس آیکون Phosphor
         */
        protected function resolve_item_icon($menu_item, $location, $depth) {
            // ۱. بررسی وجود کلاس صریح Phosphor از بخش تنظیمات منوی وردپرس
            if (!empty($menu_item->classes)) {
                foreach ((array) $menu_item->classes as $class) {
                    if (str_starts_with($class, 'ph-') || str_starts_with($class, 'ph_')) {
                        return sanitize_html_class($class);
                    }
                }
            }

            // ۲. تشخیص هوشمند بر اساس عنوان یا پیوند
            $title = mb_strtolower(trim($menu_item->title ?? ''), 'UTF-8');
            $url   = strtolower(trim($menu_item->url ?? ''));

            if (str_contains($title, 'اصلی') || $url === home_url('/') || $url === home_url() || $url === '/') {
                return 'ph-house';
            }
            if (str_contains($title, 'روایت') || str_contains($url, 'narrative')) {
                return 'ph-newspaper';
            }
            if (str_contains($title, 'رصد') || str_contains($title, 'اخبار') || str_contains($url, 'news')) {
                return 'ph-broadcast';
            }
            if (str_contains($title, 'پرونده') || str_contains($url, 'dossier')) {
                return ($depth > 0) ? 'ph-hash' : 'ph-folder-open';
            }
            if (str_contains($title, 'دیده‌بان') || str_contains($title, 'دیدبان') || str_contains($url, 'observatory')) {
                return 'ph-eye';
            }
            if (str_contains($title, 'وضعیت') || str_contains($url, 'situation')) {
                return 'ph-shield-check';
            }
            if (str_contains($title, 'رسانه‌نگار') || str_contains($title, 'چندرسانه') || str_contains($url, 'multimedia')) {
                return 'ph-play-circle';
            }
            if (str_contains($title, 'تحلیل') || str_contains($url, 'analyst')) {
                return 'ph-users-three';
            }
            if (str_contains($title, 'درباره') || str_contains($url, 'about')) {
                return 'ph-info';
            }
            if (str_contains($title, 'تماس') || str_contains($url, 'contact')) {
                return 'ph-envelope';
            }

            // ۳. مقادیر پیش‌فرض ساختاری
            if ($depth > 0) {
                return 'ph-hash';
            }
            if ($location === 'footer-col-1' || $location === 'footer-col-2') {
                return 'ph-caret-left';
            }

            return 'ph-link';
        }
    }
}

if (!function_exists('revayat_nav_menu_fallback')) {
    /**
     * فال‌بک کنترل‌شده فهرست منوها طبق ADR-002 (No Static HTML Fallback)
     *
     * به جای نمایش لینک‌های ساختگی، برای مدیر سایت راهنمایی تنظیم منو و برای بازدیدکننده خروجی پاک ارائه می‌دهد.
     *
     * @param array|object $args تنظیمات منو
     * @return string
     */
    function revayat_nav_menu_fallback($args) {
        if (!current_user_can('edit_theme_options')) {
            return '';
        }

        $args_obj  = (object) $args;
        $loc_label = $args_obj->theme_location ?? '';

        return sprintf(
            '<div class="py-1 px-3 text-xs text-white/80 bg-white/10 rounded border border-white/20 my-1">%s <a href="%s" class="underline text-white font-bold">%s</a></div>',
            esc_html__('فهرستی برای این بخش تخصیص نیافته است.', 'revayatiran'),
            esc_url(admin_url('nav-menus.php')),
            esc_html__('مدیریت فهرست‌ها', 'revayatiran')
        );
    }
}
