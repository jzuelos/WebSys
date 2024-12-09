<?php 
  session_start(); // Start session at the top
  error_reporting(E_ALL);
  ini_set('display_errors', 1);

  require_once 'database.php';

  $conn = Database::getInstance();
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }

  //Check if the user is logged in by verifying if 'user_id' exists in the session
  if (!isset($_SESSION['ad_un'])) {
    header("Location: admin_login.php"); // Redirect to login page if user is not logged in
    exit; // Stop further execution after redirection
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bootstrap Sidebar Example</title>
  <!-- Include Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <nav class="col-md-2 d-none d-md-block bg-dark text-white vh-100">
        <div class="p-4">
          <h4 class="text-center">Admin Menu</h4>
          <hr class="text-white">
          <ul class="nav flex-column">
            <li class="nav-item">
              <a class="nav-link text-white" href="#">Dashboard</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white" href="#">Users</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white" href="#">Settings</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white" href="#">Logs</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white" href="#">Profile</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white" href="#">Logout</a>
            </li>
          </ul>
        </div>
      </nav>

      <!-- Main Content -->
      <main class="col-md-10 offset-md-2 p-4">
        <h1>Welcome to the Admin Panel</h1>
        <p>This is the main content area of the application. You can manage various features here.</p>
      </main>
    </div>
  </div>

  <!-- Include Bootstrap 5 JS Bundle -->
  <script src="./admin_dashboard.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
</body>

</html>
