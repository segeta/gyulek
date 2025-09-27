<?php
require_once __DIR__ . '/../../core/auth.php';
check_permission(['admin','lelkesz']);

$id = $_GET['id'] ?? null;
if (!$id) die("Hiányzó ID.");

$stmt = $pdo->prepare("SELECT * FROM members WHERE id = ? AND org_id = ?");
$stmt->execute([$id, $_SESSION['org_id']]);
$member = $stmt->fetch();

if (!$member) die("Nincs ilyen tag.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE members SET name=?, birth_name=?, birth_place=?, birth_date=?, address=?, phone=?, email=? 
                           WHERE id=? AND org_id=?");
    $stmt->execute([
        $_POST['name'], $_POST['birth_name'], $_POST['birth_place'],
        $_POST['birth_date'] ?: null,
        $_POST['address'], $_POST['phone'], $_POST['email'],
        $id, $_SESSION['org_id']
    ]);
    header("Location: index.php");
    exit;
}
?>

<h2>Tag szerkesztése</h2>
<form method="post">
    <label>Név: <input type="text" name="name" value="<?= htmlspecialchars($member['name']) ?>" required></label><br>
    <label>Születési név: <input type="text" name="birth_name" value="<?= htmlspecialchars($member['birth_name']) ?>"></label><br>
    <label>Születési hely: <input type="text" name="birth_place" value="<?= htmlspecialchars($member['birth_place']) ?>"></label><br>
    <label>Születési dátum: <input type="date" name="birth_date" value="<?= $member['birth_date'] ?>"></label><br>
    <label>Cím: <input type="text" name="address" value="<?= htmlspecialchars($member['address']) ?>"></label><br>
    <label>Telefon: <input type="text" name="phone" value="<?= htmlspecialchars($member['phone']) ?>"></label><br>
    <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($member['email']) ?>"></label><br>
    <button type="submit">Mentés</button>
</form>
