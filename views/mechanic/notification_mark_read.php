<?php
require "../../db.php";

$id = $_POST['id'] ?? null;

if (!$id) {
    echo "error";
    exit;
}

$stmt = $mysqli->prepare("UPDATE notifications SET is_read = 1, updated_at = NOW() WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

echo "success";
?>
