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
  // Mobile menu — clean rebuild
  // Markup: <button class="menu-btn">, <div id="mobile-menu">
  //         containing .mobile-menu__backdrop + .mobile-menu__panel.
  // Open = add .is-open on the wrapper + .menu-open on body.
  // Close = click backdrop, click close button, click any link,
  //         press Escape, or viewport crosses into desktop.
  // =========================================================
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
      if (mainEl) {
        mainEl.setAttribute('aria-hidden', 'true');
        mainEl.setAttribute('inert', '');
      }
      const firstLink = panel && panel.querySelector('a');
      if (firstLink) firstLink.focus({ preventScroll: true });
    };

    const close = () => {
      menuWrap.classList.remove('is-open');
      menuWrap.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('menu-open');
      menuBtn.setAttribute('aria-expanded', 'false');
      menuBtn.setAttribute('aria-label', 'Open navigation menu');
      if (mainEl) {
        mainEl.removeAttribute('aria-hidden');
        mainEl.removeAttribute('inert');
      }
      menuBtn.focus({ preventScroll: true });
    };

    menuBtn.addEventListener('click', () => {
      if (menuWrap.classList.contains('is-open')) close(); else open();
    });

    menuWrap.querySelectorAll('[data-menu-close]').forEach((el) => {
      el.addEventListener('click', close);
    });

    menuWrap.querySelectorAll('.mobile-menu__panel a').forEach((link) => {
      link.addEventListener('click', close);
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && menuWrap.classList.contains('is-open')) close();
    });

    const mq = window.matchMedia('(min-width: 768px)');
    mq.addEventListener('change', (e) => {
      if (e.matches && menuWrap.classList.contains('is-open')) close();
    });
  }
})();
