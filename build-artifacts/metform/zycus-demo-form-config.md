# Zycus Demo Request — MetForm Build Guide

This document walks you through building the **Zycus Demo Request** form inside WordPress using the free MetForm plugin by Wpmet. Since MetForm (free) has no public JSON import format, follow these instructions manually in the visual editor. Every field, option, and setting below is pre-decided so you just click through.

- **Form name:** `Zycus Demo Request`
- **Form slug/ID:** `zycus_demo`
- **Style:** Multi-step (3 steps) with progress indicator
- **Submit button text:** `Book My Demo`
- **Trust microcopy:** `Secure & confidential. We respect your inbox — no spam, no credit card required.`

---

## Prerequisites

- WordPress 6.x or later
- Elementor (free) installed and activated
- MetForm (free) installed and activated — `Plugins → Add New → search "MetForm" by Wpmet → Install → Activate`

> **Note on Pro features:** A few items below (conditional redirects based on field values, advanced email conditional logic, reCAPTCHA v3) technically require MetForm Pro. Workarounds for the free tier are noted inline.

---

## MetForm UI step 1: Create the form

1. Go to `WP Admin → MetForm → Forms → Add New`.
2. In the popup, choose **Multi Step** as the form type.
3. Name it `Zycus Demo Request`.
4. Leave "Start from scratch" selected → click **Edit Form**.

*[screenshot: MetForm "Add New Form" modal showing the "Multi Step" tab selected and the name field filled in]*

You will land in the Elementor-style MetForm editor with a default multi-step container already placed.

---

## MetForm UI step 2: Configure the form wrapper

1. Click the outer **MetForm** widget frame (the whole form container).
2. In the left panel, go to the **Content → Form Settings** tab.
3. Set:
   - **Form ID / slug:** `zycus_demo`
   - **Show Progress Indicator:** ON
   - **Progress Indicator Style:** `Step` (numbered dots with labels)
   - **Step Position:** Top

*[screenshot: left panel "Form Settings" with Progress Indicator toggle ON]*

---

## MetForm UI step 3: Build Step 1 — "About You"

Click the first step tab at the top of the canvas. Rename it to **About You**.

Drag these widgets in order from the MetForm widgets panel (left sidebar, "MetForm Fields" section):

### 3.1 — Email field (`email`)
- Widget: **MF Email**
- **Label:** `Work Email`
- **Placeholder:** `you@company.com`
- **Name attribute:** `email`
- **Required:** ON
- **Validation type:** Email
- **Custom validation message:** `Please use your work email`

> **Free-email blocking:** MetForm free does not support regex rejection of domains natively. Two workarounds:
> 1. Use the "Custom validation" field in **Advanced → Validation** with regex:
>    `^(?!.*@(gmail|yahoo|hotmail|outlook)\.com$).+@.+\..+$`
>    and message `Please use your work email`.
> 2. OR install the free **MetForm Validator Extensions** snippet (see `functions.php` approach in the README appendix).

*[screenshot: Advanced tab showing the custom regex validation input]*

### 3.2 — First Name (`first_name`)
- Widget: **MF Text**
- **Label:** `First Name`
- **Name attribute:** `first_name`
- **Required:** ON

### 3.3 — Last Name (`last_name`)
- Widget: **MF Text**
- **Label:** `Last Name`
- **Name attribute:** `last_name`
- **Required:** ON

---

## MetForm UI step 4: Build Step 2 — "About Your Company"

Click the second step tab. Rename it to **About Your Company**.

### 4.1 — Company Name (`company_name`)
- Widget: **MF Text**
- **Label:** `Company Name`
- **Name attribute:** `company_name`
- **Required:** ON

### 4.2 — Company Size (`company_size`)
- Widget: **MF Select**
- **Label:** `Company Size`
- **Name attribute:** `company_size`
- **Required:** ON
- **Options** (label | value — one per line in the Options textarea):
  ```
  1-49 employees|small
  50-499 employees|mid
  500-4,999 employees|enterprise
  5,000+ employees|large_enterprise
  ```

