<?php
require __DIR__ . '/../../db.php';
session_start();

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    $stmt = $mysqli->prepare("SELECT id, name, password, role_id FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        if ($password === $row['password']) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['role_id'] = $row['role_id'];
            $_SESSION['email'] = $row['email'];
            // redirect based on role
            header('Location: ../dashboard.php ');
            exit;
        } else
            $err = "Invalid credentials.";
    } else
        $err = "Invalid credentials.";
}
?>
<!doctype html>
<html>

<head>
    <title>Durame Bus Station | Login</title>
    <script src="https://cdn.tailwindcss.com"></script>

   <style>
/* MAIN WRAPPER - futuristic glass effect */
.future-marquee {
  width: 100%;
  overflow: hidden;
  white-space: nowrap;
  position: relative;
  padding: 18px 0;
  background: linear-gradient(
    to right,
    rgba(0, 255, 255, 0.1),
    rgba(0, 0, 0, 0.6),
    rgba(0, 255, 255, 0.1)
  );
  border-top: 1px solid rgba(0,255,255,0.5);
  border-bottom: 1px solid rgba(0,255,255,0.5);
  backdrop-filter: blur(10px) saturate(180%);
  box-shadow: 0 0 30px rgba(0,255,255,0.3);
}

/* TEXT */
.future-marquee span {
  display: inline-block;
  padding-left: 120%;
  font-size: 2.4rem;
  font-weight: 900;
  letter-spacing: 6px;
  background: linear-gradient(90deg, #0ff, #7affea, #fff, #00eaff);
  -webkit-background-clip: text;
  color: transparent;
  animation: scroll 10s linear infinite, 
             neonPulse 2s ease-in-out infinite alternate,
             hologramShift 6s infinite;
  text-shadow: 
      0 0 20px rgba(0,255,255,0.8),
      0 0 40px rgba(0,255,255,0.6),
      0 0 70px rgba(0,255,255,0.4);
  position: relative;
}

/* 3D LAYER GLOW */
.future-marquee span::before {
  content: "The Future Smart City";
  position: absolute;
  left: 0;
  top: 0;
  color: rgba(0,255,255,0.2);
  filter: blur(4px);
  transform: translateZ(-20px);
  animation: hologramDepth 5s infinite;
}

/* LIGHT SHINE PASS */
.future-marquee span::after {
  content: "";
  position: absolute;
  top: 0;
  left: -10%;
  width: 12%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.7), transparent);
  filter: blur(10px);
  animation: shine 4s infinite;
}

/* smooth scroll */
@keyframes scroll {
  0% { transform: translateX(0); }
  100% { transform: translateX(-110%); }
}

/* neon pulsing */
@keyframes neonPulse {
  0% { text-shadow: 0 0 15px #0ff, 0 0 40px #0ff; }
  100% { text-shadow: 0 0 40px #0ff, 0 0 80px #0ff; }
}

/* hologram distortion */
@keyframes hologramShift {
  0% { filter: hue-rotate(0deg); }
  50% { filter: hue-rotate(30deg) blur(1px); }
  100% { filter: hue-rotate(0deg); }
}

/* floating 3D depth */
@keyframes hologramDepth {
  0% { transform: translate(2px, 0) scale(1); opacity: 0.3; }
  50% { transform: translate(-2px, -2px) scale(1.05); opacity: 0.5; }
  100% { transform: translate(2px, 0) scale(1); opacity: 0.3; }
}

/* light beam sweep */
@keyframes shine {
  0% { transform: translateX(-200%); }
  100% { transform: translateX(300%); }
}


/* Floating animation */
@keyframes floatUp {
  0% { transform: translateY(15px); opacity: 0; }
  100% { transform: translateY(0); opacity: 1; }
}

/* Neon border animation */
@keyframes neonBorder {
  0% { box-shadow: 0 0 4px #00ff9d, 0 0 10px #00ff9d;}
  50% { box-shadow: 0 0 12px #00ffc8, 0 0 22px #00ffc8;}
  100% { box-shadow: 0 0 4px #00ff9d, 0 0 10px #00ff9d;}
}

/* input glow */
.input-smart:focus {
  box-shadow: 0 0 12px #00ffa6 !important;
  border-color: #00ffa6 !important;
}
</style>

</head>

<body class="min-h-screen flex flex-wrap  items-center justify-center p-6 
    bg-cover bg-center bg-no-repeat relative animate-bgZoom
    before:absolute before:inset-0 before:bg-black/50 before:z-0"
    style="background-image: url('../../public/assets/bus1.jpg');">

    <div class="future-marquee">
        <span>Durame :) The Future Smart City</span>
    </div>
   <div class="w-full max-w-md mx-auto p-6 rounded-2xl relative overflow-hidden"
     style="
        animation: floatUp 1.2s ease-out;
        backdrop-filter: blur(14px);
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.3);
        box-shadow: 0 0 40px rgba(0,255,150,0.35);
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
        Login
    </h2>

    <?php if ($err): ?>
        <div class="bg-red-200/20 border border-red-300 text-red-600 p-3 rounded mb-4 text-sm backdrop-blur-md">
            <?= htmlspecialchars($err) ?>
        </div>
    <?php endif; ?>

    <!-- Form -->
    <form method="post" class="space-y-4 relative z-10">
        <input 
            name="email"
            type="email"
            placeholder="Email Address"
            class="input-smart block w-full p-3 rounded-lg bg-white/20 text-white placeholder-gray-300
                   border border-gray-400 focus:outline-none"
            required />

        <input 
            name="password"
            type="password"
            placeholder="Password"
            class="input-smart block w-full p-3 rounded-lg bg-white/20 text-white placeholder-gray-300
                   border border-gray-400 focus:outline-none"
            required />

        <button 
            class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 rounded-xl
                   shadow-lg hover:shadow-green-400/50 transition-all duration-300 tracking-wide">
            Login
        </button>
    </form>

    <p class="text-center mt-5 text-sm text-gray-300 relative z-10">
        Don't have an account? 
        <a href="register.php" class="text-green-300 hover:text-green-400 font-semibold underline">
            Register
        </a>
    </p>
</div>
</body>

</html>
