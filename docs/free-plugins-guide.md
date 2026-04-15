# Zycus Landing Page — Free Tools & Plugins Guide

A complete zero-cost substitution guide for the Zycus WordPress + Elementor landing page build. Every paid item from the original plan is mapped to a verified free alternative, with author names, exact slugs, and disambiguation tips so you install the right plugin from the WordPress directory (which is full of lookalikes).

---

## Part 1 — Free Substitutes for Every Paid Item

| Plan item                              | Paid version                           | Free substitute                                                                                                                                                                    |
| -------------------------------------- | -------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Hosting**                            | Hostinger / Kinsta ($)                 | **InfinityFree**, **000webhost**, or **AwardSpace** — free WP hosting with subdomain. Or run **LocalWP** (free desktop app) to build offline first.                                |
| **Domain**                             | $10/yr                                 | Free subdomain from the host (e.g., `zycus.infinityfreeapp.com`), or alternatives like **`.eu.org`**.                                                                              |
| **Elementor Pro**                      | $59/yr (needed for Forms + multi-step) | **Elementor Free** + **Essential Addons for Elementor (Free)** + **MetForm Free** (multi-step forms + conditional logic) + **Happy Addons Free**. Together they cover ~90% of Pro. |
| **Cool FormKit** (conditional routing) | freemium                               | **MetForm Free** has conditional logic + redirects built in.                                                                                                                       |
| **LiteSpeed Cache**                    | free already                           | Keep — it's free.                                                                                                                                                                  |
| **ShortPixel** (image WebP)            | 100 free/month                         | **Converter for Media** plugin — unlimited free WebP/AVIF conversion.                                                                                                              |
| **WPCode / Custom Code**               | freemium                               | **WPCode Lite** (free) — handles GTM head/body injection.                                                                                                                          |
| **Rank Math SEO**                      | free tier sufficient                   | Keep — free tier covers everything in the plan.                                                                                                                                    |
| **GA4 + GTM**                          | free already                           | Keep.                                                                                                                                                                              |
| **Stock photos** (testimonials)        | Unsplash                               | **Unsplash** + **Pexels** + **generated.photos** (free AI faces — clearly label as illustrative).                                                                                  |
| **Logos**                              | client supplies                        | Use placeholder grayscale boxes labeled "Client Logo" until client approves real ones — never scrape.                                                                              |
| **Calendly** (enterprise routing)      | free tier                              | **Cal.com** (open-source, free unlimited) or **Calendly Free** (1 event type — enough for demos).                                                                                  |
| **AI copy generation**                 | Claude Pro                             | **Claude.ai free tier** (run the prompts from research.txt — copy is already drafted in your plan, so you don't even need this).                                                   |

---

## Part 2 — Fastest 100% Free Starter Path

1. Install **LocalWP** (localwp.com) — runs WordPress on your laptop, no hosting needed.
2. Install **Hello Elementor** theme + **Elementor Free** + **MetForm Free** + **Essential Addons Free** + **Converter for Media** + **LiteSpeed Cache** + **WPCode Lite**.
3. Build the entire page locally using the plan tasks (skip the Elementor Pro Form widget — use **MetForm** instead for the multi-step form in Task 15).
4. When ready to go live: deploy to **InfinityFree** using the **All-in-One WP Migration** plugin (free, one-click export/import).
5. GA4 + GTM are already free — no change needed.

**Total cost: $0.**
**Trade-offs:** subdomain instead of custom domain, ~30 min extra to learn MetForm vs Elementor Pro Forms, and InfinityFree shows occasional "powered by" footer (acceptable for a demo/prototype, not for client-facing production).

---

## Part 3 — Plugin Disambiguation Table (Avoid Lookalikes)

The WordPress plugin directory has dozens of copycats with near-identical names. Use this table to install the correct plugin every time.

### Core plugins from the starter path

| #   | Plugin (exact name in search)                                                           | Author                                                  | Slug / URL                            | Active installs | How to confirm you've got the right one                                                                                                                                                                                         |
| --- | --------------------------------------------------------------------------------------- | ------------------------------------------------------- | ------------------------------------- | --------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| 1   | **Hello Elementor** (theme, not plugin — install via Appearance → Themes)               | Elementor.com                                           | `hello-elementor`                     | 2M+             | Author shown as "Elementor Team". Theme description: "a plain-vanilla & lightweight theme for Elementor".                                                                                                                       |
| 2   | **Elementor Website Builder**                                                           | Elementor.com                                           | `elementor`                           | 5M+             | Author "Elementor.com". Logo is a pink/magenta "E". Avoid "Elementor Header & Footer Builder" (different plugin).                                                                                                               |
| 3   | **MetForm – Contact Form, Survey, Quiz, & Custom Form Builder for Elementor**           | Wpmet                                                   | `metform`                             | 200k+           | Author "Wpmet" (one word). Icon is a blue paper-airplane. This gives you multi-step forms + conditional logic free.                                                                                                             |
| 4   | **Essential Addons for Elementor**                                                      | WPDeveloper                                             | `essential-addons-for-elementor-lite` | 2M+             | Author "WPDeveloper". Icon is a blue square with "Ea". The "Lite" slug is the free version — Pro is upsold inside. Avoid "Ultimate Addons", "Premium Addons", "Happy Addons" (all different — fine plugins, just not this one). |
| 5   | **Converter for Media – Optimize images \| Convert WebP & AVIF**                        | matt plugins (Mateusz Gbiorczyk)                        | `webp-converter-for-media`            | 200k+           | Author "matt plugins". Icon is a green leaf/arrow. Unlimited free WebP — the alternative "WebP Express" by Bjørn Johansen also works. Avoid "ShortPixel" if you want truly unlimited.                                           |
| 6   | **LiteSpeed Cache**                                                                     | LiteSpeed Technologies                                  | `litespeed-cache`                     | 6M+             | Author "LiteSpeed Technologies". Orange flame icon. Works fully even on non-LiteSpeed hosts (page optimization features still apply).                                                                                           |
| 7   | **WPCode – Insert Headers and Footers + Custom Code Snippets – WordPress Code Manager** | WPCode (was "Insert Headers and Footers by WPBeginner") | `insert-headers-and-footers`          | 2M+             | Author "WPCode". Was renamed from "Insert Headers and Footers by WPBeginner" — same plugin. Use this for GTM head/body injection.                                                                                               |

### Supporting plugins from the original plan

| #   | Plugin                                                              | Author                         | Slug                      | Notes                                                                                                                                                  |
| --- | ------------------------------------------------------------------- | ------------------------------ | ------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------ |
| 8   | **OMGF \| Host Google Fonts Locally**                               | Daan from Daan.dev             | `host-webfonts-local`     | Author "Daan from Daan.dev". Icon is a green "OMGF". Self-hosts Inter for performance.                                                                 |
| 9   | **All-in-One WP Migration**                                         | ServMask                       | `all-in-one-wp-migration` | Author "ServMask". Icon is a blue cloud/arrow. Free tier: 512 MB upload limit (enough for a landing page). For deployment from LocalWP → InfinityFree. |
| 10  | **Rank Math SEO – AI SEO Tools to Dominate SEO Rankings**           | Rank Math                      | `seo-by-rank-math`        | Author "Rank Math". Black icon with "Rank Math". Free tier has all you need. Avoid "Yoast SEO" only if you want fewer prompts.                         |
| 11  | **Wordfence Security – Firewall, Malware Scan, and Login Security** | Wordfence                      | `wordfence`               | Author "Wordfence". Blue shield icon. Free tier sufficient.                                                                                            |
| 12  | **UpdraftPlus: WP Backup & Migration Plugin**                       | UpdraftPlus.Com, DavidAnderson | `updraftplus`             | Author "UpdraftPlus.Com, DavidAnderson". Icon is a blue cloud-upload. Free tier backs up to Google Drive/Dropbox.                                      |

---

## Part 4 — How to Spot the Right Plugin (Quick Rules)

When searching the WP plugin directory and multiple results appear:

1. **Always check the author column** in WP plugin search results, not just the name. Many clones use near-identical names.
2. **Cross-check active install count** against the table above. Real Elementor has 5M+; a fake "Elementor Lite" with 2k installs is a copycat.
3. **Verify the slug** in the URL (`wordpress.org/plugins/<slug>/`). The slug is unique — the name isn't.
4. **Check "Last updated"** — should be within the last 2–3 months. Abandoned forks are a security risk.
5. **Avoid these common confusions:**
   - "Elementor Header & Footer Builder" by Brainstorm Force ≠ Elementor itself (it's an addon).
   - "Happy Addons" / "Premium Addons" / "Ultimate Addons" / "Master Addons" / "Royal Addons" — all legitimate Elementor addon packs, but Essential Addons is the most popular free one with the widest widget coverage. Pick **one** addon pack — stacking them slows the site.
   - "MetForm" (Wpmet) ≠ "Forminator" (WPMU DEV) ≠ "WPForms Lite" (WPForms) — all free form plugins; MetForm is the only one with native Elementor multi-step + free conditional logic.

---

## Part 5 — Install Order Reference

For the cleanest setup in LocalWP or InfinityFree, install in this order:

1. **Hello Elementor** (theme) — sets the lightweight base.
2. **Elementor Website Builder** — must be active before any addon plugin.
3. **Essential Addons for Elementor (Lite)** — extra widgets.
4. **MetForm** — replaces Elementor Pro Forms.
5. **LiteSpeed Cache** — caching + optimization (configure last, after page is built).
6. **Converter for Media** — WebP conversion (run bulk convert after uploading hero/logo images).
7. **OMGF** — self-host Google Fonts (run optimize after global font is set).
8. **WPCode Lite** — for GTM head/body snippets.
9. **Rank Math SEO** — for meta titles, descriptions, OG tags.
10. **Wordfence** — security baseline (run initial scan after install).
11. **UpdraftPlus** — schedule daily backup to Google Drive.
12. **All-in-One WP Migration** — only when ready to deploy LocalWP → live host.

---

## Part 6 — One MetForm-Specific Note (Replaces Plan Task 15)

Since you'll use MetForm instead of Elementor Pro Forms, the multi-step form build differs slightly:

1. After installing MetForm, go to **MetForm → Forms → Add New**.
2. Choose **Multi Step** template.
3. Build the 3 steps with the same fields listed in plan Task 15.
4. For conditional redirects (replaces Cool FormKit in Task 16):
   - Inside the form's settings panel, go to **Settings → Confirmation**.
   - Use **MetForm's "Conditional Logic"** module (free) to set up "If field X = Y, redirect to URL Z" rules per company-size branch.
5. To embed in your page: in the Elementor editor, drag the **MetForm** widget from the widget panel and select the form you just built.
6. Email notifications: **Settings → Email Notification** — same field shortcodes work as in Elementor Pro Forms.

Everything else in the plan stays identical.
