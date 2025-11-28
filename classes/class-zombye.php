<?php
namespace Zombye;

if ( ! defined( 'ABSPATH' ) ) exit;

class Zombye {

    public function __construct() {
        // Shortcode to display registration form and handle all steps
        add_shortcode('zombye_register', [$this, 'render_registration_form']);
    }

    // Display the registration form or handle steps
    public function render_registration_form() {
        $output = '';

        // Step 2: check if token is present
        if ( isset($_GET['zombye_token']) ) {
            $output .= $this->render_password_form(sanitize_text_field($_GET['zombye_token']));
            return $output;
        }

        // Step 1: handle email submission
        if ( isset($_POST['zombye_email']) ) {
            $output .= $this->handle_form_submission($_POST['zombye_email']);
            return $output; // replace shortcode with message
        }

        // Regular email form
        $output .= '
        <form method="POST">
            <label>Email: <input type="email" name="zombye_email" required></label>
            <button type="submit">Register</button>
        </form>';

        return $output;
    }

    // Handle email submission (step 1)
    private function handle_form_submission($email) {
        // Check if user already exists
        if (email_exists($email)) {
            return '<p>This email is already registered. Please use another email or log in.</p>';
        }

        if (!is_email($email)) {
            return '<p>Invalid email address.</p>';
        }

        // Generate token and store temporarily
        $token = wp_generate_password(20, false);
        set_transient('zombye_' . $token, $email, 12 * HOUR_IN_SECONDS);

        // Build confirmation link
        $link = add_query_arg(['zombye_token' => $token], home_url('/register/'));
        $subject = "Confirm your registration";
        $message = "Click this link to validate your account and choose your password:\n\n$link";

        wp_mail($email, $subject, $message);

        return '<p>A confirmation email has been sent. Please check your inbox!</p>';
    }

    // Render the password form (step 2)
    private function render_password_form($token) {
        $email = get_transient('zombye_' . $token);
        if (!$email) return '<p>Invalid or expired token.</p>';

        $output = '';

        // Handle password submission
        if ( isset($_POST['zombye_password'], $_POST['zombye_password_confirm']) ) {
            $password = $_POST['zombye_password'];
            $password_confirm = $_POST['zombye_password_confirm'];

            if ($password !== $password_confirm) {
                $output .= '<p>Passwords do not match. Please try again.</p>';
            } else {
                // Create the user
                $user_id = wp_create_user($email, $password, $email);

                if (is_wp_error($user_id)) {
                    $output .= '<p>Error creating account.</p>';
                } else {
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
            }
        }

        // Display password setup form with double field
        $output .= '
        <form method="POST">
            <label>Choose your password: <input type="password" name="zombye_password" required></label><br>
            <label>Confirm password: <input type="password" name="zombye_password_confirm" required></label><br>
            <button type="submit">Set password</button>
        </form>';

        return $output;
    }
}
