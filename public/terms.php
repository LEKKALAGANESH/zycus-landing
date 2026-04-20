<?php declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

$title = 'Terms of Use | Zycus';
$desc  = 'Zycus Terms of Use governing access to zycus.com and the Merlin AI demo experience.';

require __DIR__ . '/../templates/header.php';
?>
<section class="section section--light" aria-labelledby="terms-heading">
  <div class="container container--narrow">
    <h1 id="terms-heading" class="h1">Terms of Use</h1>
    <p class="lead">Last updated: <?= date('F j, Y') ?></p>

    <h2 class="h3">1. Acceptance of Terms</h2>
    <p>By accessing zycus.com or submitting a demo request, you agree to these Terms of Use. If you do not agree, please do not use this site.</p>

    <h2 class="h3">2. Use of the Site</h2>
    <p>This site is provided for informational and demo-request purposes. You agree not to attempt to disrupt service, bypass rate limits, or submit falsified information.</p>

    <h2 class="h3">3. Intellectual Property</h2>
    <p>All content, logos, and marks (including the Zycus brain logo and "Merlin AI") are the property of Zycus Inc. and are protected under applicable trademark and copyright law.</p>

    <h2 class="h3">4. Disclaimer</h2>
    <p>The site is provided "as is" without warranties of any kind. Zycus reserves the right to modify or discontinue the site at any time.</p>

    <h2 class="h3">5. Contact</h2>
    <p>For legal inquiries, email <a href="mailto:legal@zycus.com">legal@zycus.com</a>.</p>

    <p><a href="/" class="thank-you__offramp">&larr; Back to home</a></p>
  </div>
</section>
<?php
require __DIR__ . '/../templates/footer.php';
