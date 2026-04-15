<?php $faqs = require __DIR__ . '/../../config/faqs.php'; ?>
<section id="faq" class="section section--light" aria-labelledby="faq-heading">
  <div class="container container--narrow">
    <div class="section__head" data-reveal="up">
      <h2 id="faq-heading" class="h2">Your Questions, Answered</h2>
      <p class="section__sub">Everything procurement, finance, and IT leaders ask us before signing.</p>
    </div>
    <div class="faq" data-stagger="80">
      <?php foreach ($faqs as [$q, $a]): ?>
        <details class="faq__item" data-reveal="up">
          <summary class="faq__q">
            <span><?= htmlspecialchars($q, ENT_QUOTES) ?></span>
            <span class="faq__icon" aria-hidden="true"></span>
          </summary>
          <div class="faq__a-wrap">
            <div class="faq__a"><?= htmlspecialchars($a, ENT_QUOTES) ?></div>
          </div>
        </details>
      <?php endforeach; ?>
    </div>
  </div>
</section>
