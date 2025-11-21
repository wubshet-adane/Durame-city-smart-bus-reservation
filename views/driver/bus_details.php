<?php
session_start();
require '../../db.php';

if (!isset($_GET['bus_id'])) {
    echo "Bus ID missing!";
    exit;
}

$bus_id = intval($_GET['bus_id']);

$bus = $mysqli->query("SELECT * FROM buses WHERE id=$bus_id")->fetch_assoc();

$reservations = $mysqli->query("
    SELECT r.seat_number, u.name, u.phone, r.status
    FROM reservations r
    JOIN users u ON r.user_id = u.id
    WHERE r.bus_id=$bus_id
    ORDER BY r.seat_number ASC
");

$reservedSeats = [];
while($r = $reservations->fetch_assoc()){
    $reservedSeats[$r['seat_number']] = $r;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Bus Details - <?= htmlspecialchars($bus['name']) ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
/* Neon Glass Seats */
.seat {
    padding: 0.5rem;
    border-radius: 0.5rem;
    font-weight: bold;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease-in-out;
    position: relative;
}
.seat.available {
    background: rgba(16, 185, 129, 0.3);
    color: #10b981;
    box-shadow: 0 0 5px #10b981, 0 0 15px #10b98140;
}
.seat.reserved {
    background: rgba(239, 68, 68, 0.3);
    color: #ef4444;
    box-shadow: 0 0 5px #ef4444, 0 0 15px #ef444440;
}
.seat:hover::after {
    content: attr(data-info);
    position: absolute;
    top: -2.5rem;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0,0,0,0.8);
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    white-space: nowrap;
    color: #fff;
    z-index: 10;
}
</style>
</head>
<body class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 min-h-screen text-white p-6">

<div class="max-w-6xl mx-auto space-y-6">

    <!-- Bus Info -->
    <div class="bg-white/10 backdrop-blur-lg p-6 rounded-xl shadow-lg border-l-4 border-blue-500">
        <h1 class="text-3xl font-bold text-blue-400 mb-2"><?= htmlspecialchars($bus['name']) ?> (<?= $bus['plate_number'] ?>)</h1>
        <p class="text-gray-300">Route: <?= htmlspecialchars($bus['from_location']) ?> → <?= htmlspecialchars($bus['to_location']) ?></p>
        <p class="text-gray-300">Travel Date: <?= $bus['travel_date'] ?> | Departure: <?= $bus['departure_time'] ?> | Arrival: <?= $bus['arrival_time'] ?></p>
        <p class="text-gray-300">Total Seats: <?= $bus['total_seats'] ?> | Price: <?= $bus['price'] ?> Birr</p>
        <p class="text-gray-300">Status: <?= $bus['status'] ?></p>
    </div>

    <!-- Seat Map -->
    <div class="bg-white/10 backdrop-blur-lg p-6 rounded-xl shadow-lg border-l-4 border-green-500">
        <h2 class="text-2xl font-bold mb-4 text-green-400">Seat Map</h2>
        <div class="grid grid-cols-8 gap-2">
            <?php 
            for ($i=1; $i<=$bus['total_seats']; $i++) {
                if (isset($reservedSeats[$i])) {
                    echo '<div class="seat reserved" data-info="Reserved by '.$reservedSeats[$i]['name'].'">'.$i.'</div>';
                } else {
                    echo '<div class="seat available" data-info="Available">'.$i.'</div>';
                }
            }
            ?>
        </div>
    </div>

    <!-- Reserved Customers -->
    <div class="bg-white/10 backdrop-blur-lg p-6 rounded-xl shadow-lg border-l-4 border-red-500">
        <h2 class="text-2xl font-bold mb-4 text-red-400">Reserved Customers</h2>

        <?php if (count($reservedSeats) == 0): ?>
            <p class="text-gray-300">No seats reserved yet.</p>
        <?php else: ?>
            <table class="w-full table-auto border-collapse border border-gray-700">
                <thead>
                    <tr class="bg-gray-800">
                        <th class="border border-gray-700 p-2">Seat</th>
                        <th class="border border-gray-700 p-2">Customer Name</th>
                        <th class="border border-gray-700 p-2">Phone</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservedSeats as $seat => $customer): ?>
                        <tr class="hover:bg-gray-700/50 transition">
                            <td class="border border-gray-700 p-2 text-center"><?= $seat ?></td>
                            <td class="border border-gray-700 p-2"><?= htmlspecialchars($customer['name']) ?></td>
                            <td class="border border-gray-700 p-2"><?= htmlspecialchars($customer['phone']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</div>
</body>
</html>
