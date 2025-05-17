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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sell_medicine'])) {
    // Sanitize and validate customer input
    $customer_name = $db->sanitize($_POST['customer_name']);
    $customer_phone = $db->sanitize($_POST['customer_phone']);
    $customer_email = filter_var($_POST['customer_email'], FILTER_SANITIZE_EMAIL);

    // Validate customer input
    if (empty($customer_name) || empty($customer_phone)) {
        echo "<script>alert('Customer name and phone are required');</script>";
        exit();
    }

    if (!empty($customer_email) && !filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format');</script>";
        exit();
    }

    // Insert or fetch customer using prepared statement
    $existing_customer = $db->select("SELECT * FROM customers WHERE phone = ?", "s", [$customer_phone]);
    if (empty($existing_customer)) {
        $customerData = [
            'name' => $customer_name,
            'phone' => $customer_phone,
            'email' => $customer_email
        ];
        $customer_id = $db->insert('customers', $customerData);
    } else {
        $customer_id = $existing_customer[0]['id'];
    }

    $total_sale_amount = 0;
    $success = true;

    $medicine_ids = $_POST['medicine_id'];
    $quantities = $_POST['quantity'];

    foreach ($medicine_ids as $index => $med_id) {
        $qty = (int)$quantities[$index];
        $med_id = (int)$med_id;

        if ($med_id <= 0 || $qty <= 0) {
            echo "<script>alert('Invalid medicine or quantity selected');</script>";
            $success = false;
            break;
        }

        // Get medicine details using prepared statement
        $med = $db->select("SELECT * FROM medicines WHERE id = ?", "i", [$med_id]);
        if (!empty($med) && $med[0]['quantity'] >= $qty) {
            $price = $med[0]['price'];
            $total_price = $price * $qty;

            // Update medicine quantity using prepared statement
            $db->query("UPDATE medicines SET quantity = quantity - ? WHERE id = ?", "ii", [$qty, $med_id]);
            
            // Insert sale record using prepared statement
            $saleData = [
                'medicine_id' => $med_id,
                'quantity_sold' => $qty,
                'sold_by' => $username,
                'customer_id' => $customer_id,
                'total_price' => $total_price
            ];
            $db->insert('sales', $saleData);
            $total_sale_amount += $total_price;
        } else {
            $success = false;
            echo "<script>alert('Not enough stock for medicine: ".$med[0]['name']."');</script>";
            break;
        }
    }

    if ($success) {
        header("Location: receipt.php?customer_id=$customer_id");
        exit();
    }
}

