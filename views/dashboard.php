<?php
session_start();
if (!isset($_SESSION['user_id'])) { 
  header('Location: auth/login.php'); 
  exit; 
}
$role_id = $_SESSION['role_id'];
?>
<!doctype html><html>
<head><script src="https://cdn.tailwindcss.com"></script></head>
<body class="p-6">
  <div class="flex justify-between items-center">
    <div>Welcome, <?=htmlspecialchars($_SESSION['user_name'])?></div>
    <div><a href="?page=logout" class="text-sm">Logout</a></div>
  </div>

  <?php if($role_id == 1): // admin 
    header('location: admin')
    ?>
    
  <?php elseif($role_id == 2): // manager 
    header('location: manager')
    ?>
   
  <?php elseif($role_id == 3): 
    // mechanic 
    header('location: mechanic')
    ?>
    
  <?php elseif($role_id == 5): // driver
    header('location: driver') ?>
     ?>
    
  <?php else: // customer
    header('location: customer') ?>
  <?php endif; ?>
</body>
</html>
