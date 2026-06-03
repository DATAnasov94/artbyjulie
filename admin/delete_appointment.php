<?php
session_start();
require_once "../api/db.php";

if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: login.php");
    exit;
}

$id = $_GET["id"] ?? null;

if (!$id) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php");
exit;