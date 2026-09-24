<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * [ifsc_contact_form] — a simple name/email/message form for the Contact Us
 * page. Submits via admin-post.php (works for logged-out visitors too, via
 * the _nopriv_ hook) and emails the site admin; no data is stored in the DB.
 */
add_shortcode('ifsc_contact_form', function () {
    $site_key = Ifsc_Recaptcha::get_site_key();

    ob_start();
    ?>
    <div class="ifsc-contact-form">
        <?php if (isset($_GET['contact_sent'])) : ?>
            <p class="ifsc-notice ifsc-notice--success">Thanks for reaching out — we'll get back to you soon.</p>
        <?php elseif (isset($_GET['contact_error'])) : ?>
            <p class="ifsc-notice ifsc-notice--error">Please fill in your name, a valid email, and a message, and complete the "I'm not a robot" check before sending.</p>
        <?php endif; ?>

        <?php if ($site_key) : ?>
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <?php endif; ?>

        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="ifsc_finder_contact_submit">
            <?php wp_nonce_field('ifsc_finder_contact_submit'); ?>

            <div class="ifsc-hp-field" aria-hidden="true">
                <label>Leave this field empty
                    <input type="text" name="website" tabindex="-1" autocomplete="off">
                </label>
            </div>

            <p class="ifsc-field">
                <label for="ifsc-contact-name">Name</label>
                <input type="text" id="ifsc-contact-name" name="name" class="ifsc-input" required>
            </p>
            <p class="ifsc-field">
                <label for="ifsc-contact-email">Email</label>
                <input type="email" id="ifsc-contact-email" name="email" class="ifsc-input" required>
            </p>
            <p class="ifsc-field">
                <label for="ifsc-contact-message">Message</label>
                <textarea id="ifsc-contact-message" name="message" class="ifsc-input" rows="5" required></textarea>
            </p>

            <?php if ($site_key) : ?>
                <p class="ifsc-field">
                    <div class="g-recaptcha" data-sitekey="<?php echo esc_attr($site_key); ?>"></div>
                </p>
            <?php endif; ?>

            <button type="submit" class="ifsc-btn-primary">Send Message</button>
        </form>
    </div>
    <?php
    return ob_get_clean();
});

function ifsc_finder_handle_contact_submit()
{
    check_admin_referer('ifsc_finder_contact_submit');

    $redirect_to = wp_get_referer() ?: home_url('/');

    // Honeypot: real visitors never fill this hidden field in; bots often do.
    if (!empty($_POST['website'])) {
        wp_safe_redirect(remove_query_arg(['contact_sent', 'contact_error'], $redirect_to));
        exit;
    }

    $name = sanitize_text_field(wp_unslash($_POST['name'] ?? ''));
    $email = sanitize_email(wp_unslash($_POST['email'] ?? ''));
    $message = sanitize_textarea_field(wp_unslash($_POST['message'] ?? ''));
    $recaptcha_token = sanitize_text_field(wp_unslash($_POST['g-recaptcha-response'] ?? ''));

    $base_url = remove_query_arg(['contact_sent', 'contact_error'], $redirect_to);

    if (!$name || !is_email($email) || !$message || !Ifsc_Recaptcha::verify($recaptcha_token)) {
        wp_safe_redirect(add_query_arg('contact_error', '1', $base_url));
        exit;
    }

    $to = get_option('admin_email');
    $subject = sprintf('[%s] New contact message from %s', get_bloginfo('name'), $name);
    $body = "Name: {$name}\nEmail: {$email}\n\nMessage:\n{$message}";
    $headers = ['Reply-To: ' . $name . ' <' . $email . '>'];

    wp_mail($to, $subject, $body, $headers);

    wp_safe_redirect(add_query_arg('contact_sent', '1', $base_url));
    exit;
}
add_action('admin_post_ifsc_finder_contact_submit', 'ifsc_finder_handle_contact_submit');
add_action('admin_post_nopriv_ifsc_finder_contact_submit', 'ifsc_finder_handle_contact_submit');
