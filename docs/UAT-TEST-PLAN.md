# UAT Test Plan

Zycus Agentic AI Landing Page — User Acceptance Testing Test Plan. This document defines the acceptance tests the client's QA team will execute on the staging environment to validate the deliverable before go-live sign-off. Each test case below is authored to be objectively pass/fail; no subjective judgement is required to adjudicate a result.

## Scope

In scope for UAT: the public-facing home page (`/`), the post-submission thank-you page (`/thank-you/`), the Privacy Policy page (`/privacy/`), the Terms of Service page (`/terms/`), the end-to-end demo form submission flow (including email notification delivery and database persistence), GA4 event firing via the published GTM container, responsive rendering at the four reference breakpoints (1440 px, 1024 px, 767 px, 375 px), and keyboard-only navigation across all interactive elements. Both the primary WordPress + Elementor Free build and the supplemental PHP reference implementation are in scope and must pass the same cases unless a case is explicitly marked WP-only or PHP-only.

Out of scope: the MetForm Entries back-office UX for WordPress editors, the WordPress `/wp-admin` authoring UX, host-level infrastructure (DNS propagation, TLS certificate issuance, CDN cache purging), and any analytics dashboard configuration downstream of GA4 DebugView.

## Environments

| Env | URL | Purpose | Access |
| --- | --- | --- | --- |
| Local dev | `http://localhost:8000` | Developer smoke tests | Vendor only |
| Staging (InstaWP) | `https://<sandbox>.instawp.xyz` | UAT | Client + vendor |
| Production | `https://demo.zycus.com` (TBD) | Go-live | Post-signoff |

## Entry criteria

All of the following must be true before the UAT window opens:

- Staging URL resolves over HTTPS with a valid, untrusted-free certificate.
- GTM container `GTM-KG8889HK` is published to the Live environment.
- GA4 property `G-1MG1YKNRDF` is linked to the GTM container and is firing `page_view` on every page load.
- SMTP credentials are configured and a test submission successfully delivers an email to `sales@zycus.com`.
- Privacy Policy and Terms of Service pages are populated with the final approved legal copy — not lorem-ipsum or stub content.
- All brand assets are uploaded: brain logo (SVG), 13 client logos, Open Graph social share image (1200x630).

## Test case matrix

### Suite A — Content & copy (6 cases)

| ID | Title | Preconditions | Steps | Expected result | Priority |
| --- | --- | --- | --- | --- | --- |
| A-01 | Hero H1 exact match | Home loaded | Inspect hero section | H1 text equals "Cut Procurement Costs by 40% with Agentic AI"; no typos; rendered in Torea Bay | Major |
| A-02 | Subhead matches approved copy | Home loaded | Compare subhead text to `build-artifacts/copy/zycus-landing-copy.md` | Verbatim match | Major |
| A-03 | Primary CTA label consistent | Home loaded | Read CTA label in hero, sticky mobile bar, and form submit button | All three read "Book My Demo" | Blocker |
| A-04 | FAQ copy verbatim | Home loaded | Read each of the four FAQ Q&As | All four match the approved copy verbatim | Major |
| A-05 | Thank-you H1 | Successful submission | Inspect `/thank-you/` H1 | Text equals "Demo Request Confirmed!" (with exclamation) | Major |
| A-06 | Submit microcopy | Home loaded | Read text under the form submit button | Reads "Secure & confidential. We respect your inbox — no spam, no credit card required." | Minor |

### Suite B — Form functional (8 cases)

| ID | Title | Preconditions | Steps | Expected result | Priority |
| --- | --- | --- | --- | --- | --- |
| B-01 | Empty submit validation | Home loaded | Click submit on an empty form | Inline errors appear on Work Email, First Name, Last Name; keyboard focus moves to Work Email | Blocker |
| B-02 | Free-email rejection | Home loaded | Enter `user@gmail.com` in Work Email; submit | Inline error reads "Please use your work email"; submission is blocked | Blocker |
| B-03 | Mid-market routing | Valid form data | Set `company_size=mid`; submit | Browser redirects to `/thank-you/?form=zycus_demo` | Blocker |
| B-04 | Enterprise routing | Valid form data | Set `company_size=enterprise`; submit | Browser redirects to the configured Calendly URL | Blocker |
| B-05 | Persistence | Valid submission made | Query MetForm Entries (WP) or `submissions` table (PHP) | Row exists containing all 8 field values | Major |
| B-06 | Notification email | Valid submission made | Check `sales@zycus.com` inbox | Email arrives within 60 seconds; all 8 fields rendered in body | Major |
| B-07 | Rate limiting | Clean IP | Submit valid payload 12 times in under 60 seconds from same IP | 12th response returns HTTP 429 | Major |
| B-08 | CSRF replay | Two tabs open | Open form in tab A and tab B; submit A using B's nonce | Server rejects with HTTP 403 | Major |

