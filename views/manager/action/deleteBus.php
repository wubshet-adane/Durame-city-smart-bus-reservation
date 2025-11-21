<?php
include '../../../db.php';
session_start();
echo $_GET['id'];
// Check if bus_id is provided
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Bus ID missing!";
    header('Location: ' . ($_SERVER['HTTP_REFERER']));
    exit;
}

$bus_id = intval($_GET['id']);

// Delete the bus safely using prepared statements
$stmt = $mysqli->prepare("DELETE FROM buses WHERE id = ?");
$stmt->bind_param("i", $bus_id);

if ($stmt->execute()) {
    $_SESSION['success'] = "Bus deleted successfully!";
    echo $_SESSION['success'];
} else {
    $_SESSION['error'] = "Failed to delete bus. Try again!";
}

$stmt->close();
$mysqli->close();

// Redirect back to buses page
header('Location: ' . ($_SERVER['HTTP_REFERER']));
exit;

?>