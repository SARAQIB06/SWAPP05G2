<?php
session_start();
if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
    echo "Welcome " . $_SESSION['username'];
    echo "<br />" . $_SESSION['role'] . "<br />";
} else {
    header("location:login.php");
    exit();
}

if ($_SESSION['role'] !== 'facility manager' && $_SESSION['role'] !== 'admin') {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Database connection
$connection = new mysqli("localhost", "root", "", "swapproj");
if ($connection->connect_error) {
    die("Database connection failed: " . $connection->connect_error);
}

// Fetch specific record
if (isset($_GET['id'])) {
    $UsageID = $_GET['id'];

    $sql = "SELECT usage_log_id, inventory_id, usage_description, maintenance_description, usage_date 
            FROM usage_logs 
            WHERE usage_log_id = ?";

    if ($stmt = $connection->prepare($sql)) {
        $stmt->bind_param("i", $UsageID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        } else {
            echo "Record not found.";
            exit();
        }
    } else {
        echo "Failed to prepare query.";
        exit();
    }
} else {
    echo "Invalid ID.";
    exit();
}
?>

<html>
<body>
<h1>Update Logs</h1>

<form method="POST">
    <label for="usage_log_id">Usage ID:</label>
    <input type="text" name="usage_log_id" value="<?php echo $row['usage_log_id']; ?>"readonly><br><br>

    <label for="inventory_id">Inventory ID:</label>
    <input type="text" name="inventory_id" value="<?php echo $row['inventory_id']; ?>"><br><br>

    <label for="usage_description">Usage Description:</label>
    <input type="text" name="usage_description" value="<?php echo $row['usage_description']; ?>"><br><br>

    <label for="maintenance_description">Maintenance Description:</label>
    <input type="text" name="maintenance_description" value="<?php echo $row['maintenance_description']; ?>" readonly><br><br>

    <label for="usage_date">Usage Date:</label>
    <input type="date" name="usage_date" value="<?php echo $row['usage_date']; ?>"><br><br>

    <button type="submit" name="update">Update</button>
</form>

<a href="logout.php" class="logout">Logout</a>

<?php
// Handle form submission and update the database
if (isset($_POST['update'])) {
    $UsageID = $_POST['usage_log_id'];
    $InventoryID = $_POST['inventory_id'];
    $UsageDescription = $_POST['usage_description'];
    $MaintenanceDescription = ($_POST['maintenance_description']);
    $UsageDate = $_POST['usage_date'];

    // Prepare SQL query
    $update_sql = "UPDATE usage_logs SET inventory_id = ?, usage_description = ?, maintenance_description = ?, usage_date = ? WHERE usage_log_id = ?";

    if ($stmt = $connection->prepare($update_sql)) {
        // Bind parameters
        $stmt->bind_param("issii", $InventoryID, $UsageDescription, $MaintenanceDescription, $UsageDate, $UsageID);

        if ($stmt->execute()) {
            header("Location: update_usage_log.php?id=" . $UsageID);
            exit();
        } else {
            echo "<br />Error updating record: " . $stmt->error;
        }
    } else {
        echo "<br />Failed to prepare update query: " . $connection->error;
    }
}
?>

</body>
</html>