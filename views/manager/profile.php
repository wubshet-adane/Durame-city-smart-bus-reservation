<?php
session_start();
require "../../db.php";

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$userQuery = $mysqli->prepare("SELECT * FROM users WHERE id = ?");
$userQuery->bind_param("i", $user_id);
$userQuery->execute();
$userResult = $userQuery->get_result();
$user = $userResult->fetch_assoc();
$stmt = $mysqli->prepare("SELECT name, email, phone FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $phone);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile • Durame Bus Station</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
    body {
        background: linear-gradient(to right, #1c92d2, #f2fcfe);
        font-family: 'Inter', sans-serif;
    }
    .profile-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(15px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    .input-glow:focus {
        box-shadow: 0 0 10px rgba(59, 130, 246, 0.6);
        border-color: #3b82f6;
    }
</style>
</head>

<body class="min-h-screen flex items-center justify-center p-6">

<div class="w-full max-w-5xl profile-card rounded-3xl p-10 animate-fadeIn">
    
    <div class="flex flex-col md:flex-row gap-10">
        <!-- Sidebar -->
        <aside class="w-full md:w-64 flex-shrink-0 bg-white/80 rounded-2xl p-6 shadow-lg backdrop-blur-md">
            <div class="text-center mb-6">
                <img src="<?= $mechanic['photo'] ?? '../../public/assets/default.png' ?>" 
                     alt="Avatar" 
                     class="w-28 h-28 rounded-full mx-auto mb-3 border-4 border-blue-400 shadow-lg">
                <h2 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($user['name']) ?></h2>
                <p class="text-gray-500 text-sm"><?= htmlspecialchars($user['email']) ?></p>
            </div>
            <nav class="space-y-3">
                <a href="index.php" class="block py-2 px-4 rounded-lg hover:bg-blue-100 font-semibold transition">Dashboard</a>
                <a href="notification.php" class="block py-2 px-4 rounded-lg hover:bg-blue-100 font-semibold transition">Notifications</a>
                <a href="profile.php" class="block py-2 px-4 rounded-lg bg-blue-600 text-white font-semibold transition">Profile</a>
                <a href="../logout.php" class="block py-2 px-4 rounded-lg hover:bg-red-100 font-semibold text-red-600 transition">Logout</a>
            </nav>
        </aside>

        <!-- Main Profile Content -->
        <main class="flex-1 bg-white/90 backdrop-blur-md p-8 rounded-3xl shadow-lg">
            <h1 class="text-3xl md:text-4xl font-extrabold text-center text-blue-700 mb-8 tracking-wide">My Profile</h1>

            <!-- SUCCESS MESSAGE -->
            <?php if (isset($_GET['updated'])): ?>
                <div class="bg-green-100 text-green-700 p-3 rounded-xl mb-6 text-center shadow-md animate-pulse">
                    Profile updated successfully!
                </div>
            <?php endif; ?>

            <form action="../update_profile.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- NAME -->
                <div>
                    <label class="block font-semibold mb-1 text-gray-700">Full Name</label>
                    <input value="<?= htmlspecialchars($name) ?>" 
                        name="name" type="text"
                        class="w-full p-3 border rounded-xl input-glow focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <!-- EMAIL -->
                <div>
                    <label class="block font-semibold mb-1 text-gray-700">Email</label>
                    <input value="<?= htmlspecialchars($email) ?>" 
                        name="email" type="email"
                        class="w-full p-3 border rounded-xl input-glow focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <!-- PHONE -->
                <div>
                    <label class="block font-semibold mb-1 text-gray-700">Phone Number</label>
                    <input value="<?= htmlspecialchars($phone) ?>" 
                        name="phone" type="text"
                        class="w-full p-3 border rounded-xl input-glow focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- NEW PASSWORD -->
                <div>
                    <label class="block font-semibold mb-1 text-gray-700">New Password (Optional)</label>
                    <input name="new_password" type="password"
                        class="w-full p-3 border rounded-xl input-glow focus:ring-2 focus:ring-blue-500"
                        placeholder="Enter new password">
                </div>

                <!-- CONFIRM PASSWORD -->
                <div class="md:col-span-2">
                    <label class="block font-semibold mb-1 text-gray-700">Confirm New Password</label>
                    <input name="confirm_password" type="password"
                        class="w-full p-3 border rounded-xl input-glow focus:ring-2 focus:ring-blue-500"
                        placeholder="Confirm new password">
                </div>

                <div class="md:col-span-2 text-center mt-4">
                    <button class="px-8 py-3 bg-blue-600 text-white font-bold rounded-2xl hover:bg-blue-700 shadow-lg transition-all transform hover:-translate-y-1 hover:scale-105">
                        Update Profile
                    </button>
                </div>
            </form>
        </main>
    </div>
</div>

</body>
</html>
