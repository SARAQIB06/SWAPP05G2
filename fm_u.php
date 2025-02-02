<?php
session_start();
if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
    echo "Welcome " . $_SESSION['username'];
    echo "<br />" . $_SESSION['role'] . "<br />";
} else {
    header("location:login.php");
    exit();
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

// Fetch specific record
if (isset($_GET['id'])) {
    $student_inventory_id = $_GET['id'];

    $sql = "SELECT student_inventory_id, user_id, inventory_id, borrowed_date, return_date, status 
            FROM student_inventory 
            WHERE student_inventory_id = ?";

    if ($stmt = $connection->prepare($sql)) {
        $stmt->bind_param("i", $student_inventory_id);
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
<h1>Update Student Inventory</h1>

<form method="POST">
    <input type="hidden" name="student_inventory_id" value="<?php echo $row['student_inventory_id']; ?>">

    <label for="user_id">User ID:</label>
    <input type="text" name="user_id" value="<?php echo $row['user_id']; ?>"><br><br>

    <label for="inventory_id">Inventory ID:</label>
    <input type="text" name="inventory_id" value="<?php echo $row['inventory_id']; ?>"><br><br>

    <label for="borrowed_date">Borrowed Date:</label>
    <input type="text" name="borrowed_date" value="<?php echo $row['borrowed_date']; ?>" readonly><br><br>

    <label for="return_date">Return Date:</label>
    <input type="date" name="return_date" value="<?php echo $row['return_date']; ?>"><br><br>

    <label for="status">Status:</label>
    <select name="status">
        <option value="assigned" <?php if ($row['status'] == 'assigned') echo 'selected'; ?>>Assigned</option>
        <option value="in-use" <?php if ($row['status'] == 'in-use') echo 'selected'; ?>>In-Use</option>
        <option value="returned" <?php if ($row['status'] == 'returned') echo 'selected'; ?>>Returned</option>
    </select>

    <button type="submit" name="update">Update</button>
</form>

<a href="logout.php" class="logout">Logout</a>

<?php
// Handle form submission and update the database
if (isset($_POST['update'])) {
    $student_inventory_id = $_POST['student_inventory_id'];
    $user_id = $_POST['user_id'];
    $inventory_id = $_POST['inventory_id'];
    $return_date = !empty($_POST['return_date']) ? $_POST['return_date'] : null;
    $status = $_POST['status'];

    // Prepare SQL query dynamically
    if ($return_date !== null) {
        $update_sql = "UPDATE student_inventory SET user_id = ?, inventory_id = ?, return_date = ?, status = ? WHERE student_inventory_id = ?";
    } else {
        $update_sql = "UPDATE student_inventory SET user_id = ?, inventory_id = ?, status = ? WHERE student_inventory_id = ?";
    }

    if ($stmt = $connection->prepare($update_sql)) {
        if ($return_date !== null) {
            $stmt->bind_param("iissi", $user_id, $inventory_id, $return_date, $status, $student_inventory_id);
        } else {
            $stmt->bind_param("iisi", $user_id, $inventory_id, $status, $student_inventory_id);
        }

        if ($stmt->execute()) {
            header("Location: fm_u.php?id=" . $student_inventory_id);
            exit();
        } else {
            echo "<br />Error updating record.";
        }
    } else {
        echo "<br />Failed to prepare update query.";
    }
}
?>

</body>
</html>