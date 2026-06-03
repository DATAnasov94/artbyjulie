<?php
session_start();
require_once "../api/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");

    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin["password_hash"])) {
        $_SESSION["admin_logged_in"] = true;
        $_SESSION["admin_username"] = $admin["username"];

        header("Location: index.php");
        exit;
    } else {
        $error = "Грешно потребителско име или парола.";
    }
}
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Админ вход | Art By Julie</title>
</head>
<body>

<h1>Админ вход</h1>

<?php if ($error): ?>
    <p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>

<form method="POST">
    <label>
        Потребител:
        <input type="text" name="username" required>
    </label>

    <br><br>

    <label>
        Парола:
        <input type="password" name="password" required>
    </label>

    <br><br>

    <button type="submit">Вход</button>
</form>

</body>
</html>