### Suite C — Tracking (4 cases)

| ID | Title | Preconditions | Steps | Expected result | Priority |
| --- | --- | --- | --- | --- | --- |
| C-01 | `generate_lead` fires | GTM Preview attached | Submit a valid form | `generate_lead` event visible in GTM Preview with parameters `form_id`, `lead_tier`, `email_type` populated | Blocker |
| C-02 | `demo_confirmed` fires | GTM Preview attached | Load `/thank-you/` after a submission | `demo_confirmed` event fires with `tier` and `personalized` parameters populated | Blocker |
| C-03 | GA4 DebugView echoes events | GA4 DebugView open | Submit a valid form | Both events appear in DebugView within 30 seconds | Major |
| C-04 | No double `page_view` | GA4 DebugView open | Submit and land on `/thank-you/` | Exactly one `page_view` is recorded for `/thank-you/` | Major |

### Suite D — Responsiveness (5 cases)

Execute each case using Chrome DevTools device toolbar.

| ID | Title | Viewport | Expected result | Priority |
| --- | --- | --- | --- | --- |
| D-01 | Desktop hero | 1440 px | Hero renders in 2-column layout; primary CTA visible above the fold | Major |
| D-02 | Tablet form | 1024 px | Tablet breakpoint engaged; form fields remain usable and not clipped | Major |
| D-03 | Mobile no h-scroll | 767 px | No horizontal scroll anywhere from top of page to footer | Blocker |
| D-04 | iOS input zoom | 375 px | All form input fields computed font-size is at least 16 px | Major |
| D-05 | Smallest viewport | 320 px | Page renders; content reflows without overlap or clipping | Minor |

### Suite E — Accessibility (5 cases)

| ID | Title | Tool | Expected result | Priority |
| --- | --- | --- | --- | --- |
| E-01 | Lighthouse a11y | Lighthouse mobile | Accessibility score is 95 or higher | Blocker |
| E-02 | axe violations | axe DevTools | Zero violations on home, form panel, and thank-you | Blocker |
| E-03 | Keyboard navigation | Keyboard only | Tab lands on skip-link first; Enter skips to main; every interactive element reachable; Torch-Red focus ring visible | Major |
| E-04 | Screen reader | VoiceOver or NVDA | Hero H1 announced; required fields announce "required"; inline error region announces errors | Major |
| E-05 | Reduced motion | OS flag or DevTools Rendering | With `prefers-reduced-motion: reduce`, all animations are disabled | Major |

### Suite F — Performance (3 cases)

| ID | Title | Tool | Expected result | Priority |
| --- | --- | --- | --- | --- |
| F-01 | Lighthouse performance | Lighthouse mobile | Performance score is 85 or higher | Major |
| F-02 | Core Web Vitals | PageSpeed Insights mobile | LCP under 2.5 s; CLS under 0.1; INP under 200 ms | Major |
| F-03 | Clean network | Chrome DevTools Network | No 4xx or 5xx responses observed on a cold load | Minor |

## Exit criteria

All of the following must be true before UAT sign-off:

- All Blocker cases: PASS.
- All Major cases: PASS (one failed Major with an approved written waiver is tolerable).
- At least 90 percent of Minor cases: PASS.
- axe DevTools reports zero violations on home, form, and thank-you.
- Lighthouse mobile Accessibility is at least 95; Performance is at least 85; SEO is 100.
- GA4 Realtime view shows three distinct test `generate_lead` events originating from three separate sessions, proving the tracking pipeline works across sessions rather than only in the current one.

## Defect reporting

Raise all UAT defects as GitHub Issues against `https://github.com/LEKKALAGANESH/zycus-landing/issues` with the label `uat`. Required fields on every issue: test case ID, environment, browser name and version, numbered steps to reproduce, expected vs. actual outcome, and a screenshot or screencast. Defects will be triaged within 4 business hours during the UAT window. Blocker defects will be fixed and re-deployed to staging within 1 business day; Major defects within 3 business days; Minor defects are scheduled for the post-launch maintenance cycle unless the client requests otherwise.

## Sign-off

> UAT sign-off
>
> Approved by: ___________________ (Client QA Lead) Date: _____________
>
> Approved by: ___________________ (Client Marketing Ops) Date: _____________
>
> Approved by: ___________________ (Vendor Delivery Lead) Date: _____________
