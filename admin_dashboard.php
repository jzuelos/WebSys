<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database.php';

$conn = Database::getInstance();
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['ad_un'])) {
  header("Location: admin_login.php");
  exit;
}

// Handle Logout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
  session_destroy();
  header("Location: admin_login.php");
  exit;
}

// Determine the current page
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Sanitize and validate the page input
$allowed_pages = ['dashboard', 'product', 'customer', 'seller', 'orders', 'variations'];
if (!in_array($page, $allowed_pages)) {
  $page = '404';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - <?php echo ucfirst($page); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
  <div class="container-fluid p-0">
    <div class="row g-0">
      <!-- Sidebar -->
      <nav class="col-md-2 bg-dark text-white vh-100">
        <div class="p-4">
          <h4 class="text-center">Admin Menu</h4>
          <hr class="text-white">
          <ul class="nav flex-column">
            <?php
            $menu_items = [
              'dashboard' => 'Dashboard',
              'product' => 'Products',
              'customer' => 'Customers',
              'seller' => 'Sellers',
              'orders' => 'Orders',
              'variations' => 'Variations',
            ];
            foreach ($menu_items as $key => $value) {
              $active = ($page === $key) ? 'active' : '';
              echo "<li class='nav-item'>
                        <a class='nav-link text-white $active' href='?page=$key'>$value</a>
                      </li>";
            }
            ?>
            <!-- Logout as a Button -->
            <li class="nav-item mt-2">
              <form method="POST" action="">
                <button type="submit" name="logout" class="btn btn-danger text-white w-100">Logout</button>
              </form>
            </li>
          </ul>
        </div>
      </nav>

      <!-- Main Content -->
      <main class="col-md-10 p-4" style="background-color: #f0f0f0;">
        <?php
        // Include the relevant content based on the current page
        $file_path = "sections/{$page}.php";
        if (file_exists($file_path)) {
          include $file_path;
        } else {
          include "sections/404.php";
        }
        ?>
      </main>

    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
</body>

</html>