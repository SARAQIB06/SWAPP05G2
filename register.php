<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $mobile_number = $_POST['mobile_number'];
    $email = $_POST['email'];
    $school = $_POST['school'];
    $role = $_POST['role'];

    if (empty($username) || empty($password) || empty($mobile_number) || empty($email) || empty($school) || empty($role)) {
        die("All fields are required!");
    }
    
    $hashAlgo = "sha256";
	$encrypted_password= hash($hashAlgo, $password);
    // Establish a secure connection to the database
    $connection = new mysqli("localhost", "root", "", "swapproj");
    if ($connection->connect_error) {
        die("Database connection failed: " . $connection->connect_error);
    }

   // encrypt password using sha256
   

    // Prepare the SQL statement to insert the new user
    $stmt = $connection->prepare("INSERT INTO users (username, password, mobile_number, email, school, role) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Statement preparation failed: " . $connection->error);
    }

    // Bind parameters to the statement
    $stmt->bind_param("ssssss", $username, $encrypted_password, $mobile_number, $email, $school, $role);
    if ($stmt->execute()) {
        echo "Registration successful!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $connection->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
</head>
<body>
    <h1>Register</h1>
    <form method="POST">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required><br>

        <label for="mobile_number">Mobile Number:</label>
        <input type="text" name="mobile_number" id="mobile_number" required><br>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required><br>

        <label for="school">School:</label>
        <select name="school" id="school" required>
            <option value=" "> </option>
            <option value="IIT">IIT</option>
            <option value="ENG">ENG</option>
            <option value="DES">DES</option>
            <option value="BUS">BUS</option>
            <option value="ASC">ASC</option>
            <option value="HSS">HSS</option>
        </select><br>

        <label for="role">Role:</label>
        <select name="role" id="role" required>
            <option value="student">Student</option>
            <option value="facility manager">Facility Manager</option>
            <option value="admin">Admin</option>
        </select><br>

        <button type="submit">Register</button>
    </form>
</body>
</html>