<?php
session_start();
require '../../db.php'; // connection

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}
// Example: Check if user is logged in
if (!empty($_SESSION['role_id']) && $_SESSION['role_id'] != 2) {
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
// Fetch drivers
$drivers = $mysqli->query("SELECT * FROM drivers ORDER BY id DESC");

// Fetch buses with assigned driver
$buses = $mysqli->query("
    SELECT buses.*, drivers.name AS driver_name 
    FROM buses 
    LEFT JOIN drivers ON buses.driver_id = drivers.id 
    ORDER BY buses.id DESC
");

// Fetch maintenance requests
$maintenance = $mysqli->query("
    SELECT m.*, buses.plate_number, u.name AS mechanic_name
    FROM maintenance m
    JOIN buses ON m.bus_id = buses.id
    JOIN users u ON m.mechanic_id = u.id
    JOIN mechanics ON m.mechanic_id = mechanics.id
    ORDER BY m.id DESC
");
?>
<!DOCTYPE html>
<html class="bg-gray-100">

<head>
    <title>Manager Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="p-6">

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
                <a href="#" class="block py-2 px-4 rounded hover:bg-gray-100">Dashboard</a>
                <a href="reserve.php" class="block py-2 px-4 rounded hover:bg-gray-100">Reservations</a>
                <a href="notification.php" class="block py-2 px-4 rounded hover:bg-gray-100">Notifications</a>
                <a href="profile.php" class="block py-2 px-4 rounded hover:bg-gray-100">Profile</a>
                <a href="../logout.php" class="block py-2 px-4 rounded hover:bg-gray-100 text-red-500">Logout</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6">
            <!-- TOP CARDS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">

                <div class="bg-white p-5 rounded-xl shadow">
                    <h2 class="text-lg font-semibold">Total Drivers</h2>
                    <p class="text-3xl font-bold text-blue-600">
                        <?= mysqli_num_rows($drivers) ?>
                    </p>
                </div>

                <div class="bg-white p-5 rounded-xl shadow">
                    <h2 class="text-lg font-semibold">Total Buses</h2>
                    <p class="text-3xl font-bold text-green-600">
                        <?= mysqli_num_rows($buses) ?>
                    </p>
                </div>

                <div class="bg-white p-5 rounded-xl shadow">
                    <h2 class="text-lg font-semibold">Maintenance Requests</h2>
                    <p class="text-3xl font-bold text-red-600">
                        <?= mysqli_num_rows($maintenance) ?>
                    </p>
                </div>

            </div>

            <!-- ACTION BUTTONS -->
            <div class="flex gap-4 mb-6">
                <button onclick="openModal('addDriverModal')" class="bg-blue-600 text-white px-4 py-2 rounded">Add
                    Driver</button>
                <button onclick="openModal('addBusModal')" class="bg-green-600 text-white px-4 py-2 rounded">Add
                    Bus</button>
                <button onclick="openModal('assignDriverModal')"
                    class="bg-indigo-600 text-white px-4 py-2 rounded">Assign Driver to Bus</button>
            </div>

            <!-- DRIVERS TABLE -->
            <div class="bg-white p-6 rounded-xl shadow mb-8">
                <h2 class="text-2xl font-bold mb-4">Drivers</h2>
                <table class="w-full border">
                    <tr class="bg-gray-200">
                        <th class="p-2">ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Created</th>
                    </tr>
                    <?php while ($d = $drivers->fetch_assoc()): ?>
                        <tr class="border-b">
                            <td class="p-2"><?= $d['id'] ?></td>
                            <td><?= $d['name'] ?></td>
                            <td><?= $d['phone'] ?></td>
                            <td><?= $d['created_at'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>

            <!-- BUSES TABLE -->
            <div class="bg-white p-6 rounded-xl shadow mb-8">
                <h2 class="text-2xl font-bold mb-4">Buses</h2>
                <table class="w-full border">
                    <tr class="bg-gray-200">
                        <th class="p-2">ID</th>
                        <th>Plate</th>
                        <th>Model</th>
                        <th>Driver</th>
                        <th>Status</th>
                    </tr>
                    <?php
                    $buses->data_seek(0);
                    while ($b = $buses->fetch_assoc()): ?>
                        <tr class="border-b">
                            <td class="p-2"><?= $b['id'] ?></td>
                            <td><?= $b['plate_number'] ?></td>
                            <td><?= $b['name'] ?></td>
                            <td><?= $b['driver_name'] ?? "<span class='text-red-600'>Not Assigned</span>" ?></td>
                            <td><?= $b['status'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>

            <!-- MAINTENANCE TABLE -->
            <div class="bg-white p-6 rounded-xl shadow mb-8">
                <h2 class="text-2xl font-bold mb-4">Maintenance Requests</h2>
                <table class="w-full border">
                    <tr class="bg-gray-200">
                        <th class="p-2">ID</th>
                        <th>Bus</th>
                        <th>Mechanic</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Created</th>
                    </tr>
                    <?php while ($m = $maintenance->fetch_assoc()): ?>
                        <tr class="border-b">
                            <td class="p-2"><?= $m['id'] ?></td>
                            <td><?= $m['plate_number'] ?></td>
                            <td><?= $m['mechanic_name'] ?></td>
                            <td><?= $m['description'] ?></td>
                            <td>
                                <?php if ($m['status'] == "pending"): ?>
                                    <span class="text-orange-600 font-bold">Pending</span>
                                <?php else: ?>
                                    <span class="text-green-600 font-bold">Completed</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $m['created_at'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </main>
    </div>

    <!-- MODALS -->
    <?php include "modals/add_driver.php"; ?>
    <?php include "modals/add_bus.php"; ?>
    <?php include "modals/assign_driver.php"; ?>

    <!-- JS -->
    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove("hidden");
        }
        function closeModal(id) {
            document.getElementById(id).classList.add("hidden");
        }
    </script>

</body>

</html>