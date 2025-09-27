<?php
require_once __DIR__ . '/../../core/auth.php';
check_permission(['admin']);

// Felhasználók lekérése org_id alapján
$stmt = $pdo->prepare("
    SELECT u.id, u.username, u.email, u.created_at, uo.role, o.name AS org_name
    FROM users u
    JOIN user_orgs uo ON u.id = uo.user_id
    JOIN organizations o ON o.id = uo.org_id
    WHERE o.id = ?
");
$stmt->execute([$_SESSION['org_id']]);
$users = $stmt->fetchAll();

include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/menu.php';
?>

<h2>Felhasználók kezelése</h2>
<a href="add.php">➕ Új felhasználó</a>
<table border="1" cellpadding="5">
    <tr>
        <th>ID</th><th>Felhasználónév</th><th>Email</th><th>Szervezet</th><th>Szerep</th><th>Létrehozva</th><th>Műveletek</th>
    </tr>
    <?php foreach ($users as $user): ?>
    <tr>
        <td><?= $user['id'] ?></td>
        <td><?= htmlspecialchars($user['username']) ?></td>
        <td><?= htmlspecialchars($user['email']) ?></td>
        <td><?= htmlspecialchars($user['org_name']) ?></td>
        <td><?= htmlspecialchars($user['role']) ?></td>
        <td><?= $user['created_at'] ?></td>
        <td>
            <a href="edit.php?id=<?= $user['id'] ?>">✏️</a> | 
            <a href="delete.php?id=<?= $user['id'] ?>" onclick="return confirm('Biztosan törlöd?')">❌</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
