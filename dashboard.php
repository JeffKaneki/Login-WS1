<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

$sessionUser = require_login();
$statement = db()->prepare('SELECT username, email, phone, address FROM users WHERE id = :id LIMIT 1');
$statement->execute(['id' => $sessionUser['id']]);
$user = $statement->fetch();

if (!$user) {
    $_SESSION = [];
    session_destroy();
    redirect('login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="logo-container"><img class="logo" src="images/logo.jpg" alt="Logo"></div>
    <main class="dashboard-container">
        <header class="dashboard-header">
            <div><h1>User Dashboard</h1><p class="user-greeting">Welcome back, <strong><?= e($user['username']) ?></strong></p></div>
        </header>
        <div class="dashboard-content">
            <section class="dashboard-section">
                <h2>Your Profile</h2>
                <div class="info-grid">
                    <div class="info-card"><label>Email</label><p><?= e($user['email']) ?></p></div>
                    <div class="info-card"><label>Phone</label><p><?= e($user['phone'] ?: 'Not provided') ?></p></div>
                    <div class="info-card"><label>Address</label><p><?= e($user['address'] ?: 'Not provided') ?></p></div>
                </div>
            </section>
            <div class="dashboard-footer">
                <form method="post" action="logout.php">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
