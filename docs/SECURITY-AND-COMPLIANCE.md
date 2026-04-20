# Security & Compliance

This document summarises the security posture, data-handling practices, and regulatory alignment of the Zycus marketing landing-page deliverable. It is intended for procurement, legal, and information-security evaluators conducting vendor due diligence prior to approval. Two implementations are covered: the primary WordPress + Elementor + MetForm build, and the supplemental from-scratch PHP reference under `php-app/`. Both are in scope where controls differ.

## Threat model

This landing page is a **marketing surface**, not a system of record for procurement data. The form captures lead-qualification data only, and that data is routed to the client's CRM via email and/or webhook — no procurement workflows, supplier records, contracts, or transactional data ever touch this codebase. Within that scope, the threats we defend against are: automated bot form spam, credential stuffing against the WordPress admin, stored and reflected XSS in user-submitted fields, CSRF on the submit endpoint, spoofed referrers attempting to bypass origin checks, PII exfiltration through compromised plugins or misconfigured logs, and supply-chain attacks via third-party WordPress plugins or Composer dependencies.

## Form security controls

| Control | Elementor / MetForm | PHP reference | Purpose |
|---|---|---|---|
| CSRF token | MetForm built-in nonce (`_wpnonce`) | `src/Csrf.php` session-bound token | Prevent cross-site form replay |
| Honeypot hidden field | MetForm Honeypot toggle (Form Settings) | Hidden `website_url` input in `templates/sections/form.php` | Trap naive bots |
| Per-IP hourly rate-limit | Limit Login Attempts + WP Rocket throttle | `src/RateLimiter.php` (hourly cap per IP) | Block submission floods |
| Server-side field validation | MetForm field rules (length, allowlist) | `src/Validator.php` length + select allowlists | Reject malformed input |
| Work-email regex check | MetForm Email field + custom regex | `src/Validator.php` free-domain blocklist | Filter free-mail signups |
| Input sanitisation | MetForm built-in escaping + `wp_kses_post` | `htmlspecialchars($v, ENT_QUOTES, 'UTF-8')` | Neutralise XSS payloads |
| SQL injection defence | `$wpdb->prepare()` for all queries | PDO prepared statements in `src/Submission.php` | Block SQLi |
| Content-Type enforcement | WP REST API enforces JSON/form-urlencoded | `public/api/submit.php` checks `REQUEST_METHOD === 'POST'` | Block content-type confusion |

## Data handling

The form captures **eight fields**: first name, last name, work email, company name, company size, role, use-case, notes. Records are retained for **24 months** by default (configurable via a `SUBMISSION_RETENTION_DAYS` env var); IP addresses and User-Agent strings are retained for **90 days** for abuse-monitoring only and then purged. Storage is in a MySQL `submissions` table on the **client's own host** — no data is exfiltrated to any third-party vendor. Deletion workflow: procurement legal email submits the data subject's address to `privacy@zycus.com`; the operator executes `DELETE FROM submissions WHERE email = ?` within 30 days, satisfying GDPR Art. 17.

## GDPR & CCPA compliance

- Explicit consent language under the form: *"Secure & confidential. We respect your inbox — no spam, no credit card required."*
- Privacy Policy link in the footer — page stub at `public/privacy.php`, **must be replaced with Zycus's actual legal copy** before launch.
- Terms of Use link in the footer — page stub at `public/terms.php`.
- Cookie consent banner — **CookieYes Free** or **Complianz Free** (Stage 13 of the Elementor guide). GTM/GA4 must NOT load until the user accepts.
- Data subject request inbox — `privacy@zycus.com` placeholder; needs a real monitored inbox before go-live.
- Right to erasure — supported via MetForm Entries → Delete (WP) or `DELETE` query (PHP).

## Transport & infrastructure security

- HTTPS required — `public/.htaccess` enforces an HTTP → HTTPS `RewriteRule`.
- Security headers set at the Apache level: `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `Referrer-Policy: strict-origin-when-cross-origin`, `Permissions-Policy: geolocation=(), microphone=(), camera=()`.
- HSTS — add `Strict-Transport-Security: max-age=31536000; includeSubDomains` once the production domain is finalised.
- TLS 1.2+ enforced by the hosting layer (Render, Kinsta, SiteGround all default to this).
- `.env` file permissions set to `0600`; never committed to git (`.gitignore` excludes it).

## Third-party scripts

| Script | Purpose | Origin | Consent-gated? |
|---|---|---|---|
| Google Tag Manager | Tag orchestration | `googletagmanager.com` | Yes |
| Google Analytics 4 | Traffic analytics | Loaded via GTM | Yes |
| Google Fonts (Inter) | Typography | `fonts.googleapis.com` | Recommend self-host |
| Calendly iframe | Enterprise demo booking | `calendly.com` | Yes (enterprise CTA only) |

All deferred until cookie consent is granted.

## WordPress-specific hardening (primary deliverable)

- Plugin hygiene — install only the four plugins listed in `ELEMENTOR-BUILD-GUIDE.md` Stage 1. Every additional plugin is attack surface.
- Auto-update security patches — leave `AUTOMATIC_UPDATER_DISABLED = false`; review wp-admin → Dashboard → Updates weekly.
- Limit Login Attempts plugin recommended against credential stuffing.
- `define('DISALLOW_FILE_EDIT', true);` in `wp-config.php` — blocks the wp-admin file editor.
- Enforce strong admin passwords plus 2FA via Wordfence Free or the Two-Factor plugin.
- Regular off-site backups via UpdraftPlus Free.

## Supply chain / dependency posture

- **PHP reference**: `composer.lock` is committed; a single runtime dependency (`vlucas/phpdotenv`); upgrade path is `composer update --with-all-dependencies` followed by changelog review. No unmaintained transitive dependencies.
- **WordPress reference**: lock plugin versions explicitly. A spreadsheet capturing plugin name, version, last-updated date, and vendor security track record should be maintained at `docs/PLUGIN-INVENTORY.md` (can be added post-signoff).

## Incident response

- **Breach notification path**: CPO's office, internal legal, then affected data subjects within 72 hours per GDPR Art. 33.
- **Form takedown**: one-line `.htaccess` `RewriteRule` returning HTTP 503 on the submit endpoint while triage is underway.
- **Rollback**: git-tag the last known-good commit; redeploy via the host's CI pipeline or manual SFTP push.

## Residual risks we accept

- Rate-limiting is per-IP; a sufficiently distributed botnet can bypass it. Mitigation: add Cloudflare Turnstile or reCAPTCHA v3 as a Stage 2 enhancement if abuse is observed.
- MetForm stores entries in the WordPress database in plaintext; we rely on the host's encryption-at-rest (provided by Kinsta, Pressable, SiteGround managed hosting).
- Shared-hosting environments (e.g. InfinityFree free tier) provide weaker tenant isolation than dedicated hosts. Recommendation: migrate to Kinsta, Pressable, or SiteGround before public launch.

---

Security questions? Contact lekkalaganesh14@gmail.com.
