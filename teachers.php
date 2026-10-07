<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

$sessionUser = require_role(['admin', 'superadmin']);

$teacherList = db()->prepare('SELECT id, username, email, phone, address FROM users WHERE role = :role ORDER BY username ASC');
$teacherList->execute(['role' => 'teacher']);
$teachers = $teacherList->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $username = trim((string) ($_POST['username'] ?? ''));
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $address = trim((string) ($_POST['address'] ?? ''));

    if ($username === '' || $email === '' || $password === '') {
        $_SESSION['flash_error'] = 'Username, email, and password are required.';
        redirect('teachers.php');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['flash_error'] = 'Please enter a valid email address.';
        redirect('teachers.php');
    }

    if (strlen($password) < 8) {
        $_SESSION['flash_error'] = 'Password must be at least 8 characters long.';
        redirect('teachers.php');
    }

    $exists = db()->prepare('SELECT id FROM users WHERE username = :username OR email = :email LIMIT 1');
    $exists->execute(['username' => $username, 'email' => $email]);
    if ($exists->fetch()) {
        $_SESSION['flash_error'] = 'A teacher with that username or email already exists.';
        redirect('teachers.php');
    }

    $insert = db()->prepare('INSERT INTO users (username, email, password_hash, password_plain, role, phone, address) VALUES (:username, :email, :password_hash, :password_plain, :role, :phone, :address)');
    $insert->execute([
        'username' => $username,
        'email' => $email,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'password_plain' => $password,
        'role' => 'teacher',
        'phone' => $phone,
        'address' => $address,
    ]);

    $_SESSION['flash_success'] = 'Teacher account created successfully.';
    redirect('teachers.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teachers</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="logo-container"><img class="logo" src="images/logo.jpg" alt="Logo"></div>
    <main class="dashboard-container">
        <aside class="dashboard-header" aria-label="Dashboard navigation and user summary">
            <div class="dashboard-side-content">
                <div>
                    <h1><?= e(ucwords(str_replace('_', ' ', $sessionUser['role'] ?? 'user'))) ?> Dashboard</h1>
                    <p class="user-greeting">Welcome back, <strong><?= e($sessionUser['username']) ?></strong></p>

                    <nav class="dashboard-nav" aria-label="Main navigation">
                        <a href="dashboard.php" class="nav-link">My Profile</a>
                        <a href="accounts.php" class="nav-link">Accounts List</a>
                        <a href="teachers.php" class="nav-link active">Teachers List</a>
                    </nav>
                </div>
                <form method="post" action="logout.php" class="dashboard-logout-form">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
            </div>
        </aside>

        <div class="dashboard-content">
            <section class="dashboard-section">
                <h2>Create Teacher</h2>
                <?php if (!empty($_SESSION['flash_error'])): ?>
                    <p class="form-message error-message"><?= e($_SESSION['flash_error']) ?></p>
                    <?php unset($_SESSION['flash_error']); ?>
                <?php endif; ?>
                <?php if (!empty($_SESSION['flash_success'])): ?>
                    <p class="form-message success-message"><?= e($_SESSION['flash_success']) ?></p>
                    <?php unset($_SESSION['flash_success']); ?>
                <?php endif; ?>
                <form method="post" action="teachers.php" class="teacher-form">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <div class="info-grid">
                        <div class="info-card"><label>Username</label><input type="text" name="username" required></div>
                        <div class="info-card"><label>Email</label><input type="email" name="email" required></div>
                        <div class="info-card"><label>Password</label><input type="password" name="password" required minlength="8"></div>
                        <div class="info-card"><label>Phone</label><input type="text" name="phone"></div>
                        <div class="info-card teacher-address-field"><label>Address</label><input type="text" name="address"></div>
                    </div>
                    <div class="dashboard-links">
                        <button type="submit" class="logout-btn">Create Teacher</button>
                        <a href="dashboard.php" class="button-link">Back to Dashboard</a>
                    </div>
                </form>
            </section>

            <section class="dashboard-section">
                <h2>Teachers List</h2>
                <table class="accounts-table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($teachers as $teacher): ?>
                            <tr>
                                <td><?= e($teacher['username']) ?></td>
                                <td><?= e($teacher['email']) ?></td>
                                <td><?= e($teacher['phone'] ?: 'Not provided') ?></td>
                                <td><?= e($teacher['address'] ?: 'Not provided') ?></td>
                                <td><a href="edit_teacher.php?id=<?= e((string) $teacher['id']) ?>" class="button-link small-button">Edit</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </div>
    </main>
</body>
</html>
