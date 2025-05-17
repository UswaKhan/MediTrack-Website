<?php
require_once 'security.php';
require_once 'Database.php';
require_once 'SessionManager.php';
require_once 'AccessControl.php';

// Prevent caching of this page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

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

// Generate CSRF token
$csrf_token = generateCSRFToken();

// Initialize other managers
$access = AccessControl::getInstance();

// Check if user is logged in and has permission to add users
$access->enforceAccess('add_user');

$success = false;
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Verify CSRF token
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            throw new Exception("Invalid request");
        }

        // Sanitize and validate input
        $username = $db->sanitize($_POST['username']);
        $password = $_POST['password'];
        $role = $db->sanitize($_POST['role']);
        $email = $db->sanitize($_POST['email']);
        // Validate email format (server-side)
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Invalid email format. Please use example@example.com.";
        }

        $phone = $db->sanitize($_POST['phone']);

        // Validate username (letters, numbers, underscores, 3-20 characters)
        if (!preg_match('/^[A-Za-z0-9_]{3,20}$/', $username)) {
            $errors['username'] = "Username must contain only letters, numbers, or underscores and be 3–20 characters long";
        }

        // Validate role (prevent privilege escalation)
        if (!in_array($role, ['admin', 'cashier'])) {
            throw new Exception("Invalid role specified");
        }

        // Validate password length and complexity
        if (strlen($password) < 8 || !preg_match('/[0-9]/', $password)) {
            $errors['password'] = "Password must be at least 8 characters long and contain at least one number";
        } else {
            // Check if username exists using prepared statement
            $existingUser = $db->select("SELECT id FROM admin WHERE username = ?", "s", [$username]);
            
            if (!empty($existingUser)) {
                $errors['username'] = "Username already exists";
            }
        }

        if (!preg_match('/^\d{11}$/', $phone)) {
            $errors['phone'] = "Phone number must be exactly 11 digits.";
        }

        if (empty($errors)) {
            $profilePicPath = NULL;
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
                $targetDir = "Uploads/";
                if (!is_dir($targetDir)) mkdir($targetDir);
                $filename = uniqid() . "_" . basename($_FILES["profile_picture"]["name"]);
                $targetFilePath = $targetDir . $filename;
                move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFilePath);
                $profilePicPath = $targetFilePath;
            }

            // Hash password
            $hashed_password = hashPassword($password);

            // Insert new user
            $userData = [
                'username' => $username,
                'password' => $hashed_password,
                'role' => $role,
                'email' => $email,
                'phone' => $phone,
                'profile_picture' => $profilePicPath
            ];

            $db->insert('admin', $userData);
            $success = true;
        }
    } catch (Exception $e) {
        $error = "An error occurred: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Add User</title>
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
      <a class="nav-link" href="add_medicine.php">Add Medicine</a>
    </li>
    <li class="nav-item mb-3">
      <a class="nav-link" href="view_medicines.php">View Medicine</a>
    </li>
    <li class="nav-item mb-3">
      <a class="nav-link active disabled" aria-disabled="true" href="add_user.php">Add User</a>
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
  <h2 class="text-center fw-bold mb-4">Add New User</h2>

  <?php if (isset($error)): ?>
    <div class="alert alert-danger mx-auto alert-dismissible fade show" style="max-width: 600px;" role="alert">
      <?php echo htmlspecialchars($error); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="success-box mx-auto alert alert-success alert-dismissible fade show" style="max-width: 600px;" role="alert">
      <h5>🎉 User Added Successfully!</h5>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <div class="form-container mx-auto" style="max-width: 600px;">
    <form method="POST" enctype="multipart/form-data" autocomplete="off">
      <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input name="username" class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" 
               required autocomplete="off" 
               pattern="^[A-Za-z0-9_]{3,20}$"
               title="Username must contain only letters, numbers, or underscores and be 3–20 characters long"
               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
               oninput="validateUsername(this)">
        <?php if (isset($errors['username'])): ?>
          <div class="invalid-feedback">
            <?php echo htmlspecialchars($errors['username']); ?>
          </div>
        <?php endif; ?>
        <small class="text-muted">Username must contain only letters, numbers, or underscores and be 3–20 characters long</small>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input name="password" type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
               required minlength="8" pattern="(?=.*\d).{8,}" 
               title="Password must be at least 8 characters long and contain at least one number"
               autocomplete="new-password"
               oninput="validatePassword(this)">
        <?php if (isset($errors['password'])): ?>
          <div class="invalid-feedback">
            <?php echo htmlspecialchars($errors['password']); ?>
          </div>
        <?php endif; ?>
        <small class="text-muted">Password must be at least 8 characters long and contain at least one number</small>
      </div>
      <div class="mb-3">
        <label class="form-label">Role</label>
        <select name="role" class="form-control" required>
          <option value="admin">Admin</option>
          <option value="cashier">Cashier</option>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input name="email" type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
               required pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
               title="Please enter a valid email address like example@example.com"
               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
               oninput="validateEmail(this)">
        <?php if (isset($errors['email'])): ?>
          <div class="invalid-feedback">
            <?php echo htmlspecialchars($errors['email']); ?>
          </div>
        <?php endif; ?>
        <small class="text-muted">Enter a valid email address</small>
      </div>
      <div class="mb-3">
        <label class="form-label">Phone Number</label>
        <input name="phone" type="text" class="form-control <?php echo isset($errors['phone']) ? 'is-invalid' : ''; ?>" 
               required pattern="^\d{11}$" maxlength="11"
               title="Phone number must be exactly 11 digits"
               value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
               oninput="validatePhone(this)">
        <?php if (isset($errors['phone'])): ?>
          <div class="invalid-feedback">
            <?php echo htmlspecialchars($errors['phone']); ?>
          </div>
        <?php endif; ?>
        <small class="text-muted">Phone number must be exactly 11 digits</small>
      </div>
      <div class="mb-3">
        <label class="form-label">Profile Picture (optional)</label>
        <input type="file" name="profile_picture" class="form-control" accept="image/*">
      </div>
      <button type="submit" class="btn btn-custom w-100">Add User</button>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Validate username (letters, numbers, underscores, 3-20 characters)
  function validateUsername(input) {
    const errorElement = input.nextElementSibling;
    const pattern = /^[A-Za-z0-9_]{3,20}$/;
    if (!pattern.test(input.value)) {
      input.classList.add('is-invalid');
      errorElement.classList.add('invalid-feedback');
      errorElement.textContent = 'Username must contain only letters, numbers, or underscores and be 3–20 characters long';
    } else {
      input.classList.remove('is-invalid');
      errorElement.textContent = '';
    }
  }

  // Validate password (at least 8 characters, one number)
  function validatePassword(input) {
    const errorElement = input.nextElementSibling;
    const pattern = /(?=.*\d).{8,}/;
    if (!pattern.test(input.value)) {
      input.classList.add('is-invalid');
      errorElement.classList.add('invalid-feedback');
      errorElement.textContent = 'Password must be at least 8 characters long and contain at least one number';
    } else {
      input.classList.remove('is-invalid');
      errorElement.textContent = '';
    }
  }

  // Validate email
  function validateEmail(input) {
    const errorElement = input.nextElementSibling;
    const pattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (!pattern.test(input.value)) {
      input.classList.add('is-invalid');
      errorElement.classList.add('invalid-feedback');
      errorElement.textContent = 'Invalid email format. Please use example@example.com';
    } else {
      input.classList.remove('is-invalid');
      errorElement.textContent = '';
    }
  }

  // Validate phone (exactly 11 digits)
  function validatePhone(input) {
    const errorElement = input.nextElementSibling;
    const pattern = /^\d{11}$/;
    if (!pattern.test(input.value)) {
      input.classList.add('is-invalid');
      errorElement.classList.add('invalid-feedback');
      errorElement.textContent = 'Phone number must be exactly 11 digits';
    } else {
      input.classList.remove('is-invalid');
      errorElement.textContent = '';
    }
  }

  // Check for logout flag every 2 seconds
  function checkLogout() {
    if (localStorage.getItem('loggedOut') === 'true') {
      window.location.href = 'index.php';
    }
  }
  setInterval(checkLogout, 2000);
</script>
</body>
</html>