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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cashier Dashboard</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .navbar {
      background-color: #2A3D66;
      height: 80px;
    }
    .navbar-brand {
      color: white !important;
      font-size: 1.5rem;
      margin-left: 10px;
    }
    .nav-link {
      color: white !important;
    }
    .profile-img {
      width: 50px;
      height: 50px;
      border-radius: 50%;
    }
    .logout-btn {
      color: #FF9F45;
      background-color: #2A3D66;
      border: 1px solid #FF9F45;
      border-radius: 5px;
      font-size: 1rem;
      padding: 8px 16px;
      margin-left: 10px;
      transition: all 0.3s ease;
      font-weight: 500;
      margin-right: 10px;
    }
    .logout-btn:hover {
      background-color: #FF9F45;
      color: #2A3D66;
      border-color: #2A3D66;
    }
    .carousel-img {
      height: 500px;
      object-fit: cover;
    }
    .card-custom {
      background-color: #2A3D66;
      color: white;
      border: none;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      border-radius: 15px;
    }
    .card-custom:hover {
      transform: translateY(-10px);
      box-shadow: 0 8px 16px rgba(0,0,0,0.3);
      background-color: #FF9F45;
      color: #2A3D66;
    }
    .card-title {
      font-size: 1.3rem;
    }
    .banner {
      height: 500px;
      background-image: url('Untitled design.png');
      background-size: cover;
      background-position: center;
    }

  .navbar-nav .nav-link:hover {
    color: #FF9F45 !important;
  }
  .navbar-nav .nav-link.active {
    color: #FF9F45 !important;
    
  }

  .navbar-nav .nav-link {
    font-size: 23px; /* Increased font size */
    font-weight: 500; /* Slightly bold */
  }

  .navbar-nav .nav-link.disabled {
    color: #FF9F45 !important; /* Disabled link will also be orange */
    opacity: 1; /* Remove Bootstrap's default grey-out */
  }

  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">
      <!--<img src="#"  class="logo-img me-2" style="height: 100px;"> --><!-- Adjust size as needed -->
      MediTrack</a>
    <div class="d-flex">
      <!-- Casier name and profile picture -->
      <div class="me-3 text-white d-flex align-items-center">
        <img src="<?php echo $profilePic ? $profilePic : $defaultProfilePic; ?>" alt="Admin Profile" class="profile-img me-2" 
             onerror="this.onerror=null; this.src='<?php echo $defaultProfilePic; ?>';">
        <span><?php echo $adminName; ?></span> <!-- Display logged-in admin's name -->
      </div>
      <!-- Logout button -->
      <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>
    </div>
  </div>
</nav>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg" style="background-color:rgb(50, 69, 111);">
  <div class="container-fluid">
    <div class="collapse navbar-collapse show" id="navbarLinks">
      <ul class="navbar-nav mx-auto">
        <li class="nav-item">
          <a class="nav-link text-white mx-3" href="sell_medicine.php">Sell Medicine</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white mx-3" href="view_medicines.php">View Medicine</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white mx-3" href="sales_history.php">Sales History</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white mx-3" href="view_customers.php">View Customers</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="banner"></div>

<!-- Carousel Section -->
<!-- <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="b4.png" class="d-block w-100 carousel-img" alt="First Slide">
    </div>
    <div class="carousel-item">
      <img src="b5.png" class="d-block w-100 carousel-img" alt="Second Slide">
    </div>
    <div class="carousel-item">
      <img src="b7.png" class="d-block w-100 carousel-img" alt="Third Slide">
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div> -->

<!-- Admin Dashboard Headline -->
<div class="container my-4">
  <h2 class="text-center fw-bold" style="color: #2A3D66;">Welcome to Cashier Dashboard</h2>
</div>

<!-- Cards Section -->
<div class="container my-5">
  <div class="row g-4 justify-content-center">
    <div class="col-md-6 col-lg-6">
      <a href="sell_medicine.php" class="text-decoration-none">
        <div class="card text-center card-custom shadow">
          <div class="card-body">
          <img src="add_medicine.png" class="img-fluid mb-3" style="max-height: 100px;" alt="View Customers">
            <h5 class="card-title fw-bold">Sell Medicine</h5>
          </div>
        </div>
      </a>
    </div>
    <div class="col-md-6 col-lg-6">
      <a href="view_medicines.php" class="text-decoration-none">
        <div class="card text-center card-custom shadow">
          <div class="card-body">
          <img src="view_medicines.png" class="img-fluid mb-3" style="max-height: 100px;" alt="View Customers">
            <h5 class="card-title fw-bold">View Medicines</h5>
          </div>
        </div>
      </a>
    </div>
    <div class="col-md-6 col-lg-6">
      <a href="sales_history.php" class="text-decoration-none">
        <div class="card text-center card-custom shadow">
          <div class="card-body">
          <img src="sales_history.png" class="img-fluid mb-3" style="max-height: 100px;" alt="View Customers">
            <h5 class="card-title fw-bold">Sales History</h5>
          </div>
        </div>
      </a>
    </div>
    <div class="col-md-6 col-lg-6">
      <a href="view_customers.php" class="text-decoration-none">
        <div class="card text-center card-custom shadow">
          <div class="card-body">
          <img src="view_customers.png" class="img-fluid mb-3" style="max-height: 100px;" alt="View Customers">
            <h5 class="card-title fw-bold">View Customers</h5>
          </div>
        </div>
      </a>
    </div>
  </div>
