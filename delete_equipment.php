<?php
session_start();
require 'db.php';

// Ensure user is Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied: Only admins can delete equipment.");
}

// Check if inventory_id is provided
if (!isset($_GET['inventory_id']) || empty($_GET['inventory_id'])) {
    die("Error: Invalid equipment ID.");
}

$inventory_id = $_GET['inventory_id']; // Corrected variable name

// Delete the record using the correct column (inventory_id)
$stmt = $pdo->prepare("DELETE FROM inventory WHERE inventory_id = :inventory_id");
$success = $stmt->execute([':inventory_id' => $inventory_id]);

if ($success) {
    echo "✅ Equipment deleted successfully!";
    echo '<br><a href="admin.php"><button>Back to List</button></a>';
} else {
    echo "❌ Error deleting equipment.";
}
?>

