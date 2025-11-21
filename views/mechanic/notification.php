<?php
session_start();
$mechanic_id = $_SESSION['user_id']; // mechanic ID from login

require "../../db.php";

// Fetch all notifications for this mechanic
$stmt = $mysqli->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $mechanic_id);
$stmt->execute();
$result = $stmt->get_result();
$notifications = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mechanic Notifications</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

    <!-- HEADER -->
    <nav class="bg-blue-900 text-white p-4">
        <div class="max-w-6xl mx-auto flex justify-between">
            <h1 class="text-xl font-bold">Notifications</h1>
            <a href="index.php" class="bg-green-600 px-3 py-1 rounded">Back</a>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto mt-8 bg-white rounded-xl shadow p-6 space-y-4">

        <h2 class="text-2xl font-bold mb-4">Your Notifications</h2>

        <?php if (empty($notifications)): ?>
            <p class="text-gray-500 text-center py-6">No notifications found.</p>
        <?php endif; ?>

        <div id="notification-list" class="space-y-4">
            <?php foreach ($notifications as $note): ?>
                <div class="p-4 rounded-xl border shadow-sm transition 
                    <?= $note['is_read'] ? 'bg-gray-100' : 'bg-blue-50 border-blue-300' ?>"
                    id="notification-<?= $note['id'] ?>"
                >
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-semibold"><?= htmlspecialchars($note['title']) ?></h3>
                            <p class="text-gray-700 mt-1"><?= htmlspecialchars($note['message']) ?></p>
                            <p class="text-sm text-gray-500 mt-2">
                                <?= date("M d, Y h:i A", strtotime($note['created_at'])) ?>
                            </p>
                        </div>

                        <?php if (!$note['is_read']): ?>
                            <button 
                                class="mark-read bg-blue-600 text-white px-3 py-1 rounded text-sm"
                                data-id="<?= $note['id'] ?>"
                            >
                                Mark as Read
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>

<script>
// MARK AS READ – AJAX
document.querySelectorAll(".mark-read").forEach(btn => {
    btn.addEventListener("click", function () {
        let id = this.dataset.id;

        fetch("notification_mark_read.php", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: "id=" + id
        })
        .then(res => res.text())
        .then(response => {
            if (response === "success") {
                let box = document.getElementById("notification-" + id);
                box.classList.remove("bg-blue-50", "border-blue-300");
                box.classList.add("bg-gray-100");
                this.remove(); // remove button
            }
        });
    });
});
</script>

</body>
</html>
