<?php
declare(strict_types=1);

function current_release(): ?array
{
    foreach (load_json('releases.json', []) as $release) {
        if (is_array($release) && ($release['is_latest'] ?? false) && ($release['is_published'] ?? false)) {
            return $release;
        }
    }
    return null;
}

function sanitize(mixed $value): string
{
    return is_scalar($value) ? trim((string) $value) : '';
}

function release_by_id(string $id): ?array
{
    foreach (load_json('releases.json', []) as $release) {
        if (is_array($release) && ($release['id'] ?? '') === $id) {
            return $release;
        }
    }
    return null;
}

function format_date(string $date): string
{
    $timestamp = strtotime($date);
    return $timestamp === false ? '—' : date('M j, Y', $timestamp);
}

function truncate(string $value, int $length): string
{
    return mb_strlen($value) > $length ? mb_substr($value, 0, $length - 1) . '…' : $value;
}

function browser_name(): string
{
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (preg_match('/Windows/i', $userAgent)) {
        return 'Windows';
    }
    if (preg_match('/Mac/i', $userAgent)) {
        return 'macOS';
    }
    if (preg_match('/Linux/i', $userAgent)) {
        return 'Linux';
    }
    return 'Unknown';
}

function ips_hash(string $ip): string
{
    return hash('sha256', $ip);
}

function safe_filename(string $name): string
{
    $name = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($name));
    return $name === '' ? 'file' : $name;
}

function file_size_human(int $bytes): string
{
    $units = ['B', 'KB', 'MB', 'GB'];
    $value = (float) $bytes;
    for ($index = 0; $value >= 1024 && $index < count($units) - 1; $index++) {
        $value /= 1024;
    }
    return round($value, $value >= 10 || $index === 0 ? 0 : 1) . ' ' . $units[$index];
}

function random_filename(string $extension): string
{
    return bin2hex(random_bytes(16)) . '.' . strtolower($extension);
}

function sha256_file(string $path): string
{
    $hash = hash_init('sha256');
    $handle = fopen($path, 'rb');
    if ($handle === false || !hash_update_stream($hash, $handle)) {
        throw new RuntimeException('Unable to calculate installer checksum.');
    }
    fclose($handle);
    return hash_final($hash);
}

function upload_installer(array $file): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('No installer was uploaded.');
    }
    if ($file['size'] > UPLOAD_MAX_BYTES || $file['size'] <= 0) {
        throw new RuntimeException('The installer exceeds the maximum file size.');
    }
    $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXECUTABLE_EXTENSIONS, true)) {
        throw new RuntimeException('Only .exe and .msi files are supported.');
    }
    $mime = (string) (finfo_open(FILEINFO_MIME_TYPE) ?: '');
    $detected = $mime === '' ? '' : trim(finfo_file($mime, (string) $file['tmp_name']));
    $allowedMime = ['application/vnd.microsoft.portable-executable', 'application/msinstaller', 'application/x-msdownload'];
    if (!in_array($detected, $allowedMime, true)) {
        throw new RuntimeException('The installer format is not supported.');
    }
    $filename = random_filename($extension);
    $destination = config_path('browsers') . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new RuntimeException('The installer could not be stored.');
    }
    chmod($destination, 0644);
    return ['filename' => $filename, 'file_size' => (int) filesize($destination), 'checksum' => sha256_file($destination)];
}

function is_json_array_file(string $filename): bool
{
    $data = read_json_file($filename, []);
    return is_array($data) && array_is_list($data);
}

function get_settings(): array
{
    return load_json('settings.json', [
        'site_name' => APP_NAME,
        'accent_color' => '#8b5cf6',
        'supported_os' => 'Windows 10 / 11',
        'download_enabled' => true,
        'contact_email' => 'hello@2b2x.local',
    ]);
}
