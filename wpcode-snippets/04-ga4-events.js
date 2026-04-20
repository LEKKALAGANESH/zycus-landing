/* WPCode Snippet: GA4 Custom Events — Location: "Footer" (type: JavaScript)
   GA4 Measurement ID: G-1MG1YKNRDF
   GTM Container:      GTM-KG8889HK (fires GA4 tag internally)

   This script pushes named events to window.dataLayer which GTM
   forwards to GA4. All events mirror the original PHP app's analytics spec.
   ---------------------------------------------------------------- */
(function () {
  'use strict';

  window.dataLayer = window.dataLayer || [];

  /* --- CTA Click ------------------------------------------------ */
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('.elementor-button, .btn--accent, .btn--primary');
    if (!btn) return;
    window.dataLayer.push({
      event:        'cta_click',
      cta_text:     btn.textContent.trim(),
      cta_location: (btn.closest('.elementor-section') || {}).id || 'unknown'
    });
  });

  /* --- FAQ Open ------------------------------------------------- */
  document.addEventListener('click', function (e) {
    var tab = e.target.closest('.elementor-tab-title, .faq__q');
    if (!tab) return;
    window.dataLayer.push({
      event:        'faq_open',
      faq_question: tab.textContent.trim().slice(0, 120),
      faq_index:    Array.from(document.querySelectorAll('.elementor-tab-title, .faq__q')).indexOf(tab)
    });
  });

  /* --- Elementor Form Success ----------------------------------- */
  document.addEventListener('DOMContentLoaded', function () {
    if (typeof jQuery === 'undefined') return;
    jQuery(document).on('elementor_pro/forms/submit_success', function (event, response) {
      var email = '';
      try { email = response.data.fields.find(function(f){ return f.id === 'email'; }).value || ''; } catch(e) {}
      window.dataLayer.push({
        event:         'generate_lead',
        form_id:       'zycus_demo',
        form_location: 'demo_form_section',
        value:         0,
        currency:      'USD',
        email_type:    email.indexOf('@gmail') > -1 || email.indexOf('@yahoo') > -1 ? 'consumer' : 'business'
      });
    });
  });

  /* --- Page view (fallback if GTM pageview tag is not set up) --- */
  window.dataLayer.push({
    event:          'page_view',
    page_location:  window.location.href,
    page_title:     document.title,
    page_referrer:  document.referrer
  });
})();
