# ELEMENTOR-BUILD-GUIDE.md

## 0. Purpose of this document

This guide is the single source of truth for rebuilding the Zycus Agentic AI landing page inside WordPress + Elementor Pro + MetForm, per the evaluator's brief. All copy, field definitions, brand tokens, and analytics IDs below are authoritative. Where this guide and the reference PHP prototype at `build-artifacts/prototype/index.html` disagree, **this guide wins** — the PHP app in `php-app/` is a supplemental reference implementation only.

Target stack: WordPress 6.5+, Elementor Pro 3.22+, MetForm Pro, WPCode Lite, Google Tag Manager, GA4. Target Lighthouse: 90+ performance, 100 accessibility on mobile.

---

## 0. Stage 0 — Pre-flight checklist (before you touch WordPress)

Have these gathered before starting Stage 1. Skipping this costs 20-minute detours mid-build.

**Access & credentials**
- [ ] WordPress admin URL + admin username + password (from your host or from `INSTAWP-QUICKDEPLOY.md` Step 1)
- [ ] FTP / SFTP credentials (only if you need to upload the brain logo manually rather than via Media Library)
- [ ] SMTP credentials for lead notifications — Brevo / SendGrid / Amazon SES (free tier acceptable). Must have SPF + DKIM configured on the sending domain or mail will spam-folder.
- [ ] Google account with access to the GTM + GA4 properties

**Licences / plugins**
- [ ] Elementor (Free is enough for this guide; Pro unlocks header/footer builder and extra widgets)
- [ ] MetForm Lite (Free) — Pro not required, but makes conditional logic + webhooks trivial
- [ ] WPCode Lite (Free) — for pasting GTM head/body snippets
- [ ] Yoast SEO Free — for meta tags + FAQ schema

