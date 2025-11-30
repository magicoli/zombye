# Zombye

[![WordPress Plugin Version](https://img.shields.io/badge/WordPress-6.0%2B-blue.svg)](https://wordpress.org/)
[![Tested up to](https://img.shields.io/badge/Tested%20up%20to-6.8.3-green.svg)](https://wordpress.org/)
[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-purple.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/License-AGPLv3-red.svg)](https://www.gnu.org/licenses/agpl-3.0.html)
[![Version](https://img.shields.io/badge/Version-0.9.0-orange.svg)](https://github.com/magicoli/zombye/releases)
[![Buy Me A Coffee](https://img.shields.io/badge/Donate-Buy%20Me%20A%20Coffee-yellow.svg)](https://buymeacoffee.com/magicoli69)

Say goodbye to zombie registrations.

## Description

Zombye is a lightweight plugin that prevents "zombie" registrations by verifying email addresses before creating WordPress user accounts. It reduces spam signups, fake accounts, and the unwanted notifications they generate — without adding complexity or heavy UI layers.

## Features

**Current features include:**

* 🔐 A simple registration shortcode (`[zombye_register]`) providing a clean, email-based registration flow
* ✉️ Email verification through a secure, time-limited token
* 👤 User creation only after the password is set via the confirmation link
* 🚀 Automatic login after successful password submission
* 🔄 Redirection to the user profile page (compatible with w4os profile URLs if present)
* 🔍 Automatic detection of the page containing the registration shortcode
* 🔗 Seamless override of the default WordPress "Register" link
* 🎨 Unified frontend notice system with customizable CSS
* 🌍 Fully translation-ready strings
* ⚙️ A single `zombye` option array to keep configuration clean and efficient

Zombye is intentionally minimal: lightweight, fast, and easy to integrate as a library within other plugins.

## Installation

1. Upload the `zombye` folder to `/wp-content/plugins/`
2. Activate the plugin through "Plugins" in WordPress
3. Create a page and add the shortcode `[zombye_register]`
4. That's it — the plugin automatically handles the rest

## Frequently Asked Questions

### How does Zombye reduce fake accounts?

It only creates WordPress user accounts after the owner of the email confirms it and sets a password. Bots and fake addresses never reach the user database.

### Can I customize the email content?

For now, Zombye sends a simple plain-text email. HTML templates and customization options may be added in future versions.

### Will this break my existing login or membership system?

No. Zombye is lightweight and designed to coexist with other plugins. It can also be used as a library embedded inside another project.

## Future Enhancements

**Potential future enhancements:**

* Optional username/pseudonym fields
* Lightweight bot challenges (e.g., simple math)
* Customizable HTML email templates
* Detection of disposable/temporary email domains
* Optional admin tools: pending confirmations, logs, cleanup tools
* Deeper integration with other membership/profile plugins

Zombye is free, open-source, and built to stay focused and performant.

## Screenshots

1. Registration form using the `[zombye_register]` shortcode
2. Example confirmation email

## Changelog

### 0.9.0
* First functional release: complete email verification workflow, password setup, autologin, profile redirection, shortcode detection, and default "Register" override

## Upgrade Notice

### 0.9.0
Initial functional release.

## Contributing

Contributions are welcome! Feel free to submit issues and pull requests.

## Support

If you find this plugin useful, consider [buying me a coffee](https://buymeacoffee.com/magicoli69) ☕

## License

This project is licensed under the [GNU Affero General Public License v3.0](https://www.gnu.org/licenses/agpl-3.0.html) or later.

---

**Contributors:** [magicoli](https://github.com/magicoli)
**Tags:** registration, spam, bots, users
