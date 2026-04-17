# How to Use Claude Code Prompts — Zycus Landing Page Project

**Prepared by:** Front-end Developer
**Project:** Zycus B2B Enterprise Landing Page (AI-powered Source-to-Pay)
**Purpose:** Reference document showing how Claude Code prompts drove copy, layout logic, and implementation decisions across the build.

---

## Executive Summary

This project demonstrates how structured AI prompting can be treated as a professional design and conversion discipline, not a shortcut. Every Claude Code prompt in this document was engineered around the **4C framework (Clarity, Comprehension, Credibility, Conversion)** and tuned to the realities of enterprise B2B buying — multi-stakeholder committees, long sales cycles, and risk-minimization psychology. The result is a reusable prompt library that shortens production time from days to hours while keeping copy, trust signals, and objection handling aligned to Zycus's brand voice and procurement-buyer intent.

**Skills demonstrated:** AI prompt engineering | B2B conversion copywriting | CRO strategy | Core Web Vitals optimization | WCAG accessibility | GA4 and GTM event tracking | Elementor form UX | Brand-token discipline

**Impact snapshot**

- Engineered Core Web Vitals targets (LCP < 2.5s, CLS < 0.1, INP < 200ms) with a pre-handoff audit checklist to protect paid-traffic ROI.
- Validated `generate_lead` conversion tracking end-to-end in GA4 DebugView across AJAX and redirect paths, closing the attribution gap most Elementor pages miss.
- Produced a section-by-section prompt library (hero, workflow, social proof, FAQs, post-submit UX) that is reusable across future Zycus campaigns and vertical landers.

---

## 1. Why Claude Code Prompts Were Used

The Zycus landing page is a B2B enterprise page — buyer committees of 4–10 stakeholders, no impulse purchases, risk-minimization focus. Copy and structure must appeal to logic (hard ROI numbers) while offering emotional confidence (case studies, compliance badges, client logos).

Claude accelerated three workstreams:

1. **Copywriting** — headlines, sub-headlines, testimonials, FAQs, form microcopy.
2. **Structural guidance** — workflow step naming, section ordering, objection handling.
3. **Design/brand assets** — logo specification prompt, color palette decisions.

Every prompt below is production-ready, tuned to Zycus's brand voice and the 4C framework: **Clarity → Comprehension → Credibility → Conversion**.

---

## 2. Prompt Library (Grouped by Page Section)

### 2.1 Hero Section — Clarity

> "I am building a B2B landing page for Zycus, an AI-powered Source-to-Pay procurement platform. Write an outcome-focused hero headline (under 10 words) that highlights a concrete benefit like saving time or reducing risk. Write a 2-sentence sub-headline explaining how the Merlin AI platform achieves this. Suggest 3 trust signals (like 'Recognized by Gartner') to place right under the CTA button."

**Constraints applied:** headline ≤ 10 words / 44 characters, sub-headline names the audience, output includes trust signals for placement below the CTA.

---

### 2.2 "How It Works" Section — Comprehension

> "Create a 4-step 'How it works' section for Zycus's procurement platform. Structure it around the actual workflow: 1. Intake, 2. Sourcing, 3. Contracting, 4. Procure-to-Pay. For each step, provide a short header and one sentence explaining how AI automation makes this step frictionless."

**Why this works:** mirrors the real S2P workflow so procurement buyers recognize it immediately.

---

### 2.3 Social Proof — Credibility

> "Write two realistic but fictional B2B testimonials for Zycus. They should come from a Chief Procurement Officer and a CFO. Include concrete ROI metrics, such as a 75% reduction in invoice processing time and an 80% reduction in supplier query response times."

**Note:** Pair generated testimonials with a static logo grid of real Zycus clients (Danone, Tiger Brands, Netcare) placed directly below the hero CTA to reduce buyer anxiety at the decision point.

---

### 2.4 FAQs — Objection Handling

> "Write 4 short, conversational FAQs for an enterprise procurement software landing page. Address common B2B objections regarding ERP integration, implementation timelines, and data security."

Follow-up prompt used after first pass:

> "Write 3 short, factual FAQs about Zycus's implementation, security, and AI integrations to proactively remove buyer hesitation."

---

### 2.5 Thank-You and Error Messages — Post-Submit UX

**Success message prompt pattern:**

> Headline: _Demo Request Confirmed!_
> Body: _"Hi [field id=\"name\"], thank you for requesting a demo! We have successfully received your details via [field id=\"email\"]. A Zycus Agentic AI specialist will review your request and reach out within 24 hours to schedule your personalized walkthrough."_

**Server-error message prompt pattern:**

> Headline: _Connection Interrupted_
> Body: _"Hi [field id=\"name\"], we sincerely apologize, but we are experiencing a temporary server issue and couldn't process your request for [field id=\"email\"]. Please wait a few moments and try again. If the issue persists, email sales@zycus.landing.com directly to schedule your demo."_

Shortcodes `[field id="name"]` / `[field id="email"]` are pulled from the form's Advanced → Shortcode panel so messages render with the user's actual input.

---

### 2.6 Brand Asset — Logo Generation Prompt

> "A clean, modern, and minimalist B2B enterprise software logo for the company 'Zycus'. The icon should be to the left of the text and feature a stylized, geometric 'Brain' made of connected digital nodes or soft glowing lines to represent Agentic AI. The text 'Zycus' should be on the right, written in a highly legible, thick sans-serif font. The color palette must strictly use deep corporate blue (#0F3D81) and bright tech blue (#40A4FB) on a pure white background. Flat vector style, no 3D rendering, highly professional."

Output saved as `zycus-new-logo.webp` with alt text `Zycus AI Procurement Brain Logo` for SEO/a11y.

