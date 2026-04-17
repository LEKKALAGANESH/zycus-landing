<?php
ob_start();
require __DIR__ . '/public/index.php';
$html = ob_get_clean();

$themeUri = get_template_directory_uri() . '/public';
$html = str_replace('href="/assets/', 'href="' . $themeUri . '/assets/', $html);
$html = str_replace('src="/assets/',  'src="'  . $themeUri . '/assets/', $html);

echo $html;
