<?php
/*
 * Template Name: Thank You
 * Template Post Type: page
 */

$first_name = sanitize_text_field(wp_unslash($_GET['fname'] ?? ''));
$email      = sanitize_email(wp_unslash($_GET['email']      ?? ''));

get_header();
?>
<main id="main-content" role="main">
  <section class="thank-you section" aria-labelledby="ty-heading">
    <div class="container">
      <div class="thank-you__card">
        <div class="thank-you__check" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
        </div>

        <h1 id="ty-heading" class="h2">
          <?php if ($first_name): ?>
            You're confirmed, <?php echo esc_html($first_name); ?>!
          <?php else: ?>
            Demo Request Confirmed!
          <?php endif; ?>
        </h1>

        <p class="lead" style="max-width:520px;margin:0 auto 24px;">
          <?php if ($email): ?>
            Check <strong><?php echo esc_html($email); ?></strong> for your calendar invite.
          <?php else: ?>
            Check your inbox for your calendar invite.
          <?php endif; ?>
          A Zycus solution architect will reach out within one business day to confirm your session.
        </p>

        <ul class="trust-row" role="list" style="max-width:480px;margin:0 auto 32px;">
          <li class="trust-row__item">
            <span class="check" aria-hidden="true">&#10003;</span>
            30-minute personalized working session
          </li>
          <li class="trust-row__item">
            <span class="check" aria-hidden="true">&#10003;</span>
            We'll use a sample of your own spend data
          </li>
          <li class="trust-row__item">
            <span class="check" aria-hidden="true">&#10003;</span>
            No commitment required
          </li>
        </ul>

        <div class="thank-you__next">
          <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn--secondary">
            &larr; Back to Home
          </a>
          <a href="https://zycus.com" class="thank-you__offramp" target="_blank" rel="noopener">
            Explore Zycus.com while you wait &rarr;
          </a>
        </div>
      </div>
    </div>
  </section>
</main>

<script>
window.dataLayer = window.dataLayer || [];
window.dataLayer.push({
  event: 'demo_confirmed',
  form_id: 'zycus_demo',
  tier: '<?php echo esc_js(sanitize_text_field(wp_unslash($_GET['tier'] ?? 'standard'))); ?>',
  personalized: <?php echo $first_name ? 'true' : 'false'; ?>
});
</script>
<?php get_footer(); ?>
