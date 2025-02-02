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
// Fetch data from student_inventory table
$sql = "SELECT si.student_inventory_id, si.user_id, si.inventory_id, si.borrowed_date, si.return_date, si.status, s.school FROM student_inventory si JOIN users s ON si.user_id = s.user_id";
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
                <td>{$row['student_inventory_id']}</td>
                <td>{$row['user_id']}</td>
                <td>{$row['inventory_id']}</td>
                <td>{$row['borrowed_date']}</td>
                <td>{$row['return_date']}</td>
                <td>{$row['status']}</td>
                <td>{$row['school']}</td>
              	</tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No records found</td></tr>";
        }
?>
</table>
<a href="logout.php">Logout</a>
 
</body>
</html>