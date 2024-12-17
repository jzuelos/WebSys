<?php
session_start(); // Start session at the top
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database.php';

$conn = Database::getInstance();
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure customer_id is available in session
if (!isset($_SESSION['customer_id'])) {
    echo "You must be logged in to view the cart.";
    exit();
}

$customerId = $_SESSION['customer_id']; // Assuming the customer_id is in the session

// Query to get cart items for the logged-in user
$sql = "SELECT c.cart_id, c.product_id, p.p_name, p.p_desc, p.p_price, p.p_brand
        FROM cart c
        JOIN product p ON c.product_id = p.product_id
        WHERE c.customer_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customerId); // Bind the customer_id parameter
$stmt->execute();
$result = $stmt->get_result();

// Fetch cart items
$cartItems = [];
while ($row = $result->fetch_assoc()) {
    $cartItems[] = $row;
}

// Get the total price of items in the cart
$total = 0;
foreach ($cartItems as $item) {
    $total += $item['p_price'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cart</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="cart.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/92d70a2fd8.js" crossorigin="anonymous"></script>
</head>

<body class="d-flex flex-column align-items-center" style="background-color: #f8f9fa;">
  <!-- Header -->
  <div class="header d-flex justify-content-between align-items-center px-3 py-2 rounded mb-4"
    style="width: 80%; height: 80px; background-color: #3C3D37; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
    <p class="logo mb-0" style="font-size: 24px; font-weight: bold; color: white;">MOTOMAGX | Cart</p>
    <div class="cart d-flex align-items-center px-3 rounded" style="background-color: #f1f1f1; cursor: pointer;">
      <i class="fa-solid fa-cart-shopping me-2"></i>
      <p id="count" class="m-0"><?php echo count($cartItems); ?></p>
    </div>
  </div>

  <!-- Main Content -->
  <div class="container d-flex cart-container" style="width: 80%; margin-bottom: 40px;">
    <!-- Cart Items -->
    <div id="cartItems" class="cart-items bg-white w-75 p-3 d-flex flex-column" style="background-color: white; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
      <div class="cart-header">My Cart</div>

      <!-- Dynamically generate cart items from the database -->
      <?php if (count($cartItems) > 0): ?>
        <?php foreach ($cartItems as $item): ?>
          <div class="cart-item d-flex align-items-center bg-white py-3 border-bottom">
            <img src="https://via.placeholder.com/80" alt="Product Image">
            <div class="cart-item-details">
              <h6><?php echo htmlspecialchars($item['p_name']); ?></h6>
              <p>Brand: <?php echo htmlspecialchars($item['p_brand']); ?></p>
              <p><?php echo htmlspecialchars($item['p_desc']); ?></p>
            </div>
            <div class="cart-item-details d-flex justify-content-between align-items-center">
              <div class="variation mx-2">
                <label for="variationSelect" style="font-size: 12px;">Variation:</label>
                <select id="variationSelect"
                  style="font-size: 12px; padding: 5px; width: 100%; border-radius: 5px; border: 1px solid #ccc;">
                  <option value="size">Size</option>
                  <option value="color">Color</option>
                  <option value="material">Material</option>
                </select>
              </div>
            </div>
            <div class="cart-item-quantity" style="margin-right: 15px;">
              <input type="number" value="1" min="1">
            </div>
            <div class="cart-item-price" style="margin-right: 15px;">₱<?php echo number_format($item['p_price'], 2); ?></div>
            <div class="cart-item-remove">
              <input type="checkbox" class="item-checkbox">
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>Your cart is empty.</p>
      <?php endif; ?>

      <!-- Cart Summary with Select All and Delete -->
      <div class="cart-summary mt-auto d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
          <input type="checkbox" id="selectAll" style="margin-right: 10px;"> Select All
          <button id="deleteSelected" class="btn btn-danger btn-sm ms-3">Delete Selected</button>
        </div>
        <div>
          <p>Total</p>
          <p>₱<?php echo number_format($total, 2); ?></p>
        </div>
      </div>
    </div>

    <!-- Sidebar (Checkout Summary) -->
    <div class="cart-sidebar rounded p-3 text-center w-25"
      style="background-color: white; box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1); margin-left: 20px;">
      <div class="head rounded py-2 mb-3">
        <p class="m-0" style="font-weight: bold;">Checkout</p>
      </div>
      <div id="cartItem"><?php echo count($cartItems) > 0 ? "Items in cart: " . count($cartItems) : "Your cart is empty"; ?></div>
      <div class="foot d-flex justify-content-between mt-4 pt-3 border-top">
        <h4>Total</h4>
        <h4 id="total">₱<?php echo number_format($total, 2); ?></h4>
      </div>
      <div class="cart-footer">
        <button id="checkoutButton">Proceed to Checkout</button>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS & Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
