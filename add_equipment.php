<?php
session_start();
require 'db.php';

// Check if user is Admin or Facility Manager (Assuming user role is stored in session)
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'facility manager'])) {
    die("Access Denied: You do not have permission.");
}

// Initialize error variable
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $model_number = $_POST['model_number'];
    $purchase_date = $_POST['purchase_date'];
    $created_time = $_POST['created_time'];

    // Validation for Name length
    if (empty($name) || strlen($name) > 10) {
        $error = "Name is required and must be under 10 characters.";
    }

    // If there's no error, proceed with inserting the data
    if (empty($error)) {
        $sql = "INSERT INTO inventory (name, description, model_number, purchase_date, created_time) 
                VALUES (:name, :description, :model_number, :purchase_date, :created_time)";
        
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':model_number' => $model_number,
            ':purchase_date' => $purchase_date,
            ':created_time' => $created_time
        ])) {
            echo "✅ Lab equipment added successfully!";
            echo '<br><a href="view_equipment.php"><button>view equipment</button></a>';
            exit;
        } else {
            $error = "Error adding equipment.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Equipment</title>
</head>
<body>
    <h2>Add Lab Equipment</h2>
    
    <!-- Show error message if there's any -->
    <?php if (!empty($error)): ?>
        <div style="color: red;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <label>Name:</label>
        <input type="text" name="name" required><br>

        <label>Description:</label>
        <input type="text" name="description" required><br>

        <label>Model Number:</label>
        <input type="text" name="model_number" required><br>

        <label>Purchase Date:</label>
        <input type="date" name="purchase_date" required><br>
        
        <label>Created Time:</label>
        <input type="date" name="created_time" required><br>
        
        <button type="submit">Add Equipment</button>
    </form>

    <a href="view_equipment.php">View Equipment List</a> 
</body>
</html>
