# Evaluation Checklist

A 12-minute self-audit for the Zycus landing-page deliverable. Every item is a concrete, observable assertion — check it, tick it, move on. If any box fails, reference the matching stage in `ELEMENTOR-BUILD-GUIDE.md`.

**How to use:** open the live URL in one tab, GA4 Realtime in a second, GTM Preview in a third. Work top-to-bottom.

---

## 1. Functional

- [ ] Form Step 1 → Step 2 transition works; required fields block advance when empty.
- [ ] Work email validation rejects free domains (`gmail.com`, `yahoo.com`, `outlook.com`) with inline error.
- [ ] Form submit returns HTTP 200 and completes in under 1.5 seconds on a cold request.
- [ ] On successful submit, browser redirects to `/thank-you/` (not an AJAX inline message).
- [ ] Lead notification email arrives at the configured inbox within 60 seconds, with all 8 form fields present.
- [ ] Auto-responder email (if enabled) arrives with correct `From` name and clears SPF + DKIM.
- [ ] `/thank-you/` page renders with the S2P Diagnostic Guide off-ramp and fires `demo_confirmed` on load.
- [ ] Personalised thank-you copy renders the submitter's first name and email via MetForm shortcodes.

## 2. Design system

- [ ] Elementor Global Colors: Torea Bay `#0F3D81`, Dodger Blue `#40A4FB`, Torch Red `#FF1446`, Surface Base `#FAFBFD`, Surface Alt `#F4F7FB`, Ink Subtle `#4A5B7A`.
- [ ] Elementor Global Typography: Primary + Secondary both `Inter`, weights 400/500/600/700 loaded.
- [ ] H1 is 10 words or fewer and uses Torea Bay (not pure black, not the accent blue).
- [ ] No element uses `#FFFFFF` as page background — surfaces are `#FAFBFD` or `#F4F7FB`.
- [ ] Torch Red appears only on: primary CTA buttons, required-field asterisks, and focus outlines. No red icons, no red text, no red borders elsewhere.
- [ ] Body copy line-height ≥ 1.6; H1 line-height ≤ 1.2.
- [ ] Brain logo alt text is exactly `"Zycus AI Procurement Brain Logo"`.

## 3. Responsiveness

- [ ] Desktop (1440px): hero headline on two lines max, CTA visible above the fold.
- [ ] Tablet (1024px): form stacks correctly, nav collapses to hamburger at ≤ 1024px.
- [ ] Mobile (375px): no horizontal scroll at any scroll position.
- [ ] All tap targets (buttons, nav links, form fields, FAQ toggles) measure ≥ 44×44 CSS px.
- [ ] Form is fully usable on mobile: inputs are ≥ 16px font-size (no iOS zoom-on-focus), step progress visible.
- [ ] FAQ accordions open and close smoothly on mobile with no content clip or layout shift.

## 4. Tracking

- [ ] GTM container `GTM-KG8889HK` is published at version 1 or higher (check Versions tab, not Workspace).
- [ ] GA4 property `G-1MG1YKNRDF` is linked inside the GTM container and fires a pageview on landing.
- [ ] `generate_lead` event appears in GA4 Realtime within 30 seconds of a test form submission.
- [ ] `demo_confirmed` event fires on `/thank-you/` page load and appears in GA4 Realtime.
- [ ] Yoast SEO is active; the landing page has a custom meta title (≤ 60 chars) and description (≤ 155 chars).
- [ ] FAQPage schema validates clean in Google Rich Results Test — all Q&A pairs present, no errors.

## 5. Accessibility & performance

- [ ] Lighthouse mobile: Performance ≥ 85, Accessibility ≥ 95, Best Practices ≥ 95, SEO = 100.
- [ ] axe DevTools: zero violations on home, form, thank-you, FAQ.
- [ ] Keyboard-only: Tab reveals a 3px Torch-Red focus ring on every interactive element.
- [ ] `prefers-reduced-motion: reduce` (OS or DevTools emulation) — no entrance animations trigger.
- [ ] Skip-link ("Skip to main content") is the first focusable element.
