<?php
$title = $title ?? 'Zycus — AI-Powered Source-to-Pay Procurement | Book a Demo';
$desc = $desc ?? 'Cut procurement costs by 40% with Merlin AI. Automate sourcing, contracting, and invoicing end-to-end. Book a personalized demo today.';
$ogTitle = 'Zycus Merlin AI — Cut Procurement Costs by 40%';
$appUrl = (string) (\Zycus\Config::get('APP_URL') ?? 'https://www.zycus.com');
$canonical = rtrim($appUrl, '/') . ($_SERVER['REQUEST_URI'] ?? '/');
$faqs = require __DIR__ . '/../config/faqs.php';

$orgLd = [
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => 'Zycus',
    'url' => $appUrl !== '' ? $appUrl : 'https://www.zycus.com',
    'logo' => rtrim($appUrl, '/') . '/assets/img/zycus-logo.webp',
    'sameAs' => [
        'https://www.linkedin.com/company/zycus',
        'https://twitter.com/zycus',
    ],
];

$faqLd = [
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => array_map(static function (array $f): array {
        return [
            '@type' => 'Question',
            'name' => $f[0],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $f[1],
            ],
        ];
    }, $faqs),
];
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#0F3D81">
<title><?= htmlspecialchars($title, ENT_QUOTES) ?></title>
<meta name="description" content="<?= htmlspecialchars($desc, ENT_QUOTES) ?>">
<meta name="keywords" content="Zycus, AI procurement, source-to-pay, Merlin AI, agentic AI, e-procurement, spend management, supplier management, contract lifecycle, invoice automation">
<meta name="author" content="Zycus Inc.">
<link rel="canonical" href="<?= htmlspecialchars($canonical, ENT_QUOTES) ?>">
<link rel="icon" type="image/jpeg" href="/assets/img/tab-logo.jpg">
<link rel="shortcut icon" type="image/jpeg" href="/assets/img/tab-logo.jpg">
<link rel="apple-touch-icon" href="/assets/img/tab-logo.jpg">
<meta property="og:type" content="website">
<meta property="og:title" content="<?= htmlspecialchars($ogTitle, ENT_QUOTES) ?>">
<meta property="og:description" content="<?= htmlspecialchars($desc, ENT_QUOTES) ?>">
<meta property="og:url" content="<?= htmlspecialchars($canonical, ENT_QUOTES) ?>">
<meta property="og:site_name" content="Zycus">
<meta property="og:image" content="<?= rtrim($appUrl, '/') ?>/assets/img/zycus-logo.webp">
<meta property="og:image:alt" content="Zycus AI Procurement Brain Logo">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= htmlspecialchars($ogTitle, ENT_QUOTES) ?>">
<meta name="twitter:description" content="<?= htmlspecialchars($desc, ENT_QUOTES) ?>">
<meta name="twitter:image" content="<?= rtrim($appUrl, '/') ?>/assets/img/zycus-logo.webp">
<meta name="twitter:image:alt" content="Zycus AI Procurement Brain Logo">
<meta name="robots" content="index,follow,max-image-preview:large">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preload" as="image" href="/assets/img/zycus-logo.webp" type="image/webp">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
<link rel="stylesheet" href="/assets/css/styles.css">
<link rel="stylesheet" href="/assets/css/animations.css">
<script type="application/ld+json"><?= json_encode($orgLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
<script type="application/ld+json"><?= json_encode($faqLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
