# Video Walkthrough Script (2 minutes)

## Purpose

The recruiter has a 15-minute slot to evaluate your submission and a queue of other candidates waiting. They will open the video first, watch for two minutes, and only then decide whether any of the 17 documents in the repo are worth their time. This is the gate. The goal of the video is not to summarise the docs — it is to prove you actually delivered a working artefact. Show the live form submission, show the event firing in GA4, show the build guide your marketing team would follow on Monday morning. Documentation proves you planned; the video proves you shipped.

## Recording setup

- Tool: Loom free tier, OBS, or QuickTime (macOS) — 720p minimum.
- Camera on, mic on. Recruiter wants to see a person, not just a screen.
- Three browser tabs prepared: (1) GitHub repo `https://github.com/LEKKALAGANESH/zycus-landing`, (2) live staging URL from InstaWP, (3) GA4 Realtime view.
- Close Slack, Discord, personal email — no distraction pings.

## The script (2:00 total)

### Beat 1 — Hook (0:00–0:15)
- **Say:** "Hi, I'm [your name]. The brief asked for a WordPress/Elementor demo landing page with forms, responsiveness, and tracking. Here's what I delivered in 12 hours — a complete Elementor build guide, a working live demo, and a from-scratch PHP reference as a performance benchmark."
- **Show:** Your face, then cut to the GitHub repo README.

### Beat 2 — Live demo (0:15–0:45)
- **Say:** "First, the live demo — this is running WordPress, Elementor, and MetForm on a sandbox. Watch the form submission." Fill in the form with a work email, submit. "Redirect to personalised thank-you page, event fires in GA4 Realtime in real time."
- **Show:** The InstaWP staging URL — load hero, scroll through FAQ, fill form, land on thank-you, switch to GA4 Realtime tab, point at the `generate_lead` event.

### Beat 3 — The build guide (0:45–1:15)
- **Say:** "The deliverable itself is a 14-stage Elementor build guide. Your marketing team can follow it step-by-step inside WordPress admin. It includes importable artefacts — MetForm blueprint, a pre-configured GTM container, WPCode snippets, and a drop-in Elementor Custom CSS kit."
- **Show:** Open `docs/ELEMENTOR-BUILD-GUIDE.md` in GitHub — scroll through the Table of Contents, stop on the Hero wireframe ASCII diagram, scroll past Stage 8 (MetForm form build), land on Stage 12 (tracking).

### Beat 4 — Quality evidence (1:15–1:40)
- **Say:** "Three things I want to prove. One, brand discipline — Torch Red appears only on primary CTAs, nowhere else. Two, accessibility — WCAG 2.1 AA, 10.9:1 contrast ratio on body text, keyboard-only walkthrough verified. Three, performance — Lighthouse 91 mobile on the PHP reference."
- **Show:** Scroll `docs/BRAND-STYLE-GUIDE.md` (palette table), jump to `docs/ACCESSIBILITY-AUDIT.md` (compliance matrix), jump to `docs/PERFORMANCE-EVIDENCE.md` (metrics table).

### Beat 5 — Close (1:40–2:00)
- **Say:** "There are 17 documents in the repo covering security, SEO, analytics events, UAT test plan, and a post-signoff handoff checklist. Everything the evaluator needs to say yes is in `docs/CLIENT-SUBMISSION.md`. Thank you for considering my application — I'd love to discuss how I can bring this craft to Zycus."
- **Show:** `docs/` folder listing showing all 17 files; cursor lingers on `CLIENT-SUBMISSION.md`. Cut back to your face for the close.

## Do not

- Don't read the docs verbatim — narrate in your own voice.
- Don't apologise for anything. The PHP pivot is a feature (supplemental reference), not a bug.
- Don't go over 2:30. Recruiters bounce hard at 3:00.
- Don't record multiple takes and splice — one clean take is more impressive than a polished edit.
- Don't add background music.

## Post-recording

- Upload to Loom (unlisted link) or Google Drive (commenter access) — not YouTube (indexed by Google).
- Paste the URL at the top of `CLIENT-SUBMISSION.md` under "Video walkthrough".
- Test the link in an incognito browser before sending.
- Keep the raw file for 90 days in case the link dies.

The recruiter's first impression is you, not the repo. Look at the camera. Speak with conviction. You shipped.
