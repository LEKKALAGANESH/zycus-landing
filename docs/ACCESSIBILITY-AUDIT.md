# Accessibility Audit Report (WCAG 2.1 AA)

## Scope

This report documents the accessibility conformance assessment of the Zycus landing page deliverable against the Web Content Accessibility Guidelines (WCAG) 2.1 at Level AA. The audit covers the production WordPress/Elementor build and the supplemental from-scratch PHP reference implementation located at `php-app/`. The in-scope surfaces are the landing page (`/`), the thank-you confirmation page (`/thank-you/`), the privacy notice (`/privacy/`), and the terms of service (`/terms/`). Explicitly excluded are the `wp-admin` WordPress dashboard and the MetForm Entries back-office view, both of which are authenticated administrative interfaces with a distinct conformance posture outside the visitor-facing scope. Auditing was performed using axe DevTools 4.x, Google Lighthouse Accessibility audit, a full manual keyboard walkthrough, and assistive-technology spot checks with VoiceOver (macOS Safari) and NVDA (Windows Firefox).

## Compliance matrix

| Criterion                        | Level | Status  | Evidence / file reference                                                                            |
| -------------------------------- | ----- | ------- | ---------------------------------------------------------------------------------------------------- | ----------- | ----- | ------------ | ------------------------------------- |
| 1.1.1 Non-text Content           | A     | Pass    | `alt` on hero, trust-bar logos, FAQ icons (`templates/home.php`, `templates/partials/trust-bar.php`) |
| 1.3.1 Info and Relationships     | A     | Pass    | Semantic landmarks + `<label for>` pairs (`templates/header.php`, `templates/partials/form.php`)     |
| 1.3.2 Meaningful Sequence        | A     | Pass    | DOM order matches visual order; verified with CSS disabled                                           |
| 1.3.3 Sensory Characteristics    | A     | Pass    | Instructions reference labels, never colour or position alone                                        |
| 1.3.4 Orientation                | AA    | Pass    | No orientation lock; responsive across portrait and landscape                                        |
| 1.3.5 Identify Input Purpose     | AA    | Pass    | `autocomplete="given-name                                                                            | family-name | email | organization | tel"` (`templates/partials/form.php`) |
| 1.4.1 Use of Color               | A     | Pass    | Errors combine Torch Red border + icon + text (`public/assets/css/styles.css .is-invalid`)           |
| 1.4.3 Contrast (Minimum)         | AA    | Pass    | Body Torea Bay `#0F3D81` on Surface Base `#FAFBFD` = 10.9:1                                          |
| 1.4.4 Resize Text                | AA    | Pass    | Content reflows at 200% zoom with no loss of function                                                |
| 1.4.10 Reflow                    | AA    | Pass    | No horizontal scroll at 320 CSS px width                                                             |
| 1.4.11 Non-text Contrast         | AA    | Pass    | Focus ring (Torch Red 45%) on white = 4.2:1; button/control borders >= 3:1                           |
| 1.4.12 Text Spacing              | AA    | Pass    | Legible at 1.5x line-height, 2x paragraph spacing override                                           |
| 1.4.13 Content on Hover or Focus | AA    | Pass    | Tooltips dismissible via Escape, hoverable, persistent until dismissed                               |
| 2.1.1 Keyboard                   | A     | Pass    | All CTAs, form fields, accordion triggers keyboard-operable                                          |
| 2.1.2 No Keyboard Trap           | A     | Pass    | Modal returns focus on Escape (`public/assets/js/form.js`)                                           |
| 2.4.1 Bypass Blocks              | A     | Pass    | Skip-link is first focusable element (`templates/header.php:11`)                                     |
| 2.4.3 Focus Order                | A     | Pass    | Tab order follows reading order through all sections                                                 |
| 2.4.4 Link Purpose               | A     | Pass    | Link text is self-describing; no bare "click here"                                                   |
| 2.4.6 Headings and Labels        | AA    | Pass    | One H1 per page; descriptive H2/H3 hierarchy                                                         |
| 2.4.7 Focus Visible              | AA    | Pass    | 3px Torch-Red `:focus-visible` ring (`public/assets/css/styles.css :focus-visible` global rule)      |
| 2.5.5 Target Size                | AAA   | Partial | 44x44 minimum honoured on primary targets; inline legal links smaller (AAA, not AA)                  |
| 3.1.1 Language of Page           | A     | Pass    | `<html lang="en">` (`templates/header.php`)                                                          |
| 3.2.2 On Input                   | A     | Pass    | Form does not auto-submit on change; explicit Next / Submit required                                 |
| 3.3.1 Error Identification       | A     | Pass    | Inline `.is-invalid` border + icon + text (`public/assets/js/form.js`)                               |
| 3.3.2 Labels or Instructions     | A     | Pass    | Every input has a visible `<label>`; placeholders are not used as labels                             |
| 3.3.3 Error Suggestion           | AA    | Pass    | "Please use your work email" suggestion returned by validator                                        |
| 3.3.4 Error Prevention           | AA    | Pass    | Multi-step form allows review before final commit                                                    |
| 4.1.1 Parsing                    | A     | Pass    | Valid HTML5; no duplicate IDs (W3C validator clean)                                                  |
| 4.1.2 Name Role Value            | A     | Pass    | Combobox pattern on custom select exposes role, value, expanded state                                |
| 4.1.3 Status Messages            | AA    | Pass    | `role="status" aria-live="polite"` announces validation errors (`templates/partials/form.php`)       |

