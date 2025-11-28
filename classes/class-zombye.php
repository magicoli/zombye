<?php
namespace Zombye;

if ( ! defined( 'ABSPATH' ) ) exit;

class Zombye {

    public function __construct() {
        // Shortcode to display registration form
        add_shortcode('zombye_register', [$this, 'render_registration_form']);
        // Process email confirmation link and password setup
        add_action('init', [$this, 'process_confirmation']);
    }

    // Display the registration form (step 1)
    public function render_registration_form() {
        // If the user just submitted their email
        if ( isset($_POST['zombye_email']) ) {
            $this->handle_form_submission($_POST['zombye_email']);
            // Return only the confirmation message, not the form
            return '<p>A confirmation email has been sent. Please check your inbox!</p>';
        }

        // Regular form display
        return '
        <form method="POST">
            <label>Email: <input type="email" name="zombye_email" required></label>
            <button type="submit">Register</button>
        </form>';
    }

    // Handle email submission (step 1)
    private function handle_form_submission($email) {
        if (!is_email($email)) {
            echo '<p>Invalid email address.</p>';
            return;
        }

        // Generate token and store temporarily
        $token = wp_generate_password(20, false);
        set_transient('zombye_' . $token, $email, 12 * HOUR_IN_SECONDS);

        // Build confirmation link
        $link = add_query_arg(['zombye_token' => $token], home_url('/register/'));
        $subject = "Confirm your registration";
        $message = "Click this link to validate your account and choose your password:\n\n$link";

        wp_mail($email, $subject, $message);
    }

    // Process confirmation token (step 2: password)
    public function process_confirmation() {
        if ( ! isset($_GET['zombye_token']) ) return;

        $token = sanitize_text_field($_GET['zombye_token']);
        $email = get_transient('zombye_' . $token);

        if (!$email) {
            wp_die('Invalid or expired token.');
        }

        // If password form submitted
        if ( isset($_POST['zombye_password']) ) {
            $password = $_POST['zombye_password'];

            // Create the user
            $user_id = wp_create_user($email, $password, $email);

            if (is_wp_error($user_id)) {
                wp_die('Error creating account.');
            }

            // Remove token
            delete_transient('zombye_' . $token);

            // Auto-login
            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id);
            do_action('wp_login', $email, get_user_by('id', $user_id));

            // Redirect to user profile (w4os aware)
            $profile_url = function_exists('w4os_get_user_profile_url')
                ? w4os_get_user_profile_url($user_id)
                : get_edit_user_link($user_id);

            wp_redirect($profile_url);
            exit;
        }

        // Display password setup form
        echo '
        <form method="POST">
            <label>Choose your password: <input type="password" name="zombye_password" required></label>
            <button type="submit">Set password</button>
        </form>';
        exit;
    }
}
