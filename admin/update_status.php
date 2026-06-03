<?php
session_start();
require_once "../api/db.php";

if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: login.php");
    exit;
}

$id = $_GET["id"] ?? null;
$status = $_GET["status"] ?? null;

$allowedStatuses = ["pending", "confirmed", "cancelled"];

if (!$id || !in_array($status, $allowedStatuses)) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("
    UPDATE appointments
    SET status = ?
    WHERE id = ?
");

$stmt->execute([$status, $id]);

header("Location: index.php");
exit;