*[screenshot: MF Select options editor with the four rows entered]*

### 4.3 — Your Role (`role`)
- Widget: **MF Select**
- **Label:** `Your Role`
- **Name attribute:** `role`
- **Required:** ON
- **Options:**
  ```
  Procurement Leader (CPO, VP, Director)|procurement_leader
  Finance Leader (CFO, VP, Controller)|finance_leader
  IT / Digital Transformation|it
  Procurement Team Member|procurement_team
  Other|other
  ```

---

## MetForm UI step 5: Build Step 3 — "Your Priority"

Click the third step tab. Rename it to **Your Priority**.

### 5.1 — Primary Use Case (`use_case`)
- Widget: **MF Select**
- **Label:** `Primary Use Case`
- **Name attribute:** `use_case`
- **Required:** ON
- **Options:**
  ```
  Sourcing & Contract Management|s2c
  Invoice Automation (AP)|ap
  Supplier Management|supplier_mgmt
  End-to-End Source-to-Pay|s2p
  ```

### 5.2 — Notes (`notes`)
- Widget: **MF Textarea**
- **Label:** `Anything else we should know?`
- **Placeholder:** `Tell us about your biggest procurement bottleneck`
- **Name attribute:** `notes`
- **Required:** OFF
- **Max length:** `500`

*[screenshot: MF Textarea Advanced tab showing the 500-char limit setting]*

---

## MetForm UI step 6: Submit button + trust microcopy

