<?php
header("Content-Type: application/json; charset=UTF-8");

require_once "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$client_name = trim($data["client_name"] ?? "");
$phone = trim($data["phone"] ?? "");
$service = trim($data["service"] ?? "");
$appointment_date = trim($data["appointment_date"] ?? "");
$appointment_time = trim($data["appointment_time"] ?? "");

if (
    empty($client_name) ||
    empty($phone) ||
    empty($service) ||
    empty($appointment_date) ||
    empty($appointment_time)
) {
    http_response_code(400);
    echo json_encode(["error" => "Всички полета са задължителни."]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO appointments 
        (client_name, phone, service, appointment_date, appointment_time)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $client_name,
        $phone,
        $service,
        $appointment_date,
        $appointment_time
    ]);

    $to = "office@REMOVED.eu";
    $subject = "Нова резервация | Art By Julie";

    $message = "
Нова резервация:

Име: {$client_name}
Телефон: {$phone}
Услуга: {$service}
Дата: {$appointment_date}
Час: {$appointment_time}

Провери админ панела:
https://REMOVED.eu/admin/login.php
";

    $headers = "From: Art By Julie <office@REMOVED.eu>\r\n";
    $headers .= "Reply-To: office@REMOVED.eu\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    mail($to, $subject, $message, $headers);

    echo json_encode([
        "success" => true,
        "message" => "Часът е запазен успешно."
    ]);

} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        http_response_code(409);
        echo json_encode(["error" => "Този час вече е зает."]);
        exit;
    }

    http_response_code(500);
    echo json_encode(["error" => "Грешка при записване на часа."]);
}