# Changelog

All notable changes to this project. Format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/); versions follow [SemVer](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added — primary deliverable (WordPress + Elementor)

- 14-stage `docs/ELEMENTOR-BUILD-GUIDE.md` covering install, global tokens, six sections, MetForm wiring, thank-you page, responsiveness matrix, GTM/GA4 integration, a11y + SEO polish, and a pre-launch smoke test.
- Importable artefacts under `build-artifacts/`: MetForm blueprint JSON, GTM container export (`GTM-KG8889HK`), WPCode snippets (13 production-ready snippets), drop-in Elementor Custom CSS kit, approved copy source of truth, and an Elementor hero template kit (`elementor/zycus-hero-template.json`).
- Client-facing docs: `CLIENT-SUBMISSION.md`, `INSTAWP-QUICKDEPLOY.md` (genuinely-free-no-card host options), `EVALUATION-CHECKLIST.md`, `DESIGN-DECISIONS.md`, `CLIENT-FAQ.md`, `BRAND-STYLE-GUIDE.md`, `SECURITY-AND-COMPLIANCE.md`, `ANALYTICS-EVENTS.md`, `SEO-CONTENT-PLAN.md`, `HANDOFF-CHECKLIST.md`, `ACCESSIBILITY-AUDIT.md`, `UAT-TEST-PLAN.md`, `PERFORMANCE-EVIDENCE.md`, `BUILD-GUIDE.md`, `PHP-IMPLEMENTATION-PLAN.md`, `free-plugins-guide.md`, `mockups/` SVG wireframes, and a `README.md` docs index.

### Added — supplemental PHP reference

- Vanilla PHP 8.2+ landing page: hero with animated CSS-only mesh gradient, 3-step progressive lead form, single-open FAQ accordion (`grid-template-rows` pattern), dual-row counter-scrolling logo marquee, personalised thank-you + apology modals.
- Form back-end: CSRF (`src/Csrf.php`), honeypot, per-IP rate-limit (`src/RateLimiter.php`), strict field validator (`src/Validator.php`), PHPMailer via `src/Mailer.php` running after `fastcgi_finish_request()`.
- Dockerfile + `render.yaml` for one-click deploy to Render.
- Mobile hamburger drawer with focus management, scrim, Escape-to-close, `prefers-reduced-motion` kill-switch.

### Added — engineering hygiene

- GitHub Actions CI workflow — PHP 8.2/8.3 matrix + Node 20 JS syntax check.
- PHPUnit suite with 22 `ValidatorTest` + 7 `CsrfTest` + 4 `RateLimiterTest` (DI-refactor-gated, skipped with clear guidance).
- Repo-hygiene files: `LICENSE`, `.editorconfig`, `.github/dependabot.yml`, `CODEOWNERS`, PR + issue templates.
- `Makefile` with one-liner targets (`bootstrap`, `dev`, `lint`, `test`, `check`, `docker`, `clean`).

### Security

- X-Forwarded-For trust now gated on `TRUSTED_PROXY=true` env var (prevents IP spoofing bypass of rate-limit).
- CSRF token rotation on successful POST (prevents in-session replay).
- Free-email regex upgraded to match provider names across ALL TLDs (`.co.uk`, `.co.in`, etc.) — 13 providers covered.

### Fixed

- Mobile: no hamburger, no overflow-x guard, `site-header__inner` no flex-wrap, testimonials grid breakpoint at 840px instead of 768px, `apology-modal__close` 36px (< WCAG 44×44), no large-desktop type scale above 1280px.
- Docs drift: `See the Platform in Action` → `Book My Demo`; `sales@zycus.landing.com` → `sales@zycus.landing.com` (bouncing domain); `Thank You! Your Demo is Confirmed.` → `Demo Request Confirmed!`; GTM tag event name `generate_lead` → `demo_confirmed` on the thank-you-pageview tag.
- InstaWP recipe replaced because InstaWP's free tier now demands a credit card — documented three genuinely-free alternatives (WordPress Playground, InfinityFree, 000webhost).

## [0.1.0] — initial scaffolding

- PHP 8.2 landing page template bootstrap, InfinityFree deploy target, Brevo SMTP wiring.
