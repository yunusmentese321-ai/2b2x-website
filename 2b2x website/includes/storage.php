<?php
declare(strict_types=1);

function ensure_storage_directories(): void
{
    foreach (['data', 'uploads', 'uploads/browsers', 'uploads/blog'] as $dir) {
        $path = config_path($dir);
        if (!is_dir($path) && !mkdir($path, 0755, true) && !is_dir($path)) {
            throw new RuntimeException('Unable to create storage directory: ' . $path);
        }
    }
}

function read_json_file(string $filename, array $default = []): array
{
    $path = config_path('data') . '/' . basename($filename);
    if (!is_file($path)) {
        return $default;
    }
    $contents = file_get_contents($path);
    if ($contents === false) {
        return $default;
    }
    try {
        $decoded = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        return is_array($decoded) ? $decoded : $default;
    } catch (JsonException $exception) {
        error_log('Invalid JSON storage file ' . $path . ': ' . $exception->getMessage());
        return $default;
    }
}

function write_json_file(string $filename, array $data): bool
{
    ensure_storage_directories();
    $path = config_path('data') . '/' . basename($filename);
    $temporary = $path . '.tmp.' . bin2hex(random_bytes(8));
    $encoded = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    $lock = fopen($path . '.lock', 'c');
    if ($lock === false) {
        return false;
    }
    if (!flock($lock, LOCK_EX)) {
        fclose($lock);
        return false;
    }
    try {
        $handle = fopen($temporary, 'wb');
        if ($handle === false) {
            return false;
        }
        if (fwrite($handle, $encoded . PHP_EOL) === false || !fflush($handle)) {
            fclose($handle);
            return false;
        }
        fclose($handle);
        if (!chmod($temporary, 0644) || !rename($temporary, $path)) {
            @unlink($temporary);
            return false;
        }
        @unlink($path . '.lock');
        return true;
    } catch (JsonException $exception) {
        @unlink($temporary);
        error_log('Unable to write JSON storage: ' . $exception->getMessage());
        return false;
    } finally {
        flock($lock, LOCK_UN);
        fclose($lock);
    }
}

function load_json(string $filename, array $default = []): array
{
    return read_json_file($filename, $default);
}

function save_json(string $filename, array $data): bool
{
    return write_json_file($filename, $data);
}

function append_json(string $filename, array $item): bool
{
    $data = load_json($filename);
    $data[] = $item;
    return save_json($filename, $data);
}

function update_json(string $filename, callable $updater): bool
{
    $data = load_json($filename);
    $updated = $updater($data);
    return is_array($updated) && save_json($filename, $updated);
}

function delete_json_item(string $filename, callable $predicate): bool
{
    return update_json($filename, static function (array $data) use ($predicate): array {
        return array_values(array_filter($data, static fn (array $item): bool => !$predicate($item)));
    });
}

function current_user(): ?array
{
    $session = $_SESSION['user'] ?? null;
    return is_array($session) ? $session : null;
}

function is_admin(): bool
{
    return current_user() !== null && current_user()['role'] === 'admin';
}

function require_admin(): never
{
    if (!is_admin()) {
        http_response_code(403);
        header('Location: ' . app_url('admin/index.php'));
        exit;
    }
}

function require_login(): never
{
    if (current_user() === null) {
        header('Location: ' . app_url('login.php?next=' . rawurlencode($_SERVER['REQUEST_URI'] ?? '/')));
        exit;
    }
}
