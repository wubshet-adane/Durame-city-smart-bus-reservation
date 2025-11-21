<?php
// register.php
require __DIR__ . '/../../db.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'customer'; // allow selecting role only for admin; initial register as customer

    if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
        $errors[] = "Invalid input or password too short.";
    } else {
        // get role id
        $stmt = $mysqli->prepare("SELECT id FROM roles WHERE name = ?");
        $stmt->bind_param('s',$role);
        $stmt->execute(); $res = $stmt->get_result();
        $roleRow = $res->fetch_assoc();
        $role_id = $roleRow['id'] ?? 4; // default to customer

        // $passHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $mysqli->prepare("INSERT INTO users (name,email,password,phone,role_id) VALUES (?,?,?,?,?)");
        $phone = $_POST['phone'] ?? null;
        $stmt->bind_param('ssssi', $name, $email, $password, $phone, $role_id);
        if ($stmt->execute()) {
            $stmt = $mysqli->prepare("SELECT id, name, password, role_id FROM users WHERE email = ?");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($row = $res->fetch_assoc()) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['name'];
                $_SESSION['role_id'] = $row['role_id'];
                // redirect based on role
                header('Location: ../dashboard.php ');
                exit;
            } else {
                $errors[] = "Registration failed: " . $stmt->error;
            }
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <title>Register</title>
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
@keyframes floatUp {
  0% { transform: translateY(20px); opacity: 0; }
  100% { transform: translateY(0); opacity: 1; }
}

@keyframes neonBorder {
  0% { box-shadow: 0 0 8px #00ff9d, 0 0 20px #00ff9d; }
  50% { box-shadow: 0 0 18px #00ffc8, 0 0 40px #00ffc8; }
  100% { box-shadow: 0 0 8px #00ff9d, 0 0 20px #00ff9d; }
}
</style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 
    bg-cover bg-center bg-no-repeat relative animate-bgZoom
    before:absolute before:inset-0 before:bg-black/50 before:z-0"
    style="background-image: url('../../public/assets/bus1.jpg');">
    <div class="w-full max-w-md mx-auto p-6 rounded-2xl relative overflow-hidden"
     style="
        backdrop-filter: blur(16px) saturate(180%);
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.3);
        box-shadow: 0 0 40px rgba(0,123,255,0.3);
        animation: floatUp 1.2s ease-out;
     ">

    <!-- Glowing animated border -->
    <div class="absolute inset-0 rounded-2xl pointer-events-none"
         style="animation: neonBorder 2s infinite ease-in-out;"></div>

    <!-- Header -->
    <div class="text-center mb-6 relative z-10">
        <h1 class="text-3xl font-extrabold text-green-300 drop-shadow-lg tracking-wide">
            Durame Bus Station
        </h1>
        <p class="text-gray-200 text-sm mt-1 tracking-widest">
            Smart Bus Reservation System
        </p>
    </div>

    <h2 class="text-xl font-bold mb-4 text-white tracking-wide relative z-10">
        Register
    </h2>

    <?php if($errors): foreach($errors as $e): ?>
        <div class="bg-red-200/20 border border-red-400 text-red-600 p-3 rounded mb-4 text-sm backdrop-blur-md relative z-10">
            <?= htmlspecialchars($e) ?>
        </div>
    <?php endforeach; endif; ?>

    <!-- Form -->
    <form id="registerForm" method="post" novalidate class="space-y-4 relative z-10">
      
      <!-- Full Name -->
      <div>
        <input id="name" name="name" type="text"
          placeholder="Full name"
          class="block w-full p-3 rounded-lg bg-white/20 text-white placeholder-gray-300 border border-gray-400 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 transition-all duration-300">
        <small class="text-red-500 hidden" id="nameError">Full name is required</small>
      </div>

      <!-- Email -->
      <div>
        <input id="email" name="email" type="email"
          placeholder="Email address"
          class="block w-full p-3 rounded-lg bg-white/20 text-white placeholder-gray-300 border border-gray-400 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 transition-all duration-300">
        <small class="text-red-500 hidden" id="emailError">Enter a valid email</small>
      </div>

      <!-- Phone -->
      <div>
        <input id="phone" name="phone" type="text"
          placeholder="Phone number (optional)"
          class="block w-full p-3 rounded-lg bg-white/20 text-white placeholder-gray-300 border border-gray-400 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 transition-all duration-300">
        <small class="text-red-500 hidden" id="phoneError">Phone number must be 10–15 digits</small>
      </div>

      <!-- Password -->
      <div>
        <input id="password" name="password" type="password"
          placeholder="Password (min 6 characters)"
          class="block w-full p-3 rounded-lg bg-white/20 text-white placeholder-gray-300 border border-gray-400 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 transition-all duration-300">
        <small class="text-red-500 hidden" id="passwordError">
          Password must be at least 6 characters and contain letters
        </small>      
      </div>

      <button class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 rounded-xl shadow-lg hover:shadow-green-400/50 transition-all duration-300 tracking-wide">
        Register
      </button>

      <p class="text-center mt-4 text-sm text-gray-300 relative z-10">
          Already have an account? 
          <a href="login.php" class="text-green-300 hover:text-green-400 font-semibold underline">
              Login
          </a>
      </p>
  </form>
</div>



  <script>
  const form = document.getElementById("registerForm");

  const fields = {
    name: {
      input: document.getElementById("name"),
      error: document.getElementById("nameError"),
      validate: (value) => value.trim().length > 0
    },
    email: {
      input: document.getElementById("email"),
      error: document.getElementById("emailError"),
      validate: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)
    },
    phone: {
      input: document.getElementById("phone"),
      error: document.getElementById("phoneError"),
      validate: (value) => value === "" || /^[0-9]{10,15}$/.test(value)
    },
    password: {
      input: document.getElementById("password"),
      error: document.getElementById("passwordError"),
      validate: (value) => /^(?=.*[A-Za-z]).{6,}$/.test(value)
    }
  };

  // Real-time validation
  Object.values(fields).forEach(field => {
    field.input.addEventListener("input", () => validateField(field));
  });

  function validateField(field) {
    const value = field.input.value;

    if (!field.validate(value)) {
      field.error.classList.remove("hidden");
      field.input.classList.add("border-red-500");
      field.input.classList.remove("border-green-500");
      return false;
    } else {
      field.error.classList.add("hidden");
      field.input.classList.remove("border-red-500");
      field.input.classList.add("border-green-500");
      return true;
    }
  }

  // Form submit validation
  form.addEventListener("submit", (e) => {
    let allValid = true;

    Object.values(fields).forEach(field => {
      if (!validateField(field)) {
        allValid = false;
      }
    });

    if (!allValid) {
      e.preventDefault();
      //alert("Please fix the errors before submitting.");
    }
  });
</script>

</body>
</html>
