# Zycus Landing Page — Client Submission

**Prepared for:** Zycus Evaluation Panel
**Prepared by:** Lekkala Ganesh
**Date:** 16 April 2026
**Repository:** https://github.com/LEKKALAGANESH/zycus-landing

A production-ready, Core Web Vitals–compliant landing page for the Zycus product demo, shipped as a complete Elementor build guide plus a supplemental PHP reference implementation, engineered to the Zycus brand contract and measurable against your stated conversion goals.

---

## What you're submitting

- **Primary deliverable** — `docs/ELEMENTOR-BUILD-GUIDE.md`: 14-stage, copy-paste build guide covering hosting, theme, global design tokens, every section build, MetForm wiring, Yoast SEO, GTM/GA4 integration, QA, and go-live.
- **Supplemental reference** — PHP from-scratch implementation of the same landing page under `php-app/` (same markup, same CSS tokens, same form behaviour) as a performance benchmark for the Elementor build to measure against.
- **Repository** — https://github.com/LEKKALAGANESH/zycus-landing (history, issues, CI config, asset sources).
- **Live demo** — `[live WP URL pending InstaWP spin-up — see docs/INSTAWP-QUICKDEPLOY.md]` (shared within 24 hours of your sign-off on Stage 1 of the guide).
- **Importable artefacts** — MetForm form blueprint, GTM container export (`GTM-KG8889HK`), WPCode snippets, copy source of truth, Elementor global-colors palette.

---

## How it maps to your brief

| Brief requirement                 | Where it's addressed                                                                  |
| --------------------------------- | ------------------------------------------------------------------------------------- |
| WordPress / Elementor build steps | `ELEMENTOR-BUILD-GUIDE.md` Stages 1–10 (hosting → sections → publish)                 |
| Forms                             | Stage 8 (MetForm, 3-step progressive, validation, redirect, SMTP)                     |
| Responsiveness                    | Stage 11 (breakpoints 1440 / 1024 / 767, touch targets, QA matrix)                    |
| Basic tracking integration        | Stage 12 (GTM `GTM-KG8889HK`, GA4 `G-1MG1YKNRDF`, `generate_lead` + `demo_confirmed`) |

---

## Design decisions we made for you

- **Refined B2B over maximalist.** Zycus's buyers are CPOs and procurement leads. Restraint (white space, one accent, no animated gradients) signals enterprise credibility where noise erodes it.
- **Torch Red `#FF1446` reserved exclusively for CTAs and focus rings.** One job, one colour. Every red pixel on the page is clickable — no decorative red, no red gradients, no red icons. The CTA never has to compete for attention.
- **3-step progressive form over a 9-field wall.** MetForm multi-step reduces perceived friction; each step captures ~3 fields, cutting abandonment while preserving full lead qualification data.
- **Grid-template-rows FAQ over legacy `max-height` animations.** `grid-template-rows: 0fr → 1fr` animates to content's _actual_ height, eliminating the "too tall / clipped" trade-off of max-height tricks and avoiding layout thrash.
- **`fastcgi_finish_request()` for the mailer on the PHP reference.** The user sees the thank-you redirect in <100 ms regardless of SMTP latency; the mail dispatch continues after the response is flushed. Zero perceived wait, zero lost leads.

See `docs/DESIGN-DECISIONS.md` for full defence of each decision.

---

## What happens next

Three ways to move forward — pick the one that fits your procurement process:

1. **Self-serve import.** We hand over the MetForm blueprint, GTM container, and Elementor kit. Your team imports into an existing WP install; we support over email for 7 days.
2. **We deploy to your staging.** We spin up the Elementor build on your staging subdomain, wire GTM/GA4, and hand off a reviewed, QA'd site within 5 business days.
3. **Benchmark against the PHP reference.** Use the supplemental PHP build (identical markup + tokens) as a lighthouse/LCP reference to validate the Elementor version's performance budget.

---

## Contact

**Lekkala Ganesh**
lekkalaganesh14@gmail.com · 8688479455
LinkedIn: https://www.linkedin.com/in/lekkala-ganesh/
Repo: https://github.com/LEKKALAGANESH/zycus-landing
