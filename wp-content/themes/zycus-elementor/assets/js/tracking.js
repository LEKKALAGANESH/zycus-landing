(function () {
  'use strict';

  window.dataLayer = window.dataLayer || [];

  function push(payload) {
    window.dataLayer.push(payload);
  }

  /* CTA clicks */
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.btn--accent, .btn--primary, [data-track-cta]');
    if (!btn) return;
    push({
      event:        'cta_click',
      cta_text:     btn.textContent.trim(),
      cta_location: btn.closest('section')?.id || btn.closest('[class*="elementor-section"]')?.getAttribute('data-id') || 'unknown',
    });
  });

  /* FAQ open — works for both custom .faq__item and Elementor accordion */
  document.addEventListener('click', (e) => {
    const summary = e.target.closest('.faq__q, .elementor-tab-title');
    if (!summary) return;
    const question =
      summary.querySelector('span:first-child')?.textContent?.trim() ||
      summary.textContent?.trim() ||
      '';
    const index = [...document.querySelectorAll('.faq__q, .elementor-tab-title')].indexOf(summary);
    push({ event: 'faq_open', faq_question: question, faq_index: index });
  });

  /* Form step advance (custom multi-step form) */
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.btn-next');
    if (!btn) return;
    const form = btn.closest('form');
    if (!form) return;
    const steps   = [...form.querySelectorAll('.form-step')];
    const visible = steps.findIndex((s) => !s.hidden);
    push({ event: 'form_step_advance', form_id: form.id || 'zycus_demo', step_index: visible, step_label: 'Step ' + (visible + 1) });
  });

  /* Elementor form success — fires generate_lead */
  document.addEventListener('submit_success', (e) => {
    push({ event: 'generate_lead', form_id: 'zycus_demo_elementor', form_location: 'demo_form_section' });
  });

  /* Thank-you page — listen for Elementor form redirect via message */
  if (typeof jQuery !== 'undefined') {
    jQuery(document).on('elementor_pro/forms/submit_success', (event, response) => {
      push({ event: 'generate_lead', form_id: 'zycus_demo_elementor', form_location: 'demo_form_section' });
    });
  }
})();
