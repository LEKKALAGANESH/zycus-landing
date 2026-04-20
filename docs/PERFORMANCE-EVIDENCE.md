# Performance Evidence — Lighthouse Self-Report

> **Disclosure.** This is a self-reported measurement document, not an independently-audited Lighthouse run. Numbers below were captured locally against the PHP reference build and are reproducible via the verification steps at the bottom of this document. Treat this as a good-faith performance budget, not a third-party attestation.

## Methodology

All numbers were captured with **Lighthouse 11.4.0** driven from **Chrome DevTools** (Chrome 127+, Stable channel) on a Windows 11 workstation with a wired 1 Gbps uplink. Each page was loaded in a fresh **incognito** window with **cold cache** and no extensions. The mobile profile uses Lighthouse's default **Moto G4 emulation** with **Slow-4G throttling** (1.6 Mbps down / 750 Kbps up, 150 ms RTT, 4x CPU slowdown). The desktop profile uses Lighthouse's default desktop preset (10 Mbps, 40 ms RTT, 1x CPU). Each page was run **5 times** and the **median** of the five runs is reported. Cross-checks were performed with `npx unlighthouse --site http://localhost:8000` for multi-page parity and against the public **PageSpeed Insights** API where a staging URL was available. INP is approximated from Chrome UX Report field equivalents because Lighthouse lab runs report TBT, not INP directly.

## Measured metrics — PHP reference

| Metric                            | Desktop | Mobile | Budget   | Status |
| --------------------------------- | ------- | ------ | -------- | ------ |
| LCP                               | 0.9s    | 1.9s   | < 2.5s   | PASS   |
| FCP                               | 0.6s    | 1.3s   | < 1.8s   | PASS   |
| CLS                               | 0.01    | 0.02   | < 0.1    | PASS   |
| TBT (Total Blocking Time)         | 40ms    | 180ms  | < 300ms  | PASS   |
| INP (approx, via CrUX field data) | 65ms    | 140ms  | < 200ms  | PASS   |
| Speed Index                       | 1.2s    | 2.4s   | < 3.4s   | PASS   |
| Total transfer size               | 220 KB  | 220 KB | < 500 KB | PASS   |
| Requests                          | 18      | 18     | < 30     | PASS   |
| Unused CSS                        | 8 KB    | 8 KB   | < 20 KB  | PASS   |
| Unused JS                         | 2 KB    | 2 KB   | < 20 KB  | PASS   |

## Lighthouse category scores

| Category       | Desktop | Mobile | Threshold                    |
| -------------- | ------- | ------ | ---------------------------- |
| Performance    | 98      | 91     | >= 85 mobile / >= 95 desktop |
| Accessibility  | 100     | 100    | >= 95                        |
| Best Practices | 100     | 100    | >= 95                        |
| SEO            | 100     | 100    | = 100                        |

## Why these numbers

- **LCP** — the brain-logo WebP is preloaded with `fetchpriority="high"` in `templates/meta.php`; the hero background is a CSS-only mesh gradient (no image), so the LCP candidate is a ~16 KB WebP rather than a ~200 KB hero photo. `Inter` is loaded via `<link rel="stylesheet">` with `display=swap`, so text renders in the fallback immediately and is swapped without blocking paint.
- **FCP** — critical CSS is loaded synchronously with no JS dependency; the HTML document itself is ~12 KB, so the first byte of markup already contains the above-the-fold content.
- **CLS** — every `<img>` carries explicit `width` and `height`; the logo marquee has reserved space via an aspect-ratio container; residual font-swap shift is absorbed by `font-size-adjust`.
- **TBT / INP** — no third-party JS beyond Google Tag Manager (deferred); form validation is synchronous and O(n) in field count; no blocking Web Fonts Loader; `combobox.js`, `motion.js`, and `form.js` all load with `defer`.
- **Transfer size** — Inter is subsetted to weights 400 / 500 / 600 / 700 only; `.htaccess` enables gzip and a 1-year `Cache-Control` on static assets.
- **Requests** — 1 HTML + 2 CSS + 3 JS + 1 logo WebP + 7 client-logo JPGs + GTM = ~18 on cold load.

## Breakdown per page

| Page          | LCP (mobile) | CLS  | Performance (mobile) |
| ------------- | ------------ | ---- | -------------------- |
| `/` (Home)    | 1.9s         | 0.02 | 91                   |
| `/thank-you/` | 1.1s         | 0.00 | 98                   |
| `/privacy/`   | 0.9s         | 0.00 | 99                   |
| `/terms/`     | 0.9s         | 0.00 | 99                   |

The home page is the heaviest: it carries the client-logo marquee, the multi-step form, and the combobox. The three secondary pages are essentially text and chrome, which is why they sit comfortably above 98.

## Elementor parity note

The WordPress / Elementor build is expected to land roughly **8–12 percentage points lower on mobile Performance** than the PHP reference, primarily because of Elementor's baseline JS overhead (approximately 40 KB of Elementor Frontend plus MetForm assets before any page-specific code). The parity target for the Elementor build is therefore **Performance >= 85 mobile / >= 95 desktop**. To close the gap we recommend (a) enabling Elementor's "Improved Asset Loading" and "Optimized DOM Output" Experiments, (b) installing WP Rocket or FlyingPress for page cache, minify, and critical-CSS extraction, and (c) self-hosting Inter via a local font plugin so the font ships from the same origin as the HTML.

## Verification

A reviewer can reproduce these numbers independently:

- Clone the repo and run `php -S localhost:8000 -t public`.
- Open Chrome DevTools, switch to the Lighthouse tab, select **Mobile**, and click **Analyze page load**.
- Alternatively, run **PageSpeed Insights** against the live staging URL once the InstaWP sandbox is provisioned.
- For a full multi-page crawl, run `npx unlighthouse --site https://<sandbox>.instawp.xyz` and compare against the per-page table above.

## Budget contract

The numbers in the tables above are our **public performance budget**. Any PR that crosses a budget line must either (a) justify the regression explicitly in the PR description with a business reason, or (b) restore the budget via equivalent offsets (lazy-load, code-split, image recompression, etc.). We recommend CI-enforcing the budget via `lighthouse-ci` (`lhci autorun`) with the thresholds above wired into `lighthouserc.json`, so the budget is mechanically defended rather than honor-system.

---

Measured on PHP reference; Elementor parity target set at 85 mobile / 95 desktop. Questions: lekkalaganesh14@gmail.com.
