<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/header.php';

if (is_admin()) {
    header('Location: ' . app_url('admin/index.php'));
    exit;
}

$error = '';
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $username = trim((string) ($_POST['username'] ?? ''));
    $email = filter_var(trim((string) ($_POST['email'] ?? '')), FILTER_VALIDATE_EMAIL);
    $password = trim((string) ($_POST['password'] ?? ''));
    $confirm = trim((string) ($_POST['confirm'] ?? ''));

    if (!validate_username($username)) {
        $error = 'Username must be 3–24 characters and use letters, numbers, dots, dashes, or underscores.';
    } elseif (!$email || !validate_password($password)) {
        $error = 'Enter a valid email and a password with an uppercase letter, lowercase letter, number, and at least 8 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $users = load_json('users.json', []);
        $alreadyExists = array_filter(
            $users,
            static fn (mixed $user): bool => is_array($user)
                && strcasecmp((string) ($user['email'] ?? ''), $email) === 0
        );

        if ($alreadyExists) {
            $error = 'That email is already registered.';
        } else {
            $newUser = [
                'id' => 'user-' . bin2hex(random_bytes(8)),
                'username' => $username,
                'email' => $email,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'role' => 'admin',
                'active' => true,
                'created_at' => date('c'),
            ];

            if (!save_json('users.json', array_merge($users, [$newUser]))) {
                $error = 'Unable to create the administrator account.';
            } else {
                session_regenerate_id(true);
                $_SESSION['user'] = [
                    'id' => $newUser['id'],
                    'username' => $newUser['username'],
                    'role' => 'admin',
                ];
                header('Location: ' . app_url('admin/index.php'));
                exit;
            }
        }
    }
}
?>
<main class="auth-page">
    <div class="auth-card">
        <a class="brand" href="<?= e(app_url('index.php')) ?>">
            <span class="brand-mark"><i></i><i></i></span>
            <span>2B2X</span>
        </a>
        <h1>Initial administrator</h1>
        <p>Create the first administrator account. Keep this route private and remove it after setup.</p>
        <form method="post" data-validate>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="field">
                <label for="username">Username</label>
                <input id="username" name="username" minlength="3" maxlength="24" required value="<?= e($username) ?>">
            </div>
            <div class="field">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" required value="<?= e($email) ?>">
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" minlength="8" required>
            </div>
            <div class="field">
                <label for="confirm">Confirm password</label>
                <input id="confirm" name="confirm" type="password" minlength="8" required>
            </div>
            <?php if ($error): ?>
                <div class="alert error"><?= e($error) ?></div>
            <?php endif; ?>
            <button class="button button-primary" type="submit">Create administrator <span class="arrow">↗</span></button>
        </form>
    </div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
