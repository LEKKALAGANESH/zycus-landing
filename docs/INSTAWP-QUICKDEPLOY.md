# Free WordPress Hosting — Quick Deploy Options

> **Note** — InstaWP's free tier started asking for a credit card + $25 pre-auth in some regions. This guide replaces it with three genuinely-free-no-card options.

Verified currently-free-no-card WordPress sandboxes as of 2026. Prefer in this order:

1. **WordPress Playground** (https://playground.wordpress.net) — instant, no signup, runs WordPress entirely in-browser via WebAssembly. Lives 30 minutes by default. Perfect for demos. The gotcha is it's in-browser so the URL only works for your session — share a screen-recording instead.
2. **InfinityFree WordPress auto-installer** (https://infinityfree.com) — truly free, no card, persistent subdomain (`*.infinityfreeapp.com`), unlimited time. Free SSL. Account creation is 3 min.
3. **000webhost** (https://www.000webhost.com) — free, no card, WP auto-installer, PHP 8.1, MySQL included. Subdomain `*.000webhostapp.com`. ~3 min signup.

### Ruled out (why)

- **InstaWP free tier** — signup flow now demands a credit card in some regions for a "$25 free credit". Not truly free-no-card.
- **TasteWP** — similar card-gate on some plans.
- **LocalWP** — only runs on your laptop, no public URL.
- **WordPress.com free** — blocks plugins; MetForm + Elementor won't install.

---

## Option 1 — WordPress Playground (recommended for tomorrow's submission)

Ten minutes. Best for a Loom walkthrough you send the evaluator.

1. Open https://playground.wordpress.net — a WP instance boots in ~15 seconds.
2. Click the settings gear → set PHP 8.1+, WordPress 6.5+ (defaults are fine).
3. `wp-admin` opens automatically. Username `admin`, password visible in the settings gear panel.
4. `Plugins → Add New → Elementor` → Install → Activate.
5. `Plugins → Add New → MetForm` → Install → Activate.
6. Build the form + hero per `docs/ELEMENTOR-BUILD-GUIDE.md` §4 + §8.
7. Export via `Tools → Export` (WXR file). Record a Loom of the built page per `docs/VIDEO-SCRIPT.md` — that's what you share.

**Limitation**: Playground's URL is browser-local — it won't work for someone else. For a shareable URL, go to Option 2.

---

## Option 2 — InfinityFree (recommended for a shareable URL)

1. Sign up at https://infinityfree.com with email only (no card).
2. **Create Account** → pick a free subdomain `zycus-demo-2` (try variants if `zycus-demo` is taken).
3. Wait ~3 min for provisioning. Control Panel → **MySQL Databases** → create `zycus_landing`.
4. Control Panel → **Softaculous Apps Installer** → search "WordPress" → Install.
5. **In-Directory** = leave blank (install at site root). DB + admin user auto-populated. Click **Install**.
6. Open `https://<your-subdomain>.infinityfreeapp.com/wp-admin`.
7. Install the 4 plugins (Elementor, MetForm, WPCode, Yoast).
8. Build the home + thank-you pages per `docs/ELEMENTOR-BUILD-GUIDE.md`.
9. Add GTM per `docs/ELEMENTOR-BUILD-GUIDE.md` §12.
10. Smoke-test: submit the form from incognito, confirm redirect, verify `generate_lead` in GTM Preview.

**Gotcha**: InfinityFree SSL can take up to 15 min to provision. Wait until `https://` loads cleanly before sharing.

---

## Option 3 — 000webhost (backup)

1. Sign up at https://www.000webhost.com with email + password, no card.
2. **Create new site** → name → continue.
3. **Install WordPress** from the auto-installer.
4. Same plugin + build sequence as Options 1 & 2.
5. Free SSL via **Tools → SSL**. Can take up to 30 min.

---

## What to hand the evaluator

- **Option 1 route** — share the Loom screencast + the exported WXR file (evaluator can re-import).
- **Option 2 / 3 route** — share the live URL + a set of screenshots saved to `docs/screenshots/`.

Either way, paste the URL (or Loom link) at the top of `docs/CLIENT-SUBMISSION.md` under a new **"Live demo"** line.

---

## Known limitations of free WP hosts

- **Up-time not guaranteed** — fine for a 24–48 hr evaluator window, not a public launch.
- **Bandwidth caps** — all three cap monthly bandwidth in the 1–5 GB range.
- **No custom domain** on free tiers — URL will be `*.infinityfreeapp.com` or `*.000webhostapp.com`.
- **Slower than dedicated hosts** — Lighthouse mobile Performance may drop 10–15 points vs Kinsta/Pressable staging.
- **MetForm Pro** features unavailable without paid upgrade; Lite still captures leads to the WP DB.

For production, migrate to **Kinsta** ($35/mo), **SiteGround** ($3.99/mo promo), or **Pressable** ($25/mo). See `docs/HANDOFF-CHECKLIST.md`.

---

## Absolute fallback — no live URL at all

If none of the three work in your window:

1. Record a 2-minute Loom per `docs/VIDEO-SCRIPT.md` of the PHP reference running locally via `php -S localhost:8000 -t public`.
2. Export screenshots of each breakpoint into `docs/screenshots/`.
3. Commit in the cover letter to deliver a live URL "within 24 hours of selection".

Evaluators accept asynchronous delivery more often than they accept missed deadlines.

**When the first option fails, fall through to the next one. Don't get stuck.**
