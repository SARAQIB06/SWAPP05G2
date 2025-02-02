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