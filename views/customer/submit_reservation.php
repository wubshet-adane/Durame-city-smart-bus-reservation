<?php
session_start();
require '../../db.php';
$userId = $_SESSION['user_id'];
$bus_id = $_POST['bus_id'];
$seat = $_POST['seat'];

$check = $mysqli->query("SELECT id FROM reservations WHERE bus_id=$bus_id AND seat_number=$seat");

if ($check->num_rows > 0) {
    echo "Seat already booked";
    exit;
}

$stmt = $mysqli->prepare("
    INSERT INTO reservations (user_id, bus_id, seat_number, status)
    VALUES (?, ?, ?, 'Confirmed')
");
$stmt->bind_param("iii", $userId, $bus_id, $seat);

if ($stmt->execute()) echo "success";
else echo "Error saving reservation";
?>
