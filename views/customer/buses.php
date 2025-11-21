<?php
require __DIR__ . '/../../db.php';
//session_start();

// fetch upcoming trips
$res = $mysqli->query("SELECT t.*, b.plate_number, b.seats FROM trips t JOIN buses b ON t.bus_id=b.id WHERE t.depart_time > NOW() ORDER BY t.depart_time ASC");
$trips = $res->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html><html><head>
<script src="https://cdn.tailwindcss.com"></script>
</head><body class="p-6">
<h1 class="text-xl mb-4">Available Trips</h1>
<div id="trips" class="grid gap-4">
<?php foreach($trips as $t): ?>
  <div class="p-4 border rounded">
    <div class="flex justify-between">
      <div>
        <div class="font-bold"><?=htmlspecialchars($t['from_location'])?> → <?=htmlspecialchars($t['to_location'])?></div>
        <div>Depart: <?=htmlspecialchars($t['depart_time'])?></div>
        <div>Bus: <?=htmlspecialchars($t['plate_number'])?> | Seats: <?=intval($t['seats'])?></div>
        <div>Price: <?=number_format($t['price'],2)?></div>
      </div>
      <div>
        <button class="reserve-btn bg-green-600 text-white px-3 py-2 rounded" data-trip="<?=intval($t['id'])?>">Reserve</button>
      </div>
    </div>
  </div>
<?php endforeach; ?>
</div>

<script>
document.querySelectorAll('.reserve-btn').forEach(btn => {
  btn.addEventListener('click', async () => {
    const tripId = btn.dataset.trip;
    if (!confirm('Reserve seat for trip #' + tripId + '?')) return;
    const resp = await fetch('?page=reserve_api', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ trip_id: tripId })
    });
    const j = await resp.json();
    if (j.success) {
      alert('Reserved! Reservation id: '+ j.reservation_id);
      // optionally update UI
    } else {
      alert('Error: ' + (j.message || 'Unknown'));
    }
  });
});
</script>
</body></html>
