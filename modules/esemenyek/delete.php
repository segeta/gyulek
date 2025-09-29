<?php
require_once __DIR__ . '/../../core/auth.php';
check_permission(['admin','lelkesz']);

if (!isset($_GET['id'])) {
    die("❌ Hiányzó esemény ID.");
}
$event_id = (int) $_GET['id'];

// Ellenőrizzük, hogy az esemény a user szervezetéhez tartozik
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ? AND org_id = ?");
$stmt->execute([$event_id, $_SESSION['org_id']]);
$event = $stmt->fetch();

if (!$event) {
    die("❌ Nincs ilyen esemény vagy nincs jogosultság.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
        // A foreign key-ek (ON DELETE CASCADE) miatt a kapcsolódó táblák rekordjai is törlődnek
        $stmt = $pdo->prepare("DELETE FROM events WHERE id = ? AND org_id = ?");
        $stmt->execute([$event_id, $_SESSION['org_id']]);

        header("Location: index.php");
        exit;
    } else {
        header("Location: details.php?id=" . $event_id);
        exit;
    }
}

include __DIR__ . '/../../templates/header.php';
?>

<h2>Esemény törlése</h2>
<p>Biztosan törölni szeretnéd az eseményt: <b><?= htmlspecialchars($event['title']) ?></b>?</p>
<form method="post">
  <button type="submit" name="confirm" value="yes" class="btn btn-danger">
    <i class="bi bi-trash"></i> Igen, törlöm
  </button>
  <button type="submit" name="confirm" value="no" class="btn btn-secondary">
    <i class="bi bi-x-circle"></i> Mégse
  </button>
</form>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
