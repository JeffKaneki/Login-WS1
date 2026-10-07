<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

$sessionUser = require_role(['admin', 'superadmin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('accounts.php');
}

verify_csrf();

$userId = (int) ($_POST['user_id'] ?? 0);
$role = (string) ($_POST['role'] ?? 'user');
$allowedRoles = ['user', 'manager', 'admin', 'teacher', 'superadmin'];
$editableRoles = ['user', 'manager', 'admin', 'teacher'];

if ($userId <= 0 || !in_array($role, $allowedRoles, true)) {
    redirect('accounts.php');
}

if (($sessionUser['role'] ?? 'user') !== 'superadmin' && ($role === 'superadmin' || !in_array($role, $editableRoles, true))) {
    redirect('accounts.php');
}

if ($userId === (int) $sessionUser['id']) {
    redirect('accounts.php');
}

$stmt = db()->prepare('UPDATE users SET role = :role WHERE id = :id');
$stmt->execute(['role' => $role, 'id' => $userId]);

redirect('accounts.php');
