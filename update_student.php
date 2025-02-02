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
    $user_id = $_GET['id'];

    $sql = "SELECT user_id, email, password, mobile_number, username, school, reset_password 
            FROM users 
            WHERE user_id = ?";

    if ($stmt = $connection->prepare($sql)) {
        $stmt->bind_param("i", $user_id);
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
<h1>Update Student</h1>

<form method="POST">
    <label for="user_id">User ID:</label>
    <input type="text" name="user_id" value="<?php echo $row['user_id']; ?>"readonly><br><br>

    <label for="username">Name:</label>
    <input type="text" name="username" value="<?php echo $row['username']; ?>"><br><br>

    <label for="email">email:</label>
    <input type="text" name="email" value="<?php echo $row['email']; ?>"><br><br>

    <label for="password">Password:</label>
    <input type="text" name="password" value="<?php echo $row['password']; ?>" readonly><br><br>

    <label for="mobile_number">mobile number:</label>
    <input type="text" name="mobile_number" value="<?php echo $row['mobile_number']; ?>"><br><br>

    <label for="school">School:</label>
    <select name="school">
        <option value="ENG" <?php if ($row['school'] == 'ENG') echo 'selected'; ?>>ENG</option>
        <option value="IIT" <?php if ($row['school'] == 'IIT') echo 'selected'; ?>>IIT</option>
        <option value="ASC" <?php if ($row['school'] == 'ASC') echo 'selected'; ?>>ASC</option>
        <option value="HSS" <?php if ($row['school'] == 'HSS') echo 'selected'; ?>>HSS</option>
        <option value="BUS" <?php if ($row['school'] == 'BUS') echo 'selected'; ?>>BUS</option>
        <option value="DES" <?php if ($row['school'] == 'DES') echo 'selected'; ?>>DES</option>
    </select>

    <label for="reset_password">Do you want the student to reset their password?:</label>
    <select name="reset_password">
        <option value="yes" <?php if ($row['reset_password'] == 'yes') echo 'selected'; ?>>Yes</option>
        <option value="no" <?php if ($row['reset_password'] == 'no') echo 'selected'; ?>>No</option>
    </select>

    <button type="submit" name="update">Update</button>
</form>

<a href="logout.php" class="logout">Logout</a>

<?php
// Handle form submission and update the database
if (isset($_POST['update'])) {
    $user_id = $_POST['user_id'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $mobile_number = ($_POST['mobile_number']);
    $username = $_POST['username'];
    $school = $_POST['school'];
    $reset_password = $_POST['reset_password'];

    // Prepare SQL query
    $update_sql = "UPDATE users SET email = ?, mobile_number = ?, username = ?, school = ?, reset_password = ? WHERE user_id = ?";

    if ($stmt = $connection->prepare($update_sql)) {
        // Bind parameters
        $stmt->bind_param("sssssi", $email, $mobile_number, $username, $school, $reset_password, $user_id);

        if ($stmt->execute()) {
            header("Location: update_student.php?id=" . $user_id);
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