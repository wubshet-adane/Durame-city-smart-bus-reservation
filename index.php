<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Durame Bus Station</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
/* Global Smooth Scroll & Animation */
html { scroll-behavior: smooth; }

/* Floating Hero Animation */
@keyframes floatHero {
  0% { transform: translateY(20px); opacity: 0; }
  50% { transform: translateY(0px); opacity: 1; }
  100% { transform: translateY(20px); opacity: 0; }
}

/* Neon Button Glow */
.neon-btn {
  position: relative;
  z-index: 0;
  transition: all 0.3s ease-in-out;
}
.neon-btn::before {
  content: '';
  position: absolute;
  top: -2px; left: -2px; right: -2px; bottom: -2px;
  background: linear-gradient(45deg,#00eeee,#000,#ffcce0);
  filter: blur(10px);
  opacity: 0.6;
  z-index: -1;
  transition: all 0.3s ease-in-out;
  border-radius: 12px;
}
.neon-btn:hover::before {
  filter: blur(20px);
  opacity: 1;
  animation: glowPulse 1.5s infinite alternate;
}
@keyframes glowPulse {
  0% { opacity: 0.5; transform: scale(1); }
  100% { opacity: 1; transform: scale(1.05); }
}

/* Particle Background */
.particles {
  position: absolute;
  width: 100%;
  height: 100%;
  background: radial-gradient(circle, rgba(0,255,255,0.05), transparent 70%);
  overflow: hidden;
  pointer-events: none;
  z-index: 0;
}

/* Floating Features Cards Animation */
@keyframes floatCard {
  0% { transform: translateY(10px); }
  50% { transform: translateY(-10px); }
  100% { transform: translateY(10px); }
}
</style>
</head>

<body class="bg-gray-900 text-white relative overflow-x-hidden">

<!-- HERO SECTION -->
<section class="relative w-full h-screen flex items-center justify-center text-center overflow-hidden">
    
    <!-- Particle Background -->
    <div class="particles"></div>
    
    <!-- Background Image -->
    <img src="public/assets/images.jpeg" 
         class="absolute inset-0 w-full h-full object-cover brightness-90"
         alt="Bus Background">

    <!-- Overlay -->
    <div class="absolute inset-0 bg-black/50 backdrop-blur-d"></div>

    <!-- Hero Content -->
    <div class="relative z-10 flex flex-col items-center justify-center px-4">
        <h1 class="text-6xl md:text-7xl font-extrabold mb-4 animate-floatHero" style="animation: floatHero 4s ease-in-out infinite;">
            Durame Bus Station
        </h1>
        <h2 class="text-2xl md:text-3xl font-light mb-8 max-w-3xl tracking-wide opacity-90">
            Fast, Secure & Smart Bus Reservation System – Durame City
        </h2>
        <p class="max-w-3xl mb-8 text-lg md:text-xl opacity-90 leading-relaxed">
            Reserve your seat instantly, check available buses, track your trips, and enjoy a modern transport experience.
        </p>

        <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-6">
            <a href="views/auth/login.php" 
               class="neon-btn px-8 py-4 bg-blue-600 rounded-xl font-semibold text-lg shadow-lg hover:bg-blue-700 transition-all">
               Login
            </a>
            <a href="views/auth/register.php" 
               class="neon-btn px-8 py-4 bg-green-600 rounded-xl font-semibold text-lg shadow-lg hover:bg-green-700 transition-all">
               Create Customer Account
            </a>
        </div>
    </div>
</section>

<!-- FEATURES SECTION -->
<section class="py-20 bg-gray-900 relative z-10">
    <div class="max-w-6xl mx-auto text-center px-4">
        <h2 class="text-4xl md:text-5xl font-bold mb-14 text-cyan-300">Why Choose Us?</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            <div class="p-8 bg-gray-800 rounded-3xl shadow-2xl transform transition-all hover:scale-105 hover:shadow-cyan-500/50 animate-floatCard" style="animation: floatCard 5s ease-in-out infinite;">
                <img src="https://img.icons8.com/fluency/96/bus.png" class="mx-auto mb-6" />
                <h3 class="text-xl font-semibold mb-2 text-cyan-400">Available Buses</h3>
                <p class="text-gray-300">Check all buses and available seats instantly.</p>
            </div>

            <div class="p-8 bg-gray-800 rounded-3xl shadow-2xl transform transition-all hover:scale-105 hover:shadow-green-400/50 animate-floatCard" style="animation: floatCard 4s ease-in-out infinite;">
                <img src="https://img.icons8.com/fluency/96/ticket.png" class="mx-auto mb-6" />
                <h3 class="text-xl font-semibold mb-2 text-green-400">Fast Reservation</h3>
                <p class="text-gray-300">Reserve seats in seconds from any device.</p>
            </div>

            <div class="p-8 bg-gray-800 rounded-3xl shadow-2xl transform transition-all hover:scale-105 hover:shadow-purple-400/50 animate-floatCard" style="animation: floatCard 6s ease-in-out infinite;">
                <img src="https://img.icons8.com/fluency/96/security-checked.png" class="mx-auto mb-6" />
                <h3 class="text-xl font-semibold mb-2 text-purple-400">Secure System</h3>
                <p class="text-gray-300">Smart roles: Admin, Manager, Drivers, Customers, Mechanics.</p>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="bg-gray-900 text-gray-400 py-6 text-center relative z-10 border-t border-gray-700">
    <p>© <?php echo date('Y'); ?> Durame Bus Station • Smart Bus Reservation System</p>
</footer>

</body>
</html>
