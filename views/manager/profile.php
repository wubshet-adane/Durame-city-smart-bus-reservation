<?php
session_start();
require "../../db.php";  // adjust path

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

// Fetch current user data
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
    <title>My Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen p-6">
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
                <a href="notification.php" class="block py-2 px-4 rounded hover:bg-gray-100">Notifications</a>
                <a href="profile.php" class="block py-2 px-4 rounded hover:bg-gray-100">Profile</a>
                <a href="../logout.php" class="block py-2 px-4 rounded hover:bg-gray-100 text-red-500">Logout</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6">
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow-lg">

        <h1 class="text-3xl font-bold text-center text-blue-700 mb-6">My Profile</h1>

        <!-- SUCCESS MESSAGE -->
        <?php if (isset($_GET['updated'])): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-center">
                Profile updated successfully!
            </div>
        <?php endif; ?>

        <form action="../update_profile.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- NAME -->
            <div>
                <label class="block font-semibold mb-1">Full Name</label>
                <input value="<?= htmlspecialchars($name) ?>" 
                    name="name" type="text"
                    class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-600"
                    required>
            </div>

            <!-- EMAIL -->
            <div>
                <label class="block font-semibold mb-1">Email</label>
                <input value="<?= htmlspecialchars($email) ?>" 
                    name="email" type="email"
                    class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-600"
                    required>
            </div>

            <!-- PHONE -->
            <div>
                <label class="block font-semibold mb-1">Phone Number</label>
                <input value="<?= htmlspecialchars($phone) ?>" 
                    name="phone" type="text"
                    class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-600">
            </div>

            <!-- CHANGE PASSWORD SECTION -->
            <div>
                <label class="block font-semibold mb-1">New Password (Optional)</label>
                <input name="new_password" type="password"
                    class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-600"
                    placeholder="Enter new password">
            </div>

            <!-- CONFIRM PASSWORD -->
            <div class="md:col-span-2">
                <label class="block font-semibold mb-1">Confirm New Password</label>
                <input name="confirm_password" type="password"
                    class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-600"
                    placeholder="Confirm new password">
            </div>

            <div class="md:col-span-2 text-center mt-4">
                <button class="px-6 py-2 bg-blue-700 text-white font-semibold rounded-lg hover:bg-blue-800">
                    Update Profile
                </button>
            </div>
        </form>
    </div>
    </main>
    </div>

</body>
</html>
