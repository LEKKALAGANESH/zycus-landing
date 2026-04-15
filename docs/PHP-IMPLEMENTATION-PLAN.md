# Zycus Landing Page — Vanilla PHP Implementation Plan

> **For executors:** This plan builds the Zycus landing page as a from-scratch PHP website (no WordPress, no framework). Steps use checkbox (`- [ ]`) syntax for tracking.

---

## Context

The user originally received a WordPress + Elementor plan (now retired). After reviewing, they pivoted: they want a **from-scratch PHP website** — full code control, no CMS overhead, simpler artifact to deploy on free PHP hosts (InfinityFree, 000webhost). The static HTML prototype already built at `D:\Desktop\Zycus\build-artifacts\prototype\` is the visual target — this plan converts it into a PHP application with templating, a form backend, a database, email notifications, GA4/GTM tracking, the previously-discussed animation layer (Stripe/Linear-style motion), and free-hosting deployment.

**Goal:** Ship a production-ready PHP landing page that visually matches the prototype, accepts demo-request submissions to a MySQL database with email notification, tracks `generate_lead` conversions in GA4, scores LCP < 2.5s on mobile, and deploys to a free PHP host with zero cost.

**Architecture:**

- **Vanilla PHP 8.2+** — no framework. Composer for two libraries only (PHPMailer, vlucas/phpdotenv).
- **Templating:** PHP partials. `header.php`, `footer.php`, and one file per section in `templates/sections/`. Pages assemble partials via `require_once`.
- **Routing:** Three top-level entry points in `public/` — `index.php` (landing), `thank-you.php` (post-submit), `api/submit.php` (form handler). The web root is `public/`; everything else is outside the web root for security.
- **Form flow:** AJAX POST → `api/submit.php` → server validates + sanitizes + stores in MySQL + sends notification email via PHPMailer + returns JSON `{ ok: true, redirect: "/thank-you/?form=zycus_demo" }`. JS handles the redirect; no full page reload.
- **Database:** MySQL via PDO (InfinityFree's free tier supports MySQL 5.7+).
- **Email:** PHPMailer over SMTP (Gmail, Mailgun free tier, or Brevo free tier).
- **Animations:** Carry over the Stripe/Linear-style motion layer from the prototype iteration — `animations.css` + vanilla `motion.js` (no framework).
- **Security:** CSRF token on form, honeypot field, server-side validation, prepared statements, IP-based rate limiting (10 req/hour).
- **Performance:** WebP images, gzip via `.htaccess`, far-future cache headers on `/assets/`, server-side render of FAQ JSON-LD.
- **Deployment:** FTP / SFTP to InfinityFree.

**Tech Stack:**

- PHP 8.2+ (works on InfinityFree — they run 8.x)
- MySQL 5.7+
- Composer (PHPMailer 6.x, vlucas/phpdotenv 5.x)
- Apache `.htaccess` (InfinityFree uses Apache; rewrite + gzip + cache rules go here)
- Vanilla JS + CSS for animations (Motion One optional, but pure vanilla is recommended for the no-framework spirit)

---

## File / Asset Inventory

```
D:\Desktop\Zycus\
├── public/                      ← web root (point Apache here)
│   ├── index.php                ← landing page
│   ├── thank-you.php            ← post-submit page
│   ├── api/
│   │   └── submit.php           ← form handler (POST endpoint)
│   ├── assets/
│   │   ├── css/
│   │   │   ├── styles.css       ← copied from prototype
│   │   │   └── animations.css   ← from animation plan iteration
│   │   ├── js/
│   │   │   └── motion.js        ← from animation plan iteration
│   │   └── img/
│   │       ├── hero.webp        ← user supplies
│   │       ├── hero.jpg         ← fallback
│   │       └── logos/{1..6}.svg ← user supplies
│   ├── .htaccess                ← Apache rewrite + gzip + cache
│   └── robots.txt
├── templates/
│   ├── header.php               ← <head>, <header>, GTM head snippet
│   ├── footer.php               ← <footer>, sticky CTA, GTM body, scripts
│   ├── meta.php                 ← SEO meta tags + JSON-LD
│   └── sections/
│       ├── hero.php
│       ├── how-it-works.php
│       ├── logos.php
│       ├── testimonials.php
│       ├── faq.php
│       └── form.php
├── src/
│   ├── Config.php               ← env loader + DB DSN builder
│   ├── Database.php             ← PDO singleton wrapper
│   ├── Validator.php            ← form field validation rules
│   ├── Mailer.php               ← PHPMailer wrapper
│   ├── RateLimiter.php          ← IP-based throttle (file or DB-backed)
│   ├── Csrf.php                 ← token generate + verify
│   └── Submission.php           ← Submission entity + persistence
├── config/
│   └── schema.sql               ← MySQL table definition
├── storage/
│   ├── rate_limit.json          ← writable (chmod 666)
│   └── logs/                    ← writable
├── vendor/                      ← Composer-installed
├── .env                         ← gitignored, contains secrets
├── .env.example                 ← template, committed
├── composer.json
├── composer.lock
└── README.md                    ← deployment + setup instructions
```

**Reused from prior artifacts:**

- `D:\Desktop\Zycus\build-artifacts\prototype\index.html` → source of HTML structure, copy section markup into `templates/sections/*.php`
- `D:\Desktop\Zycus\build-artifacts\prototype\styles.css` → copy verbatim to `public/assets/css/styles.css`
- `D:\Desktop\Zycus\build-artifacts\copy\zycus-landing-copy.md` → reference for all in-page strings
- `D:\Desktop\Zycus\build-artifacts\gtm\GTM-zycus-landing.json` → user imports into their GTM account; GTM container ID embedded into `templates/header.php` and `templates/footer.php`
- `D:\Desktop\Zycus\build-artifacts\wpcode\snippets.md` → snippets 8 (Organization JSON-LD) and 9 (FAQ JSON-LD) ported into `templates/meta.php` as PHP-rendered output

**Obsolete (do not use):**

- `D:\Desktop\Zycus\build-artifacts\metform\` — MetForm is WordPress-only; replaced by `public/api/submit.php`
- WordPress-specific guidance in `BUILD-GUIDE.md` and `free-plugins-guide.md` — keep as historical reference but do not follow

---

## Phase 1 — Project Scaffolding

### Task 1: Initialize project structure

**Files:**

- Create: directory tree per inventory above

- [ ] **Step 1: Create directories**

```bash
cd D:/Desktop/Zycus
mkdir -p public/api public/assets/{css,js,img/logos} templates/sections src config storage/logs
```

- [ ] **Step 2: Initialize Composer**

```bash
cd D:/Desktop/Zycus
composer init --name="zycus/landing" --type=project --require="php:>=8.2" --require="phpmailer/phpmailer:^6.9" --require="vlucas/phpdotenv:^5.6" --autoload="src/" --stability=stable -n
composer install
```

- [ ] **Step 3: Configure PSR-4 autoload** in `composer.json` — confirm the `autoload` block reads:

```json
"autoload": { "psr-4": { "Zycus\\": "src/" } }
```

Run `composer dump-autoload`.

- [ ] **Step 4: Create `.gitignore`**

```
/vendor/
/.env
/storage/rate_limit.json
/storage/logs/*.log
.DS_Store
Thumbs.db
```

- [ ] **Step 5: Create `.env.example`**

```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://zycus.example.com

DB_HOST=localhost
DB_NAME=zycus_landing
DB_USER=
DB_PASS=

MAIL_HOST=smtp.brevo.com
MAIL_PORT=587
MAIL_USER=
MAIL_PASS=
MAIL_FROM_EMAIL=noreply@zycus.example.com
MAIL_FROM_NAME=Zycus Landing
MAIL_TO_EMAIL=sales@zycus.example.com

GTM_CONTAINER_ID=GTM-XXXXXXX
GA4_MEASUREMENT_ID=G-XXXXXXXXXX
```

Copy to `.env` and fill in real values during setup.

- [ ] **Step 6: Verify** — `composer dump-autoload` runs without errors. Tree matches inventory.

---

### Task 2: Configuration loader

**Files:**

- Create: `src/Config.php`

- [ ] **Step 1: Implement Config class**

```php
<?php declare(strict_types=1);
namespace Zycus;

use Dotenv\Dotenv;

final class Config {
  private static ?array $values = null;

  public static function boot(string $rootDir): void {
    Dotenv::createImmutable($rootDir)->load();
    self::$values = $_ENV;
  }

  public static function get(string $key, mixed $default = null): mixed {
    return self::$values[$key] ?? $default;
  }

  public static function require(string $key): string {
    $v = self::get($key);
    if ($v === null || $v === '') {
      throw new \RuntimeException("Missing required env: $key");
    }
    return (string) $v;
  }
}
```

- [ ] **Step 2: Create bootstrap stub** at `src/bootstrap.php`

```php
<?php declare(strict_types=1);
require_once __DIR__ . '/../vendor/autoload.php';
\Zycus\Config::boot(__DIR__ . '/..');
```

Every entry point in `public/` will `require_once __DIR__ . '/../src/bootstrap.php';` at the top.

- [ ] **Step 3: Verify** — create a temp `public/test.php` that calls `\Zycus\Config::require('APP_URL')` and prints it. Run `php -S localhost:8000 -t public` and visit `http://localhost:8000/test.php`. Should print the URL. Delete the test file.

---

## Phase 2 — Database

### Task 3: Schema + Database wrapper

**Files:**

- Create: `config/schema.sql`
- Create: `src/Database.php`

- [ ] **Step 1: Write the schema** (`config/schema.sql`)

```sql
CREATE TABLE IF NOT EXISTS submissions (
  id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email           VARCHAR(255) NOT NULL,
  first_name      VARCHAR(100) NOT NULL,
  last_name       VARCHAR(100) NOT NULL,
  company_name    VARCHAR(255) NOT NULL,
  company_size    ENUM('small','mid','enterprise','large_enterprise') NOT NULL,
  role            VARCHAR(50)  NOT NULL,
  use_case        VARCHAR(50)  NOT NULL,
  notes           TEXT NULL,
  ip_address      VARCHAR(45)  NOT NULL,
  user_agent      VARCHAR(500) NULL,
  source_url      VARCHAR(500) NULL,
  created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_email (email),
  INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS rate_limit (
  ip_address      VARCHAR(45) NOT NULL,
  hit_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_ip_time (ip_address, hit_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

- [ ] **Step 2: Implement Database class** (`src/Database.php`)

```php
<?php declare(strict_types=1);
namespace Zycus;

use PDO;

final class Database {
  private static ?PDO $pdo = null;

  public static function pdo(): PDO {
    if (self::$pdo === null) {
      $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=utf8mb4',
        Config::require('DB_HOST'),
        Config::require('DB_NAME'),
      );
      self::$pdo = new PDO($dsn, Config::require('DB_USER'), Config::require('DB_PASS'), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
      ]);
    }
    return self::$pdo;
  }
}
```

- [ ] **Step 3: Verify** — locally, install MySQL or use XAMPP. Run `mysql -u root -p < config/schema.sql` against a `zycus_landing` database. Confirm tables exist with `SHOW TABLES`.

---

## Phase 3 — Templating

### Task 4: Header + footer + meta partials

**Files:**

- Create: `templates/meta.php`, `templates/header.php`, `templates/footer.php`

- [ ] **Step 1: `templates/meta.php`** — emits the `<head>` SEO + JSON-LD

```php
<?php
$title = $title ?? 'Zycus — AI-Powered Source-to-Pay Procurement | Book a Demo';
$desc  = $desc  ?? 'Cut procurement costs by 40% with Merlin AI. Automate sourcing, contracting, and invoicing end-to-end. Book a personalized demo today.';
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($title, ENT_QUOTES) ?></title>
<meta name="description" content="<?= htmlspecialchars($desc, ENT_QUOTES) ?>">
<meta property="og:title" content="Zycus Merlin AI — Cut Procurement Costs by 40%">
<meta property="og:description" content="Automate sourcing, contracts, and invoices end-to-end with agentic AI. Book your personalized demo.">
<meta property="og:type" content="website">
<meta name="twitter:card" content="summary_large_image">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
<link rel="stylesheet" href="/assets/css/styles.css">
<link rel="stylesheet" href="/assets/css/animations.css">
<script type="application/ld+json"><?= json_encode([
  '@context' => 'https://schema.org',
  '@type'    => 'Organization',
  'name'     => 'Zycus',
  'url'      => \Zycus\Config::get('APP_URL'),
  'logo'     => \Zycus\Config::get('APP_URL') . '/assets/img/logo.svg',
  'sameAs'   => ['https://www.linkedin.com/company/zycus', 'https://twitter.com/zycus'],
], JSON_UNESCAPED_SLASHES) ?></script>
<script type="application/ld+json"><?= json_encode([
  '@context' => 'https://schema.org',
  '@type'    => 'FAQPage',
  'mainEntity' => array_map(fn($f) => [
    '@type'          => 'Question',
    'name'           => $f[0],
    'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f[1]],
  ], require __DIR__ . '/../config/faqs.php'),
], JSON_UNESCAPED_SLASHES) ?></script>
```

- [ ] **Step 2: Move FAQ data to its own file** (`config/faqs.php`)

```php
<?php return [
  ['Will Zycus integrate with our existing ERP (SAP, Oracle, NetSuite)?', 'Yes. Zycus ships with certified, pre-built connectors for SAP S/4HANA, SAP ECC, Oracle Fusion, Oracle EBS, NetSuite, Microsoft Dynamics, and Workday. Most integrations go live in under 4 weeks and sync invoices, POs, master data, and GL codes bi-directionally.'],
  ['How long does implementation actually take?', 'A typical mid-market rollout of Source-to-Contract goes live in 8–12 weeks. Full Source-to-Pay across multiple business units is usually 16–20 weeks. Our implementation team handles configuration, integration, and user enablement — your team focuses on process design.'],
  ['How is our procurement data kept secure?', 'Zycus is SOC 2 Type II, ISO 27001, and ISO 27701 certified. All data is encrypted in transit (TLS 1.3) and at rest (AES-256), hosted in your choice of AWS region with full data residency controls. We\'re GDPR, CCPA, and HIPAA compliant.'],
  ['What kind of ROI can we actually expect?', 'Customers typically report 40–60% reduction in cycle times, 5–8% savings on addressable spend, and 70%+ reduction in manual invoice handling within 12 months. We\'ll share a custom ROI model during your demo based on your current spend and team size.'],
];
```

- [ ] **Step 3: `templates/header.php`** — opens `<html>`, includes meta + GTM head, opens `<body>`, renders site header

```php
<?php $gtmId = \Zycus\Config::get('GTM_CONTAINER_ID', 'GTM-XXXXXXX'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php require __DIR__ . '/meta.php'; ?>
  <!-- Google Tag Manager -->
  <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?= htmlspecialchars($gtmId, ENT_QUOTES) ?>');</script>
</head>
<body>
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?= htmlspecialchars($gtmId, ENT_QUOTES) ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
  <div class="scroll-sentinel" aria-hidden="true" style="position:absolute;top:0;height:80px;width:1px;"></div>
  <header class="site-header">
    <div class="container site-header__inner">
      <a href="/" class="brand" aria-label="Zycus home">
        <span class="brand__mark" aria-hidden="true">Z</span>
        <span class="brand__name">Zycus</span>
      </a>
      <nav class="site-nav" aria-label="Primary">
        <a href="#how-it-works">How it works</a>
        <a href="#testimonials">Customers</a>
        <a href="#faq">FAQ</a>
      </nav>
      <a href="#demo-form" class="btn btn--primary btn--sm site-header__cta">Book My Demo</a>
    </div>
  </header>
  <main>
```

- [ ] **Step 4: `templates/footer.php`** — closes `<main>`, renders footer + sticky CTA + scripts

```php
  </main>
  <footer class="site-footer">
    <div class="container site-footer__inner">
      <div class="brand brand--footer">
        <span class="brand__mark" aria-hidden="true">Z</span>
        <span class="brand__name">Zycus</span>
      </div>
      <p class="site-footer__copy">&copy; <?= date('Y') ?> Zycus Inc. All rights reserved.</p>
    </div>
  </footer>
  <div class="mobile-cta" role="complementary" aria-label="Quick actions">
    <a href="#demo-form" class="btn btn--accent btn--block">Book Demo</a>
  </div>
  <script src="/assets/js/motion.js" defer></script>
  <script src="/assets/js/form.js" defer></script>
</body>
</html>
```

- [ ] **Step 5: Verify** — run `php -S localhost:8000 -t public` and add a temp `public/test.php` that includes header.php + footer.php. Visit it and confirm HTML renders, no PHP errors, GTM snippet has the placeholder ID.

---

### Task 5: Section partials

**Files:**

- Create: `templates/sections/hero.php`, `how-it-works.php`, `logos.php`, `testimonials.php`, `faq.php`, `form.php`

- [ ] **Step 1: Copy each `<section>` block** from `D:\Desktop\Zycus\build-artifacts\prototype\index.html` (lines 30–330) into one section partial each. Update with these PHP-specific changes:
  - Replace static FAQ markup in `faq.php` with a loop over `config/faqs.php`:

```php
<?php $faqs = require __DIR__ . '/../../config/faqs.php'; ?>
<section id="faq" class="section section--light" aria-labelledby="faq-heading">
  <div class="container container--narrow">
    <div class="section__head" data-reveal="up">
      <h2 id="faq-heading" class="h2">Your Questions, Answered</h2>
      <p class="section__sub">Everything procurement, finance, and IT leaders ask us before signing.</p>
    </div>
    <div class="faq" data-stagger="80">
      <?php foreach ($faqs as [$q, $a]): ?>
        <details class="faq__item" data-reveal="up">
          <summary class="faq__q">
            <span><?= htmlspecialchars($q, ENT_QUOTES) ?></span>
            <span class="faq__icon" aria-hidden="true"><span></span><span></span></span>
          </summary>
          <div class="faq__a"><?= htmlspecialchars($a, ENT_QUOTES) ?></div>
        </details>
      <?php endforeach; ?>
    </div>
  </div>
</section>
```

- In `form.php`: emit the CSRF token from `\Zycus\Csrf::token()` as a hidden input. Change the form to submit via fetch (handled in `form.js` later).

- [ ] **Step 2: Add the data-reveal / data-stagger / data-counter attributes** to hero, how-it-works, logos, testimonials per the animation plan iteration. (See "Animation hooks reference" appendix at the end of this plan.)

- [ ] **Step 3: Verify** — each section partial parses with `php -l templates/sections/<file>.php` (no syntax errors).

---

### Task 6: Build index.php and thank-you.php

**Files:**

- Create: `public/index.php`, `public/thank-you.php`

- [ ] **Step 1: `public/index.php`**

```php
<?php declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../templates/header.php';
require __DIR__ . '/../templates/sections/hero.php';
require __DIR__ . '/../templates/sections/how-it-works.php';
require __DIR__ . '/../templates/sections/logos.php';
require __DIR__ . '/../templates/sections/testimonials.php';
require __DIR__ . '/../templates/sections/faq.php';
require __DIR__ . '/../templates/sections/form.php';
require __DIR__ . '/../templates/footer.php';
```

- [ ] **Step 2: `public/thank-you.php`**

```php
<?php declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';
$title = 'Thank You — Your Zycus Demo Request | Zycus';
require __DIR__ . '/../templates/header.php';
?>
<section class="section">
  <div class="container container--narrow" style="text-align:center;padding:120px 0;">
    <h1 class="h1">Thanks! We'll be in touch within 4 business hours.</h1>
    <p class="lead" style="max-width:600px;margin:24px auto;">
      A Zycus procurement specialist is reviewing your request now. Expect an email shortly with calendar options for your personalized demo.
    </p>
  </div>
</section>
<?php require __DIR__ . '/../templates/footer.php';
```

- [ ] **Step 3: Verify** — `php -S localhost:8000 -t public` → visit `http://localhost:8000/`. Whole page renders. Visit `/thank-you.php` — confirmation message appears.

---

## Phase 4 — Form Backend

### Task 7: CSRF + Validator + RateLimiter

**Files:**

- Create: `src/Csrf.php`, `src/Validator.php`, `src/RateLimiter.php`

- [ ] **Step 1: `src/Csrf.php`**

```php
<?php declare(strict_types=1);
namespace Zycus;

final class Csrf {
  public static function token(): string {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
  }
  public static function verify(string $token): bool {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    return !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
  }
}
```

- [ ] **Step 2: `src/Validator.php`** — server-side rules matching the form spec

```php
<?php declare(strict_types=1);
namespace Zycus;

final class Validator {
  /** @return array{0: array<string,string>, 1: array<string,string>}  [errors, cleaned] */
  public static function validate(array $input): array {
    $errors = [];
    $clean = [];

    // Email
    $email = trim($input['email'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors['email'] = 'Please enter a valid email address.';
    } elseif (preg_match('/@(gmail|yahoo|hotmail|outlook|icloud)\.com$/i', $email)) {
      $errors['email'] = 'Please use your work email.';
    } else {
      $clean['email'] = $email;
    }

    foreach (['first_name', 'last_name', 'company_name'] as $f) {
      $v = trim($input[$f] ?? '');
      if ($v === '' || mb_strlen($v) > 255) $errors[$f] = 'Required.';
      else $clean[$f] = $v;
    }

    $sizes = ['small','mid','enterprise','large_enterprise'];
    if (!in_array($input['company_size'] ?? '', $sizes, true)) $errors['company_size'] = 'Required.';
    else $clean['company_size'] = $input['company_size'];

    $roles = ['procurement_leader','finance_leader','it','procurement_team','other'];
    if (!in_array($input['role'] ?? '', $roles, true)) $errors['role'] = 'Required.';
    else $clean['role'] = $input['role'];

    $cases = ['s2c','ap','supplier_mgmt','s2p'];
    if (!in_array($input['use_case'] ?? '', $cases, true)) $errors['use_case'] = 'Required.';
    else $clean['use_case'] = $input['use_case'];

    $notes = trim($input['notes'] ?? '');
    if (mb_strlen($notes) > 500) $errors['notes'] = 'Max 500 characters.';
    else $clean['notes'] = $notes ?: null;

    // Honeypot
    if (!empty($input['website_url'])) $errors['_honeypot'] = 'Spam detected.';

    return [$errors, $clean];
  }
}
```

- [ ] **Step 3: `src/RateLimiter.php`** — DB-backed, 10 requests per hour per IP

```php
<?php declare(strict_types=1);
namespace Zycus;

final class RateLimiter {
  private const MAX_PER_HOUR = 10;

  public static function allow(string $ip): bool {
    $pdo = Database::pdo();
    $pdo->prepare('DELETE FROM rate_limit WHERE hit_at < (NOW() - INTERVAL 1 HOUR)')->execute();
    $count = $pdo->prepare('SELECT COUNT(*) FROM rate_limit WHERE ip_address = ? AND hit_at >= (NOW() - INTERVAL 1 HOUR)');
    $count->execute([$ip]);
    if ((int) $count->fetchColumn() >= self::MAX_PER_HOUR) return false;
    $pdo->prepare('INSERT INTO rate_limit (ip_address) VALUES (?)')->execute([$ip]);
    return true;
  }
}
```

- [ ] **Step 4: Verify** — write a one-off `tests/test_validator.php` with 3 cases (valid input → no errors; missing email → email error; gmail.com → "use work email" error). Run with `php tests/test_validator.php`. Delete after passing.

---

### Task 8: Submission entity + Mailer

**Files:**

- Create: `src/Submission.php`, `src/Mailer.php`

- [ ] **Step 1: `src/Submission.php`**

```php
<?php declare(strict_types=1);
namespace Zycus;

final class Submission {
  public static function store(array $clean, string $ip, ?string $userAgent, ?string $sourceUrl): int {
    $pdo = Database::pdo();
    $stmt = $pdo->prepare('
      INSERT INTO submissions (email, first_name, last_name, company_name, company_size, role, use_case, notes, ip_address, user_agent, source_url)
      VALUES (:email, :first_name, :last_name, :company_name, :company_size, :role, :use_case, :notes, :ip, :ua, :src)
    ');
    $stmt->execute([
      ':email' => $clean['email'],
      ':first_name' => $clean['first_name'],
      ':last_name'  => $clean['last_name'],
      ':company_name' => $clean['company_name'],
      ':company_size' => $clean['company_size'],
      ':role'         => $clean['role'],
      ':use_case'     => $clean['use_case'],
      ':notes'        => $clean['notes'],
      ':ip'           => $ip,
      ':ua'           => $userAgent,
      ':src'          => $sourceUrl,
    ]);
    return (int) $pdo->lastInsertId();
  }
}
```

- [ ] **Step 2: `src/Mailer.php`** using PHPMailer over SMTP

```php
<?php declare(strict_types=1);
namespace Zycus;

use PHPMailer\PHPMailer\PHPMailer;

final class Mailer {
  public static function notifyNewSubmission(array $clean): void {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = Config::require('MAIL_HOST');
    $mail->SMTPAuth   = true;
    $mail->Username   = Config::require('MAIL_USER');
    $mail->Password   = Config::require('MAIL_PASS');
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = (int) Config::require('MAIL_PORT');

    $mail->setFrom(Config::require('MAIL_FROM_EMAIL'), Config::get('MAIL_FROM_NAME', 'Zycus'));
    $mail->addAddress(Config::require('MAIL_TO_EMAIL'));
    $mail->addReplyTo($clean['email'], "{$clean['first_name']} {$clean['last_name']}");

    $mail->Subject = "New Demo Request — {$clean['company_name']}";
    $body = "New demo request received:\n\n";
    foreach ($clean as $k => $v) $body .= sprintf("%-15s: %s\n", $k, $v);
    $mail->Body = $body;
    $mail->send();
  }
}
```

- [ ] **Step 3: Verify** — manual SMTP smoke test. Call `Mailer::notifyNewSubmission(['email'=>'test@example.com', ...])` from a one-off script with real SMTP creds, confirm an email lands.

---

### Task 9: Form handler endpoint

**Files:**

- Create: `public/api/submit.php`

- [ ] **Step 1: `public/api/submit.php`**

```php
<?php declare(strict_types=1);
require_once __DIR__ . '/../../src/bootstrap.php';

use Zycus\{Csrf, Validator, RateLimiter, Submission, Mailer};

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
  exit;
}

$ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$ip = explode(',', $ip)[0];

if (!RateLimiter::allow($ip)) {
  http_response_code(429);
  echo json_encode(['ok' => false, 'error' => 'Too many requests. Try again in an hour.']);
  exit;
}

$input = $_POST;
if (!Csrf::verify($input['csrf'] ?? '')) {
  http_response_code(403);
  echo json_encode(['ok' => false, 'error' => 'Invalid session. Please reload the page.']);
  exit;
}

[$errors, $clean] = Validator::validate($input);
if ($errors) {
  http_response_code(422);
  echo json_encode(['ok' => false, 'errors' => $errors]);
  exit;
}

try {
  $id = Submission::store($clean, $ip, $_SERVER['HTTP_USER_AGENT'] ?? null, $_SERVER['HTTP_REFERER'] ?? null);
  Mailer::notifyNewSubmission($clean);
} catch (\Throwable $e) {
  error_log("[zycus] submission failed: " . $e->getMessage());
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'Something went wrong. Please try again.']);
  exit;
}

// Conditional redirect by company size
$redirect = match ($clean['company_size']) {
  'enterprise', 'large_enterprise' => 'https://calendly.com/zycus-enterprise-ae',
  'small'                          => '/self-serve-tour/',
  default                          => '/thank-you.php?form=zycus_demo',
};

