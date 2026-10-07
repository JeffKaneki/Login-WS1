<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

$sessionUser = require_role(['admin', 'superadmin']);

$accounts = db()->query('SELECT id, username, email, role, password_plain FROM users ORDER BY username ASC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounts List</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="logo-container"><img class="logo" src="images/logo.jpg" alt="Logo"></div>
    <main class="dashboard-container">
        <aside class="dashboard-header" aria-label="Dashboard navigation and user summary">
            <div class="dashboard-side-content">
                <div>
                    <h1><?= e(ucwords(str_replace('_', ' ', $sessionUser['role'] ?? 'user'))) ?> Dashboard</h1>
                    <p class="user-greeting">Welcome back, <strong><?= e($sessionUser['username']) ?></strong><?php if (($sessionUser['role'] ?? 'user') === 'superadmin'): ?> (SuperAdmin)<?php endif; ?></p>

                    <nav class="dashboard-nav" aria-label="Main navigation">
                        <a href="dashboard.php" class="nav-link">My Profile</a>
                        <a href="accounts.php" class="nav-link active">Accounts List</a>
                        <a href="teachers.php" class="nav-link">Teachers List</a>
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
                <h2>Accounts List</h2>
                <p><?= (($sessionUser['role'] ?? 'user') === 'superadmin') ? 'SuperAdmin can view all accounts and update roles here.' : 'Admin can view all accounts and update roles here.' ?></p>
                <table class="accounts-table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Password</th>
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
                                                <option value="teacher" <?= ($account['role'] === 'teacher') ? 'selected' : '' ?>>Teacher</option>
                                                <option value="superadmin" <?= ($account['role'] === 'superadmin') ? 'selected' : '' ?>>SuperAdmin</option>
                                            </select>
                                </td>
                                <td><?= e((string) ($account['password_plain'] ?? '')) ?></td>
                                <td>
                                            <button type="submit">Save role</button>
                                        </form>
                                        <form method="post" action="update_password.php" class="password-update-form">
                                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                            <input type="hidden" name="user_id" value="<?= e((string) $account['id']) ?>">
                                            <input type="password" name="new_password" placeholder="New password" minlength="8" required>
                                            <button type="submit">Update password</button>
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
        </div>
    </main>
</body>
</html>
