<?php
session_start();
if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
    echo "Welcome ".$_SESSION['username'] ; 
    echo "<br />".$_SESSION['role']."<br />";
} else {
    header("location:login.php");
}

if ($_SESSION['role'] !== 'facility manager') {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Database connection
$connection = new mysqli("localhost", "root", "", "swapproj");
if ($connection->connect_error) {
    die("Database connection failed: " . $connection->connect_error);
}

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_id'])) {
    $delete_id = $connection->real_escape_string($_GET['delete_id']);
    $delete_sql = "DELETE FROM student_inventory WHERE student_inventory_id = '$delete_id'";
    if ($connection->query($delete_sql) === TRUE) {
        header("Location: facility_manager.php"); // Refresh the page
        exit();
    } else {
        echo "Error deleting record: " . $connection->error;
    }
}

// Fetch data from student_inventory table
$sql = "SELECT si.student_inventory_id, si.user_id, si.inventory_id, si.borrowed_date, si.return_date, si.status, s.school 
        FROM student_inventory si 
        JOIN users s ON si.user_id = s.user_id";
$result = $connection->query($sql);
?>

<style>
    /* ... (keep your existing styles the same) ... */
</style>

<html>
<body>
<h1>Facility Manager page</h1>

<h2>Student Inventory</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Student ID</th>
        <th>Inventory ID</th>
        <th>Borrowed Date</th>
        <th>Return Date</th>
        <th>Status</th>
        <th>School</th>
        <th>Update</th>
        <th>Delete</th>
    </tr>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $school_class = match ($row['school']) {
                "ENG" => "ENG",
                "BUS" => "BUS",
                "IIT" => "IIT",
                "DES" => "DES",
                "HSS" => "HSS",
                "ASC" => "ASC",
                default => ''
            };

            echo "<tr class='$school_class'>
                <td>{$row['student_inventory_id']}</td>
                <td>{$row['user_id']}</td>
                <td>{$row['inventory_id']}</td>
                <td>{$row['borrowed_date']}</td>
                <td>{$row['return_date']}</td>
                <td>{$row['status']}</td>
                <td>{$row['school']}</td>
                <td>
                    <form action='fm_u.php' method='get'>
                        <input type='hidden' name='id' value='{$row['student_inventory_id']}'>
                        <button type='submit'>Update</button>
                    </form>
                </td>
                <td>
                    <form action='facility_manager.php' method='get' onsubmit='return confirm(\"Are you sure you want to delete this record?\");'>
                        <input type='hidden' name='delete_id' value='{$row['student_inventory_id']}'>
                        <button type='submit' style='background-color: #ff4444; color: white;'>Delete</button>
                    </form>
                </td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='9'>No records found</td></tr>";
    }
    ?>
</table>
<a href="fm_c.php">Add student borrower</a>
<a href="logout.php" class="logout">Logout</a>

</body>
</html>
<?php


// Ensure only Facility Managers can access this section
if ($_SESSION['role'] !== 'facility manager') {
    echo "Unauthorized access.";
    exit();
}

// CREATE: Assign Equipment to a Student
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["create_inventory"])) {
    $user_id = $_POST["user_id"];
    $inventory_id = $_POST["inventory_id"];
    $borrowed_date = $_POST["borrowed_date"];
    $return_date = $_POST["return_date"];
    $status = $_POST["status"];

    $stmt = $connection->prepare("INSERT INTO student_inventory (user_id, inventory_id, borrowed_date, return_date, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $user_id, $inventory_id, $borrowed_date, $return_date, $status);

    if ($stmt->execute()) {
        echo "Inventory assigned successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// UPDATE: Modify Inventory Assignments
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_inventory"])) {
    $student_inventory_id = $_POST["student_inventory_id"];
    $borrowed_date = $_POST["borrowed_date"];
    $return_date = $_POST["return_date"];
    $status = $_POST["status"];

    $stmt = $connection->prepare("UPDATE student_inventory SET borrowed_date = ?, return_date = ?, status = ? WHERE student_inventory_id = ?");
    $stmt->bind_param("sssi", $borrowed_date, $return_date, $status, $student_inventory_id);

    if ($stmt->execute()) {
        echo "Inventory updated successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// DELETE: Securely Remove Inventory Assignments
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_inventory"])) {
    $student_inventory_id = $_POST["student_inventory_id"];

    // Ensure the assignment is not in "Assigned," "In-Use," or "Returned" status
    $stmt = $connection->prepare("SELECT status FROM student_inventory WHERE student_inventory_id = ?");
    $stmt->bind_param("i", $student_inventory_id);
    $stmt->execute();
    $stmt->bind_result($status);
    $stmt->fetch();
    $stmt->close();

    if ($status === "Assigned" || $status === "In-Use" || $status === "Returned") {
        echo "Cannot delete inventory assignment with active status.";
    } else {
        $stmt = $connection->prepare("DELETE FROM student_inventory WHERE student_inventory_id = ?");
        $stmt->bind_param("i", $student_inventory_id);

        if ($stmt->execute()) {
            echo "Inventory assignment deleted successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
