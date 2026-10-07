<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

$sessionUser = require_role(['superadmin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('accounts.php');
}

verify_csrf();

$userId = (int) ($_POST['user_id'] ?? 0);
$newPassword = (string) ($_POST['new_password'] ?? '');

if ($userId <= 0 || strlen($newPassword) < 8) {
    redirect('accounts.php');
}

if ($userId === (int) $sessionUser['id']) {
    redirect('accounts.php');
}

$statement = db()->prepare('UPDATE users SET password_hash = :password_hash, password_plain = :password_plain WHERE id = :id');
$statement->execute([
    'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT),
    'password_plain' => $newPassword,
    'id' => $userId,
]);

redirect('accounts.php');
