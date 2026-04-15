# Analytics Events Dictionary

## Overview

Measurement on the Zycus landing page is wired through a single aggregation point: Google Tag Manager container `GTM-KG8889HK`. Both the from-scratch PHP build and the Elementor/MetForm WordPress variant push identical event payloads onto `window.dataLayer`; GTM triggers resolve those pushes and fire the GA4 configuration tag (measurement ID `G-1MG1YKNRDF`). GA4 is the reporting source of truth — no events are sent directly from the page to GA4, and no server-side Measurement Protocol calls are in scope for v1. The PHP reference implementation pushes `generate_lead` from `public/assets/js/form.js` on a 200 OK submit, and `demo_confirmed` from an inline script in `public/thank-you.php`. The WP variant replicates these pushes through WPCode snippets documented in `build-artifacts/wpcode/snippets.md`.

Naming convention: where GA4 publishes a recommended event name we use it verbatim (`generate_lead`, `page_view`). Anything else is a custom event in snake_case (`demo_confirmed`, `cta_click`, `faq_open`). All parameter keys are snake_case. Parameter values are strings unless explicitly numeric (`value`, `step_index`, `faq_index`). Booleans are serialised as lowercase strings (`"true"`, `"false"`) to match GA4 custom-dimension constraints.

## Event catalogue

| Event | Trigger | Source | GA4 category | Parameters |
|---|---|---|---|---|
| `page_view` | Every page load | GA4 config tag (auto) | Engagement | `page_location`, `page_title`, `page_referrer` |
| `generate_lead` | Form submit returns 200 OK | `form.js:160` (PHP) / MetForm success element visible (WP) | Recommended — Lead | `form_id`, `form_location`, `value`, `currency`, `lead_tier`, `email_type` |
| `demo_confirmed` | Thank-you page load | `thank-you.php:59` (PHP) / GTM Page View trigger, path contains `/thank-you/` (WP) | Custom | `form_id`, `tier`, `personalized` |
| `form_step_advance` | Next-button click on multi-step form | `form.js` step-nav handler (v2 — not yet wired) | Custom | `form_id`, `step_index`, `step_label` |
| `cta_click` | Click on any hero / sticky / footer demo CTA | Inline handler, anchor-scroll to `#demo-form` | Custom | `cta_text`, `cta_location` |
| `faq_open` | FAQ accordion item expanded | FAQ component click handler | Custom | `faq_question`, `faq_index` |
| `apology_modal_shown` | PHP 503 or network error surfaces the Connection Interrupted modal | `form.js` `showApologyModal()` | Custom | `error_type`, `form_id` |

### Sample dataLayer payloads

```js
window.dataLayer.push({
  event: 'generate_lead',
  form_id: 'zycus_demo',
  form_location: window.location.pathname,
  value: 500,
  currency: 'USD',
  lead_tier: 'enterprise',
  email_type: 'work'
});
```

```js
window.dataLayer.push({
  event: 'demo_confirmed',
  form_id: 'zycus_demo',
  tier: 'standard',
  personalized: 'true'
});
```

```js
window.dataLayer.push({
  event: 'form_step_advance',
  form_id: 'zycus_demo',
  step_index: 1,
  step_label: 'about_you'
});
```

```js
window.dataLayer.push({
  event: 'cta_click',
  cta_text: 'Book My Demo',
  cta_location: 'hero'
});
```

```js
window.dataLayer.push({
  event: 'faq_open',
  faq_question: 'How long does implementation actually take?',
  faq_index: 2
});
```

```js
window.dataLayer.push({
  event: 'apology_modal_shown',
  error_type: 'database',
  form_id: 'zycus_demo'
});
```

`lead_tier` is derived from the `company_size` select: `small` (1–49) → `small`, `mid` (50–499) → `mid`, `enterprise` (500–4,999) → `enterprise`, `large_enterprise` (5,000+) → `large_enterprise`. `email_type` is set by the same free-email validator that flags `gmail`/`yahoo`/`hotmail`/`outlook`/`icloud` domains. `personalized` is `true` when the thank-you page received first-name + email through the session from the submit.

## Event-to-GA4-parameter mapping

Register each custom parameter in **GA4 → Admin → Custom definitions** before launch. Unregistered parameters are dropped from reports after 24 hours.

| dataLayer key | GA4 custom dimension | Scope |
|---|---|---|
| `lead_tier` | Lead Tier | event |
| `email_type` | Email Type | event |
| `tier` | Confirmation Tier | event |
| `personalized` | Personalized Confirmation | event |
| `cta_location` | CTA Location | event |
| `faq_question` | FAQ Question | event |
| `error_type` | Error Type | event |

## Conversion configuration

Mark `generate_lead` as a conversion in GA4 → Admin → Events. Do **not** mark `demo_confirmed` — it is a downstream confirmation of the same lead and double-marking will inflate conversion counts in Google Ads and GA4 attribution reports. Configure two audiences: **"Form starters"** (session scroll depth > 50% AND the `#demo-form` section entered the viewport for more than 2 seconds) and **"Lead qualified — enterprise"** (users who fired `generate_lead` with `lead_tier` in `enterprise` or `large_enterprise`). The enterprise audience is the handoff list to the SDR team via the GA4 → Google Ads audience export.

## UTM + attribution

- Honour `utm_source`, `utm_medium`, `utm_campaign`, `utm_content`, `utm_term` — GA4 parses these natively on `page_view`.
- Capture UTMs on first touch into a hidden MetForm field `utm_json` (optional; requires WPCode snippet #4 from `build-artifacts/wpcode/snippets.md`).
- Stamp `lead_source=<utm_source>` on the lead record in the WP DB / PHP `submissions` table for SDR routing and offline-conversion import.

## Debug + QA checklist

- Install the **Google Tag Assistant** Chrome extension.
- Load the site with the GTM Preview link from the Workspace (do not hand-craft `?debug_mode=true`).
- Open GA4 → Admin → DebugView — both `generate_lead` and `demo_confirmed` must appear within 30 seconds of a test submission, with all parameters populated.
- Confirm no double-fire of `page_view` on `/thank-you/` — MetForm redirect combined with the GTM page-view trigger is a known gotcha; suppress one or the other.
- Confirm the GTM container version is published to **Live**, not merely saved in the Workspace.

## Consent & privacy

All events are deferred until the CookieYes / Complianz consent banner records a "marketing" accept. `window.dataLayer` continues to queue pushes before acceptance so no events are lost; the GTM container load snippet itself is gated by the consent plugin, so nothing is transmitted to Google until consent is granted. GA4 is configured with IP anonymisation enabled and `allow_google_signals=false` for GDPR-strict jurisdictions (EU, UK, CH). Data retention is set to 14 months.

All event specs are backed by the code in `public/assets/js/form.js`, `public/thank-you.php`, and `build-artifacts/gtm/GTM-zycus-landing.json`. Any drift between this document and those files is a defect to file.
