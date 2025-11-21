<?php
session_start();

// Ensure user logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

require '../../db.php';

// Fetch bus list
$buses = $mysqli->query("
    SELECT b.*, d.name AS driver_name 
    FROM buses b 
    LEFT JOIN drivers d ON b.driver_id = d.id
    ORDER BY b.departure_time ASC
");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserve Bus Seat</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen p-6">

    <h1 class="text-3xl font-bold mb-6 text-center">Bus Reservation</h1>

    <!-- Search Filter -->
    <div class="max-w-4xl mx-auto mb-6 bg-white p-4 rounded shadow">
        <h3 class="text-lg font-semibold mb-3">Filter Buses</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            <input 
                id="filterRoute" 
                type="text" 
                placeholder="Search route..." 
                class="border p-2 rounded w-full"
            />

            <input 
                id="filterDate" 
                type="date" 
                class="border p-2 rounded w-full"
            />

            <input 
                id="filterTime" 
                type="time" 
                class="border p-2 rounded w-full"
            />

        </div>
    </div>

    <!-- Bus List -->
    <div class="max-w-5xl mx-auto" id="busList">
        <?php while ($bus = $buses->fetch_assoc()): ?>
            <div 
                class="bg-white p-4 rounded shadow mb-4 bus-card transition hover:shadow-xl"
                data-route="<?php echo htmlspecialchars($bus['route']); ?>"
                data-date="<?php echo htmlspecialchars($bus['travel_date']); ?>"
                data-time="<?php echo htmlspecialchars($bus['departure_time']); ?>"
            >
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold"><?php echo htmlspecialchars($bus['name']); ?></h2>
                        <p class="text-gray-600">Route: <?php echo htmlspecialchars($bus['route']); ?></p>
                        <p class="text-gray-600">Driver: <?php echo $bus['driver_name']; ?></p>
                        <p class="text-gray-600">Departure: 
                            <span class="font-semibold text-blue-600">
                                <?php echo $bus['travel_date'] . " | " . $bus['departure_time']; ?>
                            </span>
                        </p>
                    </div>

                    <button 
                        onclick="openSeatModal(<?php echo $bus['id']; ?>, '<?php echo $bus['name']; ?>')"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                    >
                        Select Seat
                    </button>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Seat Selection Modal -->
    <div id="seatModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
        <div class="bg-white w-[450px] p-6 rounded shadow-lg relative">
            <!-- Close -->
            <button 
                onclick="closeSeatModal()" 
                class="absolute top-2 right-2 text-gray-700 hover:text-black">
                ✖
            </button>
            <h2 id="busTitle" class="text-xl font-bold mb-4"></h2>
            <!-- Seat Map -->
            <div id="seatMap" class="grid grid-cols-4 gap-3 mb-4"></div>
            <!-- Book Button -->
            <button 
                onclick="submitReservation()" 
                class="w-full bg-green-600 text-white p-2 rounded hover:bg-green-700"
            >
                Confirm Reservation
            </button>
            <!-- Response -->
            <p id="responseMsg" class="mt-3 text-center font-semibold"></p>

        </div>
    </div>

<script>
// Filters
document.getElementById("filterRoute").addEventListener("keyup", filterBuses);
document.getElementById("filterDate").addEventListener("change", filterBuses);
document.getElementById("filterTime").addEventListener("change", filterBuses);

function filterBuses() {
    let route = document.getElementById("filterRoute").value.toLowerCase();
    let date = document.getElementById("filterDate").value;
    let time = document.getElementById("filterTime").value;

    document.querySelectorAll(".bus-card").forEach(card => {
        let cardRoute = card.getAttribute("data-route").toLowerCase();
        let cardDate = card.getAttribute("data-date");
        let cardTime = card.getAttribute("data-time");

        let show = true;

        if (route && !cardRoute.includes(route)) show = false;
        if (date && cardDate !== date) show = false;
        if (time && cardTime !== time) show = false;

        card.style.display = show ? "block" : "none";
    });
}


// ---------------- Seat Modal ----------------

let selectedSeat = null;
let selectedBusId = null;

function openSeatModal(busId, busName) {
    selectedBusId = busId;
    selectedSeat = null;

    document.getElementById("seatModal").classList.remove("hidden");
    document.getElementById("busTitle").innerText = "Select Seat for " + busName;

    loadSeatMap(busId);
}

function closeSeatModal() {
    document.getElementById("seatModal").classList.add("hidden");
}

function loadSeatMap(busId) {
    fetch("load_seats.php?bus_id=" + busId)
        .then(res => res.json())
        .then(data => {
            let map = document.getElementById("seatMap");
            map.innerHTML = "";

            data.forEach(seat => {
                let btn = document.createElement("button");
                btn.innerText = seat.number;
                btn.className = "border rounded text-center";

                if (seat.booked) {
                    btn.classList.add("bg-red-600", "text-white", "cursor-not-allowed");
                } else {
                    btn.classList.add("bg-gray-200", "hover:bg-blue-300");
                    btn.onclick = () => {
                        selectedSeat = seat.number;

                        document.querySelectorAll("#seatMap button").forEach(b => {
                            b.classList.remove("bg-blue-600", "text-white");
                        });

                        btn.classList.add("bg-blue-600", "text-white");
                    };
                }

                map.appendChild(btn);
            });
        });
}

function submitReservation() {
    if (!selectedSeat) {
        alert("Please select a seat");
        return;
    }

    fetch("submit_reservation.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "bus_id=" + selectedBusId + "&seat=" + selectedSeat
    })
    .then(res => res.text())
    .then(data => {
        let msg = document.getElementById("responseMsg");

        if (data === "success") {
            msg.innerText = "Reservation Successful!";
            msg.className = "text-green-600";
        } else {
            msg.innerText = data;
            msg.className = "text-red-600";
        }
    });
}
</script>

</body>
</html>
