<?php
declare(strict_types=1);

function start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    session_name('2b2x_session');
    $secure = APP_ENV === 'production';
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function user_record(string $email): ?array
{
    foreach (load_json('users.json', []) as $user) {
        if (is_array($user) && strtolower((string) ($user['email'] ?? '')) === strtolower($email)) {
            return $user;
        }
    }
    return null;
}

function login_user(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id' => (string) $user['id'],
        'username' => (string) $user['username'],
        'email' => (string) $user['email'],
        'role' => (string) $user['role'],
        'created_at' => (string) $user['created_at'],
    ];
    secure_cookie('2b2x_session', session_id(), 0);
}

function logout_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

function normalize_email(string $email): string
{
    return strtolower(trim($email));
}

function validate_password(string $password): bool
{
    return strlen($password) >= 8 && preg_match('/[A-Z]/', $password) === 1 && preg_match('/[a-z]/', $password) === 1 && preg_match('/\d/', $password) === 1;
}

function validate_email(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validate_username(string $username): bool
{
    return preg_match('/^[A-Za-z0-9_.-]{3,24}$/', $username) === 1;
}

function hash_password(string $password): string
{
    return password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
}

function create_user_id(): string
{
    return bin2hex(random_bytes(16));
}
