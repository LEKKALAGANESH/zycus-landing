<section id="demo-form" class="section section--dark" aria-labelledby="demo-heading">
  <div class="container">
    <div class="demo-card">
      <div class="section__head">
        <h2 id="demo-heading" class="h2">See Merlin AI Live on Your Own Data</h2>
        <p class="section__sub">Book a 30-minute working session with a Zycus solution architect. We'll plug Merlin into a sample of your spend and walk you through the results.</p>
      </div>

      <form id="demo-form-el" class="demo-form" method="POST" action="/api/submit.php" novalidate aria-describedby="demo-form-help">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars(\Zycus\Csrf::token(), ENT_QUOTES) ?>">
        <input type="text" name="website_url" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px" aria-hidden="true">

        <fieldset class="form-step">
          <legend class="form-step__legend">
            <span class="form-step__label">Step 1 of 3</span>
            <span class="form-step__title">About You</span>
          </legend>
          <div class="form-grid">
            <div class="form-field form-field--full">
              <label for="email">Work Email <span class="req" aria-hidden="true">*</span></label>
              <input type="email" id="email" name="email" required placeholder="you@company.com" />
            </div>
            <div class="form-field">
              <label for="first_name">First Name <span class="req" aria-hidden="true">*</span></label>
              <input type="text" id="first_name" name="first_name" required />
            </div>
            <div class="form-field">
              <label for="last_name">Last Name <span class="req" aria-hidden="true">*</span></label>
              <input type="text" id="last_name" name="last_name" required />
            </div>
          </div>
        </fieldset>

        <fieldset class="form-step">
          <legend class="form-step__legend">
            <span class="form-step__label">Step 2 of 3</span>
            <span class="form-step__title">About Your Company</span>
          </legend>
          <div class="form-grid">
            <div class="form-field form-field--full">
              <label for="company_name">Company Name <span class="req" aria-hidden="true">*</span></label>
              <input type="text" id="company_name" name="company_name" required />
            </div>
            <div class="form-field">
              <label for="company_size">Company Size <span class="req" aria-hidden="true">*</span></label>
              <select id="company_size" name="company_size" required aria-describedby="company_size_hint" data-combobox>
                <option value="" disabled selected>Select your company size&hellip;</option>
                <optgroup label="SMB">
                  <option value="small">1&ndash;49 employees &mdash; Startup / SMB</option>
                  <option value="mid">50&ndash;499 employees &mdash; Mid-Market</option>
                </optgroup>
                <optgroup label="Enterprise">
                  <option value="enterprise">500&ndash;4,999 employees &mdash; Enterprise</option>
                  <option value="large_enterprise">5,000+ employees &mdash; Large Enterprise / Fortune 500</option>
                </optgroup>
              </select>
              <small id="company_size_hint" class="form-hint">Helps us route you to the right solution architect.</small>
            </div>
            <div class="form-field">
              <label for="role">Your Role <span class="req" aria-hidden="true">*</span></label>
              <select id="role" name="role" required aria-describedby="role_hint" data-combobox>
                <option value="" disabled selected>Select your role&hellip;</option>
                <optgroup label="Decision Makers">
                  <option value="procurement_leader">Procurement Leader (CPO / VP / Director)</option>
                  <option value="finance_leader">Finance Leader (CFO / VP Finance)</option>
                </optgroup>
                <optgroup label="Practitioners">
                  <option value="procurement_team">Procurement Team Member</option>
                  <option value="it">IT / Systems Owner</option>
                </optgroup>
                <option value="other">Other</option>
              </select>
              <small id="role_hint" class="form-hint">Tailors the demo to your day-to-day priorities.</small>
            </div>
          </div>
        </fieldset>

        <fieldset class="form-step">
          <legend class="form-step__legend">
            <span class="form-step__label">Step 3 of 3</span>
            <span class="form-step__title">Your Priority</span>
          </legend>
          <div class="form-grid">
            <div class="form-field form-field--full">
              <label for="use_case">Primary Use Case <span class="req" aria-hidden="true">*</span></label>
              <select id="use_case" name="use_case" required aria-describedby="use_case_hint" data-combobox>
                <option value="" disabled selected>What do you want Merlin AI to solve first?</option>
                <optgroup label="Upstream (Sourcing &amp; Contracts)">
                  <option value="s2c">Sourcing &amp; Contract Management</option>
                  <option value="supplier_mgmt">Supplier Management &amp; Risk</option>
                </optgroup>
                <optgroup label="Downstream (Procure-to-Pay)">
                  <option value="ap">Invoice Automation &amp; AP</option>
                </optgroup>
                <optgroup label="Full Platform">
                  <option value="s2p">End-to-End Source-to-Pay</option>
                </optgroup>
              </select>
              <small id="use_case_hint" class="form-hint">We'll configure the demo around this workflow first.</small>
            </div>
            <div class="form-field form-field--full">
              <label for="notes">Anything else we should know?</label>
              <textarea id="notes" name="notes" rows="4" placeholder="Optional — current systems, timelines, constraints..."></textarea>
            </div>
          </div>
        </fieldset>

        <button type="submit" class="btn btn--primary btn--lg btn--block">Book My Demo</button>
        <p id="demo-form-help" class="demo-form__microcopy">
          <span aria-hidden="true">&#128274;</span> Secure &amp; confidential. We respect your inbox — no spam, no credit card required.
        </p>
      </form>
    </div>
  </div>
</section>