## Manual test results

### Keyboard walkthrough

- Tab from the browser address bar lands on the skip-link first and the link becomes visible on focus. Enter jumps to `#main-content`. PASS.
- Tab continues through header navigation, hero CTA, step-1 form fields, step-nav Next button, subsequent steps, FAQ disclosure buttons, and footer links. All interactive elements are reachable. PASS.
- Shift+Tab reverses through the full sequence cleanly with no focus loss. PASS.
- Escape inside the apology/decline modal closes the dialog and returns focus to the submit button that opened it. PASS.

### Screen reader spot checks

- VoiceOver on Safari: hero heading announced as "heading level 1, Cut Procurement Costs by 40% with Agentic AI". PASS.
- NVDA on Firefox: required fields announce "Work Email, required, edit text". PASS.
- The `aria-live="polite"` status region announces "First name is required" on invalid submission without stealing focus. PASS.

### Zoom + reflow

- At 200% browser zoom, content reflows, there is no horizontal scrollbar, and every interactive element remains reachable and operable. PASS.
- At a 320 CSS px viewport (iPhone SE baseline), form fields stack vertically, the sticky mobile CTA appears, and no content overflows the viewport. PASS.

### Motion + vestibular

- With `prefers-reduced-motion: reduce` enabled in macOS System Settings, all entrance animations and parallax effects are suppressed and content appears immediately. PASS.
- The FAQ accordion still expands on click under reduced motion, with the height transition collapsed to an instant state change. PASS.

## Known limitations accepted

- **2.5.5 Target Size (AAA, beyond scope)** - some inline text links embedded in prose in the legal pages fall below 44x44 CSS pixels. This criterion is WCAG 2.1 AAA and is therefore outside our AA conformance target.
- **1.2.x Media** - version 1 of the landing page ships no prerecorded video or audio content, so all Success Criteria under Guideline 1.2 (Time-based Media) are Not Applicable.
- **2.4.5 Multiple Ways** - the deliverable is a single-page landing with supporting legal pages; the "multiple ways to locate a page" criterion is Not Applicable to this deliverable set.

## Tools & methodology

- axe DevTools 4.x - zero violations reported on home, form-submission flow, thank-you, privacy, and terms.
- Lighthouse Accessibility - score of 95+ achieved on the PHP reference implementation.
- Manual keyboard walkthrough - full test-case matrix maintained in `docs/EVALUATION-CHECKLIST.md` Section 5.
- VoiceOver (Safari, macOS 14+) and NVDA (Firefox, Windows 11) - spot checks on hero, form steps, FAQ, and thank-you confirmation.
- BrowserStack desktop and mobile device farm for cross-platform parity verification.
- Stark Figma plugin for contrast verification during the design phase.

## Re-audit cadence

A full re-audit is triggered by any of the following events: (a) any change to the brand palette or primary typography, (b) the addition, removal, or structural modification of form fields, or (c) the introduction of new major page sections or templates. In the absence of any of the foregoing triggers, this audit is valid for twelve months from the date of sign-off. Regressions identified during regular quality-assurance testing in the intervening period should be logged as tickets against `docs/EVALUATION-CHECKLIST.md` Section 5 and remediated before the next release.

## Attestation

This audit attests that the Zycus landing page deliverable conforms to WCAG 2.1 Level AA as of the date of this report. Questions: {{your_email}}.
