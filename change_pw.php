<?php
session_start();
$connection = mysqli_connect("localhost", "root", "", "swapproj") or die("Cannot connect");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id']; // Get the user ID from session
    $email = $_POST['email'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate if the email matches the registered email
    $query = $connection->prepare("SELECT email FROM users WHERE user_id = ?");
    $query->bind_param("i", $user_id);
    $query->execute();
    $query->bind_result($registered_email);
    $query->fetch();
    $query->close();

    if ($email !== $registered_email) {
        die("Email does not match our records. Please try again.");
    }

    if ($new_password !== $confirm_password) {
        die("Passwords do not match. Please try again.");
    }

    // Encrypt new password
    $encrypted_password = hash("sha256", $new_password);

    // Update password in database and disable reset flag
    $update_sql = $connection->prepare("UPDATE users SET password=?, reset_password='no' WHERE user_id=?");
    $update_sql->bind_param("si", $encrypted_password, $user_id);
    
    if ($update_sql->execute()) {
        echo "Password reset successful. Redirecting...";
        header("refresh:2;url=student.php?user_id=" . $user_id);
        exit();
    } else {
        echo "Error updating password.";
    }

    $update_sql->close();
}
$connection->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body>
    <h2>Enter a new password</h2>
    <form method="POST">
        <label>Email:</label>
        <input type="email" name="email" required><br><br>

        <label>New Password:</label>
        <input type="password" name="new_password" required><br><br>

        <label>Confirm Password:</label>
        <input type="password" name="confirm_password" required><br><br>

        <button type="submit">Reset Password</button>
    </form>
</body>
</html>