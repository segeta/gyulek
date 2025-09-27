<?php
require_once __DIR__ . '/core/db.php';
require_once __DIR__ . '/core/functions.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $hash = hash_password($password);

        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, email) VALUES (?, ?, ?)");
            $stmt->execute([$username, $hash, $email ?: null]);

            $message = "✅ Felhasználó sikeresen létrehozva!<br>
                        Felhasználónév: <b>" . htmlspecialchars($username) . "</b><br>
                        Email: <b>" . htmlspecialchars($email) . "</b>";
        } catch (PDOException $e) {
            $message = "❌ Hiba történt: " . $e->getMessage();
        }
    } else {
        $message = "❌ Felhasználónév és jelszó kötelező!";
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Új felhasználó létrehozása</title>
</head>
<body>
    <h2>Új felhasználó létrehozása</h2>
    <form method="post">
        <label>Felhasználónév: <input type="text" name="username" required></label><br>
        <label>Email (opcionális): <input type="email" name="email"></label><br>
        <label>Jelszó: <input type="password" name="password" required></label><br>
        <button type="submit">Létrehozás</button>
    </form>
    <p><?= $message ?></p>
</body>
</html>