$customers = $conn->query("SELECT * FROM customers");
$medicines = $conn->query("SELECT * FROM medicines");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sell Medicine</title>
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
    .form-container {
      background-color: #F9F9F9; padding: 30px; border-radius: 15px;
      box-shadow: 0 6px 18px rgba(0,0,0,0.15); margin-bottom: 100px;
    }
    h2 { color: #2A3D66; }
    .btn-custom {
      background-color: #2A3D66; color: #ffffff;
      border-radius: 8px; padding: 10px 20px;
      font-weight: bold; transition: background-color 0.3s ease;
    }
    .btn-custom:hover { background-color: #FF9F45; color: #2A3D66; }
    .autocomplete-items {
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      border: 1px solid #d4d4d4;
      max-height: 150px;
      overflow-y: auto;
      background-color: white;
      z-index: 99;
      border-radius: 5px;
      box-shadow: 0px 4px 8px rgba(0,0,0,0.1);
      width: 94%;
      margin-left: 10px;
    }
    .autocomplete-items, .empty {
      border: none;
    }
    .autocomplete-item:hover { background-color: #ddd; }
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
      <a class="nav-link active disabled" aria-disabled="true" href="sell_medicine.php">Sell Medicine</a>
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
  <h2 class="text-center fw-bold mb-4">Sell Medicines</h2>

  <div class="form-container mx-auto" style="max-width: 800px;">
    <form method="POST">
      <input type="hidden" name="sell_medicine" value="1">

      <!-- Customer Info Fields -->
      <div class="mb-3">
        <label class="form-label">Customer Name</label>
        <input type="text" name="customer_name" class="form-control" required pattern="[A-Za-z ]+" title="Only alphabets and spaces are allowed">
      </div>
      <div class="mb-3">
        <label class="form-label">Phone</label>
        <input type="text" name="customer_phone" class="form-control"
               required pattern="\d{11}" maxlength="11" inputmode="numeric"
               title="Phone number must be exactly 11 digits">
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="customer_email" class="form-control" id="customer_email">
        <div id="emailError" class="text-danger mt-1" style="font-size: 0.9rem;"></div>
      </div>

      <!-- Medicine Section -->
      <div id="medicine-container">
        <div class="medicine-row row mb-3">
          <div class="col-md-5 position-relative">
            <label class="form-label">Medicine Name</label>
            <input type="text" name="medicine_name[]" class="form-control medicine-name" autocomplete="off" required>
            <div class="autocomplete-items"></div>
            <input type="hidden" name="medicine_id[]" class="medicine-id">
          </div>
          <div class="col-md-3">
            <label class="form-label">Price (PKR)</label>
            <input type="text" name="medicine_price[]" class="form-control medicine-price" readonly>
          </div>
          <div class="col-md-3">
            <label class="form-label">Quantity</label>
            <input type="number" name="quantity[]" class="form-control" min="1" required>
          </div>
          <div class="col-md-1 d-flex align-items-end">
            <button type="button" class="btn btn-danger btn-sm remove-medicine">×</button>
          </div>
        </div>
      </div>

      <button type="button" class="btn btn-secondary mb-3" id="add-medicine">Add Another Medicine</button>

      <button type="submit" class="btn btn-custom w-100">Sell Medicines</button>
    </form>
  </div>
</div>

<!-- Footer -->
<footer class="mt-5">
  <div class="text-center p-4" style="background-color: #2A3D66; color: white;">
    © 2025 MediTrack Cashier Dashboard | Designed by Uswa Khan
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const medicines = <?php echo json_encode($medicines->fetch_all(MYSQLI_ASSOC)); ?>;

// Autocomplete
function setupAutocomplete(container) {
  const input = container.querySelector(".medicine-name");
  const idField = container.querySelector(".medicine-id");
  const priceField = container.querySelector(".medicine-price");
  const autocompleteList = container.querySelector(".autocomplete-items");

  input.addEventListener("input", function() {
    const query = this.value.toLowerCase();
    autocompleteList.innerHTML = '';

    if (!query) return;

    const filtered = medicines.filter(m => m.name.toLowerCase().includes(query));
    filtered.forEach(med => {
      const item = document.createElement("div");
      item.classList.add("autocomplete-item");
      item.textContent = med.name;
      item.addEventListener("click", function() {
        input.value = med.name;
        idField.value = med.id;
        priceField.value = med.price;
        autocompleteList.innerHTML = '';
      });
      autocompleteList.appendChild(item);
    });
  });
}

// Initial setup
document.querySelectorAll(".medicine-row").forEach(setupAutocomplete);

// Add more medicines
document.getElementById("add-medicine").addEventListener("click", function() {
  const container = document.getElementById("medicine-container");
  const newRow = container.firstElementChild.cloneNode(true);

  // Clear the cloned inputs
  newRow.querySelectorAll("input").forEach(input => {
    input.value = '';
  });
  newRow.querySelector(".autocomplete-items").innerHTML = '';

  container.appendChild(newRow);
  setupAutocomplete(newRow);
});

// Remove medicine row
document.addEventListener("click", function(e) {
  if (e.target.classList.contains("remove-medicine")) {
    const rows = document.querySelectorAll(".medicine-row");
    if (rows.length > 1) {
      e.target.closest(".medicine-row").remove();
    }
  }
});

document.querySelector('input[name="customer_name"]').addEventListener('input', function(e) {
  this.value = this.value.replace(/[^A-Za-z ]/g, '');
});

document.querySelector('input[name="customer_phone"]').addEventListener('input', function(e) {
  this.value = this.value.replace(/\D/g, '').slice(0, 11);
});

// Email format validation on form submit
document.querySelector("form").addEventListener("submit", function(e) {
  const emailInput = document.getElementById("customer_email");
  const emailError = document.getElementById("emailError");
  const emailValue = emailInput.value.trim();

  emailError.textContent = ""; // Clear previous error

  if (emailValue !== "") {
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(emailValue)) {
      e.preventDefault(); // Stop form submission
      emailError.textContent = "Please enter a valid email like example@example.com.";
      emailInput.focus();
    }
  }
});
</script>
</body>
</html>