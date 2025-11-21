<?php
session_start();
require "../db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];

// Validate password if updating
if (!empty($new_password) && $new_password !== $confirm_password) {
    echo "Passwords do not match!";
    exit;
}

try {
    // Update basic info
    if (empty($new_password)) {
        $stmt = $mysqli->prepare("UPDATE users SET name=?, email=?, phone=?, updated_at=NOW() WHERE id=?");
        $stmt->bind_param("sssi", $name, $email, $phone, $user_id);
    } else {
        $stmt = $mysqli->prepare("UPDATE users SET name=?, email=?, phone=?, password=?, updated_at=NOW() WHERE id=?");
        $stmt->bind_param("ssssi", $name, $email, $phone, $new_password, $user_id);
    }

    $stmt->execute();
    $stmt->close();

    header("Location: " . $_SERVER['HTTP_REFERER'] . "?updated=1");
    exit;

} catch (Exception $e) {
    echo "Error updating profile: " . $e->getMessage();
}
