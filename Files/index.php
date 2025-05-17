<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include security headers
require_once 'security_headers.php';

$current_path = dirname($_SERVER['SCRIPT_NAME']);
$base_path = rtrim($current_path, '/');

require_once 'security.php';
require_once 'SessionManager.php';

// Initialize session manager
$session = SessionManager::getInstance();

// Generate CSRF token
$csrf_token = generateCSRFToken();
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Login</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background: linear-gradient(to right, #f8f9fa, #e3e3e3);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .login-container {
      background-color: #ffffff;
      border-radius: 10px;
      padding: 40px 30px;
      max-width: 400px;
      width: 100%;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease-in-out;
    }

    .login-container:hover {
      transform: scale(1.01);
    }

    h3 {
      text-align: center;
      margin-bottom: 20px;
      color: #2A3D66;
      font-weight: bold;
      font-size: 30px;
    }

    .form-group label {
      font-weight: 600;
      color: #495057;
      font-size: 20px;
      margin-bottom: 20px;

    }

    .form-control {
      border-radius: 8px;
      border: 1px solid #ced4da;
      transition: border-color 0.2s;
      font-size: 16px;
      margin-bottom: 20px;
      padding: 5px 10px;
      margin-left: 20px;
    }

    .form-control:focus {
      border-color: #FF9F45;
      box-shadow: 0 0 5px rgba(255, 159, 69, 0.3);
    }

    .btn-primary {
      background-color: #FF9F45;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      padding: 10px 50px;
      font-size: 20px;
      margin-left: 120px;
      margin-top: 20px;
      color: white;
    }

    .btn-primary:hover {
      background-color: #2A3D66;
    }

    small.text-danger {
      font-size: 0.8rem;
    }
  </style>
</head>
<body>

<div class="login-container">
  <h3>Admin Login</h3>
  <form action="<?php echo htmlspecialchars($base_path . '/login.php'); ?>" method="POST" onsubmit="return validateForm()">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
    
    <div class="form-group">
      <label for="username">Username</label>
      <input type="text" class="form-control" id="username" name="username" required 
             pattern="[a-zA-Z0-9_]{3,20}" 
             title="Username must be between 3 and 20 characters and can only contain letters, numbers, and underscores"
             oninput="validateUsername(this)">
      <small class="text-danger" id="username-error"></small>
    </div>

    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" class="form-control" id="password" name="password" required 
             minlength="8" pattern="(?=.*\d).{8,}" 
             title="Password must be at least 8 characters long and contain at least one number"
             oninput="validatePassword(this)">
      <small class="text-danger" id="password-error"></small>
    </div>

    <button type="submit" class="btn btn-primary btn-block">Login</button>
  </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
function validateUsername(input) {
    const error = document.getElementById('username-error');
    const pattern = /^[a-zA-Z0-9_]{3,20}$/;

    if (!pattern.test(input.value)) {
        error.textContent = 'Username must be between 3 and 20 characters and can only contain letters, numbers, and underscores';
        return false;
    }
    error.textContent = '';
    return true;
}

function validatePassword(input) {
    const error = document.getElementById('password-error');
    const pattern = /^(?=.*\d).{8,}$/;

    if (!pattern.test(input.value)) {
        error.textContent = 'Password must be at least 8 characters long and contain at least one number';
        return false;
    }
    error.textContent = '';
    return true;
}

function validateForm() {
    const username = document.getElementById('username');
    const password = document.getElementById('password');
    return validateUsername(username) && validatePassword(password);
}
</script>

</body>
</html>
