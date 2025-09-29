<?php
require_once __DIR__ . '/../../core/auth.php';
check_permission(['admin','lelkesz','penztaros','tag','megtekinto']);

// Kereső
$search = $_GET['search'] ?? '';
$sql = "SELECT * FROM members WHERE org_id = ? ";
$params = [$_SESSION['org_id']];

if ($search) {
    $sql .= "AND (name LIKE ? OR birth_name LIKE ? OR email LIKE ?)";
    $like = "%$search%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$members = $stmt->fetchAll();

include __DIR__ . '/../../templates/header.php';
?>

<h2>Tagok</h2>
<form method="get" class="mb-3">
    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Keresés név/email alapján">
    <button type="submit">Keresés</button>
</form>

<?php if(in_array($_SESSION['role'], ['admin','lelkesz'])): ?>
    <a href="add.php">➕ Új tag</a>
<?php endif; ?>

<table class="table table-striped">
    <tr>
        <th>ID</th><th>Név</th><th>Születési név</th><th>Lakcím</th><th>Születési hely</th><th>Születési idő</th><th>Email</th><th>Telefon</th><th>Műveletek</th>
    </tr>
    <?php foreach ($members as $m): ?>
    <tr>
        <td><?= $m['id'] ?></td>
        <td><?= htmlspecialchars($m['name']) ?></td>
        <td><?= htmlspecialchars($m['birth_name']) ?></td>
        <td><?= htmlspecialchars($m['adress']) ?></td>
        <td><?= htmlspecialchars($m['birth_place']) ?></td>
        <td><?= htmlspecialchars($m['birth_date']) ?></td>
        <td><?= htmlspecialchars($m['email']) ?></td>
        <td><?= htmlspecialchars($m['phone']) ?></td>
        <td>
            <?php if(in_array($_SESSION['role'], ['admin','lelkesz'])): ?>
                <a href="edit.php?id=<?= $m['id'] ?>">✏️</a> 
                <a href="delete.php?id=<?= $m['id'] ?>" onclick="return confirm('Biztosan törlöd?')">❌</a>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
