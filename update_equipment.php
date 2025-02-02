<?php
session_start();
require 'db.php';

// Ensure only Admins or Facility Managers can access
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'facility manager'])) {
    die("Access Denied: You do not have permission.");
}

// Get equipment ID from URL
if (!isset($_GET['inventory_id']) || empty($_GET['inventory_id'])) {
    die("Invalid equipment ID.");
}

$inventory_id = $_GET['inventory_id'];

// Fetch equipment details
$stmt = $pdo->prepare("SELECT * FROM inventory WHERE inventory_id = :inventory_id");
$stmt->execute([':inventory_id' => $inventory_id]);
$equipment = $stmt->fetch();

if (!$equipment) {
    die("Equipment not found.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Equipment</title>
</head>
<body>
    <h2>Update Inventory</h2>
    <form method="POST" action="update_process.php">
        <input type="hidden" name="inventory_id" value="<?= htmlspecialchars($equipment['inventory_id']) ?>">

        <label>Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($equipment['name']) ?>" required><br>

        <label>Description:</label>
        <textarea name="description" required><?= htmlspecialchars($equipment['description']) ?></textarea><br>

        <label>Model Number:</label>
        <input type="text" name="model_number" value="<?= htmlspecialchars($equipment['model_number']) ?>" required><br>

        <label>Created Time:</label>
        <input type="date" name="created_time" value="<?= htmlspecialchars($equipment['created_time']) ?>" required><br>

        <button type="submit">Update Equipment</button>
    </form>
    <a href="view_equipment.php">view_equipment</a>   
</body>
</html>

