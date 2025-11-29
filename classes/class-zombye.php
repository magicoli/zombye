<?php
namespace Zombye;

if ( ! defined( 'ABSPATH' ) ) exit;

class Zombye {

    public function __construct() {
        // Shortcode to display registration form and handle all display steps
        add_shortcode('zombye_register', [$this, 'render_registration_form']);

        // Intercept password submission early for redirect
        add_action('template_redirect', [$this, 'maybe_handle_password_submit']);

        // Redirect default WP register link
        add_action('login_form_register', [$this, 'maybe_login_form_register']);

        // Enqueue frontend styles
        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);
	}

    public function enqueue_styles() {
        wp_enqueue_style(
            'zombye-style',
            plugin_dir_url(__DIR__) . 'css/style.css',
            [],
            '1.0.0'
        );
    }

    // Display the registration form or password form
    public function render_registration_form() {
        $this->set_registration_page();
        $output = '';

        // Step 2: display password form if token present
        if ( isset($_GET['zombye_token']) ) {
            $token = sanitize_text_field($_GET['zombye_token']);
            $message = $this->get_password_message($token);
            if ($message) {
                $output .= self::notice($message, 'error');
            }
            $output .= $this->render_password_form($token);
            return $output;
        }

        // Step 1: handle email submission
        if ( isset($_POST['zombye_email']) ) {
            $email = sanitize_email($_POST['zombye_email']);
            $message = $this->handle_form_submission($email);
            if ($message) {
                $output .= self::notice($message, 'error');
            } else {
                $output .= self::notice(__('A confirmation email has been sent. Please check your inbox!', 'zombye'), 'success');
            }
            return $output; // replace shortcode with message
        }

        // Default email registration form
        $output = sprintf(
        	'<form method="POST">
         		<p><label>%s<input type="email" name="zombye_email" required></label></p>
		        <p><button type="submit">%s</button></p>
	        </form>',
			esc_html__('Email:', 'zombye'),
			esc_html__('Register', 'zombye')
		);

        return $output;
    }

    // Handle email submission (step 1)
    private function handle_form_submission($email) {
        if (!is_email($email) || email_exists($email)) {
            // generic message for all errors
            return __('There was a problem processing your registration. Please try again.', 'zombye');
        }

        $token = wp_generate_password(20, false);
        set_transient('zombye_' . $token, $email, 12 * HOUR_IN_SECONDS);

        $page_url = get_permalink();
        $link = add_query_arg(['zombye_token' => $token], $page_url);

        $subject = sprintf(
        	__('Confirm your registration to %s', 'zombye'), get_bloginfo('name')
        );
        $message = sprintf(
	        '<h2>%1$s</h2>
	        <p>%2$s</p>
	        <p><a href="%3$s">%3$s</a></p>
	        <p>%4$s</p>
			<hr>
			<p>%5$s<br><a class="smaller" href="%6$s">%6$s</a></p>',
	        __('Thank you for registering!', 'zombye'),
	        __('Click this link to validate your account and set your password:', 'zombye'),
	        $link,
	        __('If you did not request this, please ignore this email.', 'zombye'),
			get_bloginfo('name'),
			get_bloginfo('url'),
        );
        $headers = array('Content-Type: text/html; charset=UTF-8');

        wp_mail($email, $subject, $message, $headers);

        return ''; // no error
    }

    // Password form rendering (step 2)
    private function render_password_form($token) {
        $email = get_transient('zombye_' . $token);
        if (!$email) return '';

        return sprintf(
	        '<form method="POST">
	            <p><label>%s <input type="password" name="password" required></label></p>
	            <p><label>%s <input type="password" name="password_confirm" required></label></p>
	            <p><button type="submit">%s</button></p>
	        </form>',
			esc_html__('Choose your password:', 'zombye'),
			esc_html__('Confirm password:', 'zombye'),
			esc_html__('Set password', 'zombye')
        );
    }

    // Provide message for password errors
    private function get_password_message($token) {
        if (!isset($_GET['zombye_error']) || $_GET['zombye_error'] !== 'mismatch') {
            return '';
        }
        return __('Passwords do not match. Please try again.', 'zombye');
    }

    // Handle password submission early to allow redirect
    public function maybe_handle_password_submit() {
        if (!isset($_GET['zombye_token'], $_POST['password'], $_POST['password_confirm'])) return;

        $token = sanitize_text_field($_GET['zombye_token']);
        $email = get_transient('zombye_' . $token);
        if (!$email) return;

        $password = $_POST['password'];
        $password_confirm = $_POST['password_confirm'];

        if ($password !== $password_confirm) {
            $redirect_url = add_query_arg('zombye_error', 'mismatch', get_permalink());
            $redirect_url = add_query_arg('zombye_token', $token, $redirect_url);
            wp_redirect($redirect_url);
            exit;
        }

        $user_id = wp_create_user($email, $password, $email);
        if (is_wp_error($user_id)) {
            wp_die(__('Error creating account.', 'zombye'));
        }

        delete_transient('zombye_' . $token);

        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);
        do_action('wp_login', $email, get_user_by('id', $user_id));

        if ( function_exists('w4os_web_profile_url') ) {
            $profile_url = w4os_web_profile_url($user_id);
            error_log('[DEBUG] using w4os_web_profile_url ' . $profile_url);
        } else {
            $profile_url = get_edit_profile_url($user_id);
            error_log('[DEBUG] using get_edit_profile_url ' . $profile_url);
        }

        wp_redirect($profile_url);
        exit;
    }

    // Save the current page as the Zombye registration page if needed
    private function set_registration_page() {
        $current_page_id = get_the_ID();
        if (!$current_page_id) return;

        $opts = get_option('zombye', []);
        if (!empty($opts['registration_page'])) {
            $stored_content = get_post_field('post_content', $opts['registration_page']);
            if ($stored_content && has_shortcode($stored_content, 'zombye_register')) {
                return;
            }
        }

        $opts['registration_page'] = $current_page_id;
        update_option('zombye', $opts);
    }

    // Redirect WP default register link to Zombye page
    public function maybe_login_form_register() {
        $opts = get_option('zombye', []);
        if (empty($opts['registration_page'])) return;

        $content = get_post_field('post_content', $opts['registration_page']);
        if (!$content || !has_shortcode($content, 'zombye_register')) return;

        wp_redirect(get_permalink($opts['registration_page']));
        exit;
    }

    // Static method to display notice messages
    public static function notice($message, $type = 'error') {
        $class = $type === 'success' ? 'notice notice-success' : 'notice notice-error';
        return sprintf('<div class="%s">%s</div>', esc_attr($class), esc_html($message));
    }
}
