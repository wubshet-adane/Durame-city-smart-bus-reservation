<div id="addDriverModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-xl w-full max-w-md shadow">
        <h2 class="text-2xl font-bold mb-4">Add Driver</h2>
        <form action="process/add_driver.php" method="POST">
            <input type="text" name="name" placeholder="Driver Name" class="w-full p-2 border rounded mb-2" required>
            <input type="text" name="phone" placeholder="Phone" class="w-full p-2 border rounded mb-2" required>
            <button class="bg-blue-600 text-white px-4 py-2 rounded w-full">Add Driver</button>
        </form>
        <button onclick="closeModal('addDriverModal')" class="mt-2 text-red-600">Close</button>
    </div>
</div>
