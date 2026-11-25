<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/storage.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/csrf.php';
require_once dirname(__DIR__) . '/includes/functions.php';
start_session();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="theme-color" content="#050505">
<meta name="description" content="2B2X Browser is a modern, focused browser for Windows.">
<meta name="robots" content="index,follow">
<link rel="canonical" href="<?= e(app_url($_SERVER['REQUEST_URI'] ?? 'index.php')) ?>">
<meta property="og:type" content="website"><meta property="og:title" content="2B2X Browser"><meta property="og:description" content="Browse beyond limits with a modern Windows browser."><meta property="og:url" content="<?= e(app_url($_SERVER['REQUEST_URI'] ?? 'index.php')) ?>"><meta property="og:image" content="<?= e(app_url('assets/images/og.png')) ?>">
<meta name="twitter:card" content="summary_large_image">
<link rel="stylesheet" href="<?= e(app_url('assets/css/style.css')) ?>"><link rel="stylesheet" href="<?= e(app_url('assets/css/animations.css')) ?>"><link rel="stylesheet" href="<?= e(app_url('assets/css/components.css')) ?>"><link rel="stylesheet" href="<?= e(app_url('assets/css/pages.css')) ?>"><link rel="stylesheet" href="<?= e(app_url('assets/css/responsive.css')) ?>"><link rel="stylesheet" href="<?= e(app_url('assets/css/centered.css')) ?>">
<script src="<?= e(app_url('assets/js/main.js')) ?>" defer></script><script src="<?= e(app_url('assets/js/forms.js')) ?>" defer></script><script src="<?= e(app_url('assets/js/download.js')) ?>" defer></script>
</head>
<body class="page-<?= e(basename(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: 'index.php')) ?>">
<div class="noise" aria-hidden="true"></div><div class="scroll-progress" data-scroll-progress></div>