</div>

<!-- About Section -->
<div class="container my-5">
  <div class="p-4 rounded shadow-sm" style="background-color: #F9F9F9;">
    <h3 class="mb-3" style="color: #2A3D66;">About MediTrack</h3>
    <p style="color: #333; line-height: 1.7;">
      The MediTrack is an intuitive and efficient platform designed to simplify the operations of pharmacies and healthcare businesses. This dashboard enables cashiers to manage medicine sales, view medicines, and track customers efficiently.
      <br><br>
      With real-time updates, secure data handling, and role-based access, the system ensures that only authorized personnel can perform specific actions. It also provides smart search capabilities, detailed sales history, and customer tracking to improve service quality and business decision-making.
      <br><br>
      Built using modern web technologies like HTML, CSS, Bootstrap, and PHP, this system is optimized for performance and scalability. Whether you are running a small clinic or a large medical store, this solution is tailored to adapt and grow with your business.
    </p>
  </div>
</div>

<!-- Some space before footer -->
<div style="height: 20px;"></div>

<!-- Main Footer Content -->
<footer class="site-footer" style="background-color:rgb(50, 69, 110); color: white; padding-top: 50px; padding-bottom: 50px;">
    <div class="container">
        <div class="footer-content">
            <div class="row g-5">
                <!-- First Column - About MediTrack -->
                <div class="col-md-6 col-lg-4">
                    <div class="footer-col">
                        <h4 class="footer-title" style="color: #fff; margin-bottom: 20px;">About MediTrack</h4>
                        <p class="footer-text" style="color: #d1d1d1; margin-bottom: 20px;">Revolutionizing healthcare management with real-time data and efficient solutions for pharmacies and medical professionals.</p>
                        <div class="social-links">
                            <a href="#" class="social-icon" style="color: #fff; font-size: 1.5rem; margin-right: 10px;"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-icon" style="color: #fff; font-size: 1.5rem; margin-right: 10px;"><i class="fab fa-facebook"></i></a>
                            <a href="#" class="social-icon" style="color: #fff; font-size: 1.5rem; margin-right: 10px;"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-icon" style="color: #fff; font-size: 1.5rem;"><i class="fab fa-linkedin"></i></a>
                        </div>
                    </div>
                </div>
                <!-- Second Column - Contact Info -->
                <div class="col-md-6 col-lg-4">
                    <div class="footer-col">
                        <h4 class="footer-title" style="color: #fff; margin-bottom: 20px;">Contact Us</h4>
                        <ul class="contact-info" style="list-style: none; padding: 0; margin-bottom: 20px;">
                            <li style="color: #d1d1d1; margin-bottom: 10px;"><i class="fas fa-map-marker-alt"></i> 123 Healthcare St.</li>
                            <li style="color: #d1d1d1; margin-bottom: 10px;"><i class="fas fa-phone"></i> +1 (555) 123-4567</li>
                            <li style="color: #d1d1d1; margin-bottom: 10px;"><i class="fas fa-envelope"></i> support@meditrack.com</li>
                        </ul>
                    </div>
                </div>
                <!-- Third Column - Newsletter Subscription -->
                <div class="col-md-12 col-lg-4">
                    <div class="footer-col">
                        <h4 class="footer-title" style="color: #fff; margin-bottom: 20px;">Stay Updated</h4>
                        <form class="newsletter-form">
                            <input type="email" placeholder="Enter your email" class="form-control" style="background-color: #fff; color: #2A3D66; padding: 10px; margin-bottom: 15px;">
                            <button class="btn btn-primary mt-2" style="background-color: #2A3D66; color: #fff; padding: 10px 20px;">Subscribe</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Footer Bottom Section (Copyright) -->
<div style="background-color: #2A3D66; padding: 15px 0;">
    <div class="container text-center">
        <p class="copyright-text" style="color: #d1d1d1; margin: 0;">&copy; 2025 MediTrack. All rights reserved.</p>
    </div>
</div>

<!-- Add FontAwesome for social icons -->
<script src="https://kit.fontawesome.com/a076d05399.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
