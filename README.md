# Zycus — WordPress + Elementor Landing Page

High-conversion demo-request landing page for the Zycus Merlin AI procurement platform.
Built with WordPress 6.5, Elementor (Free + Pro), and a custom child theme.

---

## Quick Start (Local Development)

### Prerequisites
- Docker Desktop installed and running

### Steps

```bash
# 1. Start the stack
docker compose up -d

# 2. Visit WordPress installer
open http://localhost:8080

# 3. Complete the WP install wizard
#    Site Title:  Zycus — AI-Powered Procurement
#    Username:    admin
#    Password:    (your choice)
#    Email:       (your email)
```

### After WP install

1. **Activate theme** — Appearance → Themes → Activate **Zycus Elementor**
2. **Install Elementor** — Plugins → Add New → search "Elementor" → Install + Activate
3. **Install Elementor Pro** — Upload the Elementor Pro `.zip` via Plugins → Upload
4. **Create Landing Page**
   - Pages → Add New → Title: "Home" → Save as Draft
   - Set as Front Page: Settings → Reading → Static page → Front page: Home
   - Edit with Elementor
5. **Import Elementor Template**
   - In Elementor editor: click the folder icon (Library) → My Templates → Import
   - Upload `elementor-templates/zycus-landing-page.json`
   - Insert the template
6. **Publish** the page

---

## Required Plugins

| Plugin | Where to get | Purpose |
|--------|-------------|---------|
| **Elementor Free** | wordpress.org/plugins | Page builder |
| **Elementor Pro** | elementor.com | Form widget + redirect actions |
| **WPCode Lite** *(optional)* | wordpress.org/plugins | Inject GTM/JSON-LD snippets without editing theme files |

---

## WPCode Snippets (Analytics & Tracking)

If you use WPCode Lite, import these snippets from `wpcode-snippets/`:

| File | Location in WPCode | Purpose |
|------|--------------------|---------|
| `01-gtm-head.html` | Header (Before `</head>`) | Google Tag Manager |
| `02-gtm-body.html` | Body Start | GTM `<noscript>` fallback |
| `03-json-ld-org.html` | Header | Organization schema (SEO) |
| `04-ga4-events.js` | Footer (JavaScript) | GA4 custom events |

> **Note:** The Zycus Elementor theme already injects GTM (`GTM-KG8889HK`) and the JSON-LD schema natively via `functions.php` — WPCode snippets are only needed if you switch to a different theme or disable the theme's built-in injection.

---

## Design System

### Color Palette
| Token | Hex | Usage |
|-------|-----|-------|
| Torea Bay | `#0F3D81` | Headings, body text, footer background |
| Torch Red | `#FF1446` | Primary CTAs, focus rings, form accents |
| Dodger Blue | `#40A4FB` | Links, secondary accents, hover states |
| Surface Base | `#FAFBFD` | Page background |
| Surface Alt | `#F4F7FB` | Alternating section backgrounds |
| Ink Subtle | `#4A5B7A` | Helper text, captions |

### Typography
- Font: **Inter** (Google Fonts, weights 400/500/600/700)
- H1: 48px desktop → 32px mobile
- H2: 36px desktop → 28px mobile
- Body: 17px desktop → 16px mobile
- Line-height: 1.6 (body), 1.15 (headings)

### Responsive Breakpoints
| Breakpoint | Width | Layout |
|-----------|-------|--------|
| Mobile | < 640px | Single column, hamburger nav |
| Tablet | 640px–1024px | 2-col grid |
| Desktop | 1024px+ | Full layout, sticky glassmorphic header |
| Large | 1280px+ | H1 scales to 56px |

---

## Page Sections

1. **Hero** — Eyebrow + H1 + lead + CTA + trust row + animated dashboard mock
2. **How It Works** — 4 step cards (Intake, Sourcing, Contracting, Procure-to-Pay)
3. **Logo Carousel** — Dual-row counter-scrolling marquee, 13 enterprise logos
4. **Testimonials** — 2 testimonial cards on deep Torea Bay background
5. **FAQ** — 4-item accordion
6. **Demo Form** — 8-field form (Email, Name, Company, Size, Role, Use Case, Notes)
7. **Thank You** — Personalized confirmation page at `/thank-you/`

---