**Assets in hand**
- [ ] Brain logo: `../public/assets/img/zycus-logo.webp` (exact alt text `Zycus AI Procurement Brain Logo` is non-negotiable — brand contract)
- [ ] Client logos: `../public/assets/img/logos/` (13 JPGs for the marquee; convert to WebP in Stage 3 for payload savings)
- [ ] Copy source: `../build-artifacts/copy/zycus-landing-copy.md`
- [ ] MetForm blueprint: `../build-artifacts/metform/zycus-demo-form.json` (reference only — MetForm Lite can't import; follow the field list)
- [ ] GTM container export: `../build-artifacts/gtm/GTM-zycus-landing.json` (container `GTM-KG8889HK` pre-configured with GA4 `G-1MG1YKNRDF`)
- [ ] WPCode snippets: `../build-artifacts/wpcode/snippets.md`
- [ ] Elementor Custom CSS drop-in: `../build-artifacts/elementor-custom-css.css` (paste into Site Settings → Custom CSS after Stage 2)

**IDs that will be used throughout (pre-filled; replace only if you have your own)**
- GTM container: `GTM-KG8889HK`
- GA4 measurement: `G-1MG1YKNRDF`
- Primary sales inbox: `sales@zycus.com`
- Calendly enterprise redirect: set per your team — placeholder `https://calendly.com/zycus-enterprise-ae` is in the MetForm blueprint

**Time budget**
- Stage 1–3 (install + globals + assets): ~40 min
- Stage 4 (homepage build): ~90 min
- Stage 5–10 (form + thank-you + polish): ~2 hrs
- Stage 11–14 (responsiveness + analytics + a11y + QA): ~1 hr
- **Total: half a working day** for a WP developer who has used Elementor + MetForm once before.

---

## 1. Environment prerequisites

1. Fresh WordPress install on PHP 8.1+, MySQL 8 or MariaDB 10.6+.
2. Activate the **Hello Elementor** child theme (not the bloated default).
3. Install and license: Elementor Pro, MetForm Pro, WPCode Lite.
4. Under **Settings → Permalinks** set to "Post name".
5. Under **Elementor → Settings → Features** disable "Optimized Image Loading" only if you see CLS regressions; otherwise leave on.
6. Under **Elementor → Settings → Advanced** set CSS Print Method to "External File" for cache-friendliness.
7. Delete all default sample content, widgets, and the Hello Dolly plugin.

---

## 2. Global design tokens (Site Settings → Global Colors & Global Fonts)

Create exactly these Global Colors. Do not add more — Elementor's color picker should not be used ad-hoc anywhere.

| Token name     | Hex       | Usage                                                           |
|----------------|-----------|-----------------------------------------------------------------|
| Torea Bay      | `#0F3D81` | Body text, headings, footer bg, testimonials section bg         |
| Dodger Blue    | `#40A4FB` | Secondary accents, off-ramp links                               |
| Torch Red      | `#FF1446` | Primary CTAs ONLY, required asterisks, focus rings              |
| Surface Base   | `#FAFBFD` | Page background (never pure white)                              |
| Surface Alt    | `#F4F7FB` | Alternating light sections                                      |
| Ink Subtle     | `#4A5B7A` | Muted body, form helper text                                    |

Global Fonts: set **Primary** and **Text** both to `Inter` (weights 400/500/600/700 via Google Fonts). Body base 16px mobile, 17px desktop, line-height 1.6. Headings weight 700, line-height 1.2.

WCAG contract: all body text must pass 4.5:1 against its background; large text (≥ 24px or ≥ 19px bold) must pass 3:1. Torea Bay on Surface Base = 10.9:1. Torea Bay on Surface Alt = 10.1:1. Do not place Dodger Blue text on white — it fails 4.5:1.

---

## 3. Page structure (Elementor Canvas template)

Create a new Page titled "Zycus — Agentic AI for Procurement", slug `/`, template **Elementor Canvas**. The page is composed of six Sections, top to bottom:

1. Hero
2. Trust strip
3. How It Works
4. Testimonials
5. Demo form
6. FAQ + footer

Use **Flexbox Containers** (not legacy sections). Set site max width to 1200px, default container padding 24px mobile / 64px desktop.

---

## 4. Section 1 — Hero

### Wireframe

```
┌──────────────────────────────────────────────────────────────┐
│ [Logo]                                        [Book My Demo] │ header (sticky)
├──────────────────────────────────────────────────────────────┤
│                                                              │
│   EYEBROW (gradient)                                         │
│   ═══════════════════════════════════════════                │
│   ━━━━━━━━━━━━━━━━━━━━━━━━                                    │
│   Cut Procurement Costs by                                   │
│   40% with Agentic AI           │                            │
│   ━━━━━━━━━━━━━━━━━━━━━━━━       │     [brain logo.webp]      │
│   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━   │                            │
│   Subhead lorem lorem lorem…    │                            │
│                                                              │
│   ┌──────────────────────┐                                   │
│   │   Book My Demo   ▶   │   ← Torch Red, 48px min-height    │
│   └──────────────────────┘                                   │
│                                                              │
│   ✓ Gartner Magic Quadrant Leader                            │
│   ✓ SOC 2 Type II & ISO 27001                                │
│   ✓ Live in 250+ enterprises                                 │
│                                                              │
└──────────────────────────────────────────────────────────────┘
  60% (copy)                    40% (brain logo)
  stacks to 100% ≤ 1024px
```

### Build

Container: full-width, min-height 560px desktop / 480px mobile, background `#FAFBFD`, two columns at ≥ 1024px (60/40), stacked below.

Left column widgets (top to bottom):
- **Heading (H1)**, Torea Bay, 48px desktop / 32px mobile, weight 700:
  > Cut Procurement Costs by 40% with Agentic AI
- **Text Editor (subhead)**, Ink Subtle, 18px desktop / 16px mobile, max-width 560px:
  > Zycus Merlin AI automates sourcing, contracting, and invoice processing end-to-end — so your team closes deals faster and pays suppliers without chasing approvals. Trusted by 1,200+ enterprises worldwide.
- **Button (primary CTA)**, label `Book My Demo`, background Torch Red, text `#FFFFFF`, 48px height, 24px horizontal padding, border-radius 6px, link to `#demo-form`, hover darken 8%. Focus ring: 3px outset Torch Red at 40% alpha.
- **Icon List (trust badges)**, 3 items, check icon in Dodger Blue, 14px text in Ink Subtle:
  - Recognized as a Leader by Gartner® Magic Quadrant™
  - SOC 2 Type II & ISO 27001 Certified
  - Live in 250+ enterprises across 100 countries

Right column: **Image** widget pointing to `zycus-logo.webp` (upload from `php-app/public/assets/img/zycus-logo.webp`) with `alt="Zycus AI Procurement Brain Logo"`, max-width 420px, no entrance animation.

Do not add entrance animations anywhere in the hero.

---

## 5. Section 2 — Trust strip

Single-row container, background Surface Alt, 32px vertical padding. Five greyscale client logos (placeholders acceptable), opacity 0.7, gap 48px, horizontally centered. Wraps to 2 rows on mobile. This section has no heading.

---

## 6. Section 3 — How It Works

Container: background Surface Base, 96px vertical padding.

- **Heading (H2)**, centered, Torea Bay, 36px:
  > From Intake to Payment — Automated End-to-End
- 4-column grid (1 col mobile, 2 col tablet, 4 col desktop), gap 32px. Each cell is an inner container with:
  - Numbered badge (48px circle, Dodger Blue bg, white numeral) reading 01–04.
  - **H3** in Torea Bay, 20px, weight 600.
  - Step body in Ink Subtle, 16px.

Exact step copy:

1. **Intake** — Employees raise requests in plain language. Merlin AI routes every ask to the right category, policy, and buyer — no forms to fill out.
2. **Sourcing** — AI drafts RFXs, scores supplier responses, and surfaces the best deal against your price benchmarks in hours instead of weeks.
3. **Contracting** — Smart clause libraries and risk-flagging AI redline contracts against your playbook. Legal reviews exceptions only — not every agreement.
4. **Procure-to-Pay** — Invoices are matched, coded, and approved automatically. Exceptions route to the right approver based on policy — suppliers get paid on time, every time.

---

## 7. Section 4 — Testimonials

Container: background **Torea Bay** `#0F3D81`, 96px vertical padding, text color `#FFFFFF`.

- **Heading (H2)**, centered, white, 36px:
  > Real Results from Real Procurement Leaders

Two-column grid, 32px gap. Each card: inner container, background `rgba(255,255,255,0.06)`, 1px border `rgba(255,255,255,0.12)`, 32px padding, border-radius 8px. Inside: blockquote (18px, white, line-height 1.6), then attribution (14px Dodger Blue, weight 600).

Card 1:
> "Zycus Merlin AI cut our invoice processing time by 75% in the first six months. What used to take a team of twelve now runs with four people and zero backlog. The AI doesn't just automate — it catches policy exceptions humans missed for years."
>
> — Priya Venkataraman, CPO Global Consumer Goods Leader

Card 2:
> "We reduced supplier query response times by 80% and recovered $4.2M in duplicate and off-contract spend in year one. Zycus paid for itself before the rollout was even complete."
>
> — Daniel Osei, CFO Multinational Industrial Group

---

## 8. Section 5 — Demo form (MetForm)

### Wireframe

```
┌───────────────────────────────────────────────────────────┐
│                                                           │
│            See Merlin AI Live on Your Own Data            │
│         ──────────────────────────────────────            │
│   Book a 30-minute working session with a Zycus           │
│   solution architect…                                     │
│                                                           │
│   ┌────────────────────────────────────────┐              │
│   │  [Step 1 of 3]   About You             │              │
│   │  ┌──────────────────────────────────┐  │              │
│   │  │ Work Email *                     │  │              │
│   │  │ you@company.com________________  │  │              │
│   │  └──────────────────────────────────┘  │              │
│   │  ┌─────────────────┐ ┌──────────────┐  │              │
│   │  │ First Name *    │ │ Last Name *  │  │              │
│   │  └─────────────────┘ └──────────────┘  │              │
│   │                     [ Next ▶ ]         │              │
│   └────────────────────────────────────────┘              │
│   ┌────────────────────────────────────────┐              │
│   │  [Step 2 of 3]   About Your Company    │ hidden       │
│   └────────────────────────────────────────┘              │
│   ┌────────────────────────────────────────┐              │
│   │  [Step 3 of 3]   Your Priority         │ hidden       │
│   │  … Primary Use Case *, Notes           │              │
│   │                                        │              │
│   │  ┌──────────────────────────────────┐  │              │
│   │  │        Book My Demo              │  │  Torch Red   │
│   │  └──────────────────────────────────┘  │              │
│   │  🔒 Secure & confidential. No spam…   │  13px Subtle  │
│   └────────────────────────────────────────┘              │
│                                                           │
└───────────────────────────────────────────────────────────┘
  max-width 720px, centered, Surface Base bg
  Error states: .is-invalid → Torch Red border + #FFF5F6 bg
  Success: redirect to /thank-you/ (no inline message)
```

### Build

Container id `demo-form`, background Surface Base, 96px vertical padding, centered column max-width 720px.

- **Heading (H2)**, Torea Bay, 32px, centered:
  > See Merlin AI Live on Your Own Data

Drop a **MetForm** widget, select the pre-built form `Zycus Demo Request` (import from `build-artifacts/metform/zycus-demo-form.json`). The form is a 3-step multistep with these 8 fields:

| # | Label              | Type     | Req | Notes                                                                 |
|---|--------------------|----------|-----|-----------------------------------------------------------------------|
| 1 | Work Email         | email    | yes | Placeholder `you@company.com`; reject free domains via regex below    |
| 2 | First Name         | text     | yes |                                                                       |
| 3 | Last Name          | text     | yes |                                                                       |
| 4 | Company Name       | text     | yes |                                                                       |
| 5 | Company Size       | select   | yes | 1-49 / 50-499 / 500-4,999 / 5,000+ employees                          |
| 6 | Your Role          | select   | yes | Procurement Leader / Finance Leader / IT / Procurement Team / Other   |
| 7 | Primary Use Case   | select   | yes | Sourcing & CM / Invoice Automation (AP) / Supplier Mgmt / End-to-End  |
| 8 | Notes              | textarea | no  | 500 char max; placeholder "Tell us about your biggest procurement bottleneck" |

Free-domain rejection regex on field 1 (MetForm **Validation → Pattern**):

```
^(?!.*@(gmail|yahoo|hotmail|outlook|aol|icloud|proton(mail)?|gmx|live|msn|mail)\.).+@.+\..+$
```

Styling: labels 14px Torea Bay weight 600, required asterisk Torch Red. Inputs 48px height, 1px border Ink Subtle at 30%, border-radius 6px, focus ring 3px Torch Red at 40% alpha. Submit button identical spec to the hero CTA — label `Book My Demo`. No entrance animation on submit; it must be static.

Below the submit:
> Secure & confidential. We respect your inbox — no spam, no credit card required.

MetForm Custom Messages → Server Error:
> Connection Interrupted. Hi [field id="first_name"], we sincerely apologize, but we are experiencing a temporary server issue and couldn't process your request for [field id="email"]. Please wait a few moments and try submitting again. If the issue persists, you can bypass this form and email our team directly at sales@zycus.com to schedule your demo.

MetForm **Success Redirect** → `/thank-you/`.

---

## 9. Section 6 — FAQ + footer

FAQ uses Elementor's **Accordion** widget, titled:

> Your Questions, Answered

Four items (copy verbatim):

- **Will Zycus integrate with our existing ERP (SAP, Oracle, NetSuite)?** Yes. Zycus ships with certified, pre-built connectors for SAP S/4HANA, SAP ECC, Oracle Fusion, Oracle EBS, NetSuite, Microsoft Dynamics, and Workday. Most integrations go live in under 4 weeks and sync invoices, POs, master data, and GL codes bi-directionally.
- **How long does implementation actually take?** A typical mid-market rollout of Source-to-Contract goes live in 8–12 weeks. Full Source-to-Pay across multiple business units is usually 16–20 weeks. Our implementation team handles configuration, integration, and user enablement — your team focuses on process design.
- **How is our procurement data kept secure?** Zycus is SOC 2 Type II, ISO 27001, and ISO 27701 certified. All data is encrypted in transit (TLS 1.3) and at rest (AES-256), hosted in your choice of AWS region with full data residency controls. We're GDPR, CCPA, and HIPAA compliant.
- **What kind of ROI can we actually expect?** Customers typically report 40–60% reduction in cycle times, 5–8% savings on addressable spend, and 70%+ reduction in manual invoice handling within 12 months. We'll share a custom ROI model during your demo based on your current spend and team size.

Footer: Torea Bay background, white text 14px, three columns (About / Product / Legal) + copyright line. Off-ramp links use Dodger Blue.

---

## 10. Thank-You page (`/thank-you/`)

Separate Page, Elementor Canvas template, background Surface Base.

- **H1**, Torea Bay, 40px, centered: `Demo Request Confirmed!`
- Body (18px, Ink Subtle, max-width 640px, centered). Paste verbatim — MetForm resolves the shortcodes from the prior submission:

> Hi [field id="first_name"], thank you for requesting a demo. We have successfully received your details via [field id="email"]. A Zycus Agentic AI specialist will review your request and reach out within 24 hours to schedule your personalized walkthrough. While you wait, keep an eye on your inbox for our latest S2P Diagnostic Guide.

Two off-ramp buttons, side by side, 48px tall:
- Primary (Torch Red): `Download the S2P Diagnostic Guide`
- Secondary (outline Dodger Blue): `Explore more resources →`

---

## 11. Responsiveness

Elementor's breakpoints (Site Settings → Layout → Breakpoints): Mobile ≤ 767px, Tablet 768–1024px, Desktop ≥ 1025px. Review every Section on all three in Elementor's responsive editor.

Per-breakpoint checklist:
- Hero collapses to single column below 1024px; H1 drops from 48px to 32px; CTA stretches to full-width mobile.
- Trust strip wraps to 2-up on mobile; logos keep 32px min height.
- How It Works grid: 4-col → 2-col tablet → 1-col mobile.
- Testimonials: 2-col → 1-col below 768px.
- Form: 3-step multistep remains usable on mobile (steps stack, progress dots centered).
- FAQ accordion: keep single column; ensure tap targets are ≥ 44×44.
- Footer: 3 columns → 1 column on mobile.

Touch-target rule (WCAG 2.1 AA): every link, button, form control, accordion toggle, and nav item must be ≥ 44×44. Aim for 48×48 on primary CTAs.

---

## 12. Analytics (GTM + GA4)

Install GTM via **WPCode → Header & Footer**. Container ID `GTM-KG8889HK`. Paste the official GTM head snippet in Header, and the noscript iframe snippet in Body. GA4 measurement ID `G-1MG1YKNRDF` is fired by the GTM container — do not hard-code gtag on the page.

Import `build-artifacts/gtm/GTM-zycus-landing.json` into GTM. It ships with three tags:
- GA4 Configuration (`page_view`).
- GA4 Event `generate_lead` on MetForm success dataLayer push.
- GA4 Event `demo_confirmed` on thank-you pageview.

MetForm success dataLayer push (add via **WPCode → PHP Snippet**, snippet body in `build-artifacts/wpcode/snippets.md`) pushes `{event: 'generate_lead', form_id: 'zycus_demo'}` before the redirect.

Verify in GTM Preview mode:
1. Load homepage → `page_view` fires.
2. Submit demo form → `generate_lead` fires.
3. Land on `/thank-you/` → `demo_confirmed` fires.
4. Open GA4 → Reports → Realtime → confirm both events appear within 30 seconds.

---

## 13. Accessibility & SEO polish

- Install **Yoast SEO Free**. Set meta title + meta description + canonical + OG/Twitter card per page.
- Yoast → Features → enable **Schema.org FAQPage** output on the homepage; it auto-ingests the Accordion widget.
- Logo alt text exactly `"Zycus AI Procurement Brain Logo"` (brand contract).
- Exactly one H1 per page. Elementor widget hierarchy: H1 hero → H2 section titles → H3 step/question titles. Never skip levels.
- All images WebP where possible, `loading="lazy"` except hero logo (which gets `fetchpriority="high"`).
- Focus ring: 3px Torch Red at 40% alpha, 2px offset, never suppressed. Add via Custom CSS if the theme overrides:
  ```css
  a:focus-visible, button:focus-visible, input:focus-visible, select:focus-visible, textarea:focus-visible {
    outline: 3px solid rgba(255,20,70,0.4);
    outline-offset: 2px;
  }
  ```
- `prefers-reduced-motion: reduce` — add to Custom CSS to kill any residual entrance animations.

---

## 14. Pre-launch smoke test

Run **before** handing the URL to the client evaluator:

- Lighthouse mobile audit: Performance ≥ 85, Accessibility ≥ 95, Best Practices ≥ 95, SEO = 100.
- Keyboard-only: unplug your mouse. Tab through the whole page. Every interactive element shows the Torch-Red focus ring. Submit the form using only the keyboard.
- Form validation: submit empty → inline errors appear per field. Submit with `user@gmail.com` → free-domain regex blocks with "Please use your work email". Submit valid → redirects to `/thank-you/`.
- GA4 Realtime: `generate_lead` visible within 30 seconds of submission; `demo_confirmed` visible within 30 seconds of thank-you page.
- Cross-browser: Chrome, Safari, Firefox, Edge on desktop + mobile Safari + Chrome Android.
- Run axe DevTools — zero violations on Hero, Form, Thank-You, FAQ.

---

## Appendix A — Exact copy strings (single source of truth)

**Hero H1:** Cut Procurement Costs by 40% with Agentic AI

**Hero subhead:** Zycus Merlin AI automates sourcing, contracting, and invoice processing end-to-end — so your team closes deals faster and pays suppliers without chasing approvals. Trusted by 1,200+ enterprises worldwide.

**Primary CTA (every instance):** Book My Demo

**Form section H2:** See Merlin AI Live on Your Own Data

**Form microcopy under submit:** Secure & confidential. We respect your inbox — no spam, no credit card required.

**Thank-You H1:** Demo Request Confirmed!

**Thank-You body (with MetForm shortcodes):** Hi [field id="first_name"], thank you for requesting a demo. We have successfully received your details via [field id="email"]. A Zycus Agentic AI specialist will review your request and reach out within 24 hours to schedule your personalized walkthrough. While you wait, keep an eye on your inbox for our latest S2P Diagnostic Guide.

**Server Error (MetForm Custom Messages → Server Error):** Connection Interrupted. Hi [field id="first_name"], we sincerely apologize, but we are experiencing a temporary server issue and couldn't process your request for [field id="email"]. Please wait a few moments and try submitting again. If the issue persists, you can bypass this form and email our team directly at sales@zycus.com to schedule your demo.

---

## Appendix B — Build artefacts referenced

All paths are relative to the repository root (this guide lives at `docs/ELEMENTOR-BUILD-GUIDE.md`):

- MetForm form export: `build-artifacts/metform/zycus-demo-form.json`
- MetForm configuration notes: `build-artifacts/metform/zycus-demo-form-config.md`
- GTM container export: `build-artifacts/gtm/GTM-zycus-landing.json`
- WPCode PHP/JS snippets: `build-artifacts/wpcode/snippets.md`
- Copy source of truth: `build-artifacts/copy/zycus-landing-copy.md`
- Reference HTML prototype (non-authoritative): `build-artifacts/prototype/index.html`
- Brain logo asset (upload to WP Media Library): `php-app/public/assets/img/zycus-logo.webp`

---

## Appendix C — Out of scope / deferred to v2

- Multi-language (Polylang or WPML) — defer.
- Admin dashboard for lead management — leads land in `wp-admin → MetForm → Entries` for v1.
- A/B testing infrastructure (Google Optimize is deprecated; consider Microsoft Clarity + manual variant pages).
- CDN integration — Cloudflare free tier plugs in later with zero code change.
- File uploads in the form — not required for the demo-request flow.
- SSO / OAuth for gating the form — scope creep; defer.
- Live chat widget — evaluate after first-30-day conversion data is in.

---

*This guide is the primary deliverable for the Zycus landing-page evaluation. The `php-app/` folder in the same repository is a supplemental reference implementation built without WordPress, useful as a performance benchmark and design-token reference.*
