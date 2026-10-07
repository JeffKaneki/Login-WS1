<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

$pdo = db();
$username = 'J3fx';
$email = 'superadmin@example.com';
$password = 'J3fx1234';

$stmt = $pdo->prepare('SELECT id, role FROM users WHERE username = :username OR email = :email LIMIT 1');
$stmt->execute(['username' => $username, 'email' => $email]);
$user = $stmt->fetch();

if (!$user) {
    $insert = $pdo->prepare('INSERT INTO users (username, email, password_hash, password_plain, role, phone, address) VALUES (:username, :email, :password_hash, :password_plain, :role, :phone, :address)');
    $insert->execute([
        'username' => $username,
        'email' => $email,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'password_plain' => $password,
        'role' => 'superadmin',
        'phone' => '0000000000',
        'address' => 'System reserved',
    ]);
    echo 'SuperAdmin user created successfully.';
    exit;
}

if (($user['role'] ?? 'user') !== 'superadmin') {
    $update = $pdo->prepare('UPDATE users SET role = :role WHERE id = :id');
    $update->execute(['role' => 'superadmin', 'id' => $user['id']]);
    echo 'Existing user promoted to SuperAdmin.';
    exit;
}

echo 'SuperAdmin already exists.';
