<?php
// Check if the logged-in user is Admin or Facility Manager
session_start();
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'facility manager')) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$connection = new mysqli("localhost", "root", "", "swapproj");

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $UsageID = $_POST['usage_log_id'];
    $InventoryID = $_POST['inventory_id'];
    $UsageDescription = $_POST['usage_description'];
    $MaintenanceDescription = $_POST['maintenance_description'];
	$UsageDate= $_POST['usage_date'];

    // Validate inputs
    if (empty($UsageID) || empty($InventoryID) || empty($UsageDescription) || empty($MaintenanceDescription) || empty($UsageDate)) {
        die("All fields are required!");
    }


    // Insert the new student profile into the database
    $stmt = $connection->prepare("INSERT INTO usage_logs (usage_log_id, inventory_id, Usage_description, maintenance_description, usage_date) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Statement preparation failed: " . $connection->error);
    }

    // Bind parameters to the statement
    $stmt->bind_param("sssss", $UsageID, $InventoryID, $UsageDescription, $MaintenanceDescription, $UsageDate);
    if ($stmt->execute()) {
        echo "Usage Log created successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $connection->close();
}

?>

<body>
    <h1>Create New Usage Log</h1>
    <form method="POST">
        <label for="usage_log_id">Usage Log ID:</label>
        <input type="text" id="usage_log_id" name="usage_log_id" required>
        <br>

        <label for="inventory_id">Inventory ID:</label>
        <input type="text" id="inventory_id" name="inventory_id" required>
        <br>

        <label for="usage_description">Usage Description:</label>
        <input type="text" id="usage_description" name="usage_description" required>
        <br>

        <label for="maintenance_description">Maintenance Description:</label>
        <input type="text" id="maintenance_description" name="maintenance_description" required>
        <br>

        <label for="usage_date">Usage Date:</label>
        <input type="date" id="usage_date" name="usage_date" required>
        <br>

        <button type="submit">Create Log</button>
    </form>
</body>
