<?php

$connection = new mysqli("localhost", "root", "", "swapproj");

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $inventory_id = $_POST['inventory_id'];
    $return_date = $_POST['return_date'];

    if (empty($user_id) || empty($inventory_id) || empty($return_date)) {
        die("All fields are required!");
    }

    // Fetch the student's school from the users table
    $user_query = $connection->prepare("SELECT school FROM users WHERE user_id = ?");
    $user_query->bind_param("s", $user_id);
    $user_query->execute();
    $user_result = $user_query->get_result();
    if ($user_result->num_rows === 0) {
        die("Student not found.");
    }
    $user_data = $user_result->fetch_assoc();
    $student_school = $user_data['school'];

    // Fetch the inventory item's school from the inventory table
    $inventory_query = $connection->prepare("SELECT school FROM inventory WHERE inventory_id = ?");
    $inventory_query->bind_param("s", $inventory_id);
    $inventory_query->execute();
    $inventory_result = $inventory_query->get_result();
    if ($inventory_result->num_rows === 0) {
        die("Inventory item not found.");
    }
    $inventory_data = $inventory_result->fetch_assoc();
    $inventory_school = $inventory_data['school'];

    // Check if schools match
    if ($student_school !== $inventory_school) {
        die("The student's school does not match the inventory item's school.");
    }

    // Check if the inventory item is already borrowed
    $borrow_check_query = $connection->prepare("SELECT * FROM student_inventory WHERE inventory_id = ?");
    $borrow_check_query->bind_param("s", $inventory_id);
    $borrow_check_query->execute();
    $borrow_check_result = $borrow_check_query->get_result();
    
    if ($borrow_check_result->num_rows > 0) {
        die("This inventory item is already borrowed.");
    }

    // Insert into student_inventory if schools match and item is not borrowed
    $stmt = $connection->prepare("INSERT INTO student_inventory (user_id, inventory_id, return_date) VALUES (?, ?, ?)");
    if (!$stmt) {
        die("Statement preparation failed: " . $connection->error);
    }

    // Bind parameters to the statement
    $stmt->bind_param("sss", $user_id, $inventory_id, $return_date);
    if ($stmt->execute()) {
        echo "Added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $user_query->close();
    $inventory_query->close();
    $borrow_check_query->close();
    $connection->close();
}
?>

<body>
    <h1>New borrow</h1>
    <form method="POST">
        <label for="user_id">Student ID:</label>
        <input type="text" id="user_id" name="user_id" required>
        <br>

        <label for="inventory_id">Inventory ID:</label>
        <input type="text" id="inventory_id" name="inventory_id" required>
        <br>

        <label for="return_date">Return Date:</label>
        <input type="date" id="return_date" name="return_date" required>
        <br>

        <button type="submit">Submit</button>
    </form>
</body>