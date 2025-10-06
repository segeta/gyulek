<div class="d-flex flex-column flex-shrink-0 p-3 bg-light" style="width: 250px;">
    <h5 class="mb-3">Menü</h5>
    <ul class="nav nav-pills flex-column mb-auto" id="mainMenu">

        <!-- Kezdőlap -->
        <li>
            <a href="/gyulek/org_home.php" class="nav-link">
                <span><i class="bi bi-house-door"></i> Kezdőlap</span>
            </a>
        </li>

        <!-- Tagok -->
        <li>
            <a class="nav-link d-flex justify-content-between align-items-center"
               data-bs-toggle="collapse" href="#submenuTagok" role="button" aria-expanded="false">
                <span><i class="bi bi-people"></i> Tagok</span>
                <i class="bi bi-chevron-down small rotate-icon"></i>
            </a>
            <div class="collapse" id="submenuTagok" data-bs-parent="#mainMenu">
                <a href="/gyulek/modules/tagok/index.php" class="nav-link ms-4"><i class="bi bi-list-ul"></i> Áttekintés</a>
                <a href="/gyulek/modules/tagok/add.php" class="nav-link ms-4"><i class="bi bi-plus-circle"></i> Új tag</a>
                <a href="/gyulek/modules/tagok/search.php" class="nav-link ms-4"><i class="bi bi-search"></i> Keresés/Szűrés</a>
                <a href="/gyulek/modules/tagok/stats.php" class="nav-link ms-4"><i class="bi bi-bar-chart"></i> Statisztika</a>
            </div>
        </li>

        <!-- Események -->
        <li>
            <a class="nav-link d-flex justify-content-between align-items-center"
               data-bs-toggle="collapse" href="#submenuEsemenyek" role="button" aria-expanded="false">
                <span><i class="bi bi-calendar-event"></i> Események</span>
                <i class="bi bi-chevron-down small rotate-icon"></i>
            </a>
            <div class="collapse" id="submenuEsemenyek" data-bs-parent="#mainMenu">
                <a href="/gyulek/modules/esemenyek/index.php" class="nav-link ms-4"><i class="bi bi-list-ul"></i> Áttekintés</a>
                <a href="/gyulek/modules/esemenyek/add.php" class="nav-link ms-4"><i class="bi bi-plus-circle"></i> Új esemény</a>
                <a href="/gyulek/modules/esemenyek/search.php" class="nav-link ms-4"><i class="bi bi-search"></i> Keresés/Szűrés</a>
                <a href="/gyulek/modules/esemenyek/stats.php" class="nav-link ms-4"><i class="bi bi-bar-chart"></i> Statisztika</a>
            </div>
        </li>

        <!-- Adományok -->
        <li>
            <a class="nav-link d-flex justify-content-between align-items-center"
               data-bs-toggle="collapse" href="#submenuAdomanyok" role="button" aria-expanded="false">
                <span><i class="bi bi-cash-coin"></i> Adományok</span>
                <i class="bi bi-chevron-down small rotate-icon"></i>
            </a>
            <div class="collapse" id="submenuAdomanyok" data-bs-parent="#mainMenu">
                <a href="/gyulek/modules/adomanyok/index.php" class="nav-link ms-4"><i class="bi bi-list-ul"></i> Áttekintés</a>
                <a href="/gyulek/modules/adomanyok/add.php" class="nav-link ms-4"><i class="bi bi-plus-circle"></i> Új adomány</a>
                <a href="/gyulek/modules/adomanyok/search.php" class="nav-link ms-4"><i class="bi bi-search"></i> Keresés/Szűrés</a>
                <a href="/gyulek/modules/adomanyok/stats.php" class="nav-link ms-4"><i class="bi bi-bar-chart"></i> Statisztika</a>
            </div>
        </li>

        <!-- Iratkezelés -->
        <li>
            <a class="nav-link d-flex justify-content-between align-items-center"
               data-bs-toggle="collapse" href="#submenuIratok" role="button" aria-expanded="false">
                <span><i class="bi bi-folder"></i> Iratkezelés</span>
                <i class="bi bi-chevron-down small rotate-icon"></i>
            </a>
            <div class="collapse" id="submenuIratok" data-bs-parent="#mainMenu">
                <a href="/gyulek/modules/iratok/index.php" class="nav-link ms-4"><i class="bi bi-list-ul"></i> Áttekintés</a>
                <a href="/gyulek/modules/iratok/add.php" class="nav-link ms-4"><i class="bi bi-plus-circle"></i> Új irat</a>
                <a href="/gyulek/modules/iratok/search.php" class="nav-link ms-4"><i class="bi bi-search"></i> Keresés/Szűrés</a>
                <a href="/gyulek/modules/iratok/stats.php" class="nav-link ms-4"><i class="bi bi-bar-chart"></i> Statisztika</a>
            </div>
        </li>

        <!-- Jelentések -->
        <li>
            <a class="nav-link d-flex justify-content-between align-items-center"
               data-bs-toggle="collapse" href="#submenuJelentesek" role="button" aria-expanded="false">
                <span><i class="bi bi-clipboard-data"></i> Jelentések</span>
                <i class="bi bi-chevron-down small rotate-icon"></i>
            </a>
            <div class="collapse" id="submenuJelentesek" data-bs-parent="#mainMenu">
                <a href="/gyulek/modules/jelentesek/index.php" class="nav-link ms-4"><i class="bi bi-list-ul"></i> Áttekintés</a>
                <a href="/gyulek/modules/jelentesek/add.php" class="nav-link ms-4"><i class="bi bi-plus-circle"></i> Új jelentés</a>
                <a href="/gyulek/modules/jelentesek/search.php" class="nav-link ms-4"><i class="bi bi-search"></i> Keresés/Szűrés</a>
                <a href="/gyulek/modules/jelentesek/stats.php" class="nav-link ms-4"><i class="bi bi-bar-chart"></i> Statisztika</a>
            </div>
        </li>

        <!-- Íróasztal -->
        <li>
            <a href="/gyulek/dashboard.php" class="nav-link">
                <i class="bi bi-grid-3x3-gap"></i> Íróasztal
            </a>
        </li>

        <hr>

        <!-- Beállítások -->
        <li>
            <a href="/gyulek/modules/settings/profile.php" class="nav-link">
                <i class="bi bi-gear"></i> Beállítások
            </a>
        </li>

        <!-- Rendszerkezelés (csak rendszergazda) -->
        <?php if ($_SESSION['user_permission'] === 'rendszergazda'): ?>
        <li class="nav-item">
			 <a href="/gyulek/modules/admin/index.php" class="nav-link text-danger fw-bold">
    	<i class="bi bi-shield-lock"></i> Rendszerkezelés
   			</a>
		</li>
        <?php endif; ?>

    </ul>
</div>

<style>
.rotate-icon {
    transition: transform 0.2s;
}
a[aria-expanded="true"] .rotate-icon {
    transform: rotate(180deg);
}
</style>