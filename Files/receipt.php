<?php
require_once 'SessionManager.php';
require_once 'Database.php';

// Initialize session manager
$session = SessionManager::getInstance();

// Check if the admin is logged in and is a cashier
if (!$session->isLoggedIn() || $session->getRole() !== 'cashier') {
    header("Location: index.php");
    exit();
}

// Get database connection
$db = Database::getInstance();
$conn = $db->getConnection();

// Get the logged-in username
$username = $session->getUsername();

// Query to get the user profile data
$result = $db->select("SELECT username, profile_picture FROM admin WHERE username = ?", "s", [$username]);
$user = $result[0];

$adminName = $user['username'];
$profilePic = $user['profile_picture'];
$defaultProfilePic = 'profile1.webp';

if (isset($_GET['customer_id'])) {
    $customer_id = (int)$_GET['customer_id'];

    // Fetch customer details using prepared statement
    $customer = $db->select("SELECT * FROM customers WHERE id = ?", "i", [$customer_id]);
    if (empty($customer)) {
        echo "Customer not found.";
        exit();
    }
    $customer = $customer[0];

    // Fetch all sales related to this customer using prepared statement
    $sales = $db->select("SELECT s.*, m.name AS medicine_name, m.price 
                         FROM sales s 
                         INNER JOIN medicines m ON s.medicine_id = m.id 
                         WHERE s.customer_id = ? 
                         ORDER BY s.id DESC", "i", [$customer_id]);

    if (empty($sales)) {
        echo "No sales found for this customer.";
        exit();
    }
} else {
    echo "No customer selected.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt - Sell Medicine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .navbar {
            background-color: #2A3D66;
            height: 80px;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1050;
        }
        body, html {
            overflow-x: hidden;
        }
        .navbar-brand { color: white !important; font-size: 1.5rem; margin-left: 10px; }
        .nav-link { color: white !important; }
        .profile-img { width: 50px; height: 50px; border-radius: 50%; }
        .logout-btn {
            color: #FF9F45; background-color: #2A3D66;
            border: 1px solid #FF9F45; border-radius: 5px;
            font-size: 1rem; padding: 8px 16px;
            margin-left: 10px; transition: all 0.3s ease;
            font-weight: 500; margin-right: 10px;
        }
        .dashboard-btn {
            color: #FFFFFF; background-color: #2A3D66;
            border: 1px solid #FFFFFF; border-radius: 5px;
            font-size: 1.3rem; padding: 8px 16px;
            margin-left: 10px; transition: all 0.3s ease;
            font-weight: 500; margin-right: 10px; margin-top: 20px;
        }
        .logout-btn:hover, .dashboard-btn:hover { background-color: #FF9F45; color: #2A3D66; border-color: #2A3D66; }
        .receipt-card {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            border: 1px solid #e0e0e0;
        }
        .receipt-header {
            background-color: #2A3D66;
            padding: 10px;
            border-radius: 8px 8px 0 0;
            color: #ffffff;
            text-align: center;
            margin: -20px -20px 15px -20px;
        }
        .receipt-header h1 {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }
        .receipt-header p {
            font-size: 0.8rem;
            margin: 4px 0 0;
        }
        .section-title {
            color: #2A3D66;
            font-weight: 600;
            font-size: 1.1rem;
            margin: 15px 0 8px;
            padding-bottom: 4px;
            border-bottom: 2px solid #FF9F45;
        }
        .detail-item {
            font-size: 0.9rem;
            margin-bottom: 6px;
            color: #333;
        }
        .detail-item strong {
            color: #2A3D66;
            font-weight: 500;
        }
        .table-receipt {
            background: #ffffff;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 15px;
        }
        .table-receipt th {
            background-color: #2A3D66;
            color: #ffffff;
            font-weight: 500;
            padding: 8px;
            font-size: 0.85rem;
        }
        .table-receipt td {
            padding: 8px;
            font-size: 0.85rem;
            vertical-align: middle;
        }
        .total-row {
            font-weight: 600;
            font-size: 1rem;
            color: #2A3D66;
        }
        .receipt-footer {
            text-align: center;
            font-size: 0.8rem;
            color: #777;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #e0e0e0;
        }
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 15px;
        }
        .print-btn, .dashboard-btn-footer {
            background-color: #2A3D66;
            color: #fff;
            padding: 8px 15px;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 500;
            text-align: center;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
        }
        .dashboard-btn-footer {
            text-decoration: none;
        }
        .print-btn:hover, .dashboard-btn-footer:hover {
            background-color: #FF9F45;
            color: #2A3D66;
        }
        .navbar-nav .nav-link:hover {
            color: #FF9F45 !important;
        }
        .navbar-nav .nav-link.active {
            color: #FF9F45 !important;
        }
        .navbar-nav .nav-link {
            font-size: 23px;
            font-weight: 500;
        }
        .navbar-nav .nav-link.disabled {
            color: #FF9F45 !important;
            opacity: 1;
        }
        /* Sidebar styles */
        .sidebar {
            background-color: rgb(50, 69, 111);
            position: fixed;
            top: 80px;
            left: 0;
            width: 220px;
            height: calc(100vh - 80px);
            overflow-y: auto;
            padding-top: 1rem;
            z-index: 1030;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .sidebar .nav-link {
            color: white !important;
            font-size: 22px;
            font-weight: 500;
            padding: 12px 20px;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }
        .sidebar .nav-link:hover {
            color: #FF9F45 !important;
        }
        .sidebar .nav-link.active,
        .sidebar .nav-link.disabled {
            color: #FF9F45 !important;
            background-color: transparent;
            opacity: 1;
            pointer-events: none;
        }
        /* Push main content right */
        .main-content {
            margin-left: 240px;
            max-width: calc(100% - 240px);
            overflow-x: hidden;
        }
        /* Responsive: hide sidebar on small screens */
        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
                top: auto;
                border-radius: 0;
            }
            .main-content {
                margin-left: 0;
            }
            .receipt-card {
                margin-top: 20px;
            }
        }
        /* Print styles */
        @media print {
            .navbar, .sidebar, .action-buttons {
                display: none !important;
            }
            .main-content {
                margin-left: 0;
                max-width: 100%;
            }
            .receipt-card {
                box-shadow: none;
                border: none;
                margin: 0;
                padding: 8px;
            }
            .receipt-header {
                margin: 0;
                border-radius: 0;
                padding: 8px;
            }
            .section-title {
                margin: 10px 0 6px;
                font-size: 1rem;
            }
            .detail-item, .table-receipt th, .table-receipt td {
                font-size: 0.8rem;
            }
            .receipt-footer {
                margin-top: 10px;
                padding-top: 8px;
            }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="cashier_dashboard.php">MediTrack</a>
        <div class="d-flex">
            <div class="me-3 text-white d-flex align-items-center">
                <img src="<?php echo $profilePic ? $profilePic : $defaultProfilePic; ?>" alt="Cashier Profile" class="profile-img me-2"
                     onerror="this.onerror=null; this.src='<?php echo $defaultProfilePic; ?>';">
                <span><?php echo $adminName; ?></span>
            </div>
            <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>
        </div>
    </div>
</nav>

<!-- Sidebar (Vertical Navbar) -->
<nav class="sidebar navbar navbar-expand-lg p-3">
    <ul class="navbar-nav flex-column w-100">
        <li class="nav-item mb-3">
            <a class="nav-link" href="sell_medicine.php">Sell Medicine</a>
        </li>
        <li class="nav-item mb-3">
            <a class="nav-link" href="view_medicines.php">View Medicine</a>
        </li>
        <li class="nav-item mb-3">
            <a class="nav-link" href="sales_history.php">Sales History</a>
        </li>
        <li class="nav-item mb-3">
            <a class="nav-link" href="view_customers.php">View Customers</a>
        </li>
    </ul>
    <button class="dashboard-btn" onclick="window.location.href='cashier_dashboard.php'">Dashboard</button>
</nav>

<!-- Main Content -->
<div class="container main-content" style="padding-top: 120px;">
    <div class="receipt-card mx-auto" style="max-width: 500px;">
        <div class="receipt-header">
            <h1>MediTrack Receipt</h1>
            <p>Customer ID: <?php echo htmlspecialchars($customer['id']); ?></p>
            <p>Date: <?php echo date("Y-m-d"); ?></p>
            <p>Time: <?php echo date("H:i:s"); ?></p>
        </div>

        <div class="receipt-body">
            <h4 class="section-title">Customer Details</h4>
            <div class="detail-item"><strong>Name:</strong> <?php echo htmlspecialchars($customer['name']); ?></div>
            <div class="detail-item"><strong>Phone:</strong> <?php echo htmlspecialchars($customer['phone']); ?></div>
            <div class="detail-item"><strong>Email:</strong> <?php echo htmlspecialchars($customer['email'] ?: 'N/A'); ?></div>

            <h4 class="section-title">Medicines Sold</h4>
            <table class="table table-receipt">
                <thead>
                    <tr>
                        <th>Medicine</th>
                        <th>Price (PKR)</th>
                        <th>Qty</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_amount = 0;
                    foreach ($sales as $sale): 
                        $total_amount += $sale['total_price'];
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($sale['medicine_name']); ?></td>
                            <td><?php echo number_format($sale['price'], 2); ?></td>
                            <td><?php echo $sale['quantity_sold']; ?></td>
                            <td><?php echo number_format($sale['total_price'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="3" class="text-end"><strong>Grand Total (PKR):</strong></td>
                        <td><?php echo number_format($total_amount, 2); ?></td>
                    </tr>
                </tbody>
            </table>

            <h4 class="section-title">Sold By</h4>
            <div class="detail-item"><?php echo htmlspecialchars($adminName); ?></div>
        </div>

        <div class="receipt-footer">
            © <?php echo date('Y'); ?> MediTrack | Thank you for your purchase!
        </div>

        <div class="action-buttons">
            <button class="print-btn" onclick="window.print()">Print Receipt</button>
            <a href="cashier_dashboard.php" class="dashboard-btn-footer">Go to Dashboard</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>