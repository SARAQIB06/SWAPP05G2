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
    $name = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['mobile_number'];
    $school = $_POST['school'];
    $hashAlgo = "sha256";
	$password= $_POST['password'];

    // Validate inputs
    if (empty($name) || empty($email) || empty($phone) || empty($school) || empty($hashAlgo) || empty($password)) {
        die("All fields are required!");
    }

    $hashedPassword = hash($hashAlgo, $password);

    // Insert the new student profile into the database
    $stmt = $connection->prepare("INSERT INTO users (username, email, mobile_number, school, password) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Statement preparation failed: " . $connection->error);
    }

    // Bind parameters to the statement
    $stmt->bind_param("sssss", $name, $email, $phone, $school, $hashedPassword);
    if ($stmt->execute()) {
        echo "Student profile created successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $connection->close();
}

?>

<body>
    <h1>Create New Student Profile</h1>
    <form method="POST">
        <label for="username">Student Name:</label>
        <input type="text" id="username" name="username" required>
        <br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br>

        <label for="mobile_number">Mobile Number:</label>
        <input type="text" id="mobile_number" name="mobile_number" required>
        <br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>

        <label for="school">Department:</label>
        <select id="school" name="school" required>
            <option value="">Select Department</option>
            <option value="IIT">IIT</option>
            <option value="ENG">ENG</option>
            <option value="DES">DES</option>
            <option value="BUS">BUS</option>
            <option value="ASC">ASC</option>
            <option value="HSS">HSS</option>
        </select>
        <br>

        <button type="submit">Create Profile</button>
    </form>
</body>