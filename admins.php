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
        $delete_sql = "DELETE FROM users WHERE user_id = '$delete_id'";
        if ($connection->query($delete_sql) === TRUE) {
            header("Location: admins.php"); // Refresh the page
            exit();
        } else {
            echo "Error deleting record: " . $connection->error;
        }
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
<h1>Admin page</h1>

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
                if ($row['school'] == "ENG") {
                    $school_class = "ENG";
                } elseif ($row['school'] == "BUS") {
                    $school_class = "BUS";
                } elseif ($row['school'] == "IIT") {
                    $school_class = "IIT";
                } elseif ($row['school'] == "DES") {
                    $school_class = "DES";
                } elseif ($row['school'] == "HSS") {
                    $school_class = "HSS";
                } elseif ($row['school'] == "ASC") {
                    $school_class = "ASC";
                }

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
                <td>
                    <form action='admins.php' method='get' onsubmit='return confirm(\"Are you sure you want to delete this student?\");'>
                        <input type='hidden' name='delete_id' value='{$row['user_id']}'>
                        <button type='submit' style='background-color: #ff4444; color: white;'>Delete</button>
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
<a href="logout.php">Logout</a> 
<a href="adminsi.php">View Borrowed Items</a>
 
</body>
</html>