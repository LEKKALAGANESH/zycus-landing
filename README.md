<p align="center">
  <img src="public/assets/img/zycus-logo.webp" alt="Zycus brain logo" width="180">
</p>

<h1 align="center">Zycus Landing Page</h1>

<p align="center">
  <em>A production-grade, zero-framework PHP 8.2+ B2B landing page for Zycus AI-Powered Source-to-Pay procurement.</em>
</p>

<p align="center">
  <a href="#"><img alt="PHP"       src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white"></a>
  <a href="#"><img alt="MySQL"     src="https://img.shields.io/badge/MySQL-5.7%2B-4479A1?logo=mysql&logoColor=white"></a>
  <a href="#"><img alt="WCAG"      src="https://img.shields.io/badge/WCAG-2.1%20AA-0F3D81"></a>
  <a href="#"><img alt="CWV"       src="https://img.shields.io/badge/Core%20Web%20Vitals-pass-2ea44f"></a>
  <a href="#"><img alt="Framework" src="https://img.shields.io/badge/framework-none-FF1446"></a>
  <a href="#"><img alt="License"   src="https://img.shields.io/badge/license-Proprietary-lightgrey"></a>
</p>

<p align="center">
  <a href="#overview">Overview</a> &nbsp;•&nbsp;
  <a href="#highlights">Highlights</a> &nbsp;•&nbsp;
  <a href="#tech-stack">Stack</a> &nbsp;•&nbsp;
  <a href="#quick-start">Quick Start</a> &nbsp;•&nbsp;
  <a href="#configuration">Config</a> &nbsp;•&nbsp;
  <a href="#deployment">Deploy</a> &nbsp;•&nbsp;
  <a href="#architecture">Architecture</a> &nbsp;•&nbsp;
  <a href="#accessibility--performance">A11y &amp; Perf</a>
</p>

---

## Overview

Conversion-focused single-page site targeting **Enterprise B2B procurement leaders**. Built with vanilla PHP, hand-written CSS, and three small IIFE JavaScript modules — no frameworks, no build step, no bundler. Deployable to any LAMP/LEMP host, with the free tier of InfinityFree as the reference target.

The visual language is **refined and corporate**: Torea Bay `#0F3D81` for depth and body text, Dodger Blue `#40A4FB` for secondary accents, and Torch Red `#FF1446` reserved exclusively for primary CTAs and focus rings. Motion is purposeful and restrained, synchronised to a single Apple-HIG easing curve.

---

## Highlights

- **Progressive enhancement everywhere.** The custom combobox, multi-step form, FAQ accordion, and logo marquee all remain fully functional when JavaScript is disabled. Native `<select>`, `<details>`, and plain `<form>` submissions are the source of truth.
- **Custom ARIA-1.2 combobox** replacing native `<select>` — keyboard navigation, type-ahead, staggered option cascade, optgroup categories, Torch-Red check-on-selected, and a mutation-observer bridge that mirrors `is-invalid` from the hidden native into the visible trigger.
- **Silky FAQ accordion** driven by `grid-template-rows: 0fr → 1fr` (modern height-auto animation). Single-open behaviour with hard-pause close and zero flash of unstyled content.
- **Dual-row counter-scrolling logo marquee** with hard pause on `:hover` / `:focus-within` at the section level — the logo under the cursor freezes in place rather than drifting away.
- **Personalised thank-you page** — `submit.php` stashes the lead's first name and email in `$_SESSION`; the thank-you template reads and clears them. `dataLayer` carries `personalized: true|false` for GA4 attribution.
- **Personalised apology modal** for DB and network failures. Reads live form values on the client, no server round-trip required, graceful fallback when fields are empty.
- **CSRF, honeypot, rate-limit, per-field validator, apology taxonomy** — 200 / 422 / 403 / 429 / 503 / 500 each render a distinct, actionable UI state.
- **Non-blocking mailer.** `fastcgi_finish_request()` flushes the success redirect to the browser *before* SMTP runs; the user never waits on Brevo.
- **SEO** — JSON-LD Organization + FAQPage schema, OpenGraph and Twitter card, canonical URLs, preloaded LCP image, one `<h1>` per page, semantic heading hierarchy.
- **Accessibility** — skip-link, `banner` / `main` / `contentinfo` landmarks, 3 px Torch-Red `:focus-visible` ring globally, 44×44 minimum touch targets, `prefers-reduced-motion` kill-switch, live-region error announcements.

---

## Tech Stack

