<?php
session_start();
require_once "../api/db.php";

if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: login.php");
    exit;
}

$selectedMonth = $_GET["month"] ?? date("Y-m");
$monthStart = $selectedMonth . "-01";
$monthEnd = date("Y-m-t", strtotime($monthStart));

$stmt = $pdo->prepare("
    SELECT *
    FROM appointments
    WHERE appointment_date BETWEEN ? AND ?
    ORDER BY appointment_date ASC, appointment_time ASC
");
$stmt->execute([$monthStart, $monthEnd]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$appointmentsByDate = [];

foreach ($appointments as $appointment) {
    $appointmentsByDate[$appointment["appointment_date"]][] = $appointment;
}

$daysInMonth = date("t", strtotime($monthStart));
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>График | Art By Julie</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f6f7f7;
            color: #333;
        }

        .admin-header {
            background: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }

        .admin-header h1 {
            margin: 0;
            font-size: 24px;
        }

        .logout {
            color: #333;
            text-decoration: none;
            font-weight: bold;
        }

        .container {
            padding: 30px 40px;
        }

        .month-filter {
            background: white;
            padding: 20px;
            border-radius: 14px;
            margin-bottom: 25px;
            display: flex;
            gap: 15px;
            align-items: center;
        }

        input[type="month"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        button {
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            background: #8ec9b3;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 14px;
        }

        .day-card {
            background: white;
            border-radius: 14px;
            padding: 15px;
            min-height: 150px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.05);
        }

        .day-number {
            font-weight: bold;
            margin-bottom: 12px;
            color: #8ec9b3;
        }

        .appointment {
            background: #f1faf6;
            border-left: 4px solid #8ec9b3;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .appointment.cancelled {
            background: #fff1f1;
            border-left-color: #d66;
        }

        .appointment.confirmed {
            background: #eef9f1;
            border-left-color: #4caf50;
        }

        .appointment.pending {
            background: #fff8e8;
            border-left-color: #e0a800;
        }

        .actions a {
            font-size: 13px;
            margin-right: 8px;
            text-decoration: none;
            font-weight: bold;
        }

        .confirm {
            color: green;
        }

        .cancel {
            color: red;
        }

        .delete {
            color: #555;
        }

        .empty {
            color: #aaa;
            font-size: 13px;
        }

        @media (max-width: 1000px) {
            .calendar {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .admin-header {
                flex-direction: column;
                gap: 10px;
            }

            .container {
                padding: 20px;
            }

            .calendar {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<header class="admin-header">
    <h1>График | Art By Julie</h1>

    <div>
        Влязла като:
        <strong><?php echo htmlspecialchars($_SESSION["admin_username"]); ?></strong>
        |
        <a class="logout" href="logout.php">Изход</a>
    </div>
</header>

<div class="container">

    <form class="month-filter" method="GET">
        <label>
            Избери месец:
            <input type="month" name="month" value="<?php echo htmlspecialchars($selectedMonth); ?>">
        </label>

        <button type="submit">Покажи</button>
    </form>

    <div class="calendar">

        <?php for ($day = 1; $day <= $daysInMonth; $day++): ?>

            <?php
                $currentDate = date("Y-m-d", strtotime($selectedMonth . "-" . $day));
                $dayAppointments = $appointmentsByDate[$currentDate] ?? [];
            ?>

            <div class="day-card">
                <div class="day-number">
                    <?php echo date("d.m.Y", strtotime($currentDate)); ?>
                </div>

                <?php if (empty($dayAppointments)): ?>

                    <div class="empty">Няма записани часове</div>

                <?php else: ?>

                    <?php foreach ($dayAppointments as $appointment): ?>

                        <div class="appointment <?php echo htmlspecialchars($appointment["status"]); ?>">
                            <strong><?php echo htmlspecialchars(substr($appointment["appointment_time"], 0, 5)); ?></strong>
                            —
                            <?php echo htmlspecialchars($appointment["client_name"]); ?>

                            <br>

                            <?php echo htmlspecialchars($appointment["service"]); ?>

                            <br>

                            Тел:
                            <?php echo htmlspecialchars($appointment["phone"]); ?>

                            <br>

                            Статус:
                            <strong><?php echo htmlspecialchars($appointment["status"]); ?></strong>

                            <div class="actions">
                                <a class="confirm" href="update_status.php?id=<?php echo $appointment['id']; ?>&status=confirmed">
                                    Потвърди
                                </a>

                                <a class="cancel" href="update_status.php?id=<?php echo $appointment['id']; ?>&status=cancelled">
                                    Откажи
                                </a>

                                <a
                                    class="delete"
                                    href="delete_appointment.php?id=<?php echo $appointment['id']; ?>"
                                    onclick="return confirm('Сигурна ли си, че искаш да изтриеш този час?');"
                                >
                                    Изтрий
                                </a>
                            </div>
                        </div>

                    <?php endforeach; ?>

                <?php endif; ?>
            </div>

        <?php endfor; ?>

    </div>

</div>

</body>
</html>