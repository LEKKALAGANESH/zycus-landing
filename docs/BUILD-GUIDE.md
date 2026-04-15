# Zycus Landing Page — PHP Build Guide

Step-by-step guide to get the vanilla PHP build running locally and deployed live. Everything PHP-related lives in `D:\Desktop\Zycus\php-app\`. Work through this top-to-bottom. Paste a screenshot here when you hit a blocker.

---

## STAGE A — Confirm the project layout (2 minutes)

**Step A1.** Open `D:\Desktop\Zycus\php-app\` in your file explorer. You should see this structure:

```
php-app/
├── public/        ← web root (entry points + assets)
│   ├── index.php
│   ├── thank-you.php
│   ├── api/submit.php
│   ├── assets/css/styles.css, animations.css
│   ├── assets/js/motion.js, form.js
│   └── .htaccess
├── templates/     ← PHP partials (header, footer, sections)
├── src/           ← application classes (Config, Validator, Mailer, …)
├── config/        ← schema.sql + faqs.php
├── storage/       ← writable runtime dir
├── composer.json
├── .env.example
└── README.md
```

✅ _Confirm:_ You see all 7 top-level items above. If anything is missing, stop here and re-run the build agents — don't proceed.

**Step A2.** You'll see the finished landing page (hero + KPI counters, 4 step cards, logo strip, 2 testimonials, FAQ accordion, navy form section, sticky mobile bar) once you reach Stage F and visit `localhost:8000`. There's nothing to look at yet — proceed to Stage B.

---

## STAGE B — Install local environment (15 minutes)

You need **PHP 8.2+**, **Composer**, and **MySQL 5.7+**. Easiest one-shot install is XAMPP.

**Step B1.** Download XAMPP for Windows from [apachefriends.org](https://www.apachefriends.org/) — pick the version with PHP 8.2+. Install with default options. Skip the Bitnami panel.

**Step B2.** Open XAMPP Control Panel → click **Start** next to **Apache** and **MySQL**. Both should turn green.

**Step B3.** Install Composer from [getcomposer.org/download/](https://getcomposer.org/download/) — use the Windows installer (`Composer-Setup.exe`). When prompted for PHP location, point it at `C:\xampp\php\php.exe`.

**Step B4.** Open a NEW PowerShell or Command Prompt window. Run these and confirm versions print:

```
php -v
composer -V
mysql --version
```

✅ _Confirm:_ PHP shows `8.2.x` or higher; Composer shows `2.x`; MySQL shows `8.0.x` or `5.7.x` or MariaDB equivalent.

---

## STAGE C — Install Composer dependencies (2 minutes)

**Step C1.** In your shell:

```
cd D:\Desktop\Zycus\php-app
composer install
```

✅ _Confirm:_ Output ends with `Generating autoload files`. A new `vendor/` folder appears inside `php-app/` containing `phpmailer/` and `vlucas/`.

---

## STAGE D — Create database + import schema (5 minutes)

**Step D1.** Open `http://localhost/phpmyadmin/` in your browser (XAMPP serves this). Log in with user `root`, no password (default for XAMPP).

**Step D2.** Click **New** in the left sidebar → enter database name `zycus_landing` → Collation `utf8mb4_unicode_ci` → click **Create**.

**Step D3.** With `zycus_landing` selected, click the **Import** tab → **Choose File** → select `D:\Desktop\Zycus\php-app\config\schema.sql` → click **Import** at the bottom.

✅ _Confirm:_ Click the `zycus_landing` database in the sidebar — you should see two tables: `submissions` and `rate_limit`.

---

## STAGE E — Configure environment variables (10 minutes)

**Step E1.** Copy `.env.example` to `.env`:

```
cd D:\Desktop\Zycus\php-app
copy .env.example .env
```

**Step E2.** Open `D:\Desktop\Zycus\php-app\.env` in your editor. Fill in:

