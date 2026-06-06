<?php

require_once "../api/db.php";

$username = "******";
$password = "*********";

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("
    INSERT INTO admin_users (username, password_hash)
    VALUES (?, ?)
");

$stmt->execute([
    $username,
    $passwordHash
]);

echo "Admin user created successfully.";
