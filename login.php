<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

if (!empty($_SESSION['user'])) {
    redirect('dashboard.php');
}

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Enter your username and password.';
    } else {
        $statement = db()->prepare('SELECT id, username, email, phone, address, password_hash FROM users WHERE username = :username LIMIT 1');
        $statement->execute(['username' => $username]);
        $user = $statement->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $error = 'Incorrect username or password.';
        } else {
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id' => (int) $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'phone' => $user['phone'],
                'address' => $user['address'],
            ];
            redirect('dashboard.php');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="logo-container"><img class="logo" src="images/logo.jpg" alt="Logo"></div>
    <div class="login-container">
        <form class="login-form" method="post" action="login.php">
            <h1>Login</h1>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="form-group"><label for="username">Username:</label><input type="text" id="username" name="username" value="<?= e($username) ?>" required autocomplete="username"></div>
            <div class="form-group"><label for="password">Password:</label><input type="password" id="password" name="password" required autocomplete="current-password"></div>
            <div class="button-container"><button type="submit">Login</button><a class="button-link" href="register.php">Register</a></div>
            <?php if ($error !== ''): ?><p class="form-message error-message" role="alert"><?= e($error) ?></p><?php endif; ?>
            <?php if (isset($_GET['registered'])): ?><p class="form-message success-message" role="status">Account created. Please log in.</p><?php endif; ?>
        </form>
    </div>
</body>
</html>
