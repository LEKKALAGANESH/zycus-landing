(() => {
  const selects = document.querySelectorAll('select[data-combobox]');
  if (!selects.length) return;

  selects.forEach(enhance);

  function enhance(nativeSelect) {
    const rootId = nativeSelect.id || `cb-${Math.random().toString(36).slice(2, 8)}`;
    if (!nativeSelect.id) nativeSelect.id = rootId;
    const triggerId = `${rootId}-trigger`;
    const listboxId = `${rootId}-listbox`;

    const wrap = document.createElement('div');
    wrap.className = 'combobox';

    const trigger = document.createElement('button');
    trigger.type = 'button';
    trigger.id = triggerId;
    trigger.className = 'combobox__trigger';
    trigger.setAttribute('role', 'combobox');
    trigger.setAttribute('aria-haspopup', 'listbox');
    trigger.setAttribute('aria-expanded', 'false');
    trigger.setAttribute('aria-controls', listboxId);
    if (nativeSelect.required) trigger.setAttribute('aria-required', 'true');
    const describedBy = nativeSelect.getAttribute('aria-describedby');
    if (describedBy) trigger.setAttribute('aria-describedby', describedBy);

    const valueEl = document.createElement('span');
    valueEl.className = 'combobox__value';

    const caret = document.createElement('span');
    caret.className = 'combobox__caret';
    caret.setAttribute('aria-hidden', 'true');
    caret.innerHTML = '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>';

    trigger.append(valueEl, caret);

    const panel = document.createElement('div');
    panel.className = 'combobox__panel';
    panel.id = listboxId;
    panel.setAttribute('role', 'listbox');
    panel.tabIndex = -1;

    const label = document.querySelector(`label[for="${rootId}"]`);
    if (label) {
      label.setAttribute('for', triggerId);
      if (!label.id) label.id = `label-for-${rootId}`;
      panel.setAttribute('aria-labelledby', label.id);
    }

    const options = [];
    let optIdx = 0;

    const buildOption = (opt) => {
      const value = opt.value;
      if (value === '') {
        valueEl.textContent = opt.textContent.trim();
        valueEl.classList.add('is-placeholder');
        return null;
      }
      const node = document.createElement('div');
      node.className = 'combobox__option';
      node.setAttribute('role', 'option');
      node.id = `${rootId}-opt-${optIdx}`;
      node.dataset.value = value;
      node.style.setProperty('--i', String(optIdx));
      const selected = nativeSelect.value === value;
      node.setAttribute('aria-selected', selected ? 'true' : 'false');
      optIdx++;

      const labelSpan = document.createElement('span');
      labelSpan.className = 'combobox__option-label';
      labelSpan.textContent = opt.textContent.trim();

      const check = document.createElement('span');
      check.className = 'combobox__option-check';
      check.setAttribute('aria-hidden', 'true');
      check.innerHTML = '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>';

      node.append(labelSpan, check);
      return node;
    };

    Array.from(nativeSelect.children).forEach((child) => {
      if (child.tagName === 'OPTGROUP') {
        const group = document.createElement('div');
        group.className = 'combobox__group';
        const lbl = document.createElement('div');
        lbl.className = 'combobox__group-label';
        lbl.textContent = child.label;
        lbl.setAttribute('role', 'presentation');
        group.append(lbl);
        Array.from(child.children).forEach((opt) => {
          const node = buildOption(opt);
          if (node) { group.append(node); options.push(node); }
        });
        panel.append(group);
      } else if (child.tagName === 'OPTION') {
        const node = buildOption(child);
        if (node) { panel.append(node); options.push(node); }
      }
    });

    if (nativeSelect.value !== '') {
      const selOpt = Array.from(nativeSelect.options).find((o) => o.value === nativeSelect.value);
      if (selOpt) {
        valueEl.textContent = selOpt.textContent.trim();
        valueEl.classList.remove('is-placeholder');
      }
    }

    nativeSelect.parentNode.insertBefore(wrap, nativeSelect);
    wrap.append(trigger, panel, nativeSelect);
    nativeSelect.classList.add('combobox__native');

    let isOpen = false;
    let activeIndex = options.findIndex((o) => o.getAttribute('aria-selected') === 'true');

    const setActive = (idx) => {
      const clamped = Math.max(0, Math.min(options.length - 1, idx));
      activeIndex = clamped;
      options.forEach((o, i) => o.classList.toggle('is-active', i === clamped));
      const active = options[clamped];
      if (!active) return;
      panel.setAttribute('aria-activedescendant', active.id);
      const aRect = active.getBoundingClientRect();
      const pRect = panel.getBoundingClientRect();
      if (aRect.bottom > pRect.bottom || aRect.top < pRect.top) {
        active.scrollIntoView({ block: 'nearest' });
      }
    };

    const onDocClick = (e) => { if (!wrap.contains(e.target)) close(false); };

    const open = () => {
      if (isOpen) return;
      isOpen = true;
      trigger.setAttribute('aria-expanded', 'true');
      wrap.classList.add('is-open');
      if (activeIndex < 0) activeIndex = 0;
      setActive(activeIndex);
      panel.focus({ preventScroll: true });
      document.addEventListener('click', onDocClick, true);
    };

    const close = (refocusTrigger = true) => {
      if (!isOpen) return;
      isOpen = false;
      trigger.setAttribute('aria-expanded', 'false');
      wrap.classList.remove('is-open');
      document.removeEventListener('click', onDocClick, true);
      options.forEach((o) => o.classList.remove('is-active'));
      panel.removeAttribute('aria-activedescendant');
      if (refocusTrigger) trigger.focus({ preventScroll: true });
    };

    const select = (idx) => {
      const opt = options[idx];
      if (!opt) return;
      const value = opt.dataset.value;
      nativeSelect.value = value;
      options.forEach((o) => o.setAttribute('aria-selected', 'false'));
      opt.setAttribute('aria-selected', 'true');
      const labelNode = opt.querySelector('.combobox__option-label');
      valueEl.textContent = labelNode ? labelNode.textContent : value;
      valueEl.classList.remove('is-placeholder');
      nativeSelect.dispatchEvent(new Event('change', { bubbles: true }));
      nativeSelect.dispatchEvent(new Event('input', { bubbles: true }));
      close();
    };

    trigger.addEventListener('click', (e) => { e.preventDefault(); isOpen ? close() : open(); });
    trigger.addEventListener('keydown', (e) => {
      if (['ArrowDown', 'ArrowUp', 'Enter', ' '].includes(e.key)) { e.preventDefault(); open(); }
    });

    panel.addEventListener('keydown', (e) => {
      switch (e.key) {
        case 'ArrowDown': e.preventDefault(); setActive(activeIndex + 1); break;
        case 'ArrowUp':   e.preventDefault(); setActive(activeIndex - 1); break;
        case 'Home':      e.preventDefault(); setActive(0); break;
        case 'End':       e.preventDefault(); setActive(options.length - 1); break;
        case 'Enter':
        case ' ':         e.preventDefault(); select(activeIndex); break;
        case 'Escape':    e.preventDefault(); close(); break;
        case 'Tab':       close(false); break;
        default:
          if (/^[a-z0-9]$/i.test(e.key)) {
            const needle = e.key.toLowerCase();
            for (let i = 1; i <= options.length; i++) {
              const idx = (activeIndex + i) % options.length;
              const text = options[idx].textContent.trim().toLowerCase();
              if (text.startsWith(needle)) { setActive(idx); break; }
            }
          }
      }
    });

    options.forEach((opt, i) => {
      opt.addEventListener('click', (e) => { e.preventDefault(); select(i); });
      opt.addEventListener('mouseenter', () => setActive(i));
    });

    // Forward focus from the hidden native element to the trigger so that
    // form.js's validation auto-focus still visibly lands on the combobox.
    nativeSelect.addEventListener('focus', () => trigger.focus(), true);

    // Mirror `is-invalid` state from the native (set by form.js) onto the
    // trigger so the red-border + light-red background render correctly.
    const syncInvalid = () => {
      const bad = nativeSelect.classList.contains('is-invalid');
      trigger.classList.toggle('is-invalid', bad);
      if (bad) trigger.setAttribute('aria-invalid', 'true');
      else trigger.removeAttribute('aria-invalid');
    };
    new MutationObserver(syncInvalid).observe(nativeSelect, {
      attributes: true, attributeFilter: ['class'],
    });
  }
})();
