<?php
require_once __DIR__ . '/../../core/auth.php';
check_permission(['admin']);

$id = $_GET['id'] ?? null;

if (!$id) die("Hiányzó ID.");

$stmt = $pdo->prepare("
    SELECT u.id, u.username, u.email, uo.role, uo.org_id 
    FROM users u 
    JOIN user_orgs uo ON u.id = uo.user_id 
    WHERE u.id = ? AND uo.org_id = ?
");
$stmt->execute([$id, $_SESSION['org_id']]);
$user = $stmt->fetch();

if (!$user) die("Nincs ilyen felhasználó az adott szervezetben.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $role  = $_POST['role'];

    $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
    $stmt->execute([$email, $id]);

    $stmt = $pdo->prepare("UPDATE user_orgs SET role = ? WHERE user_id = ? AND org_id = ?");
    $stmt->execute([$role, $id, $_SESSION['org_id']]);

    header("Location: index.php");
    exit;
}
?>
<h2>Felhasználó szerkesztése</h2>
<form method="post">
    <p>Felhasználónév: <b><?= htmlspecialchars($user['username']) ?></b></p>
    <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"></label><br>
    <label>Szerep:
        <select name="role">
            <option value="admin" <?= $user['role']=='admin'?'selected':'' ?>>admin</option>
            <option value="penztaros" <?= $user['role']=='penztaros'?'selected':'' ?>>pénztáros</option>
            <option value="lelkesz" <?= $user['role']=='lelkesz'?'selected':'' ?>>lelkész</option>
            <option value="tag" <?= $user['role']=='tag'?'selected':'' ?>>tag</option>
            <option value="megtekinto" <?= $user['role']=='megtekinto'?'selected':'' ?>>megtekintő</option>
        </select>
    </label><br>
    <button type="submit">Mentés</button>
</form>
