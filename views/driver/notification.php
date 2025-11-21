<?php
session_start();
$mechanic_id = $_SESSION['user_id'];

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
<style>
/* Neon-glow cards */
.notification-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border-radius: 1rem;
    border-left-width: 4px;
    transition: all 0.3s ease-in-out;
}
.notification-card.unread {
    border-color: #3b82f6; /* Blue */
    background: rgba(59, 130, 246, 0.1);
}
.notification-card.read {
    border-color: rgba(255,255,255,0.1);
    background: rgba(255,255,255,0.05);
}
.notification-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0,255,255,0.2);
}

/* Neon mark read button */
.mark-read {
    position: relative;
    z-index: 0;
    transition: all 0.3s ease-in-out;
}
.mark-read::before {
    content: '';
    position: absolute;
    top: -2px; left: -2px; right: -2px; bottom: -2px;
    background: linear-gradient(45deg,#00f5ff,#00ff9d,#ff00ff,#ffd700);
    filter: blur(10px);
    opacity: 0.5;
    z-index: -1;
    border-radius: 0.375rem;
}
.mark-read:hover::before {
    filter: blur(20px);
    opacity: 1;
    animation: glowPulse 1.5s infinite alternate;
}
@keyframes glowPulse {
    0% { opacity: 0.5; transform: scale(1); }
    100% { opacity: 1; transform: scale(1.05); }
}
</style>
</head>
<body class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 min-h-screen text-white">

<!-- HEADER -->
<nav class="bg-gray-900/70 backdrop-blur-md text-white p-4 shadow-lg">
    <div class="max-w-6xl mx-auto flex justify-between items-center">
        <h1 class="text-2xl font-bold neon-blue">Mechanic Notifications</h1>
        <a href="index.php" class="bg-green-600 px-4 py-2 rounded-lg font-semibold hover:bg-green-700 transition">Back</a>
    </div>
</nav>

<!-- NOTIFICATIONS LIST -->
<div class="max-w-4xl mx-auto mt-8 space-y-4">

    <?php if (empty($notifications)): ?>
        <p class="text-gray-400 text-center py-6 text-lg">No notifications found.</p>
    <?php endif; ?>

    <div id="notification-list" class="space-y-4">
        <?php foreach ($notifications as $note): ?>
            <div id="notification-<?= $note['id'] ?>" 
                 class="notification-card p-4 shadow-sm <?= $note['is_read'] ? 'read' : 'unread' ?>">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-lg font-semibold neon-yellow"><?= htmlspecialchars($note['title']) ?></h3>
                        <p class="text-gray-300 mt-1"><?= htmlspecialchars($note['message']) ?></p>
                        <p class="text-sm text-gray-400 mt-2">
                            <?= date("M d, Y h:i A", strtotime($note['created_at'])) ?>
                        </p>
                    </div>

                    <?php if (!$note['is_read']): ?>
                        <button 
                            class="mark-read bg-blue-600 text-white px-3 py-1 rounded text-sm font-semibold"
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
                box.classList.remove("unread");
                box.classList.add("read");
                this.remove();
            }
        });
    });
});
</script>
</body>
</html>