## Form Configuration (Elementor Pro)

The form widget sends a notification email to the WP admin and redirects to `/thank-you/?fname={first_name}&email={email}` on success.

To configure email notifications in Elementor:
1. Edit page → click form → **Form Settings** tab
2. **Actions After Submit** → Email → configure recipient
3. **Redirect** → set URL to `/thank-you/`

### Fallback Form (no Elementor Pro)

If Elementor Pro is unavailable, use the shortcode `[zycus_demo_form]` in any page/section HTML widget. This renders the full 3-step form with WP nonce protection and email notification.

---

## Tracking Integration

| Event | Trigger | GA4 / GTM |
|-------|---------|-----------|
| `page_view` | Page load | Automatic via GTM |
| `cta_click` | Any CTA button clicked | `tracking.js` |
| `faq_open` | FAQ accordion expanded | `tracking.js` |
| `form_step_advance` | Multi-step Next clicked | `tracking.js` |
| `generate_lead` | Form submitted successfully | `tracking.js` / Elementor hook |
| `demo_confirmed` | Thank-you page loaded | Inline `<script>` in `page-thank-you.php` |

GTM Container: `GTM-KG8889HK`
GA4 Measurement ID: `G-1MG1YKNRDF`

---

## Live Deployment

### Option A — WP Engine / Kinsta
1. Create a new WordPress site
2. Upload theme ZIP via Appearance → Themes → Add New → Upload
3. Install Elementor + Pro
4. Import Elementor template
5. Set up WPCode snippets

### Option B — cPanel / Shared Hosting
1. Upload WordPress files via File Manager or FTP
2. Create MySQL database
3. Configure `wp-config.php`
4. Run WP installer
5. Follow same steps as above

### Option C — Docker (Production)
Replace the `wordpress:6.5-php8.2-apache` image with a hardened variant and add SSL:
```yaml
  nginx:
    image: nginx:alpine
    ports: ["443:443"]
    volumes: ["./nginx.conf:/etc/nginx/conf.d/default.conf", "./certs:/etc/ssl"]
```

---

## Accessibility

- WCAG 2.1 AA compliant
- Skip link, semantic landmarks, single `<h1>` per page
- All focus states: 3px Torch Red ring (`--focus-ring`)
- Touch targets: 44×44px minimum
- Reduced motion: all transitions → 0.01ms, reveals show immediately
- Form errors use `aria-live="polite"` region

---

## Performance Targets
| Metric | Target |
|--------|--------|
| LCP | < 2.5s |
| CLS | < 0.1 |
| INP | < 200ms |
| Lighthouse Performance | ≥ 90 |

---

## File Structure

```
wordpress-build/
├── docker-compose.yml
├── README.md
├── wp-content/
│   └── themes/
│       └── zycus-elementor/
│           ├── style.css                  ← Theme declaration
│           ├── functions.php              ← Enqueue, GTM, nav, shortcode
│           ├── index.php                  ← Fallback template
│           ├── header.php                 ← Sticky nav + GTM injection
│           ├── footer.php                 ← Footer + mobile CTA + wp_footer()
│           ├── page.php                   ← Elementor canvas template
│           ├── page-thank-you.php         ← Personalized confirmation page
│           └── assets/
│               ├── css/
│               │   ├── tokens.css         ← CSS custom properties
│               │   ├── global.css         ← Reset, typography, layout
│               │   ├── components.css     ← All UI components
│               │   ├── animations.css     ← Scroll reveal, marquee, reduced motion
│               │   └── elementor-overrides.css ← Widget skin fixes
│               ├── js/
│               │   ├── motion.js          ← Reveals, counters, FAQ, mobile menu
│               │   ├── marquee.js         ← Logo carousel duplication
│               │   ├── sticky-cta.js      ← Mobile sticky Book Demo bar
│               │   └── tracking.js        ← GTM dataLayer events
│               └── img/
│                   ├── zycus-logo.webp
│                   └── logos/             ← 13 enterprise client logos
├── elementor-templates/
│   └── zycus-landing-page.json            ← Import via Elementor Library
└── wpcode-snippets/
    ├── 01-gtm-head.html
    ├── 02-gtm-body.html
    ├── 03-json-ld-org.html
    └── 04-ga4-events.js
```