echo json_encode(['ok' => true, 'redirect' => $redirect, 'id' => $id]);
```

- [ ] **Step 2: Verify** — POST to `/api/submit.php` via curl with valid + invalid payloads. Confirm 200/422/429/403 responses match expectations and a row lands in `submissions`.

---

### Task 10: Frontend form JS

**Files:**

- Create: `public/assets/js/form.js`
- Modify: `templates/sections/form.php` to wire up

- [ ] **Step 1: Add CSRF + honeypot fields to `form.php`** form opening:

```php
<form class="demo-form" id="demo-form-el" method="POST" action="/api/submit.php" novalidate>
  <input type="hidden" name="csrf" value="<?= htmlspecialchars(\Zycus\Csrf::token(), ENT_QUOTES) ?>">
  <input type="text" name="website_url" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px" aria-hidden="true">
```

Update field `name`s to match the validator (`first_name`, `last_name`, `company_name`, `company_size`, `role`, `use_case`, `notes`).

- [ ] **Step 2: Add multi-step navigation + AJAX submit** in `form.js`

```js
(() => {
  const form = document.getElementById("demo-form-el");
  if (!form) return;

  // Multi-step navigation
  const steps = [...form.querySelectorAll(".form-step")];
  let idx = 0;
  const show = (i) =>
    steps.forEach((s, j) => (s.style.display = j === i ? "" : "none"));
  show(0);

  // Inject Next/Back buttons
  steps.forEach((step, i) => {
    const nav = document.createElement("div");
    nav.className = "form-nav";
    if (i > 0) {
      const back = Object.assign(document.createElement("button"), {
        type: "button",
        textContent: "Back",
        className: "btn btn--ghost",
      });
      back.addEventListener("click", () => show(--idx));
      nav.appendChild(back);
    }
    if (i < steps.length - 1) {
      const next = Object.assign(document.createElement("button"), {
        type: "button",
        textContent: "Next",
        className: "btn btn--secondary",
      });
      next.addEventListener("click", () => {
        if (validateStep(step)) show(++idx);
      });
      nav.appendChild(next);
    }
    step.appendChild(nav);
  });

  function validateStep(step) {
    const required = step.querySelectorAll("[required]");
    for (const el of required) {
      if (!el.value.trim()) {
        el.focus();
        el.classList.add("is-invalid");
        return false;
      }
      el.classList.remove("is-invalid");
    }
    return true;
  }

  // AJAX submit
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.textContent = "Sending…";
    try {
      const res = await fetch(form.action, {
        method: "POST",
        body: new FormData(form),
      });
      const data = await res.json();
      if (data.ok) {
        // dataLayer push for GTM
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
          event: "generate_lead",
          form_id: "zycus_demo",
        });
        location.assign(data.redirect);
      } else if (data.errors) {
        for (const [field, msg] of Object.entries(data.errors)) {
          const el = form.querySelector(`[name="${field}"]`);
          if (el) {
            el.classList.add("is-invalid");
            el.setAttribute("aria-describedby", `${field}-err`);
            el.insertAdjacentHTML(
              "afterend",
              `<small id="${field}-err" class="form-error">${msg}</small>`,
            );
          }
        }
        submitBtn.disabled = false;
        submitBtn.textContent = "Book My Demo";
      } else {
        alert(data.error || "Submission failed.");
        submitBtn.disabled = false;
        submitBtn.textContent = "Book My Demo";
      }
    } catch (err) {
      alert("Network error. Please try again.");
      submitBtn.disabled = false;
      submitBtn.textContent = "Book My Demo";
    }
  });
})();
```

- [ ] **Step 3: Verify** — submit form with valid + invalid data. Confirm: invalid step blocks Next; valid submit POSTs to API; on success, redirects per company-size logic; dataLayer push fires.

---

## Phase 5 — Animations Layer

### Task 11: Copy animations.css and motion.js from animation plan iteration

**Files:**

- Create: `public/assets/css/animations.css`
- Create: `public/assets/js/motion.js`

- [ ] **Step 1: `animations.css`** — paste verbatim the contents from Phase 1+ tasks of the prior animation plan iteration (design tokens, reveal utilities, hero choreography keyframes, step-card hover, marquee, testimonial hover, FAQ icon, form focus, sticky-CTA states, sticky-header glass, reduced-motion overrides). See "Animation utilities reference" appendix below.

- [ ] **Step 2: `motion.js`** — paste verbatim the IntersectionObserver-based reveal controller, hero load-time choreography, KPI counter, FAQ smooth-height, sticky-mobile-CTA logic, sticky-header sentinel observer.

- [ ] **Step 3: Copy `styles.css`** verbatim from `D:\Desktop\Zycus\build-artifacts\prototype\styles.css` to `public/assets/css/styles.css`.

- [ ] **Step 4: Verify** — open `http://localhost:8000/` in Chrome. Confirm: hero animates in on load, scroll triggers fade-up reveals, FAQ accordion smoothly expands, logo strip marquees, sticky CTA appears on scroll past hero. Toggle DevTools "Emulate prefers-reduced-motion: reduce" → all animations stop.

