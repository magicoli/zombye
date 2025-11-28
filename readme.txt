=== Zombye ===
Contributors: magicoli
Donate link: https://buymeacoffee.com/magicoli69
Tags: registration, spam, bots, users
Requires at least: 6.0
Tested up to: 6.8.3
Requires PHP: 8.0
Stable tag: 0.1.0
License: AGPLv3 or later
License URI: https://www.gnu.org/licenses/agpl-3.0.html

Say goodbye to zombie registrations.

== Description ==

Zombye is a lightweight plugin that prevents “zombie” registrations by verifying email addresses
before creating WordPress user accounts. It is designed to reduce spam registrations, fake accounts,
and the unwanted notifications they generate.

For now, Zombye provides:

* A simple registration shortcode ([zombye_register]) that displays an email input form.
* Temporary tokens sent via email to confirm registrations before creating a user.
* Automatic account creation only after the user clicks the confirmation link.
* Lightweight and easy to integrate as a library within other plugins.

Future ideas and improvements include:

* Adding optional username/pseudonym fields.
* Integrating lightweight CAPTCHA or math challenge to further reduce bots.
* Customizable email templates (HTML, styling, branding).
* Duplicate email / disposable email detection.
* Integration with other plugins (e.g., membership, avatars) for seamless workflow.
* Logging and admin dashboard for pending confirmations and statistics.

Zombye is free, open-source, and aims to stay minimal and performant while protecting your site from unwanted accounts.

== Installation ==

1. Upload `zombye` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Add the shortcode `[zombye_register]` to any page where you want the registration form to appear.

== Frequently Asked Questions ==

= How does Zombye prevent fake accounts? =

It only creates WordPress accounts after the user confirms their email. This prevents spam and “zombie” users from being added to your database.

= Can I customize the email content? =

Currently, Zombye sends a plain text confirmation email. Custom templates are planned for future versions.

= Will it work with my existing user system? =

Yes, Zombye can be used as a library within other plugins. It is lightweight and does not interfere with other user management features.

== Screenshots ==

1. Example of the registration form using the [zombye_register] shortcode.
2. Email confirmation message sent to the user.

== Changelog ==

= 0.1.0 =
* Initial release: email verification before user creation, simple shortcode form.

== Upgrade Notice ==

= 0.1.0 =
First release of Zombye. No upgrade required.
