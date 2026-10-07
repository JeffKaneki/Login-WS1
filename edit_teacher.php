<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

$sessionUser = require_role(['admin', 'superadmin']);

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    redirect('teachers.php');
}

$statement = db()->prepare('SELECT id, username, email, phone, address, role FROM users WHERE id = :id LIMIT 1');
$statement->execute(['id' => $id]);
$teacher = $statement->fetch();

if (!$teacher || ($teacher['role'] ?? '') !== 'teacher') {
    redirect('teachers.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $username = trim((string) ($_POST['username'] ?? ''));
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $address = trim((string) ($_POST['address'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if ($username === '' || $email === '') {
        $_SESSION['flash_error'] = 'Username and email are required.';
        redirect('edit_teacher.php?id=' . $id);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['flash_error'] = 'Please enter a valid email address.';
        redirect('edit_teacher.php?id=' . $id);
    }

    $duplicate = db()->prepare('SELECT id FROM users WHERE (username = :username OR email = :email) AND id != :id LIMIT 1');
    $duplicate->execute(['username' => $username, 'email' => $email, 'id' => $id]);
    if ($duplicate->fetch()) {
        $_SESSION['flash_error'] = 'Another teacher already uses that username or email.';
        redirect('edit_teacher.php?id=' . $id);
    }

    $sql = 'UPDATE users SET username = :username, email = :email, phone = :phone, address = :address';
    $params = [
        'username' => $username,
        'email' => $email,
        'phone' => $phone,
        'address' => $address,
        'id' => $id,
    ];

    if ($password !== '') {
        if (strlen($password) < 8) {
            $_SESSION['flash_error'] = 'Password must be at least 8 characters long.';
            redirect('edit_teacher.php?id=' . $id);
        }
        $sql .= ', password_hash = :password_hash, password_plain = :password_plain';
        $params['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        $params['password_plain'] = $password;
    }

    $sql .= ' WHERE id = :id';
    $update = db()->prepare($sql);
    $update->execute($params);

    $_SESSION['flash_success'] = 'Teacher account updated successfully.';
    redirect('teachers.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Teacher</title>
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
                <h2>Edit Teacher</h2>
                <?php if (!empty($_SESSION['flash_error'])): ?>
                    <p class="form-message error-message"><?= e($_SESSION['flash_error']) ?></p>
                    <?php unset($_SESSION['flash_error']); ?>
                <?php endif; ?>
                <form method="post" action="edit_teacher.php?id=<?= e((string) $teacher['id']) ?>" class="teacher-form">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <div class="info-grid">
                        <div class="info-card"><label>Username</label><input type="text" name="username" value="<?= e($teacher['username']) ?>" required></div>
                        <div class="info-card"><label>Email</label><input type="email" name="email" value="<?= e($teacher['email']) ?>" required></div>
                        <div class="info-card"><label>New Password</label><input type="password" name="password" placeholder="Leave blank to keep current password"></div>
                        <div class="info-card"><label>Phone</label><input type="text" name="phone" value="<?= e($teacher['phone'] ?: '') ?>"></div>
                        <div class="info-card teacher-address-field"><label>Address</label><input type="text" name="address" value="<?= e($teacher['address'] ?: '') ?>"></div>
                    </div>
                    <div class="dashboard-links">
                        <button type="submit" class="logout-btn">Save Changes</button>
                        <a href="teachers.php" class="button-link">Back to Teachers</a>
                    </div>
                </form>
            </section>
        </div>
    </main>
</body>
</html>
