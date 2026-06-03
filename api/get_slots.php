<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "db.php";

$date = $_GET["date"] ?? null;

if (!$date) {
    http_response_code(400);
    echo json_encode([
        "error" => "Липсва дата"
    ]);
    exit;
}

$allSlots = [
    "09:00:00",
    "10:00:00",
    "11:00:00",
    "12:00:00",
    "13:00:00",
    "14:00:00",
    "15:00:00",
    "16:00:00"
];

$stmt = $pdo->prepare("
    SELECT appointment_time
    FROM appointments
    WHERE appointment_date = ?
");

$stmt->execute([$date]);

$bookedSlots = $stmt->fetchAll(PDO::FETCH_COLUMN);

$availableSlots = array_values(
    array_diff($allSlots, $bookedSlots)
);

echo json_encode([
    "date" => $date,
    "availableSlots" => $availableSlots
]);