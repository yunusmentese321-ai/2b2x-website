<?php
declare(strict_types=1);

const APP_NAME = '2B2X Browser';
const APP_URL = 'http://localhost/2b2x';
const APP_ENV = 'development';
const APP_ADMIN_EMAIL = 'admin@2b2x.local';
const APP_ADMIN_PASSWORD = 'ChangeThisAdminPassword!2026';

const UPLOAD_MAX_BYTES = 250 * 1024 * 1024;
const ALLOWED_EXECUTABLE_EXTENSIONS = ['exe', 'msi'];
const ALLOWED_IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

function config_path(string $name): string
{
    $root = dirname(__DIR__);
    $paths = [
        'data' => $root . '/data',
        'uploads' => $root . '/uploads',
        'browsers' => $root . '/uploads/browsers',
        'blog' => $root . '/uploads/blog',
    ];
    if (!isset($paths[$name])) {
        throw new RuntimeException('Unknown configuration path: ' . $name);
    }
    return $paths[$name];
}

function app_url(string $path = ''): string
{
    $configuredUrl = getenv('APP_URL');
    if (is_string($configuredUrl) && $configuredUrl !== '') {
        return rtrim($configuredUrl, '/') . '/' . ltrim($path, '/');
    }

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $basePath = rtrim(dirname($scriptName), '/.');
    return $scheme . '://' . $host . ($basePath === '' ? '' : $basePath) . '/' . ltrim($path, '/');
}

function secure_cookie(string $name, string $value, int $seconds): void
{
    $params = [
        'httponly' => true,
        'samesite' => 'Lax',
        'path' => '/',
        'maxage' => $seconds,
    ];
    if (APP_ENV === 'production') {
        $params['secure'] = true;
    }
    setcookie($name, $value, $params);
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function safe_json(array $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function fail(string $message, int $status = 500): never
{
    http_response_code($status);
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><title>2B2X Browser</title></head><body style="min-height:100vh;display:grid;place-items:center;margin:0;background:#050505;color:#fff;font-family:system-ui"><main><h1>Something went wrong.</h1><p>' . e($message) . '</p><a href="' . app_url('index.php') . '">Return home</a></main></body></html>';
    exit;
}