| Layer            | Choice                          | Rationale |
|------------------|---------------------------------|-----------|
| Runtime          | PHP 8.2+                        | No framework overhead; ships to any LAMP/LEMP host |
| Database         | MySQL 5.7+ via PDO              | Ubiquitous on free/shared hosts |
| Mail             | PHPMailer + SMTP (Brevo)        | Free tier; no custom-domain requirement |
| Templating       | Vanilla PHP partials            | Zero compile step |
| Styling          | Hand-written CSS + CSS variables| ~1.3k lines, no PostCSS / Tailwind / utility generators |
| Scripting        | Vanilla ES2020+ IIFE modules    | `combobox.js`, `motion.js`, `form.js` — no bundler |
| Analytics        | GTM + GA4                       | Container ID + Measurement ID injected from `.env` |
| Session & Env    | `vlucas/phpdotenv`              | Only runtime dependency |

---

## Repository Layout

```
.
├── .env.example
├── composer.json
├── composer.lock
├── config/
│   ├── faqs.php                  # FAQ source of truth (markup + JSON-LD)
│   └── schema.sql                # MySQL schema for `submissions` + `rate_limits`
├── docs/
│   ├── BUILD-GUIDE.md            # Step-by-step setup, GA4/GTM, InfinityFree deploy
│   ├── PHP-IMPLEMENTATION-PLAN.md  # Architecture + binding design-rules contract
│   └── free-plugins-guide.md     # Reference for the optional WordPress/Elementor port
├── public/                       # Web root — point Apache / Nginx here
│   ├── .htaccess
│   ├── index.php
│   ├── thank-you.php
│   ├── privacy.php
│   ├── terms.php
│   ├── robots.txt
│   ├── api/submit.php            # Form POST handler
│   └── assets/
│       ├── css/styles.css        # Design tokens + all components
│       ├── css/animations.css    # Reveals, marquee, reduced-motion kill
│       ├── js/combobox.js        # Custom accessible dropdown
│       ├── js/motion.js          # Scroll reveals, counters, FAQ, marquee
│       ├── js/form.js            # Multi-step validation, apology modal, dataLayer
│       └── img/                  # Brand logo, brand marks, client logos
├── src/                          # `Zycus\` namespace
│   ├── bootstrap.php
│   ├── Config.php
│   ├── Csrf.php
│   ├── Database.php              # PDO singleton with ATTR_TIMEOUT
│   ├── Mailer.php
│   ├── RateLimiter.php
│   ├── Submission.php
│   └── Validator.php
├── storage/logs/                 # Runtime logs (gitignored)
└── templates/
    ├── header.php
    ├── footer.php
    ├── meta.php                  # <head>, OG, JSON-LD, preloads
    └── sections/                 # hero, how-it-works, logos, testimonials, faq, form
```

---

## Quick Start

### Prerequisites

