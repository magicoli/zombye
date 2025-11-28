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
    }

    // Display the registration form or password form
    public function render_registration_form() {
        // Save the current page as registration page if needed
        $this->set_registration_page();

        $output = '';

        // Step 2: display password form if token present
        if ( isset($_GET['zombye_token']) ) {
            $output .= $this->render_password_form(sanitize_text_field($_GET['zombye_token']));
            return $output;
        }

        // Step 1: handle email submission
        if ( isset($_POST['zombye_email']) ) {
            $output .= $this->handle_form_submission($_POST['zombye_email']);
            return $output; // replace shortcode with message
        }

        // Default email registration form
        $output .= '
        <form method="POST">
            <label>Email: <input type="email" name="zombye_email" required></label>
            <button type="submit">Register</button>
        </form>';

        return $output;
    }

    // Handle email submission (step 1)
    private function handle_form_submission($email) {
        if (email_exists($email)) {
            return '<p>This email is already registered. Please use another email or log in.</p>';
        }

        if (!is_email($email)) {
            return '<p>Invalid email address.</p>';
        }

        $token = wp_generate_password(20, false);
        set_transient('zombye_' . $token, $email, 12 * HOUR_IN_SECONDS);

        // Dynamic link to the current page (shortcode)
        $page_url = get_permalink();
        $link = add_query_arg(['zombye_token' => $token], $page_url);

        $subject = "Confirm your registration";
        $message = "Click this link to validate your account and choose your password:\n\n$link";

        wp_mail($email, $subject, $message);

        return '<p>A confirmation email has been sent. Please check your inbox!</p>';
    }

    // Password form rendering (step 2)
    private function render_password_form($token) {
        $email = get_transient('zombye_' . $token);
        if (!$email) return '<p>Invalid or expired token.</p>';

        $output = '';

        // If there was a mismatch error from previous submission
        if (isset($_GET['zombye_error']) && $_GET['zombye_error'] === 'mismatch') {
            $output .= '<p>Passwords do not match. Please try again.</p>';
        }

        // Password setup form
        $output .= '
        <form method="POST">
            <label>Choose your password: <input type="password" name="password" required></label><br>
            <label>Confirm password: <input type="password" name="password_confirmation" required></label><br>
            <button type="submit">Set password</button>
        </form>';

        return $output;
    }

    // Handle password submission early to allow redirect
    public function maybe_handle_password_submit() {
        if (!isset($_GET['zombye_token'], $_POST['password'], $_POST['password_confirmation'])) {
            return;
        }

        $token = sanitize_text_field($_GET['zombye_token']);
        $email = get_transient('zombye_' . $token);
        if (!$email) return;

        $password = $_POST['password'];
        $password_confirmation = $_POST['password_confirmation'];

        if ($password !== $password_confirmation) {
            // Redirect back to the same page with error query
            $redirect_url = add_query_arg('zombye_error', 'mismatch', get_permalink());
            $redirect_url = add_query_arg('zombye_token', $token, $redirect_url);
            wp_redirect($redirect_url);
            exit;
        }

        $user_id = wp_create_user($email, $password, $email);
        if (is_wp_error($user_id)) {
            wp_die('Error creating account.');
        }

        delete_transient('zombye_' . $token);

        // Auto-login
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);
        do_action('wp_login', $email, get_user_by('id', $user_id));

        // Redirect to profile (w4os aware)
        $profile_url = function_exists('w4os_get_user_profile_url')
            ? w4os_get_user_profile_url($user_id)
            : get_edit_user_link($user_id);

        wp_redirect($profile_url);
        exit;
    }

    // Save the current page as the Zombye registration page if needed
    private function set_registration_page() {
        $current_page_id = get_the_ID();
        if (!$current_page_id) return;

        // Récupère toutes les options zombye
        $opts = get_option('zombye', []);

        // Si la page enregistrée existe et contient toujours le shortcode, rien à faire
        if (!empty($opts['registration_page'])) {
            $stored_content = get_post_field('post_content', $opts['registration_page']);
            if ($stored_content && has_shortcode($stored_content, 'zombye_register')) {
                return;
            }
        }

        // Sauvegarde la page courante dans le tableau zombye
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
}
