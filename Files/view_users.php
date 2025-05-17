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

// Default profile picture in case the user has not uploaded one
$defaultProfilePic = 'profile1.webp';

// Delete user if delete button clicked
if (isset($_GET['delete_id']) && $_SESSION['role'] === 'admin') {
    $deleteId = intval($_GET['delete_id']);
    $db->query("DELETE FROM admin WHERE id = ?", "i", [$deleteId]);
    header("Location: view_users.php"); // Refresh page after delete
    exit();
}

// Fetch all users
$result = $conn->query("SELECT * FROM admin");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>View Users</title>
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
    .table-profile-img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
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
    table { background: #ffffff; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    th { background-color: #2A3D66; color: white; }
    .btn-back {
      background-color: #FF9F45; color: white; border: none;
      padding: 8px 16px; border-radius: 8px; font-weight: 600; margin-top: 20px;
    }
    .btn-back:hover { background-color: #2A3D66; color: white; }
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
      <a class="nav-link" href="add_medicine.php">Add Medicine</a>
    </li>
    <li class="nav-item mb-3">
      <a class="nav-link" href="view_medicines.php">View Medicine</a>
    </li>
    <li class="nav-item mb-3">
      <a class="nav-link" href="add_user.php">Add User</a>
    </li>
    <li class="nav-item mb-3">
      <a class="nav-link active disabled" aria-disabled="true" href="view_users.php">View User</a>
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

<!-- Table -->
<div class="container main-content" style="padding-top: 120px;">
  <h2 class="text-center mb-4 fw-bold" style="color: #2A3D66;">All Users</h2>
  <div class="table-responsive">
    <table class="table table-bordered">
      <thead class="text-center">
        <tr>
          <th>Profile Picture</th>
          <th>Username</th>
          <th>Role</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody class="text-center">
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td>
              <img src="<?php echo $row['profile_picture'] ? $row['profile_picture'] : $defaultProfilePic; ?>" 
                   alt="User Profile" 
                   class="table-profile-img"
                   onerror="this.onerror=null; this.src='<?php echo $defaultProfilePic; ?>';">
            </td>
            <td><?php echo htmlspecialchars($row['username']); ?></td>
            <td><?php echo htmlspecialchars($row['role']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['phone']); ?></td>
            <td>
              <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="view_users.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm"
                   onclick="return confirm('Are you sure you want to delete this user?');">
                  Delete
                </a>
              <?php else: ?>
                <span class="text-muted">N/A</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>