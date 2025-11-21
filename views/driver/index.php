<?php
session_start();
require '../../db.php';
// Example: Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}
// Example: Check if user is logged in
if (!empty($_SESSION['role_id']) && $_SESSION['role_id'] != 5) {
    header('Location: ../auth/login.php');
    exit;
}


$driver_id = $_SESSION['user_id'];

// Fetch user info
$userQuery = $mysqli->prepare("SELECT * FROM users WHERE id = ?");
$userQuery->bind_param("i", $driver_id);
$userQuery->execute();
$userResult = $userQuery->get_result();
$user = $userResult->fetch_assoc();

// Fetch driver info
$driver = $mysqli->query("SELECT * FROM drivers WHERE id=$driver_id")->fetch_assoc();

// Fetch assigned buses
$buses = $mysqli->query("SELECT * FROM buses WHERE driver_id=$driver_id ORDER BY travel_date ASC");

// Notifications
$notifications = $mysqli->query("SELECT * FROM notifications WHERE user_id=$driver_id ORDER BY created_at DESC LIMIT 5");

// Trips summary
$total_trips = $buses->num_rows;
$upcoming_trips = $mysqli->query("SELECT COUNT(*) AS c FROM buses WHERE driver_id=$driver_id AND travel_date >= CURDATE()")->fetch_assoc()['c'];
$completed_trips = $mysqli->query("SELECT COUNT(*) AS c FROM buses WHERE driver_id=$driver_id AND travel_date < CURDATE()")->fetch_assoc()['c'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Driver Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Glassmorphism Cards */
        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Neon Glow Buttons */
        .neon-btn {
            position: relative;
            transition: all 0.3s ease-in-out;
        }

        .neon-btn::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, #00f5ff, #00ff9d, #ff00ff, #ffcc00);
            filter: blur(15px);
            opacity: 0.5;
            z-index: -1;
            border-radius: 12px;
            transition: all 0.3s ease-in-out;
        }

        .neon-btn:hover::before {
            filter: blur(25px);
            opacity: 1;
        }

        /* Float Animations for Cards */
        @keyframes floatCard {
            0% {
                transform: translateY(5px);
            }

            50% {
                transform: translateY(-5px);
            }

            100% {
                transform: translateY(5px);
            }
        }

        .animate-float {
            animation: floatCard 5s ease-in-out infinite;
        }
    </style>
</head>

