<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

$sessionUser = require_login();
$statement = db()->prepare('SELECT id, username, email, phone, address, role FROM users WHERE id = :id LIMIT 1');
$statement->execute(['id' => $sessionUser['id']]);
$user = $statement->fetch();

if (!$user) {
    $_SESSION = [];
    session_destroy();
    redirect('login.php');
}

$canManageAccounts = in_array(($user['role'] ?? 'user'), ['admin', 'superadmin'], true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="logo-container"><img class="logo" src="images/logo.jpg" alt="Logo"></div>
    <main class="dashboard-container">
        <aside class="dashboard-header" aria-label="Dashboard navigation and user summary">
            <div class="dashboard-side-content">
                <div>
                    <h1><?= e(ucwords(str_replace('_', ' ', $user['role'] ?? 'user'))) ?> Dashboard</h1>
                    <p class="user-greeting">Welcome back, <strong><?= e($user['username']) ?></strong><?php if (($user['role'] ?? 'user') === 'superadmin'): ?> (SuperAdmin)<?php endif; ?></p>

                    <nav class="dashboard-nav" aria-label="Main navigation">
                        <a href="dashboard.php" class="nav-link active">My Profile</a>
                        <?php if ($canManageAccounts): ?>
                            <a href="accounts.php" class="nav-link">Accounts List</a>
                            <a href="teachers.php" class="nav-link">Teachers List</a>
                        <?php endif; ?>
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
                <h2>Your Profile</h2>
                <div class="info-grid">
                    <div class="info-card"><label>Email</label><p><?= e($user['email']) ?></p></div>
                    <div class="info-card"><label>Role</label><p><?= e(ucfirst($user['role'] ?? 'user')) ?></p></div>
                    <div class="info-card"><label>Phone</label><p><?= e($user['phone'] ?: 'Not provided') ?></p></div>
                    <div class="info-card"><label>Address</label><p><?= e($user['address'] ?: 'Not provided') ?></p></div>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