---

## Phase 6 — Apache, Performance, Security

### Task 12: .htaccess

**Files:**

- Create: `public/.htaccess`

- [ ] **Step 1:**

```apache
# Pretty URL: /thank-you/ → thank-you.php
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^thank-you/?$ thank-you.php [L]

# Force HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Gzip
<IfModule mod_deflate.c>
  AddOutputFilterByType DEFLATE text/html text/css text/javascript application/javascript application/json image/svg+xml
</IfModule>

# Far-future cache for static assets
<IfModule mod_expires.c>
  ExpiresActive On
  ExpiresByType text/css "access plus 1 year"
  ExpiresByType application/javascript "access plus 1 year"
  ExpiresByType image/webp "access plus 1 year"
  ExpiresByType image/jpeg "access plus 1 year"
  ExpiresByType image/svg+xml "access plus 1 year"
  ExpiresByType font/woff2 "access plus 1 year"
</IfModule>

# Security headers
<IfModule mod_headers.c>
  Header set X-Content-Type-Options "nosniff"
  Header set X-Frame-Options "SAMEORIGIN"
  Header set Referrer-Policy "strict-origin-when-cross-origin"
  Header set Permissions-Policy "geolocation=(), microphone=(), camera=()"
</IfModule>

# Block PHP execution in storage/
<FilesMatch "\.(env|json|log|sql)$">
  Require all denied
</FilesMatch>
```

- [ ] **Step 2: robots.txt** at `public/robots.txt`

```
User-agent: *
Allow: /
Disallow: /api/
Sitemap: https://zycus.example.com/sitemap.xml
```

- [ ] **Step 3: Verify** — locally with Apache (XAMPP), `curl -I http://localhost/` should show `Content-Encoding: gzip` for HTML and the security headers.

---

## Phase 7 — Deploy to Free PHP Host

### Task 13: Deploy to InfinityFree

- [ ] **Step 1: Sign up at InfinityFree.com.** Create a new website. Note the FTP credentials (host, username, password) and MySQL credentials.

- [ ] **Step 2: Create MySQL database** in the InfinityFree control panel. Note db name, user, password.

- [ ] **Step 3: Import schema:** open phpMyAdmin → select the new DB → Import → upload `config/schema.sql`.

- [ ] **Step 4: Fill `.env`** with InfinityFree's MySQL creds, GTM ID from your container, GA4 measurement ID, and SMTP creds (use Brevo's free 300 emails/day tier — sign up at brevo.com).

- [ ] **Step 5: Upload via FTP.** Using FileZilla connect to InfinityFree's FTP. Upload:
  - `public/*` → `htdocs/` (web root)
  - `templates/` → `/templates/` (one level above htdocs)
  - `src/` → `/src/`
  - `config/` → `/config/`
  - `vendor/` → `/vendor/`
  - `storage/` → `/storage/` (chmod 755 for dir, 644 for files)
  - `.env` → `/.env`

- [ ] **Step 6: Update bootstrap paths.** InfinityFree's directory structure may put `htdocs` differently; verify the `__DIR__ . '/../...'` paths in `public/index.php` resolve correctly. If not, adjust to absolute paths via `dirname(__DIR__, 2)`.

- [ ] **Step 7: Test live.** Visit your `<subdomain>.infinityfreeapp.com`. Submit a real form. Verify: thank-you redirect, email arrives, row in `submissions` table.

---

## Phase 8 — Verification

### Task 14: End-to-end checklist

- [ ] **Step 1: Functional**
  - Homepage loads, all 6 sections visible.
  - All animations fire (hero choreography, scroll reveals, counter, FAQ height, marquee, sticky CTA).
  - Reduced-motion: animations off.
  - Form: invalid submission → inline errors. Valid submission → row in DB + email in inbox + correct redirect by company size.
  - CSRF: tampered token → 403.
  - Honeypot: fill `website_url` → 422.
  - Rate limit: 11 submissions in an hour from same IP → 429.

- [ ] **Step 2: Performance**
  - PageSpeed Insights: Mobile ≥ 85, LCP < 2.5s, CLS < 0.1, INP < 200ms.
  - Network panel: total page weight < 400KB, gzip working.

