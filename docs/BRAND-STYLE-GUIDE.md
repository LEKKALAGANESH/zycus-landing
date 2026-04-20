# Zycus Landing Page — Brand Style Guide

The single source of truth for the Zycus landing-page visual system. Applies to BOTH the primary WordPress/Elementor build (see `ELEMENTOR-BUILD-GUIDE.md`) and the supplemental PHP reference implementation under `php-app/`. Every component, page section, and future iteration must conform to the rules below. Deviations require explicit sign-off.

---

## Palette

### Brand colours

| Name | Hex | Usage |
|---|---|---|
| Torea Bay | `#0F3D81` | Headings, body text, depth, footer background, testimonials background |
| Dodger Blue | `#40A4FB` | Secondary accents, off-ramp links, eyebrow gradients |
| Torch Red | `#FF1446` | Primary CTAs, required asterisks, focus rings ONLY |

### Neutrals

| Name | Hex | Usage |
|---|---|---|
| Surface Base | `#FAFBFD` | Default page background |
| Surface Alt | `#F4F7FB` | Alternating section background |
| Ink Subtle | `#4A5B7A` | Helper text, captions, meta |
| Pure White | `#FFFFFF` | Cards only — never page background |

### Colour rules

- **Torea Bay** — Do: headings, body copy, footer, testimonial blocks, navigation text. Don't: use as a CTA fill, pair with Dodger Blue at body-text size.
- **Dodger Blue** — Do: secondary outline buttons, tertiary text links (with underline), eyebrow gradient endpoints. Don't: use as standalone body-text colour on white (fails 4.5:1), use as a CTA fill, use for required-field markers.
- **Torch Red** — Do: primary CTAs, required-field asterisks, `:focus-visible` rings, `.is-invalid` borders. Don't: use on icons, decorative accents, body text, section backgrounds, or in any gradient.

---

## Typography

Inter via Google Fonts (weights 400 / 500 / 600 / 700) is the only typeface; system-ui as fallback.

| Element | Size desktop | Size mobile | Weight | Line-height | Colour |
|---|---|---|---|---|---|
| H1 | 48 px | 32 px | 700 | 1.15 | Torea Bay |
| H2 | 36 px | 28 px | 700 | 1.2 | Torea Bay |
| H3 | 24 px | 20 px | 600 | 1.3 | Torea Bay |
| Body | 17 px | 16 px | 400 | 1.6 | Torea Bay |
| Small / helper | 13 px | 13 px | 500 | 1.5 | Ink Subtle |
| Eyebrow | 13 px | 13 px | 600 | 1.4 | Gradient via `background-clip: text` (Torea Bay → Dodger Blue → Torch Red), uppercase, `letter-spacing: 0.08em` |

---

## Spacing scale

4-point scale — `4 / 8 / 12 / 16 / 24 / 32 / 48 / 64 / 96 px`. Use 16 for component internal padding, 24 for card padding, 48 for section vertical padding on mobile, 96 for section vertical padding on desktop. Never invent off-scale values.

---

## Buttons

Three variants only. No gradient buttons anywhere. No drop-shadow on primary. No scale animation on hover — subtle 8% background darkening only.

### Primary CTA — Torch Red solid

```css
background: #FF1446;
color: #FFFFFF;
min-height: 48px;
padding: 14px 32px;
border-radius: 10px;
font-weight: 600;
border: none;
transition: background 200ms linear;
/* hover */ background: color-mix(in srgb, #FF1446 88%, black);
/* focus-visible */ outline: 3px solid rgba(255,20,70,0.45); outline-offset: 3px;
```

### Secondary off-ramp — Dodger Blue outline

```css
background: transparent;
color: #0F3D81;
min-height: 44px;
padding: 12px 28px;
border: 2px solid #40A4FB;
border-radius: 10px;
font-weight: 600;
transition: background 200ms linear, color 200ms linear;
/* hover */ background: #40A4FB; color: #FFFFFF;
/* focus-visible */ outline: 3px solid rgba(255,20,70,0.45); outline-offset: 3px;
```

### Tertiary text link — underlined

```css
background: none;
color: #0F3D81;                   /* Torea Bay for AA on white; use Dodger Blue only on dark bgs */
font-weight: 500;
text-decoration: underline;
text-underline-offset: 3px;
/* hover */ color: #FF1446;
/* focus-visible */ outline: 3px solid rgba(255,20,70,0.45); outline-offset: 2px;
```

---

## Form controls

Inputs, selects, and textareas share one spec.

```css
min-height: 48px;
border: 2px solid #D6DEEB;
border-radius: 10px;
font-size: 16px;                  /* prevents iOS zoom-on-focus */
color: #0F3D81;
background: #FFFFFF;
padding: 14px 16px;
transition: border-color 200ms linear, box-shadow 200ms linear;
/* hover */ border-color: #40A4FB;
/* focus */ border-color: #FF1446; box-shadow: 0 0 0 3px rgba(255,20,70,0.22);
/* .is-invalid */ border-color: #FF1446; background: #FFF5F6;
```

---

## Motion

| Event | Duration | Easing | Notes |
|---|---|---|---|
| Page entrance | none | — | Content must be visible immediately; no fade-in |
| Button hover | 200 ms | linear | Darken-only; no transform, no scale |
| FAQ open/close | 300 ms | ease | Animate `grid-template-rows: 0fr → 1fr` |
| Logo marquee | 30 s | linear infinite | Paused on section hover and on focus-within |
| Reduced motion | 0.01 ms | — | `@media (prefers-reduced-motion: reduce)` overrides all transitions and animations |

No parallax. No scroll-jacking. No auto-playing video. No entrance animations on form fields.

---

## Logo usage

- File: `public/assets/img/zycus-logo.webp`
- Alt text (exact): `"Zycus AI Procurement Brain Logo"`
- Minimum render width: 120 px
- Clear space: equal to the logo's "Z" cap-height on all four sides
- Never placed on photographic or low-contrast backgrounds
- When used as the site logo, apply `border-radius: 10–12px`
- Do not recolour, stretch, rotate, or apply drop-shadow

---

## Accessibility commitments

- WCAG 2.1 AA conformance, audited per release
- Body-text contrast 4.5:1 minimum (Torea Bay on Surface Base measures 10.9:1)
- Large text contrast 3:1 minimum
- Visible 3 px Torch Red `:focus-visible` ring on every interactive element
- Touch targets 44×44 px minimum; 48×48 px preferred
- Exactly one `<h1>` per page
- Semantic landmarks: `<header role="banner">`, `<main>`, `<nav>`, `<footer role="contentinfo">`
- Form error region uses `aria-live="polite"`
- `prefers-reduced-motion` is a blanket kill-switch for transitions and animations

---

## Don'ts

- No gradient CTA buttons.
- No pure white `#FFFFFF` page backgrounds (use Surface Base `#FAFBFD`).
- No Dodger Blue text on white (fails 4.5:1).
- No entrance animations on the lead form.
- No emoji in UI or marketing copy.

---

## Quick reference — one-line token map

`Torea Bay #0F3D81 · Dodger Blue #40A4FB · Torch Red #FF1446 · Surface Base #FAFBFD · Surface Alt #F4F7FB · Ink Subtle #4A5B7A · Inter 400/500/600/700 · 16-17px / 1.6 · Radius 10px · Focus 3px #FF1446 · Touch 44/48 · Reduced-motion kill · One accent rule`
