(function () {
  'use strict';

  if (matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  /* Duplicate logo items so infinite scroll wraps seamlessly.
     The CSS animation translates -50%, so we need exactly 2× the items. */
  document.querySelectorAll('.logos__track').forEach((track) => {
    const items = [...track.children];
    if (!items.length) return;
    items.forEach((item) => {
      const clone = item.cloneNode(true);
      clone.setAttribute('aria-hidden', 'true');
      track.appendChild(clone);
    });
  });

  /* Pause on hover / focus is handled entirely in CSS:
     .logos:hover .logos__track, .logos:focus-within .logos__track {
       animation-play-state: paused;
     }
     No JS needed for that behaviour. */
})();
