<?php
session_start();
require 'db.php';

if (!isset($_SESSION['role'])) {
    die("Access Denied.");
}

$stmt = $pdo->query("SELECT * FROM inventory ORDER BY created_time DESC");
$equipments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Equipment List</title>
</head>
<body>
    <h2>Lab Equipment Records</h2>
    <table border="1">
        <tr>
            <th>Name</th>
            <th>description</th>
            <th>Model Number</th>
            <th>Purchase Date</th>
            <th>Update</th>
            <?php if ($_SESSION['role'] == 'admin'): ?>
            <th>Delete</th>
            <?php endif; ?>
        </tr>
        <?php foreach ($equipments as $eq): ?>
            <tr>
                <td><?= htmlspecialchars($eq['name']) ?></td>
                <td><?= htmlspecialchars($eq['description']) ?></td>
                <td><?= htmlspecialchars($eq['model_number']) ?></td>
                <td><?= htmlspecialchars($eq['purchase_date']) ?></td>
                <td>
                    <a href="update_equipment.php?inventory_id=<?= htmlspecialchars($eq['inventory_id']) ?>">Update</a>
                </td>
                <td>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <a href="delete_equipment.php?inventory_id=<?= htmlspecialchars($eq['inventory_id']) ?>" 
                        onclick="return confirm('Are you sure you want to delete this record?');">
                        Delete</a>
                    <?php endif; ?>
                </td>    
            </tr>
        <?php endforeach; ?>
    </table>
    <?php if ($_SESSION['role'] == 'facility manager'): ?>
        <a href="facility_manager.php">facility manager</a>
    <?php endif; ?>
    <?php if ($_SESSION['role'] == 'admin'): ?>
        <a href="admin.php">admin</a>
    <?php endif; ?>
</body>
</html>