<body class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 text-white min-h-screen p-6">

    <!-- HEADER -->
    <header class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-extrabold text-cyan-400">Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?></h1>
        <a href="../logout.php"
            class="px-4 py-2 bg-red-600 hover:bg-red-700 rounded-lg font-semibold neon-btn transition">Logout</a>
    </header>

    <div class="flex min-h-screen gap-6">

        <!-- SIDEBAR -->
        <aside class="w-64 glass-card p-6 rounded-2xl shadow-lg flex flex-col items-center">
            <img src="<?= $driver['photo'] ?? '../../public/assets/default.png' ?>" alt="Avatar"
                class="w-28 h-28 rounded-full mb-4 border-2 border-cyan-400">
            <h2 class="text-xl font-bold"><?= htmlspecialchars($user['name']); ?></h2>
            <p class="text-gray-300 text-sm mb-6"><?= htmlspecialchars($user['email']); ?></p>

            <nav class="w-full">
                <a href="#" class="block py-2 px-4 rounded-lg hover:bg-cyan-500/30 transition mb-2">Dashboard</a>
                <a href="notification.php" class="block py-2 px-4 rounded-lg hover:bg-blue-500/30 transition mb-2">Notifications</a>
                <a href="profile.php" class="block py-2 px-4 rounded-lg hover:bg-green-500/30 transition">Profile</a>
            </nav>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="flex-1 flex flex-col gap-6">

            <!-- STAT CARDS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="glass-card p-5 rounded-2xl shadow-lg transition hover:scale-105 animate-float">
                    <h2 class="text-gray-400 text-sm mb-2">Total Trips</h2>
                    <p class="text-3xl font-bold text-blue-400"><?= $total_trips ?></p>
                </div>

                <div class="glass-card p-5 rounded-2xl shadow-lg transition hover:scale-105 animate-float">
                    <h2 class="text-gray-400 text-sm mb-2">Upcoming Trips</h2>
                    <p class="text-3xl font-bold text-yellow-400"><?= $upcoming_trips ?></p>
                </div>

                <div class="glass-card p-5 rounded-2xl shadow-lg transition hover:scale-105 animate-float">
                    <h2 class="text-gray-400 text-sm mb-2">Completed Trips</h2>
                    <p class="text-3xl font-bold text-green-400"><?= $completed_trips ?></p>
                </div>
            </div>

            <!-- GRID CONTENT -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- COLUMN 1: Assigned Buses -->
                <div class="lg:col-span-2 glass-card p-6 rounded-2xl shadow-lg">
                    <h2 class="text-2xl font-bold mb-4 text-cyan-300">Assigned Buses & Trips</h2>

                    <?php if ($buses->num_rows == 0): ?>
                    <p class="text-gray-400">No assigned trips yet.</p>
                    <?php else: ?>
                    <div class="space-y-4">
                        <?php while ($bus = $buses->fetch_assoc()): ?>
                        <div class="border border-gray-700 p-4 rounded-xl hover:border-cyan-400 transition shadow hover:shadow-cyan-500/50">
                            <div class="flex justify-between items-center mb-2">
                                <h3 class="font-bold text-lg text-cyan-400">
                                    <?= htmlspecialchars($bus['name']) ?> (<?= htmlspecialchars($bus['plate_number']) ?>)
                                </h3>
                                <span
                                    class="bg-cyan-900/30 text-cyan-300 px-3 py-1 rounded-full text-sm font-semibold">
                                    <?= htmlspecialchars($bus['travel_date']) ?>
                                </span>
                            </div>
                            <p class="text-gray-300 text-sm">Route: <?= htmlspecialchars($bus['from_location']) ?> →
                                <?= htmlspecialchars($bus['to_location']) ?></p>
                            <p class="text-gray-400 text-sm">Departure: <?= $bus['departure_time'] ?> | Arrival:
                                <?= $bus['arrival_time'] ?></p>
                            <p class="text-gray-400 text-sm">Total Seats: <?= $bus['total_seats'] ?> | Price:
                                <?= $bus['price'] ?> Birr</p>

                            <div class="flex gap-2 mt-3">
                                <button onclick="loadSeats(<?= $bus['id'] ?>)"
                                    class="px-3 py-1 bg-green-600 hover:bg-green-700 rounded font-semibold transition neon-btn">
                                    View Reservations
                                </button>
                                <a href="bus_details.php?bus_id=<?= $bus['id'] ?>"
                                    class="px-3 py-1 bg-blue-600 hover:bg-blue-700 rounded font-semibold transition neon-btn">Bus
                                    Detail</a>
                            </div>
                            <div id="seats-<?= $bus['id'] ?>" class="mt-2 hidden p-2 border rounded bg-gray-900/50"></div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- COLUMN 2: Notifications -->
                <div class="glass-card p-6 rounded-2xl shadow-lg">
                    <h2 class="text-2xl font-bold mb-4 text-yellow-300">Notifications</h2>

                    <div id="notifArea">
                        <?php while ($n = $notifications->fetch_assoc()): ?>
                        <div class="p-3 mb-3 border-l-4 border-yellow-400 bg-yellow-900/20 rounded">
                            <p class="font-semibold text-yellow-300"><?= htmlspecialchars($n['title']) ?></p>
                            <p class="text-gray-300 text-sm"><?= htmlspecialchars($n['message']) ?></p>
                            <p class="text-xs text-gray-500"><?= $n['created_at'] ?></p>
                        </div>
                        <?php endwhile; ?>
                    </div>

                    <button onclick="refreshNotifications()"
                        class="mt-3 w-full px-4 py-2 bg-yellow-500 hover:bg-yellow-600 rounded-lg font-semibold neon-btn transition">
                        Refresh Notifications
                    </button>
                </div>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script>
        function loadSeats(busId) {
            const container = document.getElementById('seats-' + busId);
            container.classList.toggle('hidden');
            if (!container.innerHTML) {
                fetch('load_bus_seats.php?bus_id=' + busId)
                    .then(res => res.text())
                    .then(data => container.innerHTML = data);
            }
        }

        function refreshNotifications() {
            fetch("load_notifications.php")
                .then(res => res.text())
                .then(data => document.getElementById("notifArea").innerHTML = data);
        }
    </script>

</body>

</html>
