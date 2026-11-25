<?php
declare(strict_types=1);require_once __DIR__.'/header.php';$admin=current_user();if(!$admin||$admin['role']!=='admin'){header('Location: '.app_url('admin/login.php'));exit;}?>
