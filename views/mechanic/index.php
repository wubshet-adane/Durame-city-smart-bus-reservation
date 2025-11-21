<?php
session_start();
require "../../db.php";

$mechanic_id = $_SESSION['user_id'];

// Fetch maintenance tasks
$tasksStmt = $mysqli->prepare("
    SELECT m.*, b.plate_number, b.name 
    FROM maintenance m 
    JOIN buses b ON b.id = m.bus_id
    WHERE m.mechanic_id = ?
    ORDER BY m.created_at DESC
");
$tasksStmt->bind_param("i", $mechanic_id);
$tasksStmt->execute();
$tasks = $tasksStmt->get_result();

// Fetch mechanic info
$userStmt = $mysqli->prepare("SELECT * FROM users WHERE id=?");
$userStmt->bind_param("i", $mechanic_id);
$userStmt->execute();
$mechanic = $userStmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mechanic Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

    <!-- NAV -->
    <nav class="bg-blue-900 text-white p-4 shadow-md">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">Mechanic Dashboard</h1>
            <div class="flex items-center gap-4">
                <span><?= htmlspecialchars($mechanic['name']) ?></span>
                <a href="../logout.php" class="px-3 py-1 bg-red-600 rounded">Logout</a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto p-6 grid grid-cols-1 lg:grid-cols-4 gap-6">

        <!-- PROFILE -->
        <div class="bg-white rounded-xl shadow p-6 col-span-1">
            <img src="<?= $mechanic['photo'] ?? '../../public/assets/default.png' ?>" 
                 class="w-28 h-28 rounded-full mx-auto border-4 border-blue-600">
            <h2 class="text-xl mt-3 font-bold text-center"><?= $mechanic['name'] ?></h2>
            <p class="text-center text-gray-600"><?= $mechanic['email'] ?></p>

            <hr class="my-4">

            <div class="text-sm text-gray-600">
                <p><strong>Role:</strong> Mechanic</p>
                <p><strong>Member Since:</strong> <?= date("M Y", strtotime($mechanic['created_at'])) ?></p>
            </div>
            <button onclick="window.location.href='notification.php'" class="mt-4 w-full py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Notification
            </button>
            <button onclick="window.location.href='profile.php'" class="mt-4 w-full py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Edit Profile
            </button>
        </div>

        <!-- MAIN -->
        <div class="col-span-1 lg:col-span-3 space-y-6">

            <!-- SUMMARY CARDS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <?php
                $countPending = 0;
                $countProgress = 0;
                $countCompleted = 0;
                foreach ($tasks as $t) {
                    if ($t['status'] == 'pending') $countPending++;
                    if ($t['status'] == 'in_progress') $countProgress++;
                    if ($t['status'] == 'done') $countCompleted++;
                }
                ?>

                <div class="bg-yellow-300 p-6 rounded-xl shadow text-center">
                    <h3 class="text-3xl font-bold"><?= $countPending ?></h3>
                    <p class="font-medium">Pending Tasks</p>
                </div>

                <div class="bg-blue-300 p-6 rounded-xl shadow text-center">
                    <h3 class="text-3xl font-bold"><?= $countProgress ?></h3>
                    <p class="font-medium">In Progress</p>
                </div>

                <div class="bg-green-300 p-6 rounded-xl shadow text-center">
                    <h3 class="text-3xl font-bold"><?= $countCompleted ?></h3>
                    <p class="font-medium">Completed</p>
                </div>
            </div>

            <!-- TASKS TABLE -->
            <div class="bg-white shadow rounded-xl p-6">
                <h2 class="text-2xl font-bold mb-4">Maintenance Tasks</h2>

                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-200 text-left">
                            <th class="p-3 border">Bus</th>
                            <th class="p-3 border">Issue</th>
                            <th class="p-3 border">Created</th>
                            <th class="p-3 border">Status</th>
                            <th class="p-3 border">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        $tasksStmt->execute();
                        $tasks = $tasksStmt->get_result();

                        while ($task = $tasks->fetch_assoc()): ?>
                            <tr class="border hover:bg-gray-50">
                                <td class="p-3 border">
                                    <strong><?= $task['plate_number'] ?></strong><br>
                                    <span class="text-sm text-gray-500"><?= $task['name'] ?></span>
                                </td>

                                <td class="p-3 border"><?= htmlspecialchars($task['description']) ?></td>

                                <td class="p-3 border text-gray-600">
                                    <?= date("M d, Y", strtotime($task['created_at'])) ?>
                                </td>

                                <td class="p-3 border font-bold 
                                    <?= $task['status']=='pending'?'text-red-600':'' ?>
                                    <?= $task['status']=='in_progress'?'text-yellow-600':'' ?>
                                    <?= $task['status']=='completed'?'text-green-600':'' ?>">
                                    <?= ucfirst($task['status']) ?>
                                </td>

                                <td class="p-3 border">
                                    <select class="statusSelect p-2 border rounded"
                                            data-id="<?= $task['id'] ?>">
                                        <option value="pending" <?= $task['status']=='pending'?'selected':'' ?>>Pending</option>
                                        <option value="in_progress" <?= $task['status']=='in_progress'?'selected':'' ?>>In Progress</option>
                                        <option value="done" <?= $task['status']=='done'?'selected':'' ?>>Completed</option>
                                    </select>
                                </td>
                            </tr>
                        <?php endwhile; ?>

                    </tbody>
                </table>
            </div>

        </div>
    </div>

<script>
document.querySelectorAll(".statusSelect").forEach(select => {
    select.addEventListener("change", function(){
        let id = this.dataset.id;
        let status = this.value;

        fetch("update_maintenance_status.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "id=" + id + "&status=" + status
        })
        .then(res => res.text())
        .then(data => {
            alert("Status updated successfully!");
            location.reload();
        });
    });
});
</script>

</body>
</html>
