<?php

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $driver_id = intval($_POST['driver_id']);
    $bus_id = intval($_POST['bus_id']);

    if (empty($driver_id) || empty($bus_id)) {
        echo "Required fields missing!";
        exit;
    }

    // 1. Check if driver exists
    $stmt = $mysqli->prepare("SELECT id FROM drivers WHERE id = ?");
    $stmt->bind_param("i", $driver_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows == 0) {
        echo "Invalid driver selected.";
        exit;
    }

    // 2. Check if bus exists
    $stmt2 = $mysqli->prepare("SELECT id FROM buses WHERE id = ?");
    $stmt2->bind_param("i", $bus_id);
    $stmt2->execute();
    $res2 = $stmt2->get_result();

    if ($res2->num_rows == 0) {
        echo "Invalid bus selected.";
        exit;
    }

    // 3. Assign driver to bus
    $update = $mysqli->prepare("UPDATE buses SET driver_id = ? WHERE id = ?");
    $update->bind_param("ii", $driver_id, $bus_id);
    if ($update->execute()) {
        echo "successfuly updated.";
        exit;
    } else {
        echo "Failed to assign driver.";
        exit;
    }
}
?>








<?php
$driversList = $mysqli->query("SELECT * FROM drivers");
$busesList = $mysqli->query("SELECT * FROM buses");
?>
<div id="assignDriverModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-xl w-full max-w-md shadow">
        <h2 class="text-2xl font-bold mb-4">Assign Driver</h2>
        <form action="" method="POST">
            <select name="driver_id" class="w-full p-2 border rounded mb-2">
                <?php while ($d = $driversList->fetch_assoc()): ?>
                    <option value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
                <?php endwhile; ?>
            </select>

            <select name="bus_id" class="w-full p-2 border rounded mb-2">
                <?php while ($b = $busesList->fetch_assoc()): ?>
                    <option value="<?= $b['id'] ?>"><?= $b['plate_number'].' '.  $b['name']?></option>
                <?php endwhile; ?>
            </select>

            <button class="bg-indigo-600 text-white px-4 py-2 rounded w-full">Assign</button>
        </form>
        <button onclick="closeModal('assignDriverModal')" class="mt-2 text-red-600">Close</button>
    </div>
</div>
