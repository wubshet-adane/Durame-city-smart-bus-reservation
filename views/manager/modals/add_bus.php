<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate required fields
    $required = ["name", "plate_number", "route", "from_location", "to_location", "travel_date", "departure_time", "arrival_time", "total_seats", "price", "status"];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $_SESSION['error'] = "Please fill all required fields.";
            exit;
        }
    }

    // Sanitize input
    $name = trim($_POST['name']);
    $plate_number = trim($_POST['plate_number']);
    $route = trim($_POST['route']);
    $from_location = trim($_POST['from_location']);
    $to_location = trim($_POST['to_location']);
    $travel_date = trim($_POST['travel_date']);
    $departure_time = trim($_POST['departure_time']);
    $arrival_time = trim($_POST['arrival_time']);
    $total_seats = intval($_POST['total_seats']);
    $price = floatval($_POST['price']);
    $status = trim($_POST['status']);

    // Prepare SQL
    $sql = "INSERT INTO buses (name, plate_number, route, from_location, to_location, travel_date, departure_time, arrival_time, total_seats, price, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $mysqli->prepare($sql);

    if (!$stmt) {
        $_SESSION['error'] = "DB Error: " . $mysqli->error;
        exit;
    }

    // CORRECT BIND PARAMETERS
    // s = string, i = integer, d = double
    $stmt->bind_param(
        "ssssssssiis",
        $name,
        $plate_number,
        $route,
        $from_location,
        $to_location,
        $travel_date,
        $departure_time,
        $arrival_time,
        $total_seats,
        $price,
        $status
    );

    if ($stmt->execute()) {
        $_SESSION['success'] = "Bus added successfully!";
    } else {
        $_SESSION['error'] = "Failed to add bus: " . $stmt->error;
    }

    $stmt->close();
    $mysqli->close();
}
?>














<div id="addBusModal" class="hidden fixed inset-0 bg-black/50 flex overflow-scroll items-center justify-center z-50">
    <div class="bg-white p-6 rounded-xl w-full max-w-3xl shadow-xl">
        <h2 class="text-2xl font-bold mb-4 text-center">Add New Bus</h2>

        <form action="" method="POST">
            
            <!-- TWO COLUMN GRID -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <!-- LEFT SIDE -->
                <div>
                    <label class="block font-semibold mb-1">Bus Name</label>
                    <input type="text" name="name" class="w-full p-2 border rounded mb-3" required>

                    <label class="block font-semibold mb-1">Plate Number</label>
                    <input type="text" name="plate_number" class="w-full p-2 border rounded mb-3" required>

                    <label class="block font-semibold mb-1">Route</label>
                    <input type="text" name="route" class="w-full p-2 border rounded mb-3" required>

                    <label class="block font-semibold mb-1">From Location</label>
                    <input type="text" name="from_location" class="w-full p-2 border rounded mb-3" required>

                    <label class="block font-semibold mb-1">To Location</label>
                    <input type="text" name="to_location" class="w-full p-2 border rounded mb-3" required>
                </div>

                <!-- RIGHT SIDE -->
                <div>
                    <label class="block font-semibold mb-1">Travel Date</label>
                    <input type="date" name="travel_date" class="w-full p-2 border rounded mb-3" required>

                    <label class="block font-semibold mb-1">Departure Time</label>
                    <input type="time" name="departure_time" class="w-full p-2 border rounded mb-3" required>

                    <label class="block font-semibold mb-1">Arrival Time</label>
                    <input type="time" name="arrival_time" class="w-full p-2 border rounded mb-3" required>

                    <label class="block font-semibold mb-1">Total Seats</label>
                    <input type="number" name="total_seats" class="w-full p-2 border rounded mb-3" required>

                    <label class="block font-semibold mb-1">Ticket Price</label>
                    <input type="number" step="0.01" name="price" class="w-full p-2 border rounded mb-3" required>

                    <label class="block font-semibold mb-1">Status</label>
                    <select name="status" class="w-full p-2 border rounded mb-3">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="Maintenance">In Maintenance</option>
                    </select>
                </div>

            </div>

            <!-- SUBMIT BUTTON -->
            <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded w-full mt-4 font-semibold">
                Add Bus
            </button>
        </form>

        <button onclick="closeModal('addBusModal')" class="mt-3 text-red-600 block w-full text-center">
            Close
        </button>
    </div>
</div>
