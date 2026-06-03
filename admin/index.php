<?php
session_start();
require_once "../api/db.php";

if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->query("
    SELECT *
    FROM appointments
    ORDER BY appointment_date ASC, appointment_time ASC
");

$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Админ панел | Art By Julie</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background: #f4f4f4;
        }

        .confirm {
            color: green;
            text-decoration: none;
            font-weight: bold;
        }

        .cancel {
            color: red;
            text-decoration: none;
            font-weight: bold;
        }

        .logout {
            margin-left: 10px;
        }
    </style>
</head>
<body>

<h1>Записани часове</h1>

<p>
    Влязла като:
    <strong>
        <?php echo htmlspecialchars($_SESSION["admin_username"]); ?>
    </strong>

    <a class="logout" href="logout.php">
        Изход
    </a>
</p>

<?php if (empty($appointments)): ?>

    <p>Все още няма записани часове.</p>

<?php else: ?>

<table>

    <tr>
        <th>Клиент</th>
        <th>Телефон</th>
        <th>Услуга</th>
        <th>Дата</th>
        <th>Час</th>
        <th>Статус</th>
        <th>Действия</th>
    </tr>

    <?php foreach ($appointments as $appointment): ?>

        <tr>

            <td>
                <?php echo htmlspecialchars($appointment["client_name"]); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($appointment["phone"]); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($appointment["service"]); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($appointment["appointment_date"]); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($appointment["appointment_time"]); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($appointment["status"]); ?>
            </td>

            <td>

                <a
                    class="confirm"
                    href="update_status.php?id=<?php echo $appointment['id']; ?>&status=confirmed"
                >
                    Потвърди
                </a>

                |

                <a
                    class="cancel"
                    href="update_status.php?id=<?php echo $appointment['id']; ?>&status=cancelled"
                >
                    Откажи
                </a>
                |

<a
    class="delete"
    href="delete_appointment.php?id=<?php echo $appointment['id']; ?>"
    onclick="return confirm('Сигурна ли си, че искаш да изтриеш този час?');"
>
    Изтрий
</a>

            </td>

        </tr>

    <?php endforeach; ?>

</table>

<?php endif; ?>

</body>
</html>