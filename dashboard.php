<?php
require_once __DIR__ . '/core/db.php';
require_once __DIR__ . '/core/functions.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Felhasználói adatok
$userName = $_SESSION['given_name'] ?? $_SESSION['username'];

// Dinamikus köszöntés napszak szerint
$hour = (int)date('H');
if ($hour < 12) {
    $greeting = "Jó reggelt";
} elseif ($hour < 18) {
    $greeting = "Jó napot";
} else {
    $greeting = "Jó estét";
}

// --- Közelgő események ---
$stmt = $pdo->prepare("
    SELECT e.start, e.location, et.name AS type, o.name AS org_name
    FROM events e
    JOIN event_types et ON e.type_id = et.id
    JOIN organizations o ON e.org_id = o.id
    WHERE e.org_id IN (
        SELECT org_id FROM user_orgs WHERE user_id = ?
    )
    AND DATE(e.start) BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 2 DAY)
    ORDER BY e.start ASC
");
$stmt->execute([$_SESSION['user_id']]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

$groupedEvents = ['today' => [], 'tomorrow' => [], 'dayafter' => []];
foreach ($events as $event) {
    $date = date('Y-m-d', strtotime($event['start']));
    if ($date === date('Y-m-d')) {
        $groupedEvents['today'][] = $event;
    } elseif ($date === date('Y-m-d', strtotime('+1 day'))) {
        $groupedEvents['tomorrow'][] = $event;
    } elseif ($date === date('Y-m-d', strtotime('+2 day'))) {
        $groupedEvents['dayafter'][] = $event;
    }
}

// --- Születésnaposok ---
$stmt = $pdo->prepare("
    SELECT 
        m.id, 
        m.name, 
        m.birth_date,
        TIMESTAMPDIFF(YEAR, m.birth_date, CURDATE()) AS age
    FROM members m
    WHERE m.org_id IN (
        SELECT org_id FROM user_orgs WHERE user_id = ?
    )
    AND DATE_FORMAT(m.birth_date, '%m-%d') BETWEEN DATE_FORMAT(CURDATE(), '%m-%d')
                                               AND DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 2 DAY), '%m-%d')
    ORDER BY MONTH(m.birth_date), DAY(m.birth_date)
");
$stmt->execute([$_SESSION['user_id']]);
$birthdays = $stmt->fetchAll(PDO::FETCH_ASSOC);

$groupedBirthdays = ['today' => [], 'tomorrow' => [], 'dayafter' => []];
foreach ($birthdays as $p) {
    $monthDay = date('m-d', strtotime($p['birth_date']));
    if ($monthDay === date('m-d')) {
        $groupedBirthdays['today'][] = $p;
    } elseif ($monthDay === date('m-d', strtotime('+1 day'))) {
        $groupedBirthdays['tomorrow'][] = $p;
    } elseif ($monthDay === date('m-d', strtotime('+2 day'))) {
        $groupedBirthdays['dayafter'][] = $p;
    }
}

// --- Névnapok: csak a footerben, itt most nincs ---

include __DIR__ . '/templates/header.php';
?>

<main class="col-md-12 col-12 p-4">
    <h2><?= $greeting ?>, <?= htmlspecialchars($userName) ?>!</h2>
    <p>Üdvözöllek a virtuális irodádban, ahol az általad kezelt egyházszervezetek fontos információit láthatod.</p>

    <div class="row">
        <!-- Bal oldal (2/3) -->
        <div class="col-md-8">
            <!-- Közelgő események -->
            <div class="card shadow p-3 mb-4">
                <h4>Közelgő események</h4>
                <div class="row">
                    <div class="col">
                        <h6>Ma</h6>
                        <ul>
                            <?php if (empty($groupedEvents['today'])): ?>
                                <li>Nincs esemény mára.</li>
                            <?php else: ?>
                                <?php foreach ($groupedEvents['today'] as $event): ?>
                                    <li><?= date('H:i', strtotime($ev['start'])) ?> – <?= htmlspecialchars($event['type']) ?> (<?= htmlspecialchars($event['location']) ?>)</li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="col">
                        <h6>Holnap</h6>
                        <ul>
                            <?php if (empty($groupedEvents['tomorrow'])): ?>
                                <li>Nincs esemény holnapra.</li>
                            <?php else: ?>
                                <?php foreach ($groupedEvents['tomorrow'] as $event): ?>
                                    <li><?= date('H:i', strtotime($ev['start'])) ?> – <?= htmlspecialchars($event['type']) ?> (<?= htmlspecialchars($event['location']) ?>)</li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="col">
                        <h6>Holnapután</h6>
                        <ul>
                            <?php if (empty($groupedEvents['dayafter'])): ?>
                                <li>Nincs esemény holnaputánra.</li>
                            <?php else: ?>
                                <?php foreach ($groupedEvents['dayafter'] as $event): ?>
                                    <li><?= date('H:i', strtotime($ev['start'])) ?> – <?= htmlspecialchars($event['type']) ?> (<?= htmlspecialchars($event['location']) ?>)</li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Tennivalók -->
            <div class="card shadow p-3 mb-4">
                <h4>Teendők</h4>
                <p><i class="bi bi-exclamation-circle text-warning"></i> Minden eseményt rögzítettél, nincs elmaradásod.</p>
            </div>
        </div>

        <!-- Jobb oldal (1/3) -->
        <div class="col-md-4">
            <!-- Születésnaposok -->
            <!-- Születésnaposok -->
<div class="card shadow p-3 mb-4">
    <h4>Születésnaposok</h4>
    <div>
        <?php 
        $labels = ['today' => 'Ma', 'tomorrow' => 'Holnap', 'dayafter' => 'Holnapután'];
        foreach ($labels as $day => $label): ?>
            <h6><?= $label ?></h6>
            <ul>
                <?php if (empty($groupedBirthdays[$day])): ?>
                    <li>Senki sem ünnepel <?= strtolower($label) ?>.</li>
                <?php else: ?>
                    <?php foreach ($groupedBirthdays[$day] as $p): ?>
                        <li>
                            <?= htmlspecialchars($p['name']) ?> 
                            (<?= $p['age'] + ($day === 'tomorrow' || $day === 'dayafter' ? 1 : 0) ?>)
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        <?php endforeach; ?>
    </div>
</div>

            <!-- Névnaposok -->
            <div class="card shadow p-3 mb-4">
                <h4>Névnaposok</h4>
                <p>A névnaposok listája itt fog megjelenni (a members tábla keresztnév bontása után).</p>
            </div>
        </div>
    </div>
    <div class="row mt-4">
    <div class="col text-center">
      <a href="select_org.php" class="btn btn-primary">Hol kezdjük? Válassz intézményt!</a>
    </div>
  </div>
</main>

<?php include __DIR__ . '/templates/footer.php'; ?>
