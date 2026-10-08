<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<section class="rv-access-card" data-rv-auth hidden>
    <header class="rv-access-heading"><span class="rv-access-mark"><i class="ph ph-user-circle"></i></span><p>به روایت ایران خوش آمدید</p><h1>ورود یا ثبت‌نام</h1><p>با تأیید شماره موبایل وارد شوید. اگر حساب ندارید، حساب شما ساخته می‌شود.</p></header>
    <div class="rv-access-methods" role="tablist" aria-label="روش ورود"><button type="button" role="tab" aria-selected="true" data-auth-method="auth">رمز یکبار مصرف</button><button type="button" role="tab" aria-selected="false" data-auth-method="password">رمز عبور</button></div>
    <p class="rv-access-notice" data-auth-notice role="status" hidden></p>
    <form data-auth-panel="auth" data-operation="request" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" method="post">
        <input type="hidden" name="context" value="auth"><label>شماره موبایل<input type="tel" name="mobile" autocomplete="tel" inputmode="tel" placeholder="09123456789" maxlength="14" required data-digits></label><small>اعداد فارسی و انگلیسی پذیرفته می‌شوند.</small><button class="rv-access-primary" type="submit">دریافت رمز یکبار مصرف<i class="ph ph-arrow-left"></i></button>
    </form>
    <form data-auth-panel="password" data-operation="password" hidden action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" method="post">
        <label>شماره موبایل، نام کاربری یا ایمیل<input name="login" autocomplete="username" required></label><label>رمز عبور<input type="password" name="password" autocomplete="current-password" required></label><label class="rv-access-check"><input type="checkbox" name="remember" value="1" checked>مرا به خاطر بسپار</label><button type="submit" class="rv-access-primary">ورود به حساب<i class="ph ph-arrow-left"></i></button><button type="button" class="rv-access-link" data-auth-method="recover">رمز عبور را فراموش کرده‌اید؟</button>
    </form>
    <form data-auth-panel="recover" data-operation="request" hidden action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" method="post">
        <input type="hidden" name="context" value="recover"><p>برای تعیین رمز تازه، شماره موبایل حساب خود را تأیید کنید.</p><label>شماره موبایل<input type="tel" name="mobile" autocomplete="tel" inputmode="tel" placeholder="09123456789" maxlength="14" required data-digits></label><button type="submit" class="rv-access-primary">دریافت کد بازیابی</button><button type="button" class="rv-access-link" data-auth-method="auth">بازگشت به ورود</button>
    </form>
    <form data-auth-panel="verify" data-operation="verify" hidden action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" method="post">
        <p>کد ارسال‌شده به <bdi data-auth-mobile></bdi> را وارد کنید.</p><label>کد تأیید شش‌رقمی<input name="otp_code" inputmode="numeric" autocomplete="one-time-code" maxlength="6" pattern="[0-9۰-۹٠-٩]{6}" required data-digits></label><label data-auth-new-password hidden>رمز عبور تازه<input type="password" name="password" minlength="8" autocomplete="new-password" disabled></label><input type="hidden" name="remember" value="1"><button type="submit" class="rv-access-primary">تأیید و ادامه<i class="ph ph-arrow-left"></i></button><div class="rv-access-timers"><span data-auth-expiry></span><button class="rv-access-link" type="button" data-auth-resend disabled>ارسال دوباره کد</button></div><button type="button" class="rv-access-link" data-auth-change>اصلاح شماره موبایل</button>
    </form>
    <footer class="rv-access-foot"><i class="ph ph-lock-key"></i><span>شماره شما عمومی نمایش داده نمی‌شود.</span></footer>
</section>
