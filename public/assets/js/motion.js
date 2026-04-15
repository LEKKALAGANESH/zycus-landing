(function () {
  'use strict';

  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (reduce) {
    document.querySelectorAll('[data-reveal]').forEach((el) => {
      el.classList.add('is-visible');
    });
    return;
  }

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

  const heroReveals = document.querySelectorAll('.hero [data-reveal]');
  if (heroReveals.length) {
    requestAnimationFrame(() => {
      heroReveals.forEach((el) => el.classList.add('is-visible'));
    });
  }

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

  function animateCounter(el) {
    const target = parseFloat(el.getAttribute('data-counter')) || 0;
    const decimals = parseInt(el.getAttribute('data-decimals'), 10) || 0;
    const prefix = el.getAttribute('data-prefix') || '';
    const suffix = el.getAttribute('data-suffix') || '';
    const duration = 1200;
    const start = performance.now();

    function frame(now) {
      const t = Math.min(1, (now - start) / duration);
      const eased = 1 - Math.pow(1 - t, 3);
      const value = target * eased;
      el.textContent = prefix + value.toFixed(decimals) + suffix;
      if (t < 1) requestAnimationFrame(frame);
    }
    requestAnimationFrame(frame);
  }

  const counterObserver = new IntersectionObserver((entries, obs) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        animateCounter(entry.target);
        obs.unobserve(entry.target);
      }
    });
  }, { threshold: 0.5 });

  document.querySelectorAll('[data-counter]').forEach((el) => counterObserver.observe(el));

  // Single-open accordion driven by a CSS class (.is-open) on <details>,
  // which triggers grid-template-rows: 0fr -> 1fr + opacity/translate/blur
  // on the same easing curve. The native `open` attribute is kept synced
  // for a11y/semantics; it's removed only after the CSS close transition
  // completes so the content stays laid out during the animation.
  const faqItems = [...document.querySelectorAll('details.faq__item')];

  // Attach a single persistent transitionend listener per item — we only
  // act on the grid-rows wrap's transition, ignore the other props.
  faqItems.forEach((item) => {
    const wrap = item.querySelector('.faq__a-wrap');
    if (!wrap) return;
    wrap.addEventListener('transitionend', (ev) => {
      if (ev.target !== wrap) return;
      if (ev.propertyName !== 'grid-template-rows') return;
      if (!item.classList.contains('is-open')) {
        item.removeAttribute('open');
      }
    });
  });

  const closeFaq = (item) => {
    if (!item.classList.contains('is-open')) return;
    item.classList.remove('is-open');
    // `open` attribute is removed by the transitionend listener above.
  };

  const openFaq = (item) => {
    if (item.classList.contains('is-open')) return;
    // Add `open` so <details> children become display:grid (no longer
    // display:none). The wrap's grid-template-rows is still 0fr per CSS.
    item.setAttribute('open', '');
    // Force a synchronous style/layout flush so the browser commits
    // grid-template-rows: 0fr BEFORE we apply .is-open (which flips to 1fr).
    // Reading offsetHeight is cheaper and more reliable than double-rAF,
    // and it avoids the ~33ms click-to-animation lag that rAF introduces.
    const wrap = item.querySelector('.faq__a-wrap');
    if (wrap) void wrap.offsetHeight;
    item.classList.add('is-open');
  };

  faqItems.forEach((item) => {
    const summary = item.querySelector('summary');
    if (!summary) return;
    summary.addEventListener('click', (e) => {
      e.preventDefault();
      const alreadyOpen = item.classList.contains('is-open');
      faqItems.forEach((other) => { if (other !== item) closeFaq(other); });
      if (alreadyOpen) { closeFaq(item); } else { openFaq(item); }
    });
  });

  const sentinel = document.querySelector('.scroll-sentinel');
  const header = document.querySelector('.site-header');
  if (sentinel && header) {
    const headerObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) header.classList.add('is-scrolled');
        else header.classList.remove('is-scrolled');
      });
    });
    headerObserver.observe(sentinel);
  }

  const mobileCta = document.querySelector('.mobile-cta');
  const hero = document.querySelector('.hero');
  const demoForm = document.querySelector('#demo-form');
  if (mobileCta && (hero || demoForm)) {
    const state = { heroIn: !!hero, formIn: false };
    const apply = () => {
      if (state.heroIn || state.formIn) mobileCta.classList.remove('is-visible');
      else mobileCta.classList.add('is-visible');
    };
    const ctaObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.target === hero) state.heroIn = entry.isIntersecting;
        if (entry.target === demoForm) state.formIn = entry.isIntersecting;
      });
      apply();
    }, { threshold: 0.1 });
    if (hero) ctaObserver.observe(hero);
    if (demoForm) ctaObserver.observe(demoForm);
  }

  // =========================================================
  // Mobile hamburger — drawer open/close with scrim + escape
  // =========================================================
  const navToggle = document.querySelector('.nav-toggle');
  const siteNav   = document.getElementById('site-nav');
  if (navToggle && siteNav) {
    const scrim = document.createElement('div');
    scrim.className = 'nav-scrim';
    document.body.appendChild(scrim);

    const mainEl = document.getElementById('main-content');
    const open = () => {
      siteNav.classList.add('is-open');
      scrim.classList.add('is-visible');
      document.body.classList.add('nav-open');
      navToggle.setAttribute('aria-expanded', 'true');
      navToggle.setAttribute('aria-label', 'Close navigation menu');
      if (mainEl) {
        mainEl.setAttribute('aria-hidden', 'true');
        mainEl.setAttribute('inert', '');
      }
      const firstLink = siteNav.querySelector('a');
      if (firstLink) firstLink.focus({ preventScroll: true });
    };
    const close = () => {
      siteNav.classList.remove('is-open');
      scrim.classList.remove('is-visible');
      document.body.classList.remove('nav-open');
      navToggle.setAttribute('aria-expanded', 'false');
      navToggle.setAttribute('aria-label', 'Open navigation menu');
      if (mainEl) {
        mainEl.removeAttribute('aria-hidden');
        mainEl.removeAttribute('inert');
      }
      navToggle.focus({ preventScroll: true });
    };

    navToggle.addEventListener('click', () => {
      const isOpen = siteNav.classList.contains('is-open');
      isOpen ? close() : open();
    });
    scrim.addEventListener('click', close);
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && siteNav.classList.contains('is-open')) close();
    });
    siteNav.querySelectorAll('a').forEach((link) => {
      link.addEventListener('click', () => {
        if (siteNav.classList.contains('is-open')) close();
      });
    });
    // Auto-close when viewport crosses desktop breakpoint
    const mq = window.matchMedia('(min-width: 768px)');
    mq.addEventListener('change', (e) => { if (e.matches && siteNav.classList.contains('is-open')) close(); });
  }
})();
