(function () {
  'use strict';

  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (reduce) {
    document.querySelectorAll('[data-reveal]').forEach((el) => el.classList.add('is-visible'));
    return;
  }

  /* Stagger delays for grouped children */
  document.querySelectorAll('[data-stagger]').forEach((group) => {
    const step = parseInt(group.getAttribute('data-stagger'), 10) || 80;
    let i = 0;
    Array.from(group.children).forEach((child) => {
      if (child.hasAttribute('data-reveal')) {
        child.style.setProperty('--d', (i * step) + 'ms');
        i += 1;
      }
    });
  });

  /* Hero reveals fire immediately on rAF */
  const heroReveals = document.querySelectorAll('.hero [data-reveal]');
  if (heroReveals.length) {
    requestAnimationFrame(() => heroReveals.forEach((el) => el.classList.add('is-visible')));
  }

  /* Scroll-triggered reveal for everything else */
  const revealObserver = new IntersectionObserver((entries, obs) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        obs.unobserve(entry.target);
      }
    });
  }, { rootMargin: '0px 0px -100px 0px', threshold: 0.05 });

  document.querySelectorAll('[data-reveal]').forEach((el) => {
    if (!el.closest('.hero')) revealObserver.observe(el);
  });

  /* Logo section entrance */
  const logosSection = document.querySelector('.logos');
  if (logosSection) {
    const logoObserver = new IntersectionObserver((entries, obs) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          obs.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1 });
    logoObserver.observe(logosSection);
  }

  /* Animated counters */
  function animateCounter(el) {
    const target   = parseFloat(el.getAttribute('data-counter')) || 0;
    const decimals = parseInt(el.getAttribute('data-decimals'), 10) || 0;
    const prefix   = el.getAttribute('data-prefix') || '';
    const suffix   = el.getAttribute('data-suffix') || '';
    const duration = 1200;
    const start    = performance.now();

    (function frame(now) {
      const t = Math.min(1, (now - start) / duration);
      el.textContent = prefix + (target * (1 - Math.pow(1 - t, 3))).toFixed(decimals) + suffix;
      if (t < 1) requestAnimationFrame(frame);
    })(performance.now());
  }

  const counterObserver = new IntersectionObserver((entries, obs) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) { animateCounter(entry.target); obs.unobserve(entry.target); }
    });
  }, { threshold: 0.5 });

  document.querySelectorAll('[data-counter]').forEach((el) => counterObserver.observe(el));

  /* FAQ single-open accordion */
  const faqItems = [...document.querySelectorAll('details.faq__item, .faq__item')];

  faqItems.forEach((item) => {
    const wrap = item.querySelector('.faq__a-wrap');
    if (wrap) {
      wrap.addEventListener('transitionend', (ev) => {
        if (ev.target !== wrap || ev.propertyName !== 'grid-template-rows') return;
        if (!item.classList.contains('is-open')) item.removeAttribute('open');
      });
    }
  });

  const closeFaq = (item) => {
    if (!item.classList.contains('is-open')) return;
    item.classList.remove('is-open');
  };

  const openFaq = (item) => {
    if (item.classList.contains('is-open')) return;
    item.setAttribute('open', '');
    const wrap = item.querySelector('.faq__a-wrap');
    if (wrap) void wrap.offsetHeight;
    item.classList.add('is-open');
  };

  faqItems.forEach((item) => {
    const summary = item.querySelector('summary, .faq__q');
    if (!summary) return;
    summary.addEventListener('click', (e) => {
      e.preventDefault();
      const alreadyOpen = item.classList.contains('is-open');
      faqItems.forEach((other) => { if (other !== item) closeFaq(other); });
      alreadyOpen ? closeFaq(item) : openFaq(item);
    });
  });

  /* Sticky header glass effect */
  const sentinel = document.querySelector('.scroll-sentinel');
  const header   = document.querySelector('.site-header');
  if (sentinel && header) {
    new IntersectionObserver((entries) => {
      entries.forEach((e) => {
        header.classList.toggle('is-scrolled', !e.isIntersecting);
      });
    }).observe(sentinel);
  }

  /* Mobile menu */
  const menuBtn  = document.querySelector('.menu-btn');
  const menuWrap = document.getElementById('mobile-menu');
  const mainEl   = document.getElementById('main-content');

  if (menuBtn && menuWrap) {
    const panel = menuWrap.querySelector('.mobile-menu__panel');

    const open = () => {
      menuWrap.classList.add('is-open');
      menuWrap.setAttribute('aria-hidden', 'false');
      document.body.classList.add('menu-open');
      menuBtn.setAttribute('aria-expanded', 'true');
      menuBtn.setAttribute('aria-label', 'Close navigation menu');
      if (mainEl) { mainEl.setAttribute('aria-hidden', 'true'); mainEl.setAttribute('inert', ''); }
      const firstLink = panel && panel.querySelector('a');
      if (firstLink) firstLink.focus({ preventScroll: true });
    };

    const close = () => {
      menuWrap.classList.remove('is-open');
      menuWrap.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('menu-open');
      menuBtn.setAttribute('aria-expanded', 'false');
      menuBtn.setAttribute('aria-label', 'Open navigation menu');
      if (mainEl) { mainEl.removeAttribute('aria-hidden'); mainEl.removeAttribute('inert'); }
      menuBtn.focus({ preventScroll: true });
    };

    menuBtn.addEventListener('click', () => menuWrap.classList.contains('is-open') ? close() : open());
    menuWrap.querySelectorAll('[data-menu-close]').forEach((el) => el.addEventListener('click', close));
    menuWrap.querySelectorAll('.mobile-menu__panel a').forEach((link) => link.addEventListener('click', close));
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && menuWrap.classList.contains('is-open')) close(); });
    window.matchMedia('(min-width: 768px)').addEventListener('change', (e) => {
      if (e.matches && menuWrap.classList.contains('is-open')) close();
    });
  }

  /* Multi-step form logic (for shortcode fallback form) */
  const form = document.querySelector('#demo-form-el');
  if (form) {
    const submitBtn = form.querySelector('[type="submit"]');
    const originalText = submitBtn ? submitBtn.textContent : 'Book My Demo';
    const steps = [...form.querySelectorAll('.form-step')];
    let currentStep = 0;

    const showStep = (idx) => steps.forEach((s, i) => { s.hidden = i !== idx; });

    const markInvalid = (field, msg) => {
      field.classList.add('is-invalid');
      field.setAttribute('aria-invalid', 'true');
      const errId = 'err-' + (field.name || field.id);
      let err = document.getElementById(errId);
      if (!err) {
        err = document.createElement('small');
        err.className = 'form-error';
        err.id = errId;
        field.parentNode.insertBefore(err, field.nextSibling);
      }
      err.textContent = msg;
    };

    const validateStep = (idx) => {
      const step = steps[idx];
      if (!step) return true;
      let first = null;
      for (const field of step.querySelectorAll('[required]')) {
        if (!(field.value || '').trim()) {
          const label = form.querySelector(`label[for="${field.id}"]`);
          const name  = label ? label.textContent.replace('*', '').trim() : 'This field';
          markInvalid(field, name + ' is required.');
          if (!first) first = field;
        }
      }
      if (first) { first.focus(); return false; }
      return true;
    };

    steps.forEach((step, idx) => {
      const nav = document.createElement('div');
      nav.className = 'form-step-nav';
      if (idx > 0) {
        const back = document.createElement('button');
        back.type = 'button'; back.className = 'btn-back'; back.textContent = 'Back';
        back.addEventListener('click', () => { currentStep = idx - 1; showStep(currentStep); });
        nav.appendChild(back);
      }
      if (idx < steps.length - 1) {
        const next = document.createElement('button');
        next.type = 'button'; next.className = 'btn-next'; next.textContent = 'Next';
        next.addEventListener('click', () => {
          if (!validateStep(idx)) return;
          currentStep = idx + 1;
          showStep(currentStep);
          const first = steps[currentStep].querySelector('input,select,textarea');
          if (first) first.focus();
        });
        nav.appendChild(next);
      }
      step.appendChild(nav);
    });

    showStep(0);

    form.addEventListener('submit', (e) => {
      e.preventDefault();
      form.querySelectorAll('.is-invalid').forEach((el) => el.classList.remove('is-invalid'));
      form.querySelectorAll('.form-error').forEach((el) => el.remove());

      for (let i = 0; i < steps.length; i++) {
        if (!validateStep(i)) {
          if (currentStep !== i) { currentStep = i; showStep(i); }
          return;
        }
      }

      if (submitBtn) { submitBtn.disabled = true; submitBtn.textContent = 'Sending…'; }

      fetch(form.action, { method: 'POST', body: new FormData(form) })
        .then((r) => {
          if (r.redirected) { window.dataLayer = window.dataLayer || []; window.dataLayer.push({ event: 'generate_lead', form_id: 'zycus_demo' }); location.assign(r.url); }
          else { if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = originalText; } }
        })
        .catch(() => { if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = originalText; } });
    });
  }
})();
