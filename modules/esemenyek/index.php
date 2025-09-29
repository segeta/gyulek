<?php
require_once __DIR__ . '/../../core/auth.php';
check_permission(['admin','lelkesz','penztaros','tag','megtekinto']);
include __DIR__ . '/../../templates/header.php';
?>

<h2>Események</h2>

<?php if (in_array($_SESSION['role'], ['admin','lelkesz'])): ?>
  <a href="add.php" class="btn btn-primary mb-3">+ Új esemény</a>
<?php endif; ?>

<div id="calendar"></div>

<?php if (in_array($_SESSION['role'], ['admin','lelkesz'])): ?>
  <a href="add.php" class="btn btn-primary mt-3">+ Új esemény</a>
<?php endif; ?>

<!-- FullCalendar -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var calendarEl = document.getElementById('calendar');
  var calendar = new FullCalendar.Calendar(calendarEl, {
  initialView: 'listWeek',
  headerToolbar: {
    left: 'prev,next today',
    center: 'title',
    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
  },
  locale: 'hu',
  buttonText: {
    today: 'Ma',
    month: 'Hónap',
    week: 'Hét',
    day: 'Nap',
    list: 'Lista'
  },
  events: '/gyulek/modules/esemenyek/api.php', // JSON endpoint
  
  eventDidMount: function(info) {
    // Bootstrap tooltip
    new bootstrap.Tooltip(info.el, {
      title: info.event.title + 
             (info.event.extendedProps.type_name ? " (" + info.event.extendedProps.type_name + ")" : ""),
      placement: 'top',
      trigger: 'hover',
      container: 'body'
    });
  },

  eventClick: function(info) {
    // Átirányítás a részletek oldalra
    window.location.href = "/gyulek/modules/esemenyek/details.php?id=" + info.event.id;
  }
});
  calendar.render();
});
</script>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
