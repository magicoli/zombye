# Zombye Developers Guide

## Overview

This document outlines development guidelines, suggested composer libraries for anti-spam and reputation checks, recommended hooks, and example module stubs for extending Zombye.

---

## Composer Libraries

### 1. Reputation / Anti-spam Databases

* **StopForumSpam** (self-hosted possible)

  ```bash
  composer require stopforumspam/stopforumspam-php
  ```

  * Can download email hashes regularly
  * Store in local optimized table or file
  * Offline checks possible, no external dependency needed

* **DNSBL / RHSBL Lookups**

  * Use DNS queries to verify IP/domain reputation
  * Minimal network noise, zero CAPTCHA
  * Useful libs:

    * `io-developer/php-whois`
    * `jaybizzle/crawler-detect` (complementary for bot detection)

### 2. Disposable Email Detection

* **andrewmclagan/disposable-emails**

  ```bash
  composer require andrewmclagan/disposable-emails
  ```

  * Local database included, updateable via cron or fixed version

* **Trashmail / Kickbox forks**

  * MIT licensed, offline usage supported

### 3. Bot / Semantic Detection (without CAPTCHA)

* Honeypot fields (invisible, filled by bots, ignored by humans)
* Form completion timing (bots fill instantly, humans take seconds)
* Email pattern entropy (long random strings, rare TLDs, missing vowels)
* Combine checks into scoring system

### 4. Optional Self-hosted CAPTCHA

* **hCaptcha Private / Self-hosted**

  * No interaction with external servers
  * Can be used as fallback if other checks fail

### 5. Hybrid Scoring System

* Example scoring logic:

  | Check                  | Points |
  | ---------------------- | ------ |
  | Valid email            | +20    |
  | Non-disposable domain  | +20    |
  | MX record exists       | +20    |
  | Form filled > 2s       | +10    |
  | Honeypot empty         | +10    |
  | Not in local blacklist | +20    |
* Total < 60 => silent rejection, generic message

---

## Suggested Hooks

* `zombye_before_email_send($email, $token)`

  * Allows modules to intercept and block email sending
* `zombye_validate_email($email, $context)`

  * Returns true/false or a score based on validation modules
* `zombye_after_user_created($user_id, $email)`

  * Hook to run actions after successful user creation

---

## Example Filter Module Stubs

```php
interface Zombye_Filter {
    public function check(string $email, array $context = []): bool;
}

class Zombye_Filter_Disposable implements Zombye_Filter {
    public function check(string $email, array $context = []): bool {
        // Implement disposable email check
        return true; // or false if blocked
    }
}

class Zombye_Filter_Reputation implements Zombye_Filter {
    public function check(string $email, array $context = []): bool {
        // Implement reputation check using StopForumSpam or DNSBL
        return true;
    }
}

class Zombye_Filter_Behavior implements Zombye_Filter {
    public function check(string $email, array $context = []): bool {
        // Implement honeypot, timing, and entropy scoring
        return true;
    }
}

class Zombye_Filter_SuspiciousPattern implements Zombye_Filter {
    public function check(string $email, array $context = []): bool {
        // Detect bot-like patterns in email addresses
        return true;
    }
}
```

* Core can assemble modules, calculate scores, and block or approve registration without forcing dependencies.

---

## Notes

* All modules should be self-contained and not rely on external services if possible.
* Scores and vetos can be combined to make final decisions.
* This architecture allows Zombye to remain lightweight while still being extensible.
