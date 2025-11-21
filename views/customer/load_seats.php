<?php
require '../../db.php';
$busId = $_GET['bus_id'];

// Example seats (you can generate dynamically)
$seatNumbers = range(1, 40);

$res = $mysqli->query("SELECT seat_number FROM reservations WHERE bus_id=$busId");
$booked = [];
while ($row = $res->fetch_assoc()) $booked[] = $row['seat_number'];

$seatMap = [];

foreach ($seatNumbers as $num) {
    $seatMap[] = [
        "number" => $num,
        "booked" => in_array($num, $booked)
    ];
}

echo json_encode($seatMap);
?>
