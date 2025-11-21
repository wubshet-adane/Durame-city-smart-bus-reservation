<?php
require "../../db.php";

$id = $_POST['id'];
$status = $_POST['status'];

$stmt = $mysqli->prepare("UPDATE maintenance SET status=? WHERE id=?");
$stmt->bind_param("si", $status, $id);
$stmt->execute();

echo "success";
?>
