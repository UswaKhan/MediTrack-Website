<?php
require_once 'SessionManager.php';
require_once 'Database.php';

// Initialize session manager
$session = SessionManager::getInstance();

// Check if the admin is logged in
if (!$session->isLoggedIn()) {
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

// Extract user data
$adminName = $user['username'];
$profilePic = $user['profile_picture'];
$defaultProfilePic = 'profile1.webp';

// Set success variable initially false
$success = false;
$expiryError = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $qty = $_POST['quantity'];
    $expiry = $_POST['expiry_date'];
    $price = $_POST['price'];

    $today = date('Y-m-d');

    if ($expiry < $today) {
       $expiryError = "❌ Cannot add expired medicine. Please select a valid expiry date.";
    } else {
        $stmt = $conn->prepare("INSERT INTO medicines (name, description, quantity, expiry_date, price) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $name, $desc, $qty, $expiry, $price);

        if ($stmt->execute()) {
            $success = true;
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Add Medicine</title>
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
      box-shadow: 0 6px 18px rgba(0,0,0,0.15); margin-bottom: 128.5px;
    }
    h2 { color: #2A3D66; }
    .btn-custom {
      background-color: #2A3D66; color: #ffffff;
      border-radius: 8px; padding: 10px 20px;
      font-weight: bold; transition: background-color 0.3s ease;
    }
    .btn-custom:hover { background-color: #FF9F45; color: #2A3D66; }
    .success-box {
      background-color: #d4edda; border: 1px solid #c3e6cb;
      color: #155724; padding: 15px;
      border-radius: 8px; margin-bottom: 20px;
      position: relative;
    }
    .success-box .btn-close {
      position: absolute;
      top: 10px;
      right: 10px;
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
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="admin_dashboard.php">MediTrack</a>
    <div class="d-flex">
      <div class="me-3 text-white d-flex align-items-center">
        <img src="<?php echo $profilePic ? $profilePic : $defaultProfilePic; ?>" alt="Admin Profile" class="profile-img me-2"
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
      <a class="nav-link active disabled" aria-disabled="true" href="add_medicine.php">Add Medicine</a>
    </li>
    <li class="nav-item mb-3">
      <a class="nav-link" href="view_medicines.php">View Medicine</a>
    </li>
    <li class="nav-item mb-3">
      <a class="nav-link" href="add_user.php">Add User</a>
    </li>
    <li class="nav-item mb-3">
      <a class="nav-link" href="view_users.php">View User</a>
    </li>
    <li class="nav-item mb-3">
      <a class="nav-link" href="sales_history.php">Sales History</a>
    </li>
    <li class="nav-item mb-3">
      <a class="nav-link" href="view_customers.php">View Customers</a>
    </li>
  </ul>
  <button class="dashboard-btn" onclick="window.location.href='admin_dashboard.php'">Dashboard</button>
</nav>

<!-- Main Content -->
<div class="container main-content" style="padding-top: 120px;">
  <h2 class="text-center fw-bold mb-4">Add Medicine</h2>

  <?php if ($success): ?>
    <div class="success-box mx-auto alert alert-success alert-dismissible fade show" style="max-width: 600px;" role="alert">
      <h5>🎉 Medicine Added Successfully!</h5>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <div class="form-container mx-auto" style="max-width: 600px;">
    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Name</label>
        <input name="name" class="form-control" required pattern="[A-Za-z\s]+" title="Only alphabets and spaces are allowed.">
      </div>
      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" required></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Quantity</label>
        <input type="number" name="quantity" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Expiry Date</label>
        <input type="date" name="expiry_date" class="form-control <?php echo !empty($expiryError) ? 'is-invalid' : ''; ?>" 
               value="<?php echo isset($_POST['expiry_date']) ? htmlspecialchars($_POST['expiry_date']) : ''; ?>" required>
        <?php if (!empty($expiryError)): ?>
          <div class="invalid-feedback"><?php echo $expiryError; ?></div>
        <?php endif; ?>
      </div>
      <div class="mb-3">
        <label class="form-label">Price (PKR)</label>
        <input type="number" step="0.01" name="price" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-custom w-100">Add Medicine</button>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.querySelector("form").addEventListener("submit", function (e) {
    const expiryInput = document.querySelector("input[name='expiry_date']");
    const selectedDate = new Date(expiryInput.value);
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    if (selectedDate < today) {
      alert("Expiry date cannot be in the past.");
      e.preventDefault();
    }
  });
</script>
</body>
</html>