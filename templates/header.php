<?php $gtmId = (string) \Zycus\Config::get('GTM_CONTAINER_ID', 'GTM-XXXXXXX'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php require __DIR__ . '/meta.php'; ?>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?= htmlspecialchars($gtmId, ENT_QUOTES) ?>');</script>
</head>
<body>
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?= htmlspecialchars($gtmId, ENT_QUOTES) ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
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
    <nav class="site-nav" aria-label="Primary">
      <a href="#how-it-works">How it works</a>
      <a href="#testimonials">Customers</a>
      <a href="#faq">FAQ</a>
    </nav>
    <a href="#demo-form" class="btn btn--primary btn--sm site-header__cta">Book My Demo</a>
  </div>
</header>
<main id="main-content" role="main">
