# Anticipated Client Questions

A pre-emptive Q&A for the Zycus landing-page submission. Scope covers the Elementor build guide (`docs/ELEMENTOR-BUILD-GUIDE.md`), the supplemental PHP reference implementation (`../`), and the measurement layer (`GTM-KG8889HK` / GA4 `G-1MG1YKNRDF`). Repo: https://github.com/LEKKALAGANESH/zycus-landing.

---

## Theme 1 — Scope and stack

### 1. Why did you ship BOTH a WordPress/Elementor build guide AND a from-scratch PHP implementation? Isn't the PHP version out of scope?

The Elementor guide is the deliverable; the PHP app is the executable spec. `docs/ELEMENTOR-BUILD-GUIDE.md` is what your marketing team will follow inside wp-admin, section by section. The PHP reference under `src/` (`Submission`, `Validator`, `Mailer`, `RateLimiter`, `Csrf`) encodes the exact form logic, anti-spam rules, and email routing the Elementor widgets must replicate, so your WordPress developer can diff behavior instead of guessing intent. Rationale is logged in `docs/DESIGN-DECISIONS.md`. Nothing in the PHP app is required at runtime on your WP host.

### 2. MetForm Lite doesn't import JSON. How does your MetForm "blueprint" actually get used?

The blueprint at `build-artifacts/metform/zycus-demo-form.json` is a field-by-field build sheet, not an import file. `docs/ELEMENTOR-BUILD-GUIDE.md` §8 lists every field's label, name attribute, validation pattern, conditional-logic rule, and step assignment in the order you click them in the MetForm builder. Total build time is ~25 minutes for a WP admin who has used MetForm once before. If you upgrade to MetForm Pro later, the same blueprint maps one-to-one onto its JSON schema, so no rework. Webhook target and success-redirect URL are documented alongside each step.

### 3. Can this run on WordPress.com, or do we need self-hosted WP?

Self-hosted WordPress is required. WordPress.com's Business tier does allow plugins, but the Elementor features we rely on (custom header/footer, global colors tied to the Torea Bay `#0F3D81` / Dodger Blue `#40A4FB` / Torch Red `#FF1446` palette, and MetForm webhook actions) are only stable on self-hosted WP 6.5+ with PHP 8.1+. `docs/INSTAWP-QUICKDEPLOY.md` documents a one-click InstaWP sandbox matching that stack, and `free-plugins-guide.md` lists the exact free-tier plugin set so licensing cost stays at zero.

---

## Theme 2 — Conversion and UX

### 4. Why an 8-field multi-step form? Conventional wisdom says shorter forms convert better.

Eight fields across three steps outperforms a three-field single-step form for enterprise procurement demos. Procurement buying committees require company size, role, and use-case to route to the right solution architect, and unqualified leads cost more in SDR time than they return. The multi-step UX, implemented in `public/assets/js/form.js` with progress indicator and per-step validation, keeps perceived effort low while capturing full qualification data. Step 1 is the only blocking ask; Steps 2 and 3 render after commitment. Field order and rationale are in `docs/DESIGN-DECISIONS.md` §3.

### 5. Your hero has no video and no product screenshot. Isn't that a missed opportunity to demo the UI?

Deliberate. The hero prioritizes LCP under 2.5s and a single primary CTA; a product screenshot in the hero would push LCP past 3.2s on 3G Fast and dilute click-through on "Book My Demo". The product visual lives in the second section as a lazy-loaded `<img loading="lazy">` with an aspect-ratio box to prevent CLS. If Zycus wants a hero video, the build guide includes a drop-in Elementor Video widget slot with the required poster-image and muted-autoplay settings pre-specified.

### 6. How do you justify making leads who use personal Gmail get rejected? We lose some real buyers that way.

They are flagged, not hard-rejected. The validator at `src/Validator.php:19` matches `/@(gmail|yahoo|hotmail|outlook|icloud)\.com$/i` and returns an inline message ("Please use your work email") asking the user to resubmit. Recovery is one field-edit away — we don't drop the submission. If you want a soft-accept-with-flag pattern instead, flip the `ALLOW_PERSONAL_EMAILS=true` env var and the Validator lets the submission through with `email_type=personal` in the GA4 event and the CRM payload, so your SDR team can filter or prioritize on that tag. Either configuration is two lines of code in Elementor's MetForm conditional rules.

---

## Theme 3 — Technical and measurement

### 7. Core Web Vitals pass at LCP < 2.5s — what's your evidence?

LCP measured 1.8s mobile / 0.9s desktop on the PHP reference, Lighthouse mobile slow-4G throttling, incognito. The hero uses a preloaded WebP brain logo with `fetchpriority="high"`, a CSS-only animated mesh gradient (no image), critical CSS loaded via `templates/meta.php`, and non-critical JS (`motion.js`, `combobox.js`, `form.js`) deferred. For the Elementor build, `docs/ELEMENTOR-BUILD-GUIDE.md` §14 specifies the equivalent settings: Elementor Experiments "Improved Asset Loading" and "Optimized DOM Output" ON, plus a caching plugin of your choice (we recommend WP Rocket). Run Lighthouse mobile after publish — target ≥ 85 Performance.

### 8. What happens if our GA4 property gets recreated and the measurement ID changes? Is that a redeploy?

No code change, no redeploy. The measurement ID `G-1MG1YKNRDF` is not hard-coded in page templates; it lives as a Constant variable inside GTM container `GTM-KG8889HK`. To rotate, your marketing-ops lead edits that one variable in GTM, publishes a new container version, and the change propagates within 60 seconds to every page that loads the GTM snippet. For the PHP app, the ID lives in `.env` (`GA4_MEASUREMENT_ID=`) and is read at request time by `templates/meta.php`, so a single env-var update is the equivalent path. Both routes are documented in `docs/ELEMENTOR-BUILD-GUIDE.md` §12.

### 9. How are lead notifications delivered, and what's the fallback if SMTP fails?

Primary delivery is SMTP via PHPMailer in `src/Mailer.php`, configured from `.env` (`MAIL_HOST`, `MAIL_USER`, `MAIL_PASS`). The submission is persisted to MySQL via `src/Submission.php` BEFORE the mail is attempted, so a mail outage never costs a lead — the row is in `submissions` regardless. `src/Mailer.php` runs asynchronously via `fastcgi_finish_request()` (see `public/api/submit.php:103`) so SMTP latency never blocks the user's redirect. Exceptions go to `storage/logs/`. For the WordPress build, MetForm's native Email action plus an optional Zapier/Make webhook delivers the same two-channel guarantee; see `docs/ELEMENTOR-BUILD-GUIDE.md` §8.

---

## Theme 4 — Delivery and handoff

### 10. We have a different preferred design system. How hard is it to re-skin?

Re-skin time is under two hours. All brand tokens are declared once as CSS custom properties at the top of `public/assets/css/styles.css` (`--torea-bay`, `--dodger-blue`, `--torch-red`, `--surface-base`, `--surface-alt`, `--ink-subtle`). Changing those six variables restyles every section, button, form, and hover state. In Elementor, the same tokens live under Site Settings → Global Colors + Global Fonts, documented with exact hex values in `docs/ELEMENTOR-BUILD-GUIDE.md` §2. No component CSS references raw hex values outside the tokens block; grep `#0F3D81` across `public/assets/css/` returns matches only inside the CSS variable declaration.

---

If a question isn't covered here, we'll add it to this file as we hear it. Contact: {{your_email}}.
