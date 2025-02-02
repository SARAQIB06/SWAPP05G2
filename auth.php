<?php
session_start();

function authenticate($myusername, $mypassword)
{
    if (empty($myusername) || empty($mypassword)) {
        die("Username or password is empty!");
    }

    // Encrypt password using sha256
    $hashAlgo = "sha256";
    $encrypted_mypassword = hash($hashAlgo, $mypassword);

    // Establish connection
    $connection = mysqli_connect("localhost", "root", "", "swapproj") or die("Cannot connect");

    // Prepare the SQL query with placeholders (Added reset_password)
    $sql = $connection->prepare("SELECT user_id, username, password, role, reset_password FROM users WHERE username=? AND password=?");

    // Check if prepare was successful
    if ($sql === false) {
        die('MySQL prepare error: ' . $connection->error);
    }

    // Bind the parameters to the query
    $sql->bind_param("ss", $myusername, $encrypted_mypassword);

    // Execute the query
    $sql->execute();

    // Bind the result to variables (Added reset_password)
    $sql->bind_result($id, $username, $password, $role, $reset_password);

    // If result matched $myusername and $mypassword
    if ($sql->fetch()) {
        // Register session variables
        $_SESSION['role'] = $role;
        $_SESSION['username'] = $myusername;
        $_SESSION['user_id'] = $id;

        if ($_SESSION['role'] == "student") {
            if ($reset_password === "yes") {
                header("Location: change_pw.php?user_id=" . $id);
                exit();
            } elseif ($reset_password === "no") {
                header("Location: student.php?user_id=" . $id);
                exit();
            }
        } elseif ($_SESSION['role'] == "admin") {
            header("Location: admin.php");
            exit();
        } elseif ($_SESSION['role'] == "facility manager") {
            header("Location: facility_manager.php");
            exit();
        } else {
            echo "Invalid user or wrong password";
        }        
    } else {
        echo "Invalid username or password";
    }

    // Close the prepared statement and connection
    $sql->close();
    $connection->close();
}

// Grab username and password from form via POST
$username = $_REQUEST['username'];
$password = $_REQUEST['password'];

authenticate($username, $password);
?>