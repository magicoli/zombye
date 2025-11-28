<?php
namespace Magicoli\Zombye;

if ( ! defined( 'ABSPATH' ) ) exit;

class Zombye {

    public function __construct() {
        // Shortcode to display registration form
        add_shortcode('zombye_register', [$this, 'render_registration_form']);
        // Process email confirmation link
        add_action('init', [$this, 'process_confirmation']);
    }

    // Display the registration form
    public function render_registration_form() {
        if ( isset($_POST['zombye_email']) ) {
            $this->handle_form_submission($_POST['zombye_email']);
        }

        return '
        <form method="POST">
            <label>Email: <input type="email" name="zombye_email" required></label>
            <button type="submit">Register</button>
        </form>';
    }

    // Handle form submission
    private function handle_form_submission($email) {
        if (!is_email($email)) {
            echo '<p>Invalid email address.</p>';
            return;
        }

        // Generate token and store temporarily
        $token = wp_generate_password(20, false);
        set_transient('zombye_' . $token, $email, 12 * HOUR_IN_SECONDS);

        // Build confirmation link
        $link = add_query_arg(['zombye_token' => $token], home_url('/'));
        $subject = "Confirm your registration";
        $message = "Click this link to validate your account: $link";

        wp_mail($email, $subject, $message);

        echo '<p>A confirmation email has been sent. Please check your inbox!</p>';
    }

    // Create account after confirmation
    public function process_confirmation() {
        if ( ! isset($_GET['zombye_token']) ) return;

        $token = sanitize_text_field($_GET['zombye_token']);
        $email = get_transient('zombye_' . $token);

        if (!$email) {
            wp_die('Invalid or expired token.');
        }

        // Create user account
        $user_id = wp_create_user($email, wp_generate_password(), $email);

        if (is_wp_error($user_id)) {
            wp_die('Error creating account.');
        }

        // Remove token
        delete_transient('zombye_' . $token);

        echo '<p>Account successfully created! You can now log in.</p>';
        exit;
    }
}
