<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Cut procurement costs by 40% with Merlin AI. Automate sourcing, contracting, and invoicing end-to-end. Book a personalized demo today.">
  <meta property="og:title" content="<?php wp_title('|', true, 'right'); bloginfo('name'); ?>">
  <meta property="og:description" content="Cut procurement costs by 40% with Merlin AI. Automate sourcing, contracting, and invoicing end-to-end.">
  <meta property="og:image" content="<?php echo esc_url(get_template_directory_uri() . '/assets/img/zycus-logo.webp'); ?>">
  <meta property="og:type" content="website">
  <meta name="twitter:card" content="summary_large_image">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a href="#main-content" class="skip-link">Skip to main content</a>
<div class="scroll-sentinel" aria-hidden="true" style="position:absolute;top:0;height:80px;width:1px;pointer-events:none;"></div>

<header class="site-header" role="banner">
  <div class="container site-header__inner">
    <a href="<?php echo esc_url(home_url('/')); ?>" class="brand" aria-label="Zycus home — AI Procurement">
      <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/zycus-logo.webp'); ?>"
           alt="Zycus AI Procurement Brain Logo"
           class="brand__logo"
           width="160" height="44"
           decoding="async"
           fetchpriority="high">
    </a>

    <nav class="site-nav-desktop" aria-label="Primary">
      <a href="#how-it-works">How it works</a>
      <a href="#testimonials">Customers</a>
      <a href="#faq">FAQ</a>
    </nav>

    <a href="#demo-form" class="btn btn--primary btn--sm site-header__cta">Book My Demo</a>

    <button type="button"
            class="menu-btn"
            aria-expanded="false"
            aria-controls="mobile-menu"
            aria-label="Open navigation menu">
      <span aria-hidden="true"></span>
      <span aria-hidden="true"></span>
      <span aria-hidden="true"></span>
    </button>
  </div>
</header>

<div id="mobile-menu" class="mobile-menu" aria-hidden="true">
  <div class="mobile-menu__backdrop" data-menu-close></div>
  <nav class="mobile-menu__panel" aria-label="Mobile primary">
    <button type="button" class="mobile-menu__close" data-menu-close aria-label="Close navigation menu">&times;</button>
    <a href="#how-it-works">How it works</a>
    <a href="#testimonials">Customers</a>
    <a href="#faq">FAQ</a>
    <a href="#demo-form" class="btn btn--primary mobile-menu__cta">Book My Demo</a>
  </nav>
</div>
