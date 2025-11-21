<?php
require __DIR__ . '/../../db.php';
session_start();

// Example: Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}
// Example: Check if user is logged in
if (!empty($_SESSION['role_id']) && $_SESSION['role_id'] != 4) {
    header('Location: ../auth/login.php');
    exit;
}


$user_id = $_SESSION['user_id'];

// Fetch user info
$userQuery = $mysqli->prepare("SELECT * FROM users WHERE id = ?");
$userQuery->bind_param("i", $user_id);
$userQuery->execute();
$userResult = $userQuery->get_result();
$user = $userResult->fetch_assoc();

// Fetch recent reservations
$resQuery = $mysqli->prepare("SELECT r.*, b.plate_number AS bus_name, b.route AS bus_route, b.travel_date 
                            FROM reservations r
                            JOIN buses b ON r.bus_id = b.id
                            WHERE r.user_id = ?
                            ORDER BY r.reserved_at DESC LIMIT 5");
$resQuery->bind_param("i", $user_id);
$resQuery->execute();
$resResult = $resQuery->get_result();

// Fetch notifications
$notifQuery = $mysqli->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$notifQuery->bind_param("i", $user_id);
$notifQuery->execute();
$notifResult = $notifQuery->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Customer Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg p-6">
            <div class="text-center mb-6">
                <img src="<?= $mechanic['photo'] ?? '../../public/assets/default.png' ?>" alt="Avatar"
                    class="w-24 h-24 rounded-full mx-auto mb-2">
                <h2 class="text-xl font-bold"><?php echo htmlspecialchars($user['name']); ?></h2>
                <p class="text-gray-500 text-sm"><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
            <nav>
                <a href="index.php" class="block py-2 px-4 rounded hover:bg-gray-100">Dashboard</a>
                <a href="reserve.php" class="block py-2 px-4 rounded hover:bg-gray-100">Reservations</a>
                <a href="notification.php" class="block py-2 px-4 rounded hover:bg-gray-100">Notifications</a>
                <a href="profile.php" class="block py-2 px-4 rounded hover:bg-gray-100">Profile</a>
                <a href="../logout.php" class="block py-2 px-4 rounded hover:bg-gray-100 text-red-500">Logout</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6">
            <h1 class="text-2xl font-bold mb-4">Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h1>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white shadow rounded p-4">
                    <h3 class="font-semibold text-gray-700">Total Reservations</h3>
                    <?php
                    $totalRes = $mysqli->query("SELECT COUNT(*) as count FROM reservations WHERE user_id=$user_id")->fetch_assoc()['count'];
                    ?>
                    <p class="text-2xl font-bold text-blue-500"><?php echo $totalRes; ?></p>
                </div>
                <div class="bg-white shadow rounded p-4">
                    <h3 class="font-semibold text-gray-700">Upcoming Trips</h3>
                    <?php
                    $upcomingRes = $mysqli->query("SELECT COUNT(*) as count FROM reservations WHERE user_id=$user_id AND reserved_at >= CURDATE()")->fetch_assoc()['count'];
                    ?>
                    <p class="text-2xl font-bold text-green-500"><?php echo $upcomingRes; ?></p>
                </div>
                <div class="bg-white shadow rounded p-4">
                    <h3 class="font-semibold text-gray-700">Notifications</h3>
                    <p class="text-2xl font-bold text-yellow-500"><?php echo $notifResult->num_rows; ?></p>
                </div>
            </div>

            <!-- Recent Reservations Table -->
            <div class="bg-white shadow rounded p-4 mb-6">
                <h2 class="font-semibold text-gray-700 mb-4">Recent Reservations</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="p-2 border">Bus</th>
                                <th class="p-2 border">Route</th>
                                <th class="p-2 border">Seat</th>
                                <th class="p-2 border">Travel Date</th>
                                <th class="p-2 border">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $resResult->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="p-2 border"><?php echo htmlspecialchars($row['bus_name']); ?></td>
                                    <td class="p-2 border"><?php echo htmlspecialchars($row['bus_route']); ?></td>
                                    <td class="p-2 border"><?php echo htmlspecialchars($row['seat_number']); ?></td>
                                    <td class="p-2 border"><?php echo htmlspecialchars($row['travel_date']); ?></td>
                                    <td class="p-2 border">
                                        <span
                                            class="<?php echo $row['status'] == 'Confirmed' ? 'text-green-500' : 'text-red-500'; ?>">
                                            <?php echo htmlspecialchars($row['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Notifications -->
            <div class="bg-white shadow rounded p-4">
                <h2 class="font-semibold text-gray-700 mb-4">Recent Notifications</h2>
                <ul>
                    <?php while ($notif = $notifResult->fetch_assoc()): ?>
                        <li class="p-2 border-b hover:bg-gray-50">
                            <span class="font-medium"><?php echo htmlspecialchars($notif['title']); ?>:</span>
                            <span><?php echo htmlspecialchars($notif['message']); ?></span>
                            <span
                                class="text-gray-400 text-xs float-right"><?php echo date('M d, Y', strtotime($notif['created_at'])); ?></span>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </main>
    </div>
</body>
</html>