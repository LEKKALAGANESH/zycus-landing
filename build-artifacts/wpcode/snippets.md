# WPCode Lite — Ready-to-Paste Snippets Pack

This pack is designed for the **WPCode Lite** plugin (formerly *Insert Headers and Footers by WPBeginner*). Each snippet below is self-contained and production-ready — copy the code block into a new snippet in **WPCode → Add Snippet → Add Your Custom Code**, configure the *Code Type* and *Insert Location* exactly as noted, then toggle **Active** once you've replaced any placeholders.

---

## Table of Contents

1. [Snippet 1: GTM Head snippet](#snippet-1-gtm-head-snippet)
2. [Snippet 2: GTM Body `<noscript>` snippet](#snippet-2-gtm-body-noscript-snippet)
3. [Snippet 3: GA4 fallback (gtag.js) — only if NOT using GTM](#snippet-3-ga4-fallback-gtagjs--only-if-not-using-gtm)
4. [Snippet 4: Smooth-scroll polyfill with sticky-header offset](#snippet-4-smooth-scroll-polyfill-with-sticky-header-offset)
5. [Snippet 5: Sticky mobile CTA (HTML + CSS)](#snippet-5-sticky-mobile-cta-html--css)
6. [Snippet 6: Elementor/MetForm submit_success → dataLayer push](#snippet-6-elementormetform-submit_success--datalayer-push)
7. [Snippet 7: Prevent hero image from lazy-loading (LCP fix)](#snippet-7-prevent-hero-image-from-lazy-loading-lcp-fix)
8. [Snippet 8: JSON-LD — Organization schema](#snippet-8-json-ld--organization-schema)
9. [Snippet 9: JSON-LD — FAQPage schema](#snippet-9-json-ld--faqpage-schema)
10. [Snippet 10: Preconnect / dns-prefetch for Google Fonts](#snippet-10-preconnect--dns-prefetch-for-google-fonts)
11. [Snippet 11: Disable WordPress emoji script](#snippet-11-disable-wordpress-emoji-script)
12. [Snippet 12: Disable jQuery Migrate](#snippet-12-disable-jquery-migrate)
13. [Snippet 13: Honeypot spam protection for all forms](#snippet-13-honeypot-spam-protection-for-all-forms)
14. [Activation order](#activation-order)

---

## Snippet 1: GTM Head snippet

**Type:** HTML
**Insert location:** Header (Site Wide)
**Priority:** 1
**Why it's needed:** Loads the Google Tag Manager container — the single source of truth for all marketing/analytics tags.

**Code:**

```html
<!-- Google Tag Manager -->
<!-- REPLACE "GTM-KG8889HK" BELOW WITH YOUR REAL CONTAINER ID (find it at https://tagmanager.google.com → Workspace → top-right) -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-KG8889HK');</script>
<!-- End Google Tag Manager -->
```

**Setup notes:**
- Replace `GTM-KG8889HK` with your actual container ID (two occurrences if you count inline — there is only one here).
- Must load as early as possible in `<head>` — leave Priority = 1.
- Pair this with **Snippet 2** (the `<noscript>` iframe) so non-JS visitors are still counted.

---

## Snippet 2: GTM Body `<noscript>` snippet

**Type:** HTML
**Insert location:** Body (start) — immediately after opening `<body>` tag (Site Wide)
**Priority:** 1
**Why it's needed:** Fallback iframe for users with JavaScript disabled so GTM still fires a pageview.

**Code:**

```html
<!-- Google Tag Manager (noscript) -->
<!-- REPLACE "GTM-KG8889HK" BELOW WITH YOUR REAL CONTAINER ID (same one as Snippet 1) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KG8889HK"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
```

**Setup notes:**
- Replace `GTM-KG8889HK` with the **same** container ID used in Snippet 1.
- WPCode Lite offers "Body (start)" as an insert location in the dropdown — pick that.
- If your theme doesn't fire `wp_body_open` (WordPress 5.2+), this snippet will not render. Every maintained theme supports it; test by viewing source.

---

## Snippet 3: GA4 fallback (gtag.js) — only if NOT using GTM

**Type:** HTML
**Insert location:** Header (Site Wide)
**Priority:** 2
**Why it's needed:** Direct GA4 integration for sites that don't use Google Tag Manager.

> **IMPORTANT:** Skip this snippet if you've installed GTM (Snippet 1) — GA4 should be loaded via GTM, not directly. Running both causes double pageviews and inflated metrics.

**Code:**

```html
<!-- Google tag (gtag.js) — GA4 direct install -->
<!-- REPLACE "G-1MG1YKNRDF" WITH YOUR GA4 MEASUREMENT ID (find it at GA4 → Admin → Data Streams → Web) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-1MG1YKNRDF"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-1MG1YKNRDF', {
    anonymize_ip: true,
    send_page_view: true
  });
</script>
```

**Setup notes:**
- Replace **both** occurrences of `G-1MG1YKNRDF` with your GA4 Measurement ID.
- `anonymize_ip` is defaulted on for GDPR-friendly baseline — remove the line if you don't need it.
- Disable this snippet the moment you switch to GTM.

---

## Snippet 4: Smooth-scroll polyfill with sticky-header offset

**Type:** JavaScript
**Insert location:** Footer (Site Wide)
**Priority:** 10
**Why it's needed:** Intercepts in-page anchor clicks (`a[href^="#"]`) and scrolls smoothly with an 80 px top offset so content isn't hidden behind the sticky header. Respects `prefers-reduced-motion`.

**Code:**

```html
<script>
(() => {
  'use strict';
  const OFFSET = 80; // px — bump this if your sticky header is taller
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  document.addEventListener('click', (event) => {
    const link = event.target.closest('a[href^="#"]');
    if (!link) return;

    const hash = link.getAttribute('href');
    if (!hash || hash === '#' || hash.length < 2) return;

    const target = document.querySelector(hash);
    if (!target) return;

    event.preventDefault();
    const top = target.getBoundingClientRect().top + window.pageYOffset - OFFSET;

    window.scrollTo({
      top,
      behavior: prefersReducedMotion ? 'auto' : 'smooth'
    });

    // Update the URL without jumping
    history.pushState(null, '', hash);
  }, { passive: false });
})();
</script>
```

**Setup notes:**
- Adjust the `OFFSET` constant if your sticky header is taller or shorter than 80 px.
- Footer load is intentional — waits until DOM is parsed, no `DOMContentLoaded` wrapper needed.
- Users with OS-level "Reduce motion" enabled get an instant jump instead of smooth scroll.

---

## Snippet 5: Sticky mobile CTA (HTML + CSS)

**Type:** HTML
**Insert location:** Footer (Site Wide) — or limit to specific pages via WPCode's conditional logic
**Priority:** 20
**Why it's needed:** Always-visible "Book Demo" CTA on mobile viewports, anchor-linked to the `#demo-form` section. Drives conversions on long landing pages.

**Code:**

```html
<style>
  .zycus-mobile-cta {
    position: fixed;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 9999;
    padding: 12px 16px;
    background: #ffffff;
    box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.08);
    display: flex;
    justify-content: center;
  }
  .zycus-mobile-cta a {
    display: block;
    width: 100%;
    max-width: 480px;
    padding: 14px 20px;
    text-align: center;
    font-size: 16px;
    font-weight: 600;
    line-height: 1.2;
    color: #ffffff;
    background: #ff5a1f; /* swap for brand accent */
    border-radius: 8px;
    text-decoration: none;
    transition: background-color 0.15s ease-in-out;
  }
  .zycus-mobile-cta a:hover,
  .zycus-mobile-cta a:focus {
    background: #e14e16;
  }
  @media (min-width: 768px) {
    .zycus-mobile-cta { display: none; }
  }
</style>

<div class="zycus-mobile-cta" role="complementary" aria-label="Book a demo">
  <a href="#demo-form">Book Demo</a>
</div>
```

**Setup notes:**
- Brand color is `#ff5a1f` — change the two occurrences to match your palette.
- Target anchor is `#demo-form`; make sure your demo form section/container has `id="demo-form"`.
- If you want this on landing pages only, use WPCode's "Smart Conditional Logic" (Pro) or limit insert location to specific page IDs.
- Add a bottom spacer (`padding-bottom: 72px`) on `<body>` on mobile so the bar doesn't overlap the footer.

---

## Snippet 6: Elementor/MetForm submit_success → dataLayer push

**Type:** JavaScript
**Insert location:** Footer (Site Wide)
**Priority:** 15
**Why it's needed:** Pushes a `formSubmit` event into `dataLayer` the moment an Elementor or MetForm form succeeds, giving GTM a reliable trigger even if Element Visibility doesn't fire (SPA nav, AJAX submits, etc.).

**Code:**

```html
<script>
(() => {
  'use strict';
  window.dataLayer = window.dataLayer || [];

  const pushFormEvent = (formEl, source) => {
    const formId = formEl?.getAttribute('name')
      || formEl?.getAttribute('id')
      || formEl?.dataset?.formId
      || 'unknown';

    window.dataLayer.push({
      event: 'formSubmit',
      form_source: source,     // 'elementor' or 'metform'
      form_id: formId,
      form_location: window.location.pathname
    });
  };

  // Elementor Pro Forms
  document.addEventListener('submit_success', (e) => {
    const form = e.target?.closest('.elementor-form');
    if (form) pushFormEvent(form, 'elementor');
  });

  // MetForm (jQuery-based event)
  document.addEventListener('metform_form_submit_success', (e) => {
    const form = e.target?.closest('.metform-form') || e.target;
    pushFormEvent(form, 'metform');
  });

  // Extra safety net for MetForm (which sometimes dispatches on document only)
  document.addEventListener('submit_success', (e) => {
    const form = e.target?.closest('.metform-form');
    if (form) pushFormEvent(form, 'metform');
  });
})();
</script>
```

**Setup notes:**
- In GTM, create a **Custom Event** trigger with Event name = `formSubmit`, then map `form_source` / `form_id` / `form_location` to DataLayer Variables for richer reporting.
- If you only use one form plugin, you can safely delete the other listener to keep the snippet lean.
- Works alongside (not instead of) GTM's native Element Visibility trigger — it's a belt-and-braces approach.

---

## Snippet 7: Prevent hero image from lazy-loading (LCP fix)

**Type:** JavaScript
**Insert location:** Header (Site Wide) — runs early, before layout
**Priority:** 3
**Why it's needed:** WordPress 5.5+ auto-injects `loading="lazy"` onto all images, which sabotages LCP when the hero image is above the fold. This removes the attribute from any `<img class="no-lazy">`.

**How to use the class:** In Elementor, edit the hero Image widget → **Advanced** tab → **CSS Classes** field → enter `no-lazy`. For Gutenberg, use the block's **Additional CSS class(es)** field.

**Code:**

```html
<script>
(() => {
  'use strict';
  const unlazy = () => {
    document.querySelectorAll('img.no-lazy').forEach((img) => {
      img.removeAttribute('loading');
      // Hint the browser to prioritize it
      img.setAttribute('fetchpriority', 'high');
      img.setAttribute('decoding', 'async');
    });
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', unlazy, { once: true });
  } else {
    unlazy();
  }
})();
</script>
```

**Setup notes:**
- Only add the `no-lazy` class to **one** image per page (the LCP element). Removing lazy-load from multiple images hurts overall performance.
- `fetchpriority="high"` is a modern Chromium hint and is safely ignored by other browsers.
- Combine with Snippet 10 (preconnect) for max LCP improvement.

---

## Snippet 8: JSON-LD — Organization schema

**Type:** HTML
**Insert location:** Header (Site Wide)
**Priority:** 30
**Why it's needed:** Gives Google a structured profile of the organization (brand, logo, socials, contact) — unlocks knowledge-panel enrichment and richer SERP cards.

**Code:**

```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "Zycus",
  "url": "https://www.zycus.com/",
  "logo": "https://www.zycus.com/wp-content/uploads/logo.png",
  "sameAs": [
    "https://www.linkedin.com/company/zycus",
    "https://twitter.com/zycus"
  ],
  "contactPoint": [{
    "@type": "ContactPoint",
    "contactType": "sales",
    "email": "sales@zycus.com",
    "availableLanguage": ["English"]
  }]
}
</script>
```

**Setup notes:**
- Replace the `logo` URL with the production URL of your actual logo (PNG/SVG, min 112×112 px).
- Update the `sameAs` array with your real LinkedIn / Twitter / YouTube / etc. profiles.
- Replace `sales@zycus.com` if you have a dedicated inbound inbox (e.g. `demo@zycus.com`).
- Validate with Google's Rich Results Test before going live.

---

## Snippet 9: JSON-LD — FAQPage schema

**Type:** HTML
**Insert location:** Specific Pages — attach to the landing page (or any page that visibly displays these 4 FAQs)
**Priority:** 31
**Why it's needed:** FAQPage markup makes answer accordions eligible for expandable FAQ rich results in Google search. Only insert on pages that **visibly** show the same Q&A — inserting elsewhere violates Google's policy.

**Code:**

```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "Will Zycus integrate with our existing ERP (SAP, Oracle, NetSuite)?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Yes. Zycus ships with certified, pre-built connectors for SAP S/4HANA, SAP ECC, Oracle Fusion, Oracle EBS, NetSuite, Microsoft Dynamics, and Workday. Most integrations go live in under 4 weeks and sync invoices, POs, master data, and GL codes bi-directionally."
      }
    },
    {
      "@type": "Question",
      "name": "How long does implementation actually take?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "A typical mid-market rollout of Source-to-Contract goes live in 8–12 weeks. Full Source-to-Pay across multiple business units is usually 16–20 weeks. Our implementation team handles configuration, integration, and user enablement — your team focuses on process design."
      }
    },
    {
      "@type": "Question",
      "name": "How is our procurement data kept secure?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Zycus is SOC 2 Type II, ISO 27001, and ISO 27701 certified. All data is encrypted in transit (TLS 1.3) and at rest (AES-256), hosted in your choice of AWS region with full data residency controls. We're GDPR, CCPA, and HIPAA compliant."
      }
    },
    {
      "@type": "Question",
      "name": "What kind of ROI can we actually expect?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Customers typically report 40–60% reduction in cycle times, 5–8% savings on addressable spend, and 70%+ reduction in manual invoice handling within 12 months. We'll share a custom ROI model during your demo based on your current spend and team size."
      }
    }
  ]
}
</script>
```

**Setup notes:**
- In WPCode, set "Insert Method" = *Auto Insert*, "Location" = *Page Specific → Insert on Specific Pages*, and pick the landing page.
- The same 4 questions and answers **must be visible on the page** (e.g. in an accordion or list) — otherwise Google flags it as hidden content.
- Validate at https://search.google.com/test/rich-results after publishing.

---

## Snippet 10: Preconnect / dns-prefetch for Google Fonts

**Type:** HTML
**Insert location:** Header (Site Wide) — as high as possible, ideally Priority 2–3
**Priority:** 2
**Why it's needed:** Opens the TCP + TLS handshake to Google Fonts origins before the CSS parser discovers `@font-face` URLs, cutting ~100–300 ms off font load time and reducing CLS.

**Code:**

```html
<!-- Google Fonts preconnect -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="dns-prefetch" href="https://fonts.googleapis.com">
<link rel="dns-prefetch" href="https://fonts.gstatic.com">
```

**Setup notes:**
- `crossorigin` on the `fonts.gstatic.com` preconnect is required — font files are fetched in anonymous CORS mode.
- If you self-host fonts (recommended for GDPR compliance in the EU), disable this snippet and use your own CDN origin instead.
- Safe to run alongside Elementor / Astra / GeneratePress which inject their own `preconnect` tags — duplicates are harmless.

---

## Snippet 11: Disable WordPress emoji script

**Type:** PHP Snippet
**Insert location:** Run Everywhere (Site Wide)
**Priority:** 10
**Why it's needed:** WordPress auto-loads `wp-emoji-release.min.js` (~14 KB) on every page to normalize emoji rendering. Modern browsers render emoji natively — removing this saves a network request and parse/exec time.

**Code:**

```php
<?php
/**
 * Remove WordPress auto-injected emoji script + styles.
 * Saves ~14 KB + one HTTP request on every page load.
 */
add_action( 'init', 'zycus_disable_wp_emojis' );
function zycus_disable_wp_emojis() {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

    // Also strip the TinyMCE plugin reference.
    add_filter( 'tiny_mce_plugins', 'zycus_disable_emojis_tinymce' );

    // And kill the DNS prefetch that WP adds for s.w.org.
    add_filter( 'wp_resource_hints', 'zycus_disable_emojis_dns_prefetch', 10, 2 );
}

function zycus_disable_emojis_tinymce( $plugins ) {
    return is_array( $plugins ) ? array_diff( $plugins, array( 'wpemoji' ) ) : array();
}

function zycus_disable_emojis_dns_prefetch( $urls, $relation_type ) {
    if ( 'dns-prefetch' === $relation_type ) {
        $emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/' );
        $urls = array_diff( $urls, array( $emoji_svg_url ) );
    }
    return $urls;
}
```

**Setup notes:**
- In WPCode, choose *Code Type: PHP Snippet* — the opening `<?php` tag is automatically handled by WPCode, so you can either include it (as shown, WPCode will ignore duplicates) or delete the first line.
- Users on Windows 7 / older Android may see text replacements instead of emoji — acceptable trade-off in 2026.
- If you edit posts with lots of emoji, re-enable temporarily or use the 📋 block.

---

## Snippet 12: Disable jQuery Migrate

**Type:** PHP Snippet
**Insert location:** Run Everywhere (Site Wide)
**Priority:** 10
**Why it's needed:** `jquery-migrate.min.js` is a ~10 KB compatibility shim for jQuery 3.x syntax that WordPress still ships for legacy plugin support. Modern plugins don't need it.

> **WARNING — test thoroughly.** Some older plugins (particularly form builders, sliders, and page builders from pre-2021) still depend on jQuery Migrate. Disable this snippet immediately if your forms, carousels, or admin UI break.

**Code:**

```php
<?php
/**
 * Dequeue jQuery Migrate on the front end.
 * Saves ~10 KB + one HTTP request. TEST ALL FORMS/SLIDERS BEFORE GOING LIVE.
 */
add_action( 'wp_default_scripts', 'zycus_remove_jquery_migrate' );
function zycus_remove_jquery_migrate( $scripts ) {
    if ( is_admin() ) {
        return; // Leave admin untouched — many admin plugins still use it.
    }
    if ( ! empty( $scripts->registered['jquery'] ) ) {
        $jquery_dependencies = $scripts->registered['jquery']->deps;
        $scripts->registered['jquery']->deps = array_diff(
            $jquery_dependencies,
            array( 'jquery-migrate' )
        );
    }
}
```

**Setup notes:**
- Walk through your site after activation: homepage, landing page, every form, every slider, every modal, WooCommerce checkout if applicable.
- Keep it disabled in staging for a week before activating in production.
- If you run JS error tracking (Sentry etc.), watch for `$(...).live is not a function` or similar — that's the tell-tale sign migrate was needed.

---

## Snippet 13: Honeypot spam protection for all forms

**Type:** JavaScript
**Insert location:** Footer (Site Wide)
**Priority:** 25
**Why it's needed:** Adds a hidden `website_url` field to every form. Bots auto-fill all fields and trip it; humans never see it. Lightweight alternative to reCAPTCHA — no external request, no privacy concerns, no friction.

**Code:**

```html
<script>
(() => {
  'use strict';
  const HONEYPOT_NAME = 'website_url';

  const injectHoneypot = (form) => {
    if (form.querySelector(`input[name="${HONEYPOT_NAME}"]`)) return;
    const field = document.createElement('input');
    field.type = 'text';
    field.name = HONEYPOT_NAME;
    field.tabIndex = -1;
    field.autocomplete = 'off';
    field.setAttribute('aria-hidden', 'true');
    field.style.cssText = 'display:none !important;position:absolute;left:-9999px;opacity:0;pointer-events:none;';
    form.appendChild(field);
  };

  const attach = (form) => {
    injectHoneypot(form);
    form.addEventListener('submit', (event) => {
      const field = form.querySelector(`input[name="${HONEYPOT_NAME}"]`);
      if (field && field.value.trim() !== '') {
        // Bot detected — silently abort.
        event.preventDefault();
        event.stopImmediatePropagation();
        console.warn('[honeypot] Form submission blocked (bot suspected).');
      }
    }, true); // capture phase so we beat other listeners
  };

  const wireAll = () => {
    document.querySelectorAll('form').forEach(attach);
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', wireAll, { once: true });
  } else {
    wireAll();
  }

  // Handle forms injected dynamically (Elementor Popups, AJAX loads, etc.)
  const observer = new MutationObserver((mutations) => {
    mutations.forEach((m) => {
      m.addedNodes.forEach((node) => {
        if (node.nodeType !== 1) return;
        if (node.tagName === 'FORM') attach(node);
        node.querySelectorAll?.('form').forEach(attach);
      });
    });
  });
  observer.observe(document.body, { childList: true, subtree: true });
})();
</script>
```

**Setup notes:**
- The field name `website_url` is deliberately innocuous — bots love filling URL-like fields. If your real form already has a field called `website_url`, rename the constant at the top.
- Blocked submissions are silent to bots and logged to the browser console (`console.warn`) for debugging.
- Not a replacement for rate-limiting or email verification on truly high-value forms — layer defenses.
- Make sure your server-side form handler does **not** reject forms containing the honeypot field (it shouldn't, it just stays hidden).

---

## Activation Order

Enable snippets in this order and test after each one:

1. **Snippet 10 — Preconnect to fonts.** Pure `<link>` tags; zero risk, immediate benefit.
2. **Snippet 1 — GTM Head** + **Snippet 2 — GTM Body.** Verify with Tag Assistant that `dataLayer` is available and pageview fires.
3. **Snippet 3 — GA4 fallback** (ONLY if you're not using GTM — otherwise skip permanently).
4. **Snippet 8 — Organization JSON-LD.** Validate in Google Rich Results Test.
5. **Snippet 9 — FAQPage JSON-LD.** Make sure the 4 FAQs are actually rendered on the page first.
6. **Snippet 7 — LCP lazy-load fix.** Add `no-lazy` class to the hero image in Elementor; measure LCP in PageSpeed Insights before/after.
7. **Snippet 4 — Smooth scroll** + **Snippet 5 — Sticky mobile CTA.** UX polish; test on real mobile device.
8. **Snippet 6 — Form → dataLayer.** Submit a test form; confirm `formSubmit` event appears in GTM Preview → DataLayer tab.
9. **Snippet 13 — Honeypot.** Submit a form normally (should succeed), then fill the hidden field via DevTools and submit (should be blocked).
10. **Snippet 11 — Disable emojis.** Low risk; verify no emoji is unexpectedly replaced.
11. **Snippet 12 — Disable jQuery Migrate.** HIGH RISK — enable in staging first, walk the entire site, monitor JS console for a week.

**Caveats to re-read before activating anything:**
- Snippets 1, 2, 3, 8, 9 contain placeholders (`GTM-KG8889HK`, `G-1MG1YKNRDF`, logo URL, social URLs) that MUST be replaced or Google will ignore / mis-report.
- Snippet 3 is mutually exclusive with Snippets 1+2 — never run both.
- Snippet 9 requires the 4 FAQ questions+answers to be visibly rendered on the target page.
- Snippet 12 is the only one with real breakage risk — treat it like a production deploy.
