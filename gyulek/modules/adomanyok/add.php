<?php
require_once __DIR__ . '/../../core/auth.php';
check_permission(['admin','penztaros','lelkesz']);

$message = "";

// Típusok lekérdezése
$types = $pdo->query("SELECT id, name FROM donation_types ORDER BY name")->fetchAll();

include __DIR__ . '/../../templates/header.php';
?>

<h2>Új adomány rögzítése</h2>
<form method="post" action="confirm.php" class="form">
  <div class="mb-3">
    <label class="form-label">Dátum</label>
    <input type="date" name="date" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Tag</label>
    <input type="text" id="memberSearch" class="form-control" placeholder="Kezdj el gépelni a nevét..." autocomplete="off" required>
    <input type="hidden" name="member_id" id="memberId">
    <div id="memberResults" class="list-group"></div>
  </div>
  <div class="mb-3">
    <label class="form-label">Adomány típusa</label>
    <div class="input-group">
      <select name="type_id" class="form-select" required>
        <option value="">-- Válassz típust --</option>
        <?php foreach ($types as $t): ?>
          <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newTypeModal">
        <i class="bi bi-plus-circle"></i>
      </button>
    </div>
  </div>
  <div class="mb-3">
    <label class="form-label">Összeg (Ft)</label>
    <input type="number" step="0.01" name="amount" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Megjegyzés</label>
    <textarea name="note" class="form-control"></textarea>
  </div>
  <div class="d-flex gap-2">
    <button type="submit" class="btn btn-success">
      <i class="bi bi-check-circle"></i> Mentés
    </button>
    <a href="index.php" class="btn btn-secondary">
      <i class="bi bi-x-circle"></i> Mégse
    </a>
  </div>
</form>

<script>
document.addEventListener("DOMContentLoaded", function() {
  const searchInput = document.getElementById("memberSearch");
  const resultsDiv = document.getElementById("memberResults");
  const hiddenInput = document.getElementById("memberId");

  let timeout = null;

  searchInput.addEventListener("input", function() {
    clearTimeout(timeout);
    const term = this.value.trim();
    if (term.length < 2) {
      resultsDiv.innerHTML = "";
      return;
    }
    timeout = setTimeout(() => {
      fetch("member_search.php?term=" + encodeURIComponent(term))
        .then(res => res.json())
        .then(data => {
          resultsDiv.innerHTML = "";
          data.forEach(m => {
            const item = document.createElement("a");
            item.href = "#";
            item.className = "list-group-item list-group-item-action";
            item.textContent = `${m.name} (${m.birth_date || 'n/a'}, ${m.address || 'n/a'})`;
            item.addEventListener("click", e => {
              e.preventDefault();
              searchInput.value = m.name;
              hiddenInput.value = m.id;
              resultsDiv.innerHTML = "";
            });
            resultsDiv.appendChild(item);
          });
        });
    }, 300); // debounce
  });
});
</script>

<!-- Új típus modal -->
<div class="modal fade" id="newTypeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" action="new_type.php" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Új adomány típus</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" name="name" class="form-control" placeholder="Típus neve" required>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Mentés</button>
      </div>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
