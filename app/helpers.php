<?php
function require_role($role_names = []) {
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: ?page=login'); exit;
    }
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT r.name FROM roles r JOIN users u ON r.id=u.role_id WHERE u.id=?");
    $stmt->bind_param('i', $_SESSION['user_id']); $stmt->execute();
    $r = $stmt->get_result()->fetch_assoc();
    $role = $r['name'] ?? null;
    if (!in_array($role, (array)$role_names)) {
        http_response_code(403);
        echo "Forbidden. You need role: " . implode(", ", $role_names);
        exit;
    }
    return $role;
}