- PHP 8.2 or later (CLI + one of: PHP-FPM, built-in server, mod_php)
- Composer 2.x
- MySQL 5.7+ (or MariaDB 10.3+)
- An SMTP account for outbound email ([Brevo](https://www.brevo.com/) free tier recommended)

### Local setup

```bash
# 1. Clone
git clone https://github.com/LEKKALAGANESH/zycus-landing.git
cd zycus-landing

# 2. Install runtime dependencies
composer install

# 3. Create your .env
cp .env.example .env
# Then edit .env — set DB_*, MAIL_*, GTM_*, GA4_*, APP_URL, APP_CALENDLY_URL.

# 4. Create the database and load the schema
mysql -u root -p -e "CREATE DATABASE zycus_landing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
mysql -u root -p zycus_landing < config/schema.sql

# 5. Run the PHP built-in server
php -S localhost:8000 -t public

# 6. Open in your browser
#    http://localhost:8000
```

---

## Configuration

All runtime configuration flows from `.env`. The file is gitignored; use `.env.example` as the template.

| Key                 | Purpose                                                    | Example                                   |
|---------------------|------------------------------------------------------------|-------------------------------------------|
| `APP_URL`           | Used for canonical URLs, OG tags, JSON-LD                  | `https://zycus-demo.example.com`          |
| `APP_CALENDLY_URL`  | Enterprise / Large Enterprise tier redirect                | `https://calendly.com/zycus-enterprise`   |
| `DB_HOST`           | MySQL host                                                 | `sql100.byetcluster.com`                  |
| `DB_NAME`           | Database name                                              | `if0_XXXXXXXX_zycus_landing`              |
| `DB_USER`           | Database user                                              | `if0_XXXXXXXX`                            |
| `DB_PASS`           | Database password                                          | *(set by host)*                           |
| `DB_TIMEOUT`        | PDO `ATTR_TIMEOUT` in seconds (fail-fast)                  | `5`                                       |
| `MAIL_HOST`         | SMTP host                                                  | `smtp-relay.brevo.com`                    |
| `MAIL_PORT`         | SMTP port                                                  | `587`                                     |
| `MAIL_USER`         | SMTP username                                              | *(set by provider)*                       |
| `MAIL_PASS`         | SMTP password / API key                                    | *(set by provider)*                       |
| `MAIL_FROM_EMAIL`   | "From" header                                              | `noreply@yourdomain.com`                  |
| `MAIL_FROM_NAME`    | "From" display name                                        | `Zycus Landing`                           |
| `MAIL_TO_EMAIL`     | Recipient for new-lead notifications                       | `sales@yourcompany.com`                   |
| `GTM_CONTAINER_ID`  | Google Tag Manager container ID (injected into the page)   | `GTM-XXXXXXX`                             |
| `GA4_MEASUREMENT_ID`| GA4 measurement ID (used inside the GTM container)         | `G-XXXXXXXXXX`                            |

---

## Deployment

**Reference host:** [InfinityFree](https://infinityfree.com) (free, PHP 8+, MySQL, `mod_rewrite`). Full FTP walkthrough in [`docs/BUILD-GUIDE.md`](./docs/BUILD-GUIDE.md) Stage K. Any LAMP-style host that supports PHP-FPM and `mod_rewrite` works equally well.

Pre-flight checklist:

- [ ] `APP_ENV=production`, `APP_DEBUG=false`
- [ ] Live `DB_*` credentials set; `config/schema.sql` imported into the production DB
- [ ] `APP_URL` set to the live HTTPS origin
- [ ] `.env` permissions set to `0600` (readable only by the PHP process)
- [ ] `storage/logs/` writable by the PHP process
- [ ] Composer dependencies installed (`composer install --no-dev --optimize-autoloader`)
- [ ] GTM container published with the correct GA4 measurement ID
- [ ] Lighthouse audit (desktop + mobile): Performance ≥ 90, Accessibility ≥ 95, SEO = 100

---

## Architecture

- **Zero-framework PHP.** Requests enter at `public/index.php`, `public/thank-you.php`, or `public/api/submit.php`. No router. No controllers. No middleware stack.
- **Single source of truth for DOM.** The native `<select>`, `<details>`, and `<form>` are always present. JavaScript enhances them; it never replaces them. If `combobox.js` fails to load, the native `<select>` submits correctly with identical validation.
- **Request handler order in `api/submit.php`**: CSRF → field validator → rate-limit → DB insert → session stash → response flush → SMTP (after `fastcgi_finish_request`). Validation runs before any DB call, so an empty submission returns 422 instantly even when the database is unreachable.
- **Error taxonomy.**

  | Status | Shape                                                   | Client treatment                              |
  |--------|---------------------------------------------------------|-----------------------------------------------|
  | 200    | `{ok: true, redirect, id}`                              | Navigate to `redirect`                        |
  | 422    | `{ok: false, errors: {field: message}}`                 | Inline `.form-error`, jump to first bad step  |
  | 403    | `{ok: false, error}`                                    | Apology modal (generic)                       |
  | 429    | `{ok: false, error}`                                    | Apology modal (rate-limit message)            |
  | 503    | `{ok: false, errorType: 'database', errorHeadline, …}`  | Apology modal titled "Connection Interrupted" |
  | 500    | `{ok: false, error}`                                    | Apology modal (generic)                       |

- **Design-rules contract.** [`docs/PHP-IMPLEMENTATION-PLAN.md`](./docs/PHP-IMPLEMENTATION-PLAN.md) defines the binding specification — palette usage, gradient scope, WCAG targets, Core Web Vitals targets, Enterprise B2B form pattern, exact success / apology copy strings — which also governs the WordPress/Elementor port.

---

## Accessibility & Performance

**WCAG 2.1 AA.**

- Skip-link first in tab order; `role="banner"`, `role="main"`, `role="contentinfo"` landmarks.
- `:focus-visible { outline: 3px solid #FF1446 }` globally.
- Minimum 44×44 touch targets; `min-height: 48px` on all form controls.
- Exactly one `<h1>` per page; no skipped heading levels.
- Every interactive state announced through a hidden `role="status" aria-live="polite"` region.
- `prefers-reduced-motion: reduce` kills all transitions and animations uniformly.

**Core Web Vitals targets.**

- **LCP < 2.5 s** — brain logo preloaded with `fetchpriority="high"`; hero uses a CSS-only animated mesh gradient (no images).
- **CLS < 0.1** — every `<img>` carries explicit `width` and `height`; fonts load `font-display: swap`.
- **INP < 200 ms** — no third-party JS outside GTM; form validation is synchronous and O(n).

---

## Roadmap

- [ ] Convert client logos from JPG to WebP for a ~40% payload reduction
- [ ] Self-host Inter woff2 with `<link rel="preload" as="font">`
- [ ] Optional WordPress / Elementor port following the spec in `docs/PHP-IMPLEMENTATION-PLAN.md`
- [ ] PHP-enum migration for `company_size`, `role`, `use_case` magic strings
- [ ] CI pipeline (PHP-CS-Fixer, Psalm, Lighthouse CI)

---

## License

Proprietary. All brand assets, copy, and the Zycus brain logo are property of Zycus Inc. The source code is not licensed for redistribution without written permission from the repository owner.
