(function () {
  'use strict';

  const mobileCta = document.querySelector('.mobile-cta');
  const hero      = document.querySelector('.hero');
  const demoForm  = document.querySelector('#demo-form, [id*="demo-form"]');

  if (!mobileCta) return;

  const state = { heroIn: !!hero, formIn: false };

  const apply = () => {
    const shouldShow = !state.heroIn && !state.formIn;
    mobileCta.classList.toggle('is-visible', shouldShow);
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.target === hero)     state.heroIn = entry.isIntersecting;
      if (entry.target === demoForm) state.formIn = entry.isIntersecting;
    });
    apply();
  }, { threshold: 0.1 });

  if (hero)     observer.observe(hero);
  if (demoForm) observer.observe(demoForm);
})();
