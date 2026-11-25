<?php
declare(strict_types=1);require_once __DIR__.'/includes/header.php';if($_SERVER['REQUEST_METHOD']==='POST'){require_csrf();logout_user();header('Location: '.app_url('index.php'));exit;}header('Location: '.app_url('index.php'));exit;
