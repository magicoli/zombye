# ROADMAP

🧩 1. Reputation Checks: Local Anti-Spam Databases  
There are several libraries that allow you to integrate checks on:  
- Disposable domains  
- Suspicious IPs  
- Compromised emails or emails listed in dumps  
- Patterns used by known botnets  

🔹 **Bouncer / StopForumSpam API client (self-hosted possible)**  
StopForumSpam is the reference: 15 years of spammer data.  
Most importantly, you can self-host their dataset.  

Composer:  
composer require stopforumspam/stopforumspam-php

You can:  
- Regularly download their “email hashes” list  
- Store it in a local file or a small optimized table (hashes only)  
- Perform offline checks (no external dependency)  

🔹 **DNSBL / RHSBL lookups (without direct external dependency)**  
DNSBL only use DNS queries — no identifiable data exchange.  
Network noise is minimal, and zero CAPTCHA.  

Useful libraries:  
- io-developer/php-whois  
- jaybizzle/crawler-detect (interesting complement)  

---

🧪 2. Disposable Email Detection (Local Database)  

Two excellent libraries:  

🔹 **andrewmclagan/disposable-emails**  
Simple, included database, self-hostable:  
composer require andrewmclagan/disposable-emails  
You can update the database via cron, or freeze a version.  

🔹 **Trashmail checker — Kickbox fork**  
Kickbox maintains a huge database (MIT).  
Several forks allow offline usage.  

---

🧠 3. “Semantic” Bot Detection, No CAPTCHA  
The goal: block bots without annoying humans.  

A silent layer can be built with three elements:  

✔ **Invisible Honeypot**  
A hidden field styled via CSS:  
- Humans don’t fill it  
- ~90% of bots do  

✔ **Form Completion Timing**  
- Humans take 3–20 seconds  
- Bots fill instantly  

✔ **Email Entropy & Structure**  
- Some bot-generated emails have very recognizable patterns:  
  - Abnormal length  
  - Pseudo-random sequences (trgh48fqthe@…)  
  - No vowels (classic)  
  - Very rare or very recent TLDs  

A simple scoring system can already block many bots 🌲🔥⛔.  

---

🔐 4. Self-Hosted hCaptcha (Optional)  
hCaptcha offers a “Private self-hosted” mode with no server interaction.  
It is discreet, elegant, and much more privacy-friendly than Google.  
Still a CAPTCHA — should be used as a fallback only.  

---

🧩 5. Hybrid Scoring System  
You can implement a reliability score like this:  

| Criterion                        | Points |
|---------------------------------|--------|
| Valid email                       | +20    |
| Non-disposable domain             | +20    |
| Domain with existing MX           | +20    |
| Form completion time > 2s         | +10    |
| Honeypot empty                     | +10    |
| Email not in local blacklist      | +20    |

**Score 0–100:**  
- <60 = silent rejection (generic message)  

Efficient, fast, and fully self-hostable.  

---

🧬 6. Ideal Architecture for Zombye  
You can add a generic interface:  

interface Zombye_Filter {
    public function check(string $email, array $context = []): bool;
}

Then have modules:  
- Zombye_Filter_Disposable  
- Zombye_Filter_Reputation  
- Zombye_Filter_Behavior  
- Zombye_Filter_SuspiciousPattern  

Each module returns a score or veto.  
The core assembles results without imposing any dependencies.
