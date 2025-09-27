<?php
require_once __DIR__ . '/../../core/auth.php';
check_permission(['admin','lelkesz']);

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO members (org_id, name, birth_name, birth_place, birth_date, address, phone, email) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    try {
        $stmt->execute([
            $_SESSION['org_id'],
            $_POST['name'], $_POST['birth_name'], $_POST['birth_place'],
            $_POST['birth_date'] ?: null,
            $_POST['address'], $_POST['phone'], $_POST['email']
        ]);
        header("Location: index.php");
        exit;
    } catch (PDOException $e) {
        $message = "❌ Hiba: " . $e->getMessage();
    }
}
?>

<h2>Új tag</h2>
<form method="post">
    <label>Név: <input type="text" name="name" required></label><br>
    <label>Születési név: <input type="text" name="birth_name"></label><br>
    <label>Születési hely: <input type="text" name="birth_place"></label><br>
    <label>Születési dátum: <input type="date" name="birth_date"></label><br>
    <label>Cím: <input type="text" name="address"></label><br>
    <label>Telefon: <input type="text" name="phone"></label><br>
    <label>Email: <input type="email" name="email"></label><br>
    <button type="submit">Mentés</button>
</form>
<p><?= $message ?></p>
