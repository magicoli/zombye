=== Zombye ===
Contributors: magicoli
Donate link: https://buymeacoffee.com/magicoli69
Tags: registration, spam, bots, users
Requires at least: 6.0
Tested up to: 6.8.3
Requires PHP: 8.0
Stable tag: 0.9.0
License: AGPLv3 or later
License URI: https://www.gnu.org/licenses/agpl-3.0.html

Say goodbye to zombie registrations.

== Description ==

Zombye is a lightweight plugin that prevents “zombie” registrations by verifying email addresses
before creating WordPress user accounts. It reduces spam signups, fake accounts, and the unwanted
notifications they generate — without adding complexity or heavy UI layers.

**Current features include:**

* A simple registration shortcode (`[zombye_register]`) providing a clean, email-based registration flow.
* Email verification through a secure, time-limited token.
* User creation only after the password is set via the confirmation link.
* Automatic login after successful password submission.
* Redirection to the user profile page (compatible with w4os profile URLs if present).
* Automatic detection of the page containing the registration shortcode.
* Seamless override of the default WordPress “Register” link.
* Unified frontend notice system with customizable CSS.
* Fully translation-ready strings.
* A single `zombye` option array to keep configuration clean and efficient.

Zombye is intentionally minimal: lightweight, fast, and easy to integrate as a library
within other plugins.

**Potential future enhancements:**

* Optional username/pseudonym fields.
* Lightweight bot challenges (e.g., simple math).
* Customizable HTML email templates.
* Detection of disposable/temporary email domains.
* Optional admin tools: pending confirmations, logs, cleanup tools.
* Deeper integration with other membership/profile plugins.

Zombye is free, open-source, and built to stay focused and performant.

== Installation ==

1. Upload the `zombye` folder to `/wp-content/plugins/`.
2. Activate the plugin through “Plugins” in WordPress.
3. Create a page and add the shortcode `[zombye_register]`.
4. That's it — the plugin automatically handles the rest.

== Frequently Asked Questions ==

= How does Zombye reduce fake accounts? =

It only creates WordPress user accounts after the owner of the email confirms it and sets a password.
Bots and fake addresses never reach the user database.

= Can I customize the email content? =

For now, Zombye sends a simple plain-text email. HTML templates and customization options may be added
in future versions.

= Will this break my existing login or membership system? =

No. Zombye is lightweight and designed to coexist with other plugins. It can also be used as a
library embedded inside another project.

== Screenshots ==

1. Registration form using the `[zombye_register]` shortcode.
2. Example confirmation email.

== Changelog ==

= 0.9.0 =
* First functional release: complete email verification workflow, password setup, autologin, profile redirection, shortcode detection, and default “Register” override.

== Upgrade Notice ==

= 0.9.0 =
Initial functional release.
