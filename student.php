<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");  
    exit();
}

// Validate the user_id in the session matches the one in the URL
$page_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
$session_id = $_SESSION['user_id'];

if ($page_id != $session_id) {
    session_destroy();
    header("Location: login.php"); 
    exit();
}

// Connect to the database
$connection = mysqli_connect("localhost", "root", "", "swapproj") or die("Cannot connect");

// SQL query to fetch borrowed items
$sql = $connection->prepare("
    SELECT i.name, i.description, si.borrowed_date, si.return_date
    FROM student_inventory si
    JOIN inventory i ON si.inventory_id = i.inventory_id
    WHERE si.user_id = ? AND si.status IN ('assigned', 'in-use')
");

// Bind the user ID and execute the query
$sql->bind_param("i", $session_id);
$sql->execute();

// Bind the results to variables
$sql->bind_result($name, $description, $borrowed_date, $return_date);

// Display the results
echo "<h2>Your Borrowed Items</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%; text-align: left;'>
        <tr>
            <th style='padding: 8px; border: 1px solid black;'>Item Name</th>
            <th style='padding: 8px; border: 1px solid black;'>Description</th>
            <th style='padding: 8px; border: 1px solid black;'>Borrowed Date</th>
            <th style='padding: 8px; border: 1px solid black;'>Return Date</th>
        </tr>";

while ($sql->fetch()) {
    echo "<tr>
            <td style='padding: 8px; border: 1px solid black;'>$name</td>
            <td style='padding: 8px; border: 1px solid black;'>$description</td>
            <td style='padding: 8px; border: 1px solid black;'>$borrowed_date</td>
            <td style='padding: 8px; border: 1px solid black;'>" . ($return_date ? $return_date : "Not Returned") . "</td>
          </tr>";
}

echo "</table>";

$sql->close();
$connection->close();
?>

<div style="text-align: center; margin-top: 20px;">
    <form action="change_pw.php?user_id=" . $page_id method="get">
        <button type="submit" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">Change Password</button>
    </form>
</div>