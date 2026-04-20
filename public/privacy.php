<?php declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

$title = 'Privacy Policy | Zycus';
$desc  = 'Zycus privacy policy — how we collect, use, and protect personal data submitted via our demo request form.';

require __DIR__ . '/../templates/header.php';
?>
<section class="section section--light" aria-labelledby="privacy-heading">
  <div class="container container--narrow">
    <h1 id="privacy-heading" class="h1">Privacy Policy</h1>
    <p class="lead">Last updated: <?= date('F j, Y') ?></p>

    <h2 class="h3">1. Information We Collect</h2>
    <p>When you request a demo, we collect your name, work email, company name, company size, role, primary use case, and any notes you provide. We also log your IP address, user agent, and referring URL for security and rate-limiting.</p>

    <h2 class="h3">2. How We Use Your Information</h2>
    <p>We use your information solely to respond to your demo request, schedule a session with a Zycus solution architect, and send directly relevant follow-up communications. We do not sell or share your data with third parties for marketing purposes.</p>

    <h2 class="h3">3. Data Retention</h2>
    <p>Demo request records are retained for 24 months unless you request earlier deletion. Access logs are retained for 90 days.</p>

    <h2 class="h3">4. Your Rights</h2>
    <p>You may request access, correction, or deletion of your personal data at any time by emailing <a href="mailto:privacy@zycus.com">privacy@zycus.com</a>. We will respond within 30 days as required under GDPR and applicable regulations.</p>

    <h2 class="h3">5. Security</h2>
    <p>Zycus is SOC 2 Type II and ISO 27001 certified. Data is encrypted in transit (TLS 1.2+) and at rest.</p>

    <p><a href="/" class="thank-you__offramp">&larr; Back to home</a></p>
  </div>
</section>
<?php
require __DIR__ . '/../templates/footer.php';