1. Scroll to the bottom of the step 3 canvas.
2. Click the default Submit button widget.
3. Set **Button Text:** `Book My Demo`.
4. Drag an **MF Text Editor** widget (or Elementor's Text Editor) just below the submit button and enter:
   > `Secure & confidential. We respect your inbox — no spam, no credit card required.`
5. Style it small and muted: `font-size: 13px; color: #6b7280; text-align: center;`.

*[screenshot: bottom of form showing submit button and trust microcopy]*

---

## MetForm UI step 7: Email Notification Setup

Go to the MetForm frame → left panel → **Settings → Email Notification**. Toggle **Enable Email Notification** ON.

Fill in as follows:

- **Email To:** `sales@zycus.com`
- **Email From:** `noreply@zycus.com`  *(must be a domain where SPF + DKIM are configured on the sending server — otherwise Gmail/Outlook will junk the mail)*
- **Email Subject:** `New Demo Request — [mf-form-data field="company_name"]`
- **Reply To:** `[mf-form-data field="email"]`
- **Email Body** (paste this HTML/shortcode block into the Body editor, switching to Text/Code view):

```html
<h2>New Zycus Demo Request</h2>
<ul>
  <li><strong>Work Email:</strong> [mf-form-data field="email"]</li>
  <li><strong>First Name:</strong> [mf-form-data field="first_name"]</li>
  <li><strong>Last Name:</strong> [mf-form-data field="last_name"]</li>
  <li><strong>Company Name:</strong> [mf-form-data field="company_name"]</li>
  <li><strong>Company Size:</strong> [mf-form-data field="company_size"]</li>
  <li><strong>Role:</strong> [mf-form-data field="role"]</li>
  <li><strong>Primary Use Case:</strong> [mf-form-data field="use_case"]</li>
  <li><strong>Notes:</strong> [mf-form-data field="notes"]</li>
</ul>
<hr>
<p><small>
  Submitted at: [mf-submission-date]<br>
  Source URL: [mf-page-url]
</small></p>
```

> **Shortcode reference (free MetForm):**
> - `[mf-form-data field="FIELD_NAME"]` — pulls a submitted field value
> - `[mf-submission-date]` — submission timestamp
> - `[mf-page-url]` — URL of the page the form was submitted from
> If your MetForm version doesn't expose `[mf-page-url]`, add a hidden field `source_url` and populate it with `{{current_url}}` via MetForm's dynamic fields (Advanced tab).

*[screenshot: Email Notification panel with all fields populated]*

---

## MetForm UI step 8: Conditional Logic Setup (post-submit redirects)

Go to **Settings → Confirmation** in the left panel.

MetForm free supports a single global redirect URL. For the multi-branch logic below, use one of these approaches:

### Option A — MetForm Pro (native conditional logic)
`Settings → Confirmation → Conditional Logic → Add Rule`

Create three rules in this order (first match wins):

1. **Rule 1 (Enterprise route)**
   - IF `company_size` `equals` `enterprise` OR `company_size` `equals` `large_enterprise`
   - THEN **Redirect to URL:** `https://calendly.com/zycus-enterprise-ae`
     *(placeholder — replace with the real Calendly AE link)*

2. **Rule 2 (Self-serve route)**
   - IF `company_size` `equals` `small`
   - THEN **Redirect to URL:** `/self-serve-tour/`

3. **Rule 3 (Default fallback)**
   - IF no prior rule matched
   - THEN **Redirect to URL:** `/thank-you/?form=zycus_demo`

*[screenshot: Conditional Logic panel with three rules listed]*

### Option B — Free-tier workaround (no Pro)
Set the global redirect in `Settings → Confirmation → Redirect To` to `/thank-you/?form=zycus_demo` and add this snippet to your theme's `functions.php` (or a Code Snippets plugin) to override per company size:

```php
add_action('metform/after_store_form_data', function($form_id, $form_data) {
    if ($form_id !== 'zycus_demo') return;
    $size = $form_data['company_size'] ?? '';
    if (in_array($size, ['enterprise', 'large_enterprise'], true)) {
        wp_send_json_success(['redirect_to' => 'https://calendly.com/zycus-enterprise-ae']);
    } elseif ($size === 'small') {
        wp_send_json_success(['redirect_to' => '/self-serve-tour/']);
    }
}, 10, 2);
```

---

## MetForm UI step 9: Save & publish the form

1. Click the green **Update & Close** button at the bottom-left of the MetForm editor.
2. You'll see the form listed under `MetForm → Forms` with the title `Zycus Demo Request`.

---

## MetForm UI step 10: Embedding into Elementor

1. Edit any page with Elementor.
2. In the Elementor widget panel (left sidebar), search for **MetForm**.
3. Drag the **MetForm** widget into the page.
4. In the widget's Content tab, open the **Select Form** dropdown and choose `Zycus Demo Request`.
5. Toggle **Custom Style** ON if you want to override MetForm's defaults with Elementor styling controls.
6. Click **Update** to publish.

*[screenshot: Elementor sidebar with MetForm widget dragged in and "Zycus Demo Request" selected in the dropdown]*

---

## Appendix — Free-email domain blocking via `functions.php`

If you can't use the regex validator above, drop this into `functions.php`:

```php
add_filter('metform/validate/field/email', function($is_valid, $value, $field) {
    if ($field['mf_input_name'] !== 'email') return $is_valid;
    $blocked = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com'];
    $domain = strtolower(substr(strrchr($value, '@'), 1));
    if (in_array($domain, $blocked, true)) {
        return new WP_Error('free_email', 'Please use your work email');
    }
    return $is_valid;
}, 10, 3);
```

---

## Summary checklist

- [ ] Form created with slug `zycus_demo`
- [ ] 3 steps with correct names and progress indicator ON
- [ ] All 8 fields added with exact name attributes
- [ ] Submit button reads `Book My Demo`
- [ ] Trust microcopy below submit button
- [ ] Email notification to `sales@zycus.com` with body shortcodes
- [ ] Conditional redirects set (Pro) or `functions.php` snippet added (free)
- [ ] Free-email domain rejection configured
- [ ] Form embedded on a page via Elementor's MetForm widget