---

## 3. Brand Tokens Claude Was Instructed to Respect

| Token       | Hex       | Use                                          |
| ----------- | --------- | -------------------------------------------- |
| Torea Bay   | `#0F3D81` | Backgrounds, primary text, footer            |
| Dodger Blue | `#40A4FB` | Secondary links, accents, graphics           |
| Torch Red   | `#FF1446` | **CTA buttons only** (isolated action color) |

**Typography:** sans-serif, minimum 16px body, line-height 1.6 unitless.
**Headlines:** ≤ 10 words / 44 characters.
**Touch targets:** ≥ 44×44 px (48×48 px preferred).
**Core Web Vitals targets:** LCP < 2.5s, CLS < 0.1, INP < 200ms.

---

## 4. How to Use This Prompt Library (Workflow)

1. **Start with the 4C framework** — know which section you are writing for (Clarity, Comprehension, Credibility, or Conversion).
2. **Open the matching prompt from §2** and paste it into Claude.
3. **Layer context onto the prompt** — audience role (CPO, CFO, IT lead), industry vertical, and known ROI metrics. Explicit constraints produce sharper output.
4. **Tune the output against brand tokens (§3)** — if copy references colors, numbers, or claims, verify against the Zycus brand guide before shipping.
5. **Never ship a first draft.** Validate with the 5-second headline test (can a stranger describe Zycus in 5 seconds?) and the squint test (does the Torch Red CTA still dominate?).
6. **Personalize post-submit messages** with Elementor field shortcodes so the user sees their own name/email reflected back.

---

## 5. Conversion & Form-Design Principles Encoded in the Prompts

- **Never use the word "Submit."** Use value-linked copy: _"See the Platform in Action"_ or _"Book My Demo"_.
- **First-person CTAs outperform second-person.** _"Book My Demo"_ > _"Book Your Demo"_ — psychological ownership lifts conversion.
- **Form length by intent:**
  - High volume: 1–2 fields (name + email).
  - Standard B2B qualification: 3–4 fields.
  - Enterprise routing: 6–8 fields, broken into a multi-step form.
- **Conditional routing:** company size 500+ → AE's Calendly; <50 → self-serve product tour.
- **Trust microcopy** below the button reduces anxiety (_"No credit card required"_).

---

## 6. Tracking the Conversions the Prompts Are Driving

Elementor forms submit via AJAX, so page-reload tracking misses them. Two validated options:

**Option A — Redirect to Thank You page.** Form action → Redirect → `/thank-you/?form-name=zycus-demo`. Track pageview as GA4 conversion.

**Option B — GTM Element Visibility.** Trigger on CSS selector `.elementor-message.elementor-message-success` with _Observe DOM Changes_ enabled. Fire a `generate_lead` GA4 event and mark it a Key Event.

Verify either path in GA4 DebugView before declaring the page live.

---

## 8. Prompt Engineering Pattern — The Constraint Stack

Every prompt in section 2 follows the same four-layer structure — what I call the **constraint stack**:

1. **Role priming** — "I am building a B2B landing page for Zycus, an AI-powered Source-to-Pay procurement platform." Anchors domain vocabulary and buyer context.
2. **Audience + platform context** — committee buyers (CPO, CFO, IT), enterprise risk posture, Elementor/PHP delivery surface.
3. **Concrete output constraints** — word counts (≤ 10 words), character limits (44 chars), pixel targets (44×44 px taps), hex codes (`#0F3D81`, `#40A4FB`, `#FF1446`), numeric ROI anchors (75%, 80%).
4. **Deliverable shape** — explicit format: headline + sub-headline + 3 trust signals, or 4 steps × (header + one sentence).

Generic prompts ("write a headline for Zycus") return generic output. The constraint stack forces the model to resolve measurable trade-offs inside the response, so drafts land closer to shippable on the first pass and require copy-level — not structural — editing.

---

## 9. Iteration Log — What Was Refined and Why

**CTA copy — "Book Your Demo" vs "Book My Demo".** First-person pronouns transfer psychological ownership to the buyer: the click feels like a decision they have already made, not an instruction being issued to them. "Book My Demo" won and became the standing pattern for every primary CTA on the page.

**FAQ prompt — marketing tone to objection-removal tone.** The first pass (§2.4) produced conversational but promotional answers that re-sold the platform instead of neutralizing doubt. The refined prompt explicitly requested "short, factual, objection-removing" copy scoped to implementation, security, and ERP integration — the three blockers a procurement committee actually escalates.

**Hero headline — benefit-led vs pain-focused.** Two variants were drafted: a benefit frame ("Procurement, automated end-to-end") and a pain frame ("Stop losing 40% of sourcing cycles to manual work"). A 5-second comprehension test favored the benefit-led variant because pain framing required the reader to self-diagnose before the value registered — friction the hero cannot afford.

---

## 10. Audit Checklist (Before Handoff)

- [ ] LCP < 2.5s, CLS < 0.1, INP < 200ms on PageSpeed Insights
- [ ] Hero image NOT lazy-loaded
- [ ] Elementor _Improved Asset Loading_, _Optimized DOM Output_, _Inline Font Icons_ enabled
- [ ] All images served as WebP/AVIF
- [ ] Tab-key navigation reaches every form field with a visible focus outline
- [ ] Body text contrast ≥ 4.5:1; large text ≥ 3:1
- [ ] Exactly one H1 per page; no skipped heading levels
- [ ] Sticky mobile CTA present; tap targets ≥ 44×44 px
- [ ] `generate_lead` event verified in GA4 DebugView
- [ ] Success and server-error custom messages render with dynamic name/email shortcodes

---

_End of document._
