(() => {
  const form = document.querySelector("#demo-form-el");
  if (!form) return;

  const submitBtn = form.querySelector('[type="submit"]');
  const originalSubmitText = submitBtn ? submitBtn.textContent : "Book My Demo";
  const steps = [...form.querySelectorAll(".form-step")];

  let liveRegion = form.querySelector(".form-live");
  if (!liveRegion) {
    liveRegion = document.createElement("p");
    liveRegion.className = "form-live";
    liveRegion.setAttribute("role", "status");
    liveRegion.setAttribute("aria-live", "polite");
    liveRegion.style.cssText =
      "position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden;";
    form.appendChild(liveRegion);
  }
  const announce = (msg) => {
    liveRegion.textContent = "";
    setTimeout(() => (liveRegion.textContent = msg), 60);
  };

  const showStep = (idx) => {
    steps.forEach((step, i) => {
      step.hidden = i !== idx;
    });
  };

  const markInvalid = (field, message) => {
    field.classList.add("is-invalid");
    field.setAttribute("aria-invalid", "true");
    const errId = `err-${field.name || field.id}`;
    let err = document.getElementById(errId);
    if (!err) {
      err = document.createElement("small");
      err.className = "form-error";
      err.id = errId;
      field.parentNode.insertBefore(err, field.nextSibling);
    }
    err.textContent = message;
    field.setAttribute("aria-describedby", errId);
  };

  const labelFor = (field) => {
    const label = form.querySelector(`label[for="${field.id}"]`);
    return label ? label.textContent.replace("*", "").trim() : "This field";
  };

  const stepIndexOf = (field) => steps.findIndex((s) => s.contains(field));

  const validateStep = (idx) => {
    const step = steps[idx];
    if (!step) return true;
    const requiredFields = [...step.querySelectorAll("[required]")];
    let firstInvalidField = null;
    for (const field of requiredFields) {
      const value = (field.value ?? "").trim();
      if (!value) {
        const labelText = labelFor(field);
        markInvalid(field, `${labelText} is required.`);
        if (!firstInvalidField) firstInvalidField = field;
      }
    }
    if (firstInvalidField) {
      firstInvalidField.focus();
      announce(`${labelFor(firstInvalidField)} is required.`);
      return false;
    }
    return true;
  };

  const validateAllSteps = () => {
    for (let i = 0; i < steps.length; i++) {
      if (!validateStep(i)) {
        if (currentStep !== i) {
          currentStep = i;
          showStep(i);
        }
        const invalid = steps[i].querySelector(".is-invalid");
        if (invalid instanceof HTMLElement) invalid.focus();
        return false;
      }
    }
    return true;
  };

  const clearErrors = () => {
    form.querySelectorAll(".is-invalid").forEach((el) => {
      el.classList.remove("is-invalid");
      el.removeAttribute("aria-invalid");
    });
    form.querySelectorAll(".form-error").forEach((el) => el.remove());
  };

  const restoreButton = () => {
    if (!submitBtn) return;
    submitBtn.disabled = false;
    submitBtn.textContent = originalSubmitText;
  };

  let currentStep = 0;

  steps.forEach((step, idx) => {
    const nav = document.createElement("div");
    nav.className = "form-step-nav";

    if (idx === 1 || idx === 2) {
      const back = document.createElement("button");
      back.type = "button";
      back.className = "btn-back";
      back.textContent = "Back";
      back.addEventListener("click", () => {
        currentStep = idx - 1;
        showStep(currentStep);
        announce(`Step ${currentStep + 1} of ${steps.length}`);
        const firstInput = steps[currentStep].querySelector(
          "input, select, textarea",
        );
        if (firstInput) firstInput.focus();
      });
      nav.appendChild(back);
    }

    if (idx === 0 || idx === 1) {
      const next = document.createElement("button");
      next.type = "button";
      next.className = "btn-next";
      next.textContent = "Next";
      next.addEventListener("click", () => {
        if (!validateStep(idx)) return;
        currentStep = idx + 1;
        showStep(currentStep);
        announce(`Step ${currentStep + 1} of ${steps.length}`);
        const firstInput = steps[currentStep].querySelector(
          "input, select, textarea",
        );
        if (firstInput) firstInput.focus();
      });
      nav.appendChild(next);
    }

    step.appendChild(nav);
  });

  showStep(0);

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    clearErrors();

    if (!validateAllSteps()) {
      return;
    }

    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.textContent = "Sending…";
    }

    try {
      const res = await fetch(form.action, {
        method: "POST",
        body: new FormData(form),
      });
      const data = await res.json();

      if (data.ok) {
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
          event: "generate_lead",
          form_id: "zycus_demo",
        });
        location.assign(data.redirect);
        return;
      }

      if (data.errors) {
        let firstInvalid = null;
        let firstInvalidStepIdx = -1;
        for (const [field, message] of Object.entries(data.errors)) {
          const input = form.querySelector(`[name="${field}"]`);
          if (!input) continue;
          markInvalid(input, String(message));
          if (!firstInvalid) {
            firstInvalid = input;
            firstInvalidStepIdx = stepIndexOf(input);
          }
        }
        if (firstInvalidStepIdx >= 0 && firstInvalidStepIdx !== currentStep) {
          currentStep = firstInvalidStepIdx;
          showStep(currentStep);
        }
        if (firstInvalid instanceof HTMLElement) firstInvalid.focus();
        announce("Please fix the highlighted fields.");
        restoreButton();
        return;
      }

      if (data.errorType === "database") {
        showApologyModal("Connection Interrupted", buildDbErrorBody());
        announce(
          "Connection interrupted. Your request could not be processed.",
        );
        restoreButton();
        return;
      }

      showApologyModal(
        "Submission failed.",
        data.error ||
          "Something went wrong. Please try again, or email sales@zycus.landing.com to schedule your demo.",
      );
      announce(data.error || "Submission failed.");
      restoreButton();
    } catch {
      showApologyModal("Connection Interrupted", buildDbErrorBody());
      announce("Network error. Please try again.");
      restoreButton();
    }
  });

  function getLeadName() {
    const v = (form.querySelector('[name="first_name"]')?.value ?? "").trim();
    return v.split(/\s+/)[0] || "";
  }
  function getLeadEmail() {
    return (form.querySelector('[name="email"]')?.value ?? "").trim();
  }
  function buildDbErrorBody() {
    const name = getLeadName();
    const email = getLeadEmail();
    const greeting = name ? `Hi ${name}, ` : "";
    const emailPhrase = email ? ` for ${email}` : "";
    return `${greeting}we sincerely apologize, but we are experiencing a temporary server issue and couldn't process your request${emailPhrase}. Please wait a few moments and try submitting again. If the issue persists, you can bypass this form and email our team directly at sales@zycus.landing.com to schedule your demo.`;
  }

  function showApologyModal(headline, body) {
    document.getElementById("zycus-apology-modal")?.remove();

    const modal = document.createElement("div");
    modal.id = "zycus-apology-modal";
    modal.className = "apology-modal";
    modal.setAttribute("role", "alertdialog");
    modal.setAttribute("aria-modal", "true");
    modal.setAttribute("aria-labelledby", "apology-modal-headline");
    modal.setAttribute("aria-describedby", "apology-modal-body");

    modal.innerHTML = `
      <div class="apology-modal__backdrop" data-close></div>
      <div class="apology-modal__card" role="document">
        <button type="button" class="apology-modal__close" aria-label="Close" data-close>&times;</button>
        <h2 id="apology-modal-headline" class="apology-modal__headline">${escapeHtml(headline)}</h2>
        <p id="apology-modal-body" class="apology-modal__body">${escapeHtml(body)}</p>
        <div class="apology-modal__actions">
          <button type="button" class="btn btn--primary" data-close>Try Again</button>
          <a href="mailto:sales@zycus.landing.com?subject=Zycus%20Demo%20Request" class="btn btn--secondary">Email Sales Instead</a>
        </div>
      </div>
    `;

    document.body.appendChild(modal);
    const prevFocus = document.activeElement;
    const closeBtn = modal.querySelector(".apology-modal__close");
    if (closeBtn instanceof HTMLElement) closeBtn.focus();

    const close = () => {
      modal.remove();
      if (prevFocus instanceof HTMLElement) prevFocus.focus();
    };
    modal.addEventListener("click", (ev) => {
      if (
        ev.target instanceof HTMLElement &&
        ev.target.hasAttribute("data-close")
      )
        close();
    });
    modal.addEventListener("keydown", (ev) => {
      if (ev.key === "Escape") close();
    });
  }

  function escapeHtml(str) {
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#39;");
  }
})();
