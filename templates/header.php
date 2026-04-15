<?php $gtmId = (string) \Zycus\Config::get('GTM_CONTAINER_ID', ''); ?>
<?php if ($gtmId === '' && (\Zycus\Config::get('APP_ENV') === 'production')) { error_log('[Zycus] GTM_CONTAINER_ID is not set — analytics will not fire.'); } ?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php require __DIR__ . '/meta.php'; ?>
<?php if ($gtmId !== ''): ?>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?= htmlspecialchars($gtmId, ENT_QUOTES) ?>');</script>
<?php endif; ?>
</head>
<body>
<?php if ($gtmId !== ''): ?>
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?= htmlspecialchars($gtmId, ENT_QUOTES) ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<?php endif; ?>
<a href="#main-content" class="skip-link">Skip to main content</a>
<div class="scroll-sentinel" aria-hidden="true" style="position:absolute;top:0;height:80px;width:1px;"></div>
<header class="site-header" role="banner">
  <div class="container site-header__inner">
    <a href="/" class="brand" aria-label="Zycus home — AI Procurement">
      <img src="/assets/img/zycus-logo.webp"
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

<!-- Mobile menu — rendered as a body-level element so it escapes the
     site-header's z-index stacking context. -->
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
<main id="main-content" role="main">