```env
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_HOST=127.0.0.1
DB_NAME=zycus_landing
DB_USER=root
DB_PASS=

# Brevo SMTP (free 300/day) — sign up at brevo.com → SMTP & API → SMTP keys
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USER=YOUR_BREVO_LOGIN_EMAIL
MAIL_PASS=YOUR_BREVO_SMTP_KEY
MAIL_FROM_EMAIL=noreply@yourdomain.com
MAIL_FROM_NAME=Zycus Landing
MAIL_TO_EMAIL=your_personal_email@gmail.com

# Will fill these in Stage H
GTM_CONTAINER_ID=GTM-KG8889HK
GA4_MEASUREMENT_ID=G-1MG1YKNRDF
APP_CALENDLY_URL=https://calendly.com/your-handle/demo
```

**Step E3 — Brevo SMTP setup** (if you don't have an SMTP provider):

- Sign up at [brevo.com](https://www.brevo.com/) → free tier, no credit card.
- Go to **SMTP & API → SMTP** → click **Generate a new SMTP key**.
- Copy the key into `MAIL_PASS`. Use your Brevo login email for `MAIL_USER`.
- Verify your sender domain or use Brevo's sandbox sender for testing.

✅ _Confirm:_ `.env` file saved. Don't commit it (it's in `.gitignore`).

---

## STAGE F — Run dev server + smoke test (5 minutes)

**Step F1.** From the same shell:

```
cd D:\Desktop\Zycus\php-app
php -S localhost:8000 -t public
```

The shell will hang (this is correct — it's serving). Leave it running.

**Step F2.** Open [http://localhost:8000/](http://localhost:8000/) in a browser.

✅ _Confirm:_ The full landing page loads — hero, 4 step cards, logos, testimonials, FAQ, demo form, footer. Looks like the prototype but served by PHP.

**Step F3 — Animations:**

- On page load: hero fades up in sequence (eyebrow → H1 → lead → CTA → trust → visual).
- Bars in the dashboard mock animate from 0 height (~600ms after load).
- KPI counters animate ($0.0B → $4.2B, 0% → −62%, 0% → 87%).
- Scroll past hero: the site header gets a glassmorphic background.
- Scroll to How It Works: 4 cards stagger in. Hover one → lifts.
- Logos marquee continuously. Hover → pauses.
- Click a FAQ item: smoothly opens, plus icon rotates to minus.

If any of these don't fire: open DevTools → Console — look for JS errors in `motion.js`.

---

## STAGE G — Test the form (10 minutes)

**Step G1.** Scroll to the demo form. Fill in:

- Step 1: Email = `you@yourcompany.com` (NOT gmail — free emails are rejected); first/last name.
- Step 2: Company name; size = `50-499 employees`; role.
- Step 3: Use case; notes (optional).

Click **See the Platform in Action**.

✅ _Confirm:_

- Browser redirects to `/thank-you/?form=zycus_demo`.
- The thank-you page loads.
- Open phpMyAdmin → `zycus_landing` → `submissions` → click **Browse** — your row is there.
- Check your `MAIL_TO_EMAIL` inbox — notification email arrived.

**Step G2 — Test conditional redirect for enterprise:**

- Submit again with company size = `5,000+ employees`.
- Should redirect to your Calendly URL (or the placeholder if you didn't set `APP_CALENDLY_URL`).

**Step G3 — Test validation:**

- Submit with email `test@gmail.com` → should show "Please use your work email" error inline.
- Open DevTools → Network → submit a valid form → confirm the request to `/api/submit.php` returns `200` with `{"ok":true,"redirect":"...","id":N}`.

**Step G4 — Test rate limit:** submit 11 valid forms in a row from the same browser. The 11th should return `429 Too many requests`.

---

## STAGE H — Set up GA4 + GTM (20 minutes)

> **Active container for this build:** `GTM-KG8889HK` (already wired into `.env` at Stage E2). If you want to use your own container, replace every `GTM-KG8889HK` below with your new ID in both `.env` and the two snippets.

**Step H1.** Active GA4 property for this build has **Measurement ID `G-1MG1YKNRDF`** — already wired into `.env` at Stage E2. If you want your own, go to [analytics.google.com](https://analytics.google.com) → Admin → **Create Property** named `Zycus Landing` → set up a **Web data stream** for `http://localhost:8000` (placeholder; update later for production) and copy its Measurement ID.

**Step H2.** Confirm `.env` contains `GA4_MEASUREMENT_ID=G-1MG1YKNRDF` (or your own ID).

**Step H3.** Google Tag Manager container `GTM-KG8889HK` is already provisioned for this project. If you need to create your own, go to [tagmanager.google.com](https://tagmanager.google.com) → **Create Account** named `Zycus`, container `zycus-landing`, target = Web. Copy the **Container ID**.

**Step H4.** Confirm `.env` contains `GTM_CONTAINER_ID=GTM-KG8889HK` (or your own ID). Restart the dev server (Ctrl+C and re-run `php -S localhost:8000 -t public`) so the new env loads.

**Step H4a — Verify the injected snippets.**

The PHP build reads `GTM_CONTAINER_ID` from `.env` and injects the GTM tags at runtime — you do **not** need to hand-paste them. For reference, here is exactly what gets rendered on every page (confirm with DevTools → Elements after the restart):

Inside `<head>` (rendered by `templates/header.php` line 6):

```html
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-KG8889HK');</script>
<!-- End Google Tag Manager -->
```

Immediately after `<body>` opens (rendered by `templates/header.php` line 9):

```html
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KG8889HK"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
```

If DevTools shows `GTM-XXXXXXX` instead of `GTM-KG8889HK`, your `.env` was not reloaded — stop and restart `php -S` before continuing.

**Step H5 — Import the GTM container:**

- In GTM: **Admin → Import Container** → upload `D:\Desktop\Zycus\build-artifacts\gtm\GTM-zycus-landing.json` → choose **Default workspace** → **Merge → Overwrite conflicting** → Confirm.
- _If import errors:_ open the JSON file in a text editor, find the `configSettingsTable` block in the GA4 Config tag, delete it, save, re-import.
- In GTM, find the **Constant** variable holding `G-XXXXXXXXXX` and edit it → set value to **`G-1MG1YKNRDF`** → Save.
- Click **Submit** (top-right) → **Publish** → name version `v1 initial setup`.

**Step H6 — Verify tracking:**

- In GTM, click **Preview** → enter `http://localhost:8000` → opens a new browser window with the GTM debugger panel attached. Confirm the connected container reads **`GTM-KG8889HK`**.
- Submit a test form. In the debugger, confirm `generate_lead` event fires (pushed by `public/assets/js/form.js`).
- Navigate to the thank-you page — confirm `demo_confirmed` event fires (pushed by `public/thank-you.php`).
- Open GA4 → **Reports → Realtime** → confirm both events appear within ~30 seconds.

---

## STAGE I — Performance + accessibility audit (15 minutes)

**Step I1.** Open the site in Chrome → DevTools → **Lighthouse** tab → run with categories Performance + Accessibility + SEO + Best Practices, Mobile mode.

✅ _Confirm:_ Performance ≥ 85; LCP < 2.5s; CLS < 0.1; INP < 200ms; Accessibility ≥ 95.

**Step I2.** If Performance < 85:

- "Properly size images" → swap the placeholder hero image for a properly-sized WebP. Add it as `php-app/public/assets/img/hero.webp` and update the hero section's `<img>` to use it.
- "Reduce unused CSS" → confirm `mod_deflate` is on (XAMPP has it by default). Look for the `Content-Encoding: gzip` response header.
- "Eliminate render-blocking resources" → fonts are already preconnected. CSS is small enough to be fine.
- Hero LCP: confirm the hero image element has the `no-lazy` class so it doesn't lazy-load.

**Step I3.** Test reduced-motion: DevTools → **Rendering** tab → "Emulate CSS media feature prefers-reduced-motion" → set to `reduce`. Reload the page → all animations should snap to final state instantly. The form still works.

**Step I4.** Test mobile: DevTools → device toolbar (Ctrl+Shift+M) → choose iPhone 12 Pro → reload. Confirm the sticky CTA bar appears at the bottom; it slides away when the demo form is in view.

---

## STAGE J — Cross-browser sanity check (5 minutes)

**Step J1.** Open the site in:

- Chrome (already done)
- Firefox
- Edge
- Safari (if you have a Mac available)

For each, check: hero animates, form submits, FAQ expands smoothly, sticky CTA appears on phone-width.

`backdrop-filter` (used on the sticky header glass) degrades gracefully in older Firefox to a solid-white background. No fix needed.

---

## STAGE K — Deploy to InfinityFree (free PHP host) (30 minutes)

**Step K1.** ✅ **Done** — account `if0_41671451` provisioned for **`zycus-demo.infinityfreeapp.com`**. InfinityFree's setup takes ~5 minutes before the domain resolves; while you wait, continue with K2.

> **Naming gotcha (for reference):** the Subdomain field accepts only the label, not the full hostname. Valid: `zycus-demo`. Invalid: `zycus-demo.infinityfreeapp.com` (trailing dot), leading/trailing dash, uppercase, or underscores.

**Step K2.** In the InfinityFree control panel, click **MySQL Databases** → **Create Database** → name it (e.g. `zycus_landing`) → note the auto-generated DB user, password, and host.

**Step K3.** Click **phpMyAdmin** for that database → **Import** tab → upload `php-app/config/schema.sql` → **Go**.

**Step K4.** Open `php-app/.env` and create a production version with the InfinityFree credentials:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://zycus-demo.infinityfreeapp.com`
- `DB_HOST=sql100.byetcluster.com` (this account's assigned cluster)
- `DB_NAME=if0_41671451_zycus_landing`
- `DB_USER=if0_41671451`
- `DB_PASS=` the password InfinityFree showed when the DB was created (Control Panel → **MySQL Databases** → click the database if you need to reveal it)
- Keep your real `MAIL_*`, `GTM_CONTAINER_ID`, `GA4_MEASUREMENT_ID`, `APP_CALENDLY_URL`.

**Step K5.** In the InfinityFree control panel, click **FTP Accounts** → note the FTP host, username, password.

**Step K6.** Install [FileZilla Client](https://filezilla-project.org/) → File → Site Manager → New Site → enter InfinityFree's FTP host/user/pass → Connect.

**Step K7.** On FileZilla, after connecting you'll land in the account root `/`. You'll see a `htdocs/` folder (the web root) plus a few system files. InfinityFree lets you create sibling folders at `/`, and `open_basedir` is wide enough to let PHP read them. Upload structure:

| Local (laptop) | Remote (FTP) |
| --- | --- |
| Contents of `php-app/public/` (index.php, thank-you.php, privacy.php, terms.php, api/, assets/, .htaccess, robots.txt) | **inside** `htdocs/` — upload the FILES, not the folder itself |
| `php-app/src/` | `/src/` (sibling of htdocs) |
| `php-app/templates/` | `/templates/` |
| `php-app/config/` | `/config/` |
| `php-app/vendor/` | `/vendor/` |
| `php-app/storage/` | `/storage/` |
| `php-app/.env` | `/.env` (the file, at the account root — NOT inside htdocs) |

Path resolution check: `htdocs/index.php` runs `require __DIR__ . '/../src/bootstrap.php';` → resolves to `/src/bootstrap.php` ✓. `src/bootstrap.php` loads `vendor/autoload.php` and boots Config from `/` which reads `/.env` ✓.

> **Fallback if InfinityFree rejects sibling uploads** (rare on free tier, but possible on some accounts): upload everything under `htdocs/` instead — `htdocs/src/`, `htdocs/templates/`, `htdocs/.env`, etc. Then change one line in each entry file to flatten paths: `__DIR__ . '/../src/bootstrap.php'` → `__DIR__ . '/src/bootstrap.php'` in `index.php`, `thank-you.php`, `privacy.php`, `terms.php`. Leave `api/submit.php` unchanged (it needs the `..`). The `.htaccess` already blocks `/src`, `/templates`, `/config`, `/storage`, `/vendor` via `RedirectMatch 404`, so web access stays safe.

**Step K8.** In FileZilla, right-click `/storage/` → **File Permissions** → Numeric value `755`. Right-click `/storage/logs/` → `755`. Right-click `/.env` → `600` (only PHP reads it). Right-click `/vendor/` → `755`. Files inside → `644`.

**Step K9.** Visit `https://zycus-demo.infinityfreeapp.com/` in your browser.

✅ _Confirm:_ The site loads. SSL works (InfinityFree provisions Let's Encrypt automatically — may take a few minutes).

**Step K10.** Submit a test form. Check the InfinityFree phpMyAdmin → row appears in `submissions`. Email arrives.

**Step K11.** Update GA4 → Admin → **Data Streams** → edit the stream URL to `https://zycus-demo.infinityfreeapp.com`.

---

## STAGE L — Final verification (10 minutes)

Run this end-to-end script on the LIVE site:

1. Open the homepage in incognito → loads under 3 seconds.
2. Hero animates in; KPI counters tick up; bars grow.
3. Scroll past hero → sticky header glass appears.
4. Click hero CTA → smooth-scrolls to form.
5. Submit form with `5,000+ employees` → redirects to your Calendly URL.
6. Submit form with `50-499 employees` → redirects to `/thank-you/`.
7. Submit form with `test@gmail.com` → inline error "Please use your work email".
8. Submit 11 forms in a row → 11th returns 429.
9. Open on phone → sticky CTA bar visible at bottom; disappears when form is in view.
10. GA4 Realtime shows three `generate_lead` events from your tests.

If all 10 pass: you're live.

---

## STAGE M — Post-launch (optional)

**Step M1.** Set up free Cloudflare in front of the site (just point your custom domain's nameservers at Cloudflare → add InfinityFree's IP as A record). Adds a free CDN + bot mitigation.

**Step M2.** Set up daily MySQL backups: in InfinityFree control panel → **Backups → Schedule Daily Backup** (or use phpMyAdmin Export weekly).

**Step M3.** Monitor uptime: sign up for free [UptimeRobot](https://uptimerobot.com/) → add your site → 5-minute checks, free email alerts.

**Step M4.** Add a Meta Pixel or LinkedIn Insight Tag for retargeting → add a new Tag in your GTM container, no code change to the PHP needed.

---

## When you get stuck

Paste a screenshot with one sentence describing what you see vs. what you expected. Common issues + fixes:

| Symptom                               | Likely cause                             | Fix                                                                       |
| ------------------------------------- | ---------------------------------------- | ------------------------------------------------------------------------- |
| `Class "Zycus\Config" not found`      | Composer autoload missing                | Run `composer install` inside `php-app/`                                  |
| `SQLSTATE[HY000] [2002] No such file` | DB_HOST = `localhost` resolves to socket | Change `DB_HOST` to `127.0.0.1`                                           |
| `Missing required env: X`             | `.env` not loaded or var missing         | Check `.env` exists at `php-app/.env`, contains the var                   |
| 500 error on form submit              | Probably DB or SMTP failure              | Check XAMPP's `apache/logs/error.log` or PHP's error log                  |
| `PHPMailer SMTP connect() failed`     | Wrong SMTP creds or port blocked         | Try port `2525` (Brevo supports it) if `587` is blocked                   |
| 419 / 403 on form submit              | CSRF token expired or sessions broken    | Confirm `session.save_path` in `php.ini` is writable                      |
| Form posts but no email arrives       | SMTP throttled or spam-filtered          | Check Brevo dashboard → Statistics → Errors                               |
| Animations don't play                 | JS error in `motion.js`                  | Open DevTools → Console; share the error                                  |
| Pretty URL `/thank-you/` 404s         | `mod_rewrite` disabled on host           | InfinityFree has it by default; if 404, link to `/thank-you.php` directly |
| Page styles missing                   | Wrong asset paths after deploy           | Confirm `public/assets/` uploaded to `htdocs/assets/`                     |

**Start at Stage A. Tell me when you finish each stage or hit a blocker.**
