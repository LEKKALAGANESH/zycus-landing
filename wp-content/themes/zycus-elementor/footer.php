<footer class="site-footer" role="contentinfo">
  <div class="container site-footer__inner">
    <div class="brand brand--footer" aria-label="Zycus">
      <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/zycus-logo.webp'); ?>"
           alt="Zycus AI Procurement Brain Logo"
           class="brand__logo"
           width="140" height="38"
           loading="lazy"
           decoding="async"
           style="filter:brightness(0) invert(1);">
    </div>
    <p class="site-footer__copy">
      &copy; <?php echo esc_html(date('Y')); ?> Zycus Inc. All rights reserved. &nbsp;|&nbsp;
      <a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>" class="site-footer__link">Privacy Policy</a>
      &nbsp;|&nbsp;
      <a href="<?php echo esc_url(home_url('/terms-of-use/')); ?>" class="site-footer__link">Terms of Use</a>
    </p>
  </div>
</footer>

<div class="mobile-cta" role="complementary" aria-label="Quick actions">
  <a href="#demo-form" class="btn btn--accent btn--block">Book Demo</a>
</div>

<?php wp_footer(); ?>
</body>
</html>
