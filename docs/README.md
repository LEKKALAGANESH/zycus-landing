# Zycus Landing Page — Documentation Index

This directory is the **primary client-facing deliverable**. The parent directory (`php-app/`) is a supplemental PHP reference implementation.

Read in this order:

| # | Document | Purpose | Audience |
|---|---|---|---|
| 1 | [`CLIENT-SUBMISSION.md`](CLIENT-SUBMISSION.md) | Cover letter. What's being submitted, how it maps to the brief, what happens next. | Evaluator (first 60 seconds) |
| 2 | [`ELEMENTOR-BUILD-GUIDE.md`](ELEMENTOR-BUILD-GUIDE.md) | **The deliverable.** 14-stage, step-by-step WordPress + Elementor + MetForm + WPCode build guide. | Implementer (90-minute build) |
| 3 | [`INSTAWP-QUICKDEPLOY.md`](INSTAWP-QUICKDEPLOY.md) | 10-step recipe for a live WP demo URL in ~15 minutes on InstaWP's free sandbox. | Anyone who needs a URL to click tomorrow |
| 4 | [`EVALUATION-CHECKLIST.md`](EVALUATION-CHECKLIST.md) | 36-item self-audit covering Functional / Design System / Responsiveness / Tracking / A11y + Performance. | Evaluator (12-minute walkthrough) |
| 5 | [`DESIGN-DECISIONS.md`](DESIGN-DECISIONS.md) | The five load-bearing decisions with `Decision / Why / Alternative / Evidence` rationale. | Evaluator who wants to stress-test choices |
| 6 | [`CLIENT-FAQ.md`](CLIENT-FAQ.md) | Ten anticipated questions with crisp, file-referenced answers. | Evaluator who has a follow-up |
| 7 | [`BRAND-STYLE-GUIDE.md`](BRAND-STYLE-GUIDE.md) | Single-page visual system contract — palette, type scale, buttons, form controls, motion rules, logo usage, banned patterns. | Designer / design reviewer |
| 8 | [`SECURITY-AND-COMPLIANCE.md`](SECURITY-AND-COMPLIANCE.md) | Threat model, form-security controls matrix, GDPR/CCPA, transport hardening, WP plugin hygiene, incident response. | Procurement / legal / security evaluator |
| 9 | [`ANALYTICS-EVENTS.md`](ANALYTICS-EVENTS.md) | Events dictionary — 7 events with triggers, parameters, sample dataLayer payloads, GA4 custom-dimension mapping, consent & debug checklist. | Marketing ops / GA4 analyst |
| 10 | [`SEO-CONTENT-PLAN.md`](SEO-CONTENT-PLAN.md) | Per-page meta-title + meta-description table, keyword targets with intent stages, JSON-LD schemas, OG/Twitter spec, technical SEO checklist. | SEO lead / content editor |
| 11 | [`HANDOFF-CHECKLIST.md`](HANDOFF-CHECKLIST.md) | Post-signoff: what Marketing Ops / IT / Legal / Security / Design each owe, plus a 6-day rollout timeline. | Zycus delivery manager |
| 12 | [`ACCESSIBILITY-AUDIT.md`](ACCESSIBILITY-AUDIT.md) | Formal WCAG 2.1 AA attestation — 30-row compliance matrix, manual test results, tools + methodology, re-audit cadence. | Legal / a11y reviewer |
| 13 | [`UAT-TEST-PLAN.md`](UAT-TEST-PLAN.md) | 31 test cases across 6 suites (content, form, tracking, responsive, a11y, performance) with entry + exit criteria + sign-off block. | Client QA team |
| 14 | [`PERFORMANCE-EVIDENCE.md`](PERFORMANCE-EVIDENCE.md) | Self-reported Lighthouse numbers + budget contract + per-page breakdown + Elementor parity note. | Performance reviewer |
| 15 | [`BUILD-GUIDE.md`](BUILD-GUIDE.md) | Original PHP-deploy step-by-step (hosting, DB, FTP, InfinityFree). Supplemental. | PHP reference deployer only |
| 16 | [`PHP-IMPLEMENTATION-PLAN.md`](PHP-IMPLEMENTATION-PLAN.md) | Architecture + binding design-rules contract. The source of truth for design tokens, copy strings, error taxonomy, and the WordPress port contract. | Architect / tech reviewer |
| 17 | [`free-plugins-guide.md`](free-plugins-guide.md) | WordPress plugin reference: free-tier plugin set + rationale for each. | WordPress developer |

## Importable artefacts (outside this directory)

| File | What to do with it |
|---|---|
| `../build-artifacts/metform/zycus-demo-form.json` | Field-by-field build blueprint for MetForm (referenced from the Elementor guide §8). |
| `../build-artifacts/metform/zycus-demo-form-config.md` | Companion notes for the blueprint. |
| `../build-artifacts/gtm/GTM-zycus-landing.json` | Importable Google Tag Manager container export (container ID `GTM-KG8889HK`, GA4 `G-1MG1YKNRDF`). |
| `../build-artifacts/wpcode/snippets.md` | 13 WPCode snippets — GTM head/body, GA4 fallback, sticky CTA, dataLayer pushes, JSON-LD schemas, performance tweaks. |
| `../build-artifacts/elementor-custom-css.css` | Drop-in CSS for Elementor → Site Settings → Custom CSS. Brand tokens, focus ring, touch-targets, marquee pause, FAQ grid-rows animation, reduced-motion kill. |
| `../build-artifacts/copy/zycus-landing-copy.md` | Approved copy source of truth for every landing-page section. |
| `../public/assets/img/zycus-logo.webp` | Brain logo asset (upload to WP Media Library; alt text must be exactly `"Zycus AI Procurement Brain Logo"`). |

## Key identifiers

- **Repository:** https://github.com/LEKKALAGANESH/zycus-landing
- **GTM container:** `GTM-KG8889HK`
- **GA4 measurement ID:** `G-1MG1YKNRDF`
- **Brand palette:** Torea Bay `#0F3D81` · Dodger Blue `#40A4FB` · Torch Red `#FF1446`
- **Typography:** Inter 400/500/600/700 · 16px base (17px ≥ 768px) · line-height 1.6
- **Performance targets:** LCP < 2.5s · CLS < 0.1 · INP < 200ms · Lighthouse Performance ≥ 85, A11y ≥ 95, SEO = 100

## Ready to submit?

1. Replace every `{{your_name}}`, `{{your_email}}`, `{{your_phone}}`, `{{your_linkedin}}` placeholder in `CLIENT-SUBMISSION.md` and `CLIENT-FAQ.md`.
2. Spin up the InstaWP sandbox per `INSTAWP-QUICKDEPLOY.md` and paste the resulting URL into `CLIENT-SUBMISSION.md` → "Live demo".
3. Send `CLIENT-SUBMISSION.md` as the email body and link to this folder in the repo as the full artefact bundle.
