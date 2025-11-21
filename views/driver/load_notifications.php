<?php
require '../../db.php';
session_start();

$user_id = $_SESSION['user_id'];

$data = $mysqli->query("SELECT * FROM notifications WHERE user_id=$user_id ORDER BY created_at DESC LIMIT 5");

while ($n = $data->fetch_assoc()) {
    echo '
    <div class="p-3 mb-3 border-l-4 border-blue-600 bg-blue-50 rounded">
        <p class="font-semibold">'.htmlspecialchars($n['title']).'</p>
        <p class="text-sm text-gray-600">'.htmlspecialchars($n['message']).'</p>
        <p class="text-xs text-gray-400">'.$n['created_at'].'</p>
    </div>';
}
