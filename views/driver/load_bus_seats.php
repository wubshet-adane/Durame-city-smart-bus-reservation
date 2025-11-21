<?php
require '../../db.php';
$bus_id = $_GET['bus_id'];

// Fetch reservations for this bus
$reservations = $mysqli->query("SELECT r.seat_number, u.name
                            FROM reservations r
                            JOIN users u ON r.user_id = u.id
                            WHERE r.bus_id=$bus_id
                            ORDER BY r.seat_number ASC");

if ($reservations->num_rows == 0) {
    echo "<p class='text-gray-600'>No seats reserved yet.</p>";
} else {
    echo "<ul class='text-gray-100'>";
    while ($r = $reservations->fetch_assoc()) {
        echo "<li>Seat {$r['seat_number']} → {$r['name']}</li>";
    }
    echo "</ul>";
}
?>
