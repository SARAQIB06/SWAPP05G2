<?php
session_start();
require 'db.php';

// Ensure only Admins or Facility Managers can update
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'facility manager'])) {
    die("Access Denied: You do not have permission.");
}

// Validate POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inventory_id = $_POST['inventory_id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $model_number = trim($_POST['model_number']);
    $created_time = $_POST['created_time'];

    // Error Handling
    if (empty($name) || strlen($name) > 10) {
        echo "Error: Name is required and must be under 10 characters.";
        echo '<br><a href="update_equipment.php?inventory_id=' . $inventory_id . '"><button>Go Back</button></a>';
        exit;
    }

    if (empty($description)) {
        echo "Error: Description is required.";
        echo '<br><a href="update_equipment.php?inventory_id=' . $inventory_id . '"><button>Go Back</button></a>';
        exit;
    }

    if (strlen($model_number) > 100) {
        echo "Error: Model Number must be under 100 characters.";
        echo '<br><a href="update_equipment.php?inventory_id=' . $inventory_id . '"><button>Go Back</button></a>';
        exit;
    }

    if (empty($created_time)) {
        echo "Error: Created Time is required.";
        echo '<br><a href="update_equipment.php?inventory_id=' . $inventory_id . '"><button>Go Back</button></a>';
        exit;
    }

    // ✅ FIXED SQL QUERY: Removed extra comma before WHERE
    $stmt = $pdo->prepare("UPDATE inventory SET 
        name = :name, 
        description = :description, 
        model_number = :model_number, 
        created_time = :created_time 
        WHERE inventory_id = :inventory_id");

    // ✅ FIXED ERROR: Ensure $success is always set
    $success = $stmt->execute([
        ':inventory_id' => $inventory_id,
        ':name' => $name,
        ':description' => $description,
        ':model_number' => $model_number,
        ':created_time' => $created_time,
    ]);

    if ($success) {
        echo "✅ Equipment updated successfully!";
        echo '<br><a href="view_equipment.php"><button>Back to List</button></a>';
    } else {
        echo "❌ Error updating equipment.";
        echo '<br><a href="update_equipment.php?inventory_id=' . $inventory_id . '"><button>Go Back</button></a>';
    }
}
?>



