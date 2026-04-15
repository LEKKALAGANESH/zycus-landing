# Post-Signoff Handoff Checklist

Everything the Zycus team needs to provide or approve after accepting the deliverable. Grouped by owner. Target: all items complete within **5 business days** of signoff.

---

## Marketing Ops owns

### Copy & brand
- [ ] Sign off on the final copy block-by-block from `build-artifacts/copy/zycus-landing-copy.md` — mark each block "approved" or comment with the replacement wording.
- [ ] Provide the real **enterprise Calendly URL** (currently the placeholder `https://calendly.com/zycus-enterprise-ae` is wired into the MetForm blueprint and `php-app/.env`'s `APP_CALENDLY_URL`).
- [ ] Confirm the two testimonial attributions (Priya Venkataraman, CPO; Daniel Osei, CFO) are approved for public use, or supply replacement attributions with photo consent.
- [ ] Approve the four FAQ Q&As verbatim (see `ELEMENTOR-BUILD-GUIDE.md` §9) or supply replacements — these also feed the JSON-LD `FAQPage` schema.
- [ ] Commission a **1200×630 OG share image** (see `SEO-CONTENT-PLAN.md` "OpenGraph + Twitter" — current brain-logo fallback is 160×160 and renders poorly on LinkedIn/Twitter previews).

### Measurement
- [ ] Confirm GTM container `GTM-KG8889HK` is owned by the Zycus GTM account (not a vendor's). If not, create a Zycus-owned container and provide the new ID — we will rewire in both `.env` and the WPCode snippets.
- [ ] Confirm GA4 property `G-1MG1YKNRDF` is the production Zycus property. If a different measurement ID should be used, update the GTM "GA4 Measurement ID" Constant variable.
- [ ] Register the 7 custom dimensions in GA4 → Admin → Custom definitions per `ANALYTICS-EVENTS.md` — failure to register drops custom parameters after 24 hours.
- [ ] Mark `generate_lead` as a conversion in GA4 → Admin → Events.

---

## IT / DevOps owns

### Hosting
- [ ] Choose production host: **Kinsta** / **SiteGround** / **Pressable** (managed WP) recommended; InfinityFree / Render acceptable for staging only.
- [ ] Provision the production domain (e.g. `demo.zycus.com`) and point A-record / CNAME to the host.
- [ ] Issue SSL certificate — Let's Encrypt via the host, or corporate wildcard cert if required.
- [ ] Open firewall / Cloudflare allowlist for the SMTP relay origin (Brevo IPs: `185.107.232.0/24`).

### Email & deliverability
- [ ] Provision the sending domain `noreply@zycus.com` with **SPF** + **DKIM** + **DMARC** records. Without these, Gmail and Outlook will junk the notification emails.
- [ ] Create the `privacy@zycus.com` inbox (GDPR Art. 15/17 data-subject requests land here).
- [ ] Create the `sales@zycus.com` inbox (or forward to an existing SDR Slack-routing automation).
- [ ] Decide MetForm Pro vs Lite. Pro unlocks conditional logic, webhooks, reCAPTCHA v3. Licence procurement is ~$79/year.

### Access
- [ ] Create a Zycus-owned GitHub organisation and fork the repo from `https://github.com/LEKKALAGANESH/zycus-landing` (or transfer it).
- [ ] Grant 1Password / Bitwarden access to the vendor for `.env` values during the migration window only.
- [ ] Enable 2FA on the WP admin account and the GTM account.

---

## Legal / Compliance owns

- [ ] Replace `public/privacy.php` stub with the real Zycus privacy policy. Must cover: what the demo form collects, retention period (we recommend 24 months), legal basis (GDPR Art. 6(1)(f) legitimate interest + 6(1)(a) consent), DSR process pointing at `privacy@zycus.com`.
- [ ] Replace `public/terms.php` stub with the real Terms of Use.
- [ ] Approve the cookie consent banner copy and category split (Necessary / Analytics / Marketing). CookieYes Free or Complianz Free are the recommended plugins (see `SECURITY-AND-COMPLIANCE.md`).
- [ ] Approve the data retention policy: submissions 24 months, IP + UA logs 90 days. If Legal dissents, revise `SUBMISSION_RETENTION_DAYS` env var.
- [ ] Sign off that US-based leads are acceptable under CCPA; EU-based leads under GDPR. Scope is marketing-lead-qualification only — no procurement data processing.

---

## Security owns

- [ ] Review `SECURITY-AND-COMPLIANCE.md` — specifically the 8-row form-security controls matrix and the incident-response plan.
- [ ] Run an internal vulnerability scan (Nessus, Qualys, or Zycus's tool of record) against the staging URL before public launch.
- [ ] Decide Cloudflare Turnstile or reCAPTCHA v3 — currently only per-IP rate-limiting + honeypot are in place. Enabling one of these is a 5-line WP plugin install.
- [ ] Set the `Strict-Transport-Security` header to `max-age=31536000; includeSubDomains` after 1 week of stable HTTPS (not before — rollback becomes difficult once HSTS caches).
- [ ] Configure `DISALLOW_FILE_EDIT = true` in `wp-config.php`.

---

## Design owns (optional — only if you want to override the shipped design)

- [ ] Review `BRAND-STYLE-GUIDE.md` and sign off, OR propose changes. Override limits: any change to the three brand hex values, typography, button variants, or motion rules requires a re-audit of the 36 items in `EVALUATION-CHECKLIST.md`.
- [ ] Provide a hero product screenshot if you want to replace the CSS-only mesh gradient (see `CLIENT-FAQ.md` Q5 for the LCP trade-off).

---

## Vendor owns (our remaining tasks)

- [ ] Spin up the InstaWP sandbox per `INSTAWP-QUICKDEPLOY.md` and share the URL + admin magic-login.
- [ ] Run Lighthouse mobile + axe-core on the staging URL and commit the reports to `docs/lighthouse/` and `docs/axe/`.
- [ ] Migrate the MetForm blueprint + GTM container + WPCode snippets into the Zycus-owned WP install once the host is chosen.
- [ ] Be available for a 30-minute walkthrough during UAT week.

---

## Suggested rollout timeline

| Day | Owner | Activity |
|---|---|---|
| 0 | All | Deliverable accepted; checklist kicked off |
| 1–2 | Marketing, Legal | Copy + privacy policy + terms signed off |
| 2 | IT | Host chosen, domain DNS propagated |
| 3 | IT | SMTP + SPF/DKIM configured; `sales@` and `privacy@` inboxes live |
| 3–4 | Vendor | Staging site built, migrated, URL shared |
| 4 | Marketing | GTM + GA4 verified in staging; custom dimensions registered |
| 5 | Security | Vuln scan pass; HSTS-pending approved |
| 5 | All | UAT walkthrough + go/no-go decision |
| Day 6+ | IT | DNS cutover to production; HSTS enabled after 1 week of stability |

---

Stuck on any of these? Contact {{your_email}} — we keep a live FAQ at `CLIENT-FAQ.md`.
