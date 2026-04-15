<?php declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

$title = 'Thank You — Your Zycus Demo is Confirmed | Zycus';
$desc  = 'Your Zycus Merlin AI demo request is confirmed. A procurement specialist will email you within 24 hours with calendar options.';
$tier  = (string) ($_GET['tier'] ?? '');

$lead       = $_SESSION['zycus_lead'] ?? [];
$firstName  = (string) ($lead['first_name'] ?? '');
$email      = (string) ($lead['email'] ?? '');
$hasLead    = $firstName !== '' && $email !== '';
$safeName   = htmlspecialchars($firstName, ENT_QUOTES);
$safeEmail  = htmlspecialchars($email, ENT_QUOTES);

if ($hasLead) {
    unset($_SESSION['zycus_lead']);
}

require __DIR__ . '/../templates/header.php';
?>
<section class="section thank-you" aria-labelledby="thank-you-heading">
  <div class="container">
    <div class="thank-you__card">
      <div class="thank-you__check" aria-hidden="true">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
      </div>
      <h1 id="thank-you-heading" class="h1">Demo Request Confirmed!</h1>
      <?php if ($hasLead): ?>
        <p class="lead">
          Hi <strong><?= $safeName ?></strong>, thank you for requesting a demo. We have successfully received your details via <strong><?= $safeEmail ?></strong>. A Zycus Agentic AI specialist will review your request and reach out within <strong>24 hours</strong> to schedule your personalized walkthrough. While you wait, keep an eye on your inbox for our latest <strong>S2P Diagnostic Guide</strong>.
        </p>
      <?php else: ?>
        <p class="lead">
          Thank you for requesting a demo. A Zycus Agentic AI specialist will review your request and reach out within <strong>24 hours</strong> to schedule your personalized walkthrough. While you wait, keep an eye on your inbox for our latest <strong>S2P Diagnostic Guide</strong>.
        </p>
      <?php endif; ?>

      <div class="thank-you__next">
        <a href="/assets/downloads/zycus-s2p-diagnostic-guide.pdf"
           class="btn btn--secondary btn--lg"
           rel="noopener">
          Download the S2P Diagnostic Guide
        </a>
        <a href="https://www.zycus.com/resource-center"
           class="thank-you__offramp"
           rel="noopener">
          Explore more resources &rarr;
        </a>
      </div>
    </div>
  </div>
</section>
<script>
  window.dataLayer = window.dataLayer || [];
  window.dataLayer.push({
    event: 'demo_confirmed',
    form_id: 'zycus_demo',
    tier: <?= json_encode($tier !== '' ? $tier : 'standard') ?>,
    personalized: <?= $hasLead ? 'true' : 'false' ?>
  });
</script>
<?php
require __DIR__ . '/../templates/footer.php';
