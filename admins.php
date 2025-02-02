<?php

session_start();
if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
    echo "Welcome ".$_SESSION['username'] ; 
	echo "<br />".$_SESSION['role']."<br />";

}else{
	header("location:login.php");
}
if ($_SESSION['role'] !== 'admin') {
    session_destroy();
    header("Location: login.php");
    exit();
}
//db conn
$connection = new mysqli("localhost", "root", "", "swapproj");
    if ($connection->connect_error) {
        die("Database connection failed: " . $connection->connect_error);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_id'])) {
        $delete_id = $connection->real_escape_string($_GET['delete_id']);
        $delete_sql = "DELETE FROM usage_logs WHERE usage_log_id = '$delete_id'";
        if ($connection->query($delete_sql) === TRUE) {
            header("Location: admins.php"); // Refresh the page
            exit();
        } else {
            echo "Error deleting record: " . $connection->error;
        }
    }
    

// Fetch data from usage_log table
$sql = "SELECT usage_log_id, inventory_id, usage_description, maintenance_description, usage_date FROM usage_logs";
$result = $connection->query($sql);

?>
<style>
    body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .logout {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 20px;
            background-color: red;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
</style>
<html>
<body>
<h1>Admin page</h1>

<h2>Usage Log List</h2>
<table>
    <tr>
        <th>Usage Logs</th>
        <th>Inventory ID</th>
        <th>Usage Description</th>
        <th>Maintenance Description</th>
        <th>Usage Date</th>
        <th>Actions</th>
    </tr>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['usage_log_id']}</td>";
            echo "<td>{$row['inventory_id']}</td>";
            echo "<td>{$row['usage_description']}</td>";
            echo "<td>{$row['maintenance_description']}</td>";
            echo "<td>{$row['usage_date']}</td>";
            echo "<td>
                    <form action='update_usage_log.php' method='get' style='display:inline;'>
                        <input type='hidden' name='id' value='{$row['usage_log_id']}'>
                        <button type='submit'>Update</button>
                    </form>
                    <form action='admins.php' method='get' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this Log?\");'>
                        <input type='hidden' name='delete_id' value='{$row['usage_log_id']}'>
                        <button type='submit' style='background-color: #ff4444; color: white;'>Delete</button>
                    </form>
                  </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No records found</td></tr>";
    }
    ?>
</table>

<a href="create_usage_log.php">Add Usage Log</a>
<a href="logout.php">Logout</a> 
<a href="adminsi.php">View Borrowed Items</a>
