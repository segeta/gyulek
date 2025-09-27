<?php
require_once __DIR__ . '/../../core/auth.php';
check_permission(['admin']);
require_once __DIR__ . '/../../core/functions.php';

// Szervezetek lekérdezése
$stmt = $pdo->query("SELECT id, name FROM organizations ORDER BY name");
$organizations = $stmt->fetchAll();

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $org_id   = $_POST['org_id'];
    $role     = $_POST['role'];

    $hash = hash_password($password);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, email) VALUES (?, ?, ?)");
        $stmt->execute([$username, $hash, $email ?: null]);
        $user_id = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO user_orgs (user_id, org_id, role) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $org_id, $role]);

        header("Location: index.php");
        exit;
    } catch (PDOException $e) {
        $message = "❌ Hiba: " . $e->getMessage();
    }
}
?>
<h2>Új felhasználó</h2>
<form method="post">
    <label>Felhasználónév: <input type="text" name="username" required></label><br>
    <label>Email: <input type="email" name="email"></label><br>
    <label>Jelszó: <input type="password" name="password" required></label><br>
    <label>Szervezet:
        <select name="org_id" required>
            <?php foreach ($organizations as $org): ?>
                <option value="<?= $org['id'] ?>"><?= htmlspecialchars($org['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </label><br>
    <label>Szerep:
        <select name="role">
            <option value="admin">admin</option>
            <option value="penztaros">pénztáros</option>
            <option value="lelkesz">lelkész</option>
            <option value="tag">tag</option>
            <option value="megtekinto">megtekintő</option>
        </select>
    </label><br>
    <button type="submit">Létrehozás</button>
</form>
<p><?= $message ?></p>
