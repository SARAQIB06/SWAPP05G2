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

// Fetch data from student_inventory table
$sql = "SELECT user_id, email, mobile_number, username, school FROM users WHERE role = 'student'";
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
        .ENG { background-color: purple; } /* Light Red */
        .BUS { background-color: yellow; } /* Light Green */
        .IIT { background-color: blue; } /* Light Blue */
        .DES { background-color: cyan; } /* Light Yellow */
        .HSS { background-color: orange; } /* Default Gray */
        .ASC { background-color: green; }
</style>

<html>
<body>
<h1>Facility Manager page</h1>

<h2>Student List</h2>
    <table>
        <tr>
            <th>Student ID</th>
            <th>Name</th>
            <th>Email Address</th>
            <th>Mobile Number</th>
            <th>School</th>
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
                <td>{$row['user_id']}</td>
                <td>{$row['username']}</td>
                <td>{$row['email']}</td>
                <td>{$row['mobile_number']}</td>
                <td>{$row['school']}</td>
                <td>
                    <form action='update_student.php' method='get'>
                        <input type='hidden' name='id' value='{$row['user_id']}'>
                        <button type='submit'>Update</button>
                    </form>
                </td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No records found</td></tr>";
    }
    ?>
</table>
<a href="create_student.php">Add student</a>
<a href="facility_managersi.php">View Borrowed Items</a>
<a href="logout.php" class="logout">Logout</a>

</body>
</html>