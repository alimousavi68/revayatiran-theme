<?php
/** Native comment list, moderation notices, pagination and threaded replies. */
if (!defined('ABSPATH') || post_password_required()) { return; }
?>
<section id="comments" class="rv-discussion<?php echo have_comments() ? '' : ' rv-discussion--empty'; ?>" aria-label="<?php esc_attr_e('دیدگاه‌ها', 'revayatiran'); ?>">
    <?php if (have_comments()) : ?>
    <h2 id="rv-discussion-title"><?php esc_html_e('دیدگاه‌ها', 'revayatiran'); ?> <span><?php echo esc_html(revayat_en2fa((string) get_comments_number())); ?></span></h2>

        <ol class="rv-comment-list"><?php wp_list_comments(['style' => 'ol', 'short_ping' => true, 'avatar_size' => 40, 'format' => 'html5']); ?></ol>
        <?php the_comments_pagination(['prev_text' => __('دیدگاه‌های قبلی', 'revayatiran'), 'next_text' => __('دیدگاه‌های بعدی', 'revayatiran')]); ?>
    <?php endif; ?>
    <?php if (comments_open()) {
        comment_form([
            'title_reply' => __('دیدگاه شما', 'revayatiran'),
            'title_reply_to' => __('پاسخ به %s', 'revayatiran'),
            'cancel_reply_link' => __('لغو پاسخ', 'revayatiran'),
            'comment_notes_before' => '',
            'must_log_in' => '<p class="must-log-in"><a href="' . esc_url( add_query_arg( 'redirect_to', get_permalink() . '#comments', home_url( '/auth/' ) ) ) . '">' . esc_html__( 'برای ثبت دیدگاه وارد حساب شوید.', 'revayatiran' ) . '</a></p>',
            'comment_field' => '<p class="comment-form-comment"><label for="comment">' . esc_html__('دیدگاه شما', 'revayatiran') . ' <span aria-hidden="true">*</span></label><textarea id="comment" name="comment" rows="3" maxlength="65525" required placeholder="' . esc_attr__('دیدگاه خود را بنویسید…', 'revayatiran') . '"></textarea></p>',
            'label_submit' => __('ارسال دیدگاه', 'revayatiran'),
            'comment_notes_after' => '',
        ]);
    } elseif (have_comments()) { ?>
        <p><?php esc_html_e('ثبت دیدگاه جدید برای این مطلب بسته است.', 'revayatiran'); ?></p>
    <?php } ?>
</section>