- [ ] **Step 3: Tracking**
  - GTM Preview Mode shows `gtm.js` loading and `generate_lead` firing on form success.
  - GA4 Realtime shows `generate_lead` event within 30s.
  - Thank-you-page pageview also fires `generate_lead` (via the GTM container's pageview trigger from `D:\Desktop\Zycus\build-artifacts\gtm\GTM-zycus-landing.json`).

- [ ] **Step 4: Security**
  - `.env` not accessible via browser (returns 403).
  - `/api/submit.php` GET returns 405.
  - All input echoed back is `htmlspecialchars`-escaped (no XSS).
  - No PHP errors visible to users (`APP_DEBUG=false` hides them).

- [ ] **Step 5: Accessibility**
  - Keyboard-only navigation: can tab through nav → CTA → form fields → submit.
  - Form errors are announced (`aria-describedby` is set on invalid fields).
  - Screen reader: section landmarks present.

If all 5 pass: production-ready.

---

## Appendix A — Animation Hooks Reference

When copying section markup from the prototype into PHP partials, add these `data-*` attributes to enable animations:

| Element                                            | Attribute                                                                                                       |
| -------------------------------------------------- | --------------------------------------------------------------------------------------------------------------- |
| `.hero__copy`                                      | `data-stagger="120"`                                                                                            |
| Hero eyebrow, H1, lead, actions, trust-row, visual | `data-reveal="up"` (visual: `data-reveal="scale"`)                                                              |
| Each `.bar` in dashboard mock                      | `style="--i:N"` (N = 0..6)                                                                                      |
| Each `.stat__value`                                | `data-counter="<num>" data-prefix="$" data-suffix="B" data-decimals="1"` (per stat)                             |
| Each section's `.section__head`                    | `data-reveal="up"`                                                                                              |
| `.steps`, `.testimonials`, `.faq`                  | `data-stagger="100"` (steps), `"150"` (testimonials), `"80"` (faq)                                              |
| Each `.step-card`, `.testimonial`, `.faq__item`    | `data-reveal="up"`                                                                                              |
| `.faq__icon` inner content                         | `<span></span><span></span>` (two empty spans for plus/minus)                                                   |
| `.logos__viewport`                                 | `data-marquee`                                                                                                  |
| `.logos__track`                                    | duplicate the 6 `<li>` once for seamless loop                                                                   |
| `.demo-card`                                       | `data-reveal="scale"`                                                                                           |
| Body, before `<header>`                            | `<div class="scroll-sentinel" aria-hidden="true" style="position:absolute;top:0;height:80px;width:1px;"></div>` |

## Appendix B — animations.css and motion.js source

Refer to the previous animation plan iteration's Tasks 1–12 for the complete content of `animations.css` (design tokens + reveal utilities + keyframes + reduced-motion override) and `motion.js` (IntersectionObserver controller + counter + FAQ smooth-height + sticky-header observer + mobile-CTA observer). Paste those verbatim into `public/assets/css/animations.css` and `public/assets/js/motion.js`.

## Appendix C — Out of Scope (defer to v2)

- Multi-language support (i18n)
- Admin dashboard for viewing submissions (use phpMyAdmin instead)
- A/B testing infrastructure
- CDN integration (Cloudflare free tier is a quick add later)
- Email queueing (PHPMailer fires synchronously; fine for low volume)
- File uploads in form
- OAuth or SSO

---

# 2026-04-15 Addendum — Brand Refresh, Thank-You Fix, A11y/SEO Pass

This addendum supersedes any earlier conflicting palette or animation guidance for the form and thank-you flow. The rest of the document (architecture, DB, mailer, rate-limiting) is unchanged.

## 1. Official Zycus Color Palette (2025/2026 refresh)

| Token | Hex | Use |
| --- | --- | --- |
| **Torch Red** | `#FF1446` | Primary CTAs only (Book My Demo, form submit), required-field asterisk, focus ring |
| **Dodger Blue** | `#40A4FB` | Secondary links, off-ramp CTA on thank-you page, illustrative accents |
| **Torea Bay** | `#0F3D81` | Primary text color, footer background, dark sections, brand depth |

Applied in `public/assets/css/styles.css` via `--torch-red`, `--dodger-blue`, `--torea-bay`. Legacy `--navy` / `--teal` / `--accent` aliases are remapped so existing rules pick up the refresh automatically. Background text contrast on white passes WCAG AA (4.5:1): Torea Bay on white ≈ 11.5:1.

## 2. Thank-You Page Fix (root cause of "Thank you page is not came")

The form POST succeeded but the redirect target `/thank-you/` (trailing slash) only resolves when Apache mod_rewrite executes the `.htaccess` rule. On the PHP built-in dev server (`php -S`) and on hosts without mod_rewrite, that URL 404s.

**Fix applied:**
1. `public/api/submit.php` — redirect target changed to `/thank-you.php?form=zycus_demo[&tier=small]`. Works everywhere; no rewrite required.
2. `public/.htaccess` — rewrite `^thank-you/?$` -> `thank-you.php [L,QSA]` still kept so pretty URLs work in production. `QSA` flag preserves query string so `?form=…` still reaches the page.
3. `public/thank-you.php` — rebuilt per Skeleton View 2: confirmation H1, subheadline, off-ramp CTA (Dodger Blue secondary button), schema-pushable `dataLayer` conversion event `demo_confirmed`.

## 3. Static Form Styles (No Entrance Animations)

- Removed `data-reveal="scale"` from `.demo-card` — form no longer animates in.
- `@media (prefers-reduced-motion: reduce)` now globally disables animation/transition in `styles.css` for users who opt out.
- Step-nav (Back / Next) buttons given static, keyboard-accessible styling with 3px Torch Red focus ring on `:focus-visible`.
- Hover uses only a 1px translate + box-shadow; no bounce, scale, or spring.

## 4. Brain Logo Implementation

- Source file: `D:\Desktop\Zycus\zycus-new-logo.webp` -> copied to `public/assets/img/zycus-logo.webp`.
- `templates/header.php`: `<img src="/assets/img/zycus-logo.webp" alt="Zycus AI Procurement Brain Logo" class="brand__logo" width="160" height="44" decoding="async" fetchpriority="high">` — preloaded via `<link rel="preload" as="image">` in `meta.php` for LCP.
- `templates/footer.php`: same image, `loading="lazy"`, white-rendered via `filter:brightness(0) invert(1)` on the dark footer.
- Favicon + Apple touch icon point to the same WebP.
- OpenGraph and Twitter meta reference it as the share image with the exact alt `"Zycus AI Procurement Brain Logo"`.

## 5. Accessibility + Keyboard Controls

| Area | Change |
| --- | --- |
| Skip link | `<a class="skip-link" href="#main-content">` — first focusable element, visible on focus |
| Landmark | `<main id="main-content" role="main">`, `<header role="banner">`, `<footer role="contentinfo">` |
| Focus ring | Global `:focus-visible { outline: 3px solid #FF1446; outline-offset: 3px; }` — applies to links, inputs, buttons, step-nav |
| Form labels | Visible labels above each field (no placeholder-only reliance); required marked with `*` + `aria-hidden` decoration |
| Live region | Hidden `role="status" aria-live="polite"` region announces validation errors and network failures to screen readers |
| Invalid state | `aria-invalid="true"` + `aria-describedby` wired to inline `.form-error` message; first invalid field auto-focused |
| Reduced motion | Global `prefers-reduced-motion` media query kills all animation durations |
| Semantic headings | Exactly one `<h1>` per page, `<h2>` for section headings, `<h3>` for step/question titles — no skipped levels |

## 6. SEO + Performance

- Added `meta[name=keywords]`, `meta[name=theme-color]`, `meta[name=robots]` with `max-image-preview:large`.
- OpenGraph + Twitter cards fully populated with image, alt, URL, site_name.
- JSON-LD Organization schema now points `logo` at the WebP path.
- FAQ JSON-LD unchanged (already implemented).
- Preload hint for the hero-critical brain logo (LCP candidate).
- `.htaccess` keeps gzip + 1-year cache for WebP/CSS/JS, never-cache for HTML.

## 7. Skeleton View 1 — Main Landing Page (current file mapping)

| Skeleton item | File / selector |
| --- | --- |
| Header (logo only, minimal nav) | `templates/header.php` — `.brand` + `.site-nav` |
| Hero (2-col, H1 + lead + CTA + trust list) | `templates/sections/hero.php` |
| Trust ribbon / logo grid | `templates/sections/logos.php` |
| How It Works (H2 + 4 H3 steps) | `templates/sections/how-it-works.php` |
| Testimonials (H2 + 2 cards) | `templates/sections/testimonials.php` |
| FAQ (H2 + H3 `<details>`) | `templates/sections/faq.php` |
| Lead form (3 steps, visible labels, Torch Red CTA) | `templates/sections/form.php` |
| Bottom close / off-ramp | rolled into form section; duplicate-CTA not needed |
| Footer (brand + privacy/terms) | `templates/footer.php` |

## 8. Skeleton View 2 — Thank You Page

Implemented in `public/thank-you.php`:

1. Header with brain logo (same component).
2. `.thank-you__card` — centered success container:
   - Checkmark SVG badge (Dodger Blue tint).
   - `<h1>Demo Request Confirmed!</h1>`
   - Sub-headline: 24-hour response commitment.
   - `<h2>While you wait</h2>` section.
   - Off-ramp: `.btn--secondary` (Dodger Blue outline) "Download the S2P Diagnostic Guide" + underlined text link "Explore more resources →".
3. Footer (shared).
4. GTM conversion push: `dataLayer.push({ event: 'demo_confirmed', form_id, tier })`.

## 9. Verification Checklist (run before claiming done)

- [ ] Load `/` — logo renders, no 404s for `zycus-logo.webp`, Inter font loads, dashboard mock animates only on first reveal.
- [ ] Tab through the page — focus ring (Torch Red) visible on every interactive element including the skip link.
- [ ] Submit the form with empty fields — inline `.form-error` appears, `aria-live` announces, first invalid field receives focus.
- [ ] Submit a valid form with `company_size=small` — browser navigates to `/thank-you.php?form=zycus_demo&tier=small`, page renders with H1 and off-ramp CTA.
- [ ] Submit with `company_size=enterprise` — redirects to Calendly URL from `APP_CALENDLY_URL`.
- [ ] Lighthouse desktop run: Performance ≥ 90, Accessibility ≥ 95, Best Practices ≥ 95, SEO = 100.
- [ ] `prefers-reduced-motion: reduce` (via OS or DevTools emulation) — no reveal animations trigger; content is visible immediately.
- [ ] Semantic audit: exactly one `<h1>` per page; `landmarks` (header/main/footer) present; alt text on logo matches `"Zycus AI Procurement Brain Logo"` exactly.

---

# 2026-04-15 Addendum II — B2B Design Rules Contract (Carries to WordPress port)

These rules apply to the PHP reference build and are the binding spec for the upcoming WordPress/Elementor rebuild. Any design choice that deviates must be explicitly justified.

## Audience = Enterprise B2B

Zycus is high-ticket, trust-driven enterprise software. The design must read as **premium, calm, sophisticated** — not as a consumer app. No neon, no vibrant gradients, no playful animations.

## Palette and where each color is allowed

| Color | Hex | Allowed use | Disallowed use |
|---|---|---|---|
| **Torea Bay** | `#0F3D81` | Primary body text, deep backgrounds (hero mesh base, testimonials, form section, footer), brand mark | Never as a CTA button fill |
| **Dodger Blue** | `#40A4FB` | Secondary accents, illustrative graphics, mesh-gradient highlights, off-ramp text links on dark backgrounds | Never as body text on white (fails WCAG AA ~2.8:1) |
| **Torch Red** | `#FF1446` | **Exclusively** primary CTA buttons + required-field markers + focus ring | Never as a decorative accent or section fill |

Enforced in `public/assets/css/styles.css` via the `--torea-bay` / `--dodger-blue` / `--torch-red` tokens. The legacy `--navy`/`--teal`/`--accent` aliases are remapped to these three.

## Gradients (subtle only)

- **Hero**: animated mesh gradient of Torea Bay + Dodger Blue radial blobs, 24s ease-in-out drift, disabled under `prefers-reduced-motion`. Implemented in `.hero` + `.hero::before` + `@keyframes meshDrift`.
- **Eyebrow text**: linear gradient across Torea Bay → Dodger Blue → Torch Red via `background-clip: text` with solid-color `@supports` fallback.
- **Section dividers**: soft 48px linear gradient using `.section--wave::after` to transition between light and dark blocks.
- Never apply gradients to CTA buttons — they must remain solid Torch Red.
- Never apply gradients to body text or headline text on content-heavy sections.

## Backgrounds — never pure white

- Global body background is `#FAFBFD` (near-white muted pastel) — not `#FFFFFF`.
- `.section--light` = `#F4F7FB` (softer pastel).
- `.section--dark` = Torea Bay with two subtle radial overlays (Dodger Blue top-left, Torch Red bottom-right at 8% opacity).
- Deep Torea Bay blocks are mandatory for: testimonials, form, footer. Optional for: a dedicated "Security & Trust" block.

## Measurements (WCAG + Core Web Vitals)

| Metric | Target | Enforced at |
|---|---|---|
| Body text contrast | ≥ 4.5:1 | Torea Bay `#0F3D81` on `#FAFBFD` ≈ 11.2:1 ✓ |
| Large text contrast | ≥ 3:1 | All H1/H2 use Torea Bay on pastel — pass |
| Minimum body font size | 16px | `body { font-size: 16px; }` — bumped to 17px ≥ 768px |
| Unitless line-height | ~1.6 | `body { line-height: 1.6; }` |
| H1 length | ≤ 10 words / ≤ 44 chars | "Cut Procurement Costs by 40% with Agentic AI" = 7 words / 44 chars ✓ |
| Touch target minimum | 44×44 px (48×48 ideal) | `.btn { min-height: 48px; }` and 44px minimum applied to nav links, FAQ summaries, step-nav buttons |
| LCP | < 2.5s | Brain logo preloaded with `fetchpriority="high"`; hero uses CSS-only mesh (no images) |
| CLS | < 0.1 | All images have explicit `width`/`height`; fonts use `font-display: swap` (Google Fonts default) |
| INP | < 200ms | No third-party JS beyond GTM; form validation is synchronous and cheap |

## Form qualification tier (Enterprise B2B)

Zycus targets mid-market through Fortune 500 procurement. Use the **high-qualification** pattern:

- 6–8 fields across 3 progressive-disclosure steps (current: email, first name, last name, company name, company size, role, use case, notes = 8 fields / 3 steps ✓)
- Company size drives lead routing: `enterprise`/`large_enterprise` → Calendly; `small`/`mid` → thank-you nurture page
- Required-field marker `*` rendered in Torch Red
- CTA label must be outcome-oriented: "Book My Demo" (never "Submit" / "Send")
- Microcopy under CTA: "Secure & confidential" + lock glyph — already present

## WordPress port contract (when we rebuild in Elementor/WP)

Every rule above carries over verbatim. The WordPress build MUST:

1. Use Elementor Site Settings → Global Colors set to the three hex codes above, named `Torea Bay`, `Dodger Blue`, `Torch Red`.
2. Use Global Typography: Inter font family, 16px base, 1.6 line-height, H1 48px / H2 36px / H3 24px (desktop).
3. Import the brain logo to Media Library with Alt Text = `"Zycus AI Procurement Brain Logo"` and set as Site Logo.
4. Build the hero as a Flexbox Container with a Motion Effect "Mouse Track = None" and a CSS `::before` mesh gradient via Custom CSS — **no entrance animations on form**.
5. Use MetForm (config in `build-artifacts/metform/`) with visible labels above every field, required-asterisk in Torch Red, Torch Red submit button.
6. Set "Actions After Submit" → Redirect → thank-you page URL (create the page first using the copy from `php-app/public/thank-you.php`).
7. Paste GTM snippet `GTM-KG8889HK` via WPCode in the Header position; paste the `<noscript>` iframe via WPCode in the Body (after-opening) position.
8. Enable "Improved Asset Loading" and "Optimized DOM Output" in Elementor → Features.
9. Run Lighthouse after publish — if Performance < 90 or Accessibility < 95, do not ship.

## System copy — exact strings (PHP + Elementor parity)

These strings are the binding contract. Implemented in PHP: `public/thank-you.php` (headline + body), `public/api/submit.php` (503 DB-error payload), `public/assets/js/form.js` (modal render), `public/assets/css/styles.css` (`.apology-modal__*`). Copy them into MetForm → *Additional Options → Custom Messages → Success Message / Server Error* when porting to WordPress.

### Thank-You success — personalized (PHP default + Elementor Success Message)

- **Headline:** `Demo Request Confirmed!`
- **Body (personalized):** `Hi {first_name}, thank you for requesting a demo. We have successfully received your details via {email}. A Zycus Agentic AI specialist will review your request and reach out within 24 hours to schedule your personalized walkthrough. While you wait, keep an eye on your inbox for our latest S2P Diagnostic Guide.`
- **Body (fallback, no session data):** `Thank you for requesting a demo. A Zycus Agentic AI specialist will review your request and reach out within 24 hours to schedule your personalized walkthrough. While you wait, keep an eye on your inbox for our latest S2P Diagnostic Guide.`

**PHP implementation:** `public/api/submit.php` stashes `first_name` + `email` in `$_SESSION['zycus_lead']` after successful `Submission::store()`. `public/thank-you.php` reads and `htmlspecialchars`-escapes them, then clears the session value so a direct revisit falls back to the generic copy. The `demo_confirmed` dataLayer event carries a `personalized: true|false` flag for GA4 attribution.

**Elementor implementation:** In *MetForm → Additional Options → Custom Messages → Success Message*, paste the personalized body and replace `{first_name}` / `{email}` with the exact shortcodes shown in each field's *Advanced → Shortcode* box (typically `[field id="first_name"]` and `[field id="email"]`). Elementor's form widget resolves these server-side per submission.

### Apology / database error — personalized (Elementor Server Error)

- **Headline:** `Connection Interrupted`
- **Body (personalized):** `Hi {first_name}, we sincerely apologize, but we are experiencing a temporary server issue and couldn't process your request for {email}. Please wait a few moments and try submitting again. If the issue persists, you can bypass this form and email our team directly at sales@zycus.com to schedule your demo.`
- **Body (fallback, empty fields):** same sentence without the "Hi {name}," prefix and the "for {email}" clause — `buildDbErrorBody()` in `form.js` strips both when the values are empty.

**PHP implementation:** `form.js` reads the live `first_name` and `email` values from the form at the moment of failure (no server round-trip needed — the data is already in the DOM). `showApologyModal("Connection Interrupted", buildDbErrorBody())` is called on both 503 responses and network-level `fetch` failures, so the user sees the same consistent apology regardless of whether the PHP process responded.

**Elementor implementation:** Paste the apology body into the *Server Error* box with Elementor's `[field id="…"]` shortcodes. Elementor only fires Server Error when its own DB insert fails (e.g. `wp_e_submissions` write error) — which matches our `PDOException` catch semantics.

### Error taxonomy (server response shape)

| HTTP status | `errorType` | Triggered by | Frontend treatment |
|---|---|---|---|
| 200 | — (success) | `Submission::store()` + Mailer succeed | `location.assign(data.redirect)` |
| 422 | — (`errors` map) | `Validator::validate()` finds bad fields | inline per-field `.form-error`, aria-invalid, auto-focus first invalid |
| 403 | — | CSRF mismatch | generic apology modal ("Submission failed") |
| 429 | — | `RateLimiter::allow()` returns false | apology modal with retry guidance |
| 503 | `database` | `PDOException` thrown from `Submission::store()` | **DB apology modal** using the exact headline/body above |
| 500 | `generic` | Any other `\Throwable` | generic apology modal |

### Elementor / WPCode implementation cue for the apology modal

The PHP build renders `<div id="zycus-apology-modal" class="apology-modal" role="alertdialog">` with focus-trapping on close button and Escape. In Elementor:

1. In *MetForm → Additional Options → Custom Messages* paste the exact headline/body into the Server Error box with shortcodes for personalization.
2. Wrap the body copy in `<div id="zycus-apology-modal">…</div>` so custom CSS can upgrade it to the floating modal style.
3. Copy the `.apology-modal__*` rules from `public/assets/css/styles.css` into *Site Settings → Custom CSS* verbatim.
4. Add a "Try Again" button (re-submit) and "Email Sales Instead" button (`mailto:sales@zycus.com?subject=Zycus%20Demo%20Request`) inside the same wrapper.

## Form dropdown polish (PHP reference + MetForm port)

Dropdowns were upgraded to enterprise-B2B qualification quality. All three selects now use `<optgroup>` semantic grouping, descriptive option labels, helper hints below each select, and caret SVGs that tint Dodger Blue on hover + Torch Red on focus.

| Dropdown | Groups | Example options | Hint text |
|---|---|---|---|
| **Company Size** | `SMB` / `Enterprise` | `1–49 employees — Startup / SMB`, `5,000+ employees — Large Enterprise / Fortune 500` | *Helps us route you to the right solution architect.* |
| **Your Role** | `Decision Makers` / `Practitioners` + `Other` | `Procurement Leader (CPO / VP / Director)`, `IT / Systems Owner` | *Tailors the demo to your day-to-day priorities.* |
| **Primary Use Case** | `Upstream` / `Downstream` / `Full Platform` | `Sourcing & Contract Management`, `Invoice Automation & AP`, `End-to-End Source-to-Pay` | *We'll configure the demo around this workflow first.* |

CSS contract for the new form controls (all in `styles.css`):

- `.form-field input/select/textarea { min-height: 48px; border-radius: 10px; border-width: 2px; }` — meets 48px ideal touch target.
- `.form-field select` uses an inline-SVG caret at 16×16px positioned at `right 18px center`. Three state-based SVG variants swap the stroke color: Torea Bay (default), Dodger Blue (`:hover`), Torch Red (`:focus-visible`).
- `optgroup` labels render bold, Torea Bay, with a `var(--bg-light)` background to differentiate from options.
- `.form-hint` is 13px Muted under each dropdown, light enough to not compete with the label but readable (passes 4.5:1 on white).
- Select placeholder (`option[value=""]`) renders in `var(--muted)` so the unselected state reads as a prompt, and the select itself is `:invalid` until a real value is chosen.

**MetForm port:** set each select to *Required*, use MetForm's *Option Group* feature (or raw HTML in a "Custom Options" text box) to preserve the optgroup structure, and add a *Help Text* field matching the hint copy. Apply the same caret SVG via the *Style → Advanced CSS* tab.
