<?php
// Menü komponens – minden oldal tartalmazza
?>
<nav class="menu" id="sidebar">
    <a href="/gyulek/index.php" class="menu-item">
        <i class="bi bi-house-door"></i> <span>Kezdőlap</span>
    </a>
    <a href="/gyulek/modules/tagok/index.php" class="menu-item">
        <i class="bi bi-people"></i> <span>Tagok</span>
    </a>

    <!-- Események submenu -->
    <div class="menu-item submenu">
        <a href="#" class="submenu-toggle d-flex align-items-center">
            <i class="bi bi-calendar-event"></i> 
            <span class="flex-grow-1">Események</span>
            <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <div class="submenu-items collapse ps-4">
            <a href="/gyulek/modules/esemenyek/index.php" class="submenu-item">
                <i class="bi bi-calendar3"></i> <span>Naptár</span>
            </a>
            <a href="/gyulek/modules/esemenyek/add.php" class="submenu-item">
                <i class="bi bi-plus-circle"></i> <span>Új esemény</span>
            </a>
        </div>
    </div>

    <!-- Adományok submenu -->
    <div class="menu-item submenu">
        <a href="#" class="submenu-toggle d-flex align-items-center">
            <i class="bi bi-cash-stack"></i> 
            <span class="flex-grow-1">Adományok</span>
            <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <div class="submenu-items collapse ps-4">
            <a href="/gyulek/modules/adomanyok/index.php" class="submenu-item">
                <i class="bi bi-bar-chart"></i> <span>Statisztika</span>
            </a>
            <a href="/gyulek/modules/adomanyok/add.php" class="submenu-item">
                <i class="bi bi-plus-circle"></i> <span>Új adomány</span>
            </a>
        </div>
    </div>

    <!-- Iratkezelés submenu -->
    <div class="menu-item submenu">
        <a href="#" class="submenu-toggle d-flex align-items-center">
            <i class="bi bi-folder2"></i> 
            <span class="flex-grow-1">Iratkezelés</span>
            <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <div class="submenu-items collapse ps-4">
            <a href="/gyulek/modules/iratok/index.php" class="submenu-item">
                <i class="bi bi-archive"></i> <span>Iratlista</span>
            </a>
            <a href="/gyulek/modules/iratok/add.php" class="submenu-item">
                <i class="bi bi-plus-circle"></i> <span>Új irat</span>
            </a>
        </div>
    </div>

    <!-- Felhasználók -->
    <?php if ($_SESSION['role'] === 'admin'): ?>
  <a href="/gyulek/modules/users/index.php" class="menu-item">
    <i class="bi bi-person-gear"></i> <span>Felhasználók</span>
  </a>
<?php else: ?>
  <a href="/gyulek/modules/settings/profile.php" class="menu-item">
    <i class="bi bi-gear"></i> <span>Beállítások</span>
  </a>
<?php endif; ?>

       <!-- Dark mode -->
    <div class="menu-item mt-auto">
        <label style="cursor:pointer; display:flex; align-items:center;">
            <input type="checkbox" id="darkModeToggle" style="margin-right:8px;">
            <i class="bi bi-moon"></i> <span>Dark Mode</span>
        </label>
    </div>
</nav>

<script>
document.addEventListener("DOMContentLoaded", function() {
  document.querySelectorAll(".submenu-toggle").forEach(toggle => {
    toggle.addEventListener("click", function(e) {
      e.preventDefault();
      const submenu = this.nextElementSibling;
      submenu.classList.toggle("show");

      // Chevron ikon váltása
      const icon = this.querySelector(".bi-chevron-down, .bi-chevron-up");
      if (submenu.classList.contains("show")) {
        icon.classList.replace("bi-chevron-down", "bi-chevron-up");
      } else {
        icon.classList.replace("bi-chevron-up", "bi-chevron-down");
      }
    });
  });
});
</script>
