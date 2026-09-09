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

$accounts = null;
if (($user['role'] ?? 'user') === 'superadmin') {
    $accounts = db()->query('SELECT id, username, email, role FROM users ORDER BY username ASC')->fetchAll();
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
        <aside class="dashboard-header" aria-label="Dashboard navigation and user summary">
            <div>
                <h1><?= e(ucwords(str_replace('_', ' ', $user['role'] ?? 'user'))) ?> Dashboard</h1>
                <p class="user-greeting">Welcome back, <strong><?= e($user['username']) ?></strong><?php if (($user['role'] ?? 'user') === 'superadmin'): ?> (SuperAdmin)<?php endif; ?></p>
            </div>
        </aside>
        <div class="dashboard-content">
            <?php if (($user['role'] ?? 'user') === 'superadmin'): ?>
                <section class="dashboard-section">
                    <h2>Accounts List</h2>
                    <p>SuperAdmin can view all user accounts and update roles here.</p>
                    <table class="accounts-table">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($accounts as $account): ?>
                                <tr>
                                    <td><?= e($account['username']) ?></td>
                                    <td><?= e($account['email']) ?></td>
                                    <td>
                                        <?php if ((int) $account['id'] !== (int) $sessionUser['id']): ?>
                                            <form method="post" action="set_role.php">
                                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                                <input type="hidden" name="user_id" value="<?= e((string) $account['id']) ?>">
                                                <select name="role">
                                                    <option value="user" <?= ($account['role'] === 'user') ? 'selected' : '' ?>>User</option>
                                                    <option value="manager" <?= ($account['role'] === 'manager') ? 'selected' : '' ?>>Manager</option>
                                                    <option value="admin" <?= ($account['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
                                                    <option value="superadmin" <?= ($account['role'] === 'superadmin') ? 'selected' : '' ?>>SuperAdmin</option>
                                                </select>
                                    </td>
                                    <td>
                                                <button type="submit">Save role</button>
                                            </form>
                                        <?php else: ?>
                                            <span><?= e(ucfirst($account['role'])) ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </section>
            <?php endif; ?>

            <section class="dashboard-section">
                <h2>Your Profile</h2>
                <div class="info-grid">
                    <div class="info-card"><label>Email</label><p><?= e($user['email']) ?></p></div>
                    <div class="info-card"><label>Role</label><p><?= e(ucfirst($user['role'] ?? 'user')) ?></p></div>
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
