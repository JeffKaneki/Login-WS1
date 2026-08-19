<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

if (!empty($_SESSION['user'])) {
    redirect('dashboard.php');
}

$error = '';
$values = ['username' => '', 'email' => '', 'phone' => '', 'address' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    foreach ($values as $field => $_) {
        $values[$field] = trim((string) ($_POST[$field] ?? ''));
    }
    $values['email'] = strtolower($values['email']);
    $password = (string) ($_POST['password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

    if (strlen($values['username']) < 3 || strlen($values['username']) > 50) {
        $error = 'Username must be between 3 and 50 characters.';
    } elseif (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } elseif ($values['phone'] === '' || $values['address'] === '') {
        $error = 'Phone number and address are required.';
    } else {
        try {
            $statement = db()->prepare('INSERT INTO users (username, email, password_hash, phone, address) VALUES (:username, :email, :password_hash, :phone, :address)');
            $statement->execute([
                'username' => $values['username'],
                'email' => $values['email'],
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'phone' => $values['phone'],
                'address' => $values['address'],
            ]);
            redirect('login.php?registered=1');
        } catch (PDOException $exception) {
            $error = $exception->getCode() === '23000'
                ? 'That username or email address is already registered.'
                : 'Unable to create the account. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="logo-container"><img class="logo" src="images/logo.jpg" alt="Logo"></div>
    <div class="login-container">
        <form class="login-form" method="post" action="register.php">
            <h1>Register</h1>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="form-group"><label for="username">Username:</label><input type="text" id="username" name="username" value="<?= e($values['username']) ?>" required autocomplete="username" minlength="3" maxlength="50"></div>
            <div class="form-group"><label for="email">Email:</label><input type="email" id="email" name="email" value="<?= e($values['email']) ?>" required autocomplete="email"></div>
            <div class="form-group"><label for="password">Password:</label><input type="password" id="password" name="password" required autocomplete="new-password" minlength="8"></div>
            <div class="form-group"><label for="confirm_password">Confirm Password:</label><input type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password" minlength="8"></div>
            <div class="form-group"><label for="phone">Phone Number:</label><input type="tel" id="phone" name="phone" value="<?= e($values['phone']) ?>" required autocomplete="tel" maxlength="30"></div>
            <div class="form-group"><label for="address">Address:</label><input type="text" id="address" name="address" value="<?= e($values['address']) ?>" required autocomplete="street-address" maxlength="255"></div>
            <div class="button-container"><button type="submit">Register</button><a class="button-link" href="login.php">Back to Login</a></div>
            <?php if ($error !== ''): ?><p class="form-message error-message" role="alert"><?= e($error) ?></p><?php endif; ?>
        </form>
    </div>
</body>
</html>
