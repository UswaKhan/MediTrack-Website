<?php
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['role'] !== 'cashier') {
    header("Location: index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "medicine_inventory");
$customer_id = intval($_GET['id']);

$customer = $conn->query("SELECT * FROM customers WHERE id=$customer_id")->fetch_assoc();
$sales = $conn->query("SELECT s.*, m.name FROM sales s JOIN medicines m ON s.medicine_id = m.id WHERE s.customer_id=$customer_id ORDER BY s.sold_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <h3>Purchase History: <?= $customer['name'] ?> (<?= $customer['phone'] ?>)</h3>
    <table class="table table-bordered">
        <tr>
            <th>Medicine</th>
            <th>Quantity</th>
            <th>Sold By</th>
            <th>Date</th>
        </tr>
        <?php while ($row = $sales->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['name'] ?></td>
            <td><?= $row['quantity_sold'] ?></td>
            <td><?= $row['sold_by'] ?></td>
            <td><?= $row['sold_at'] ?></td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
