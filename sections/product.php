<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database.php';
$conn = Database::getInstance();

// Check for connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['save_product'])) {

    $brand = $_POST['brand'];
    $productName = $_POST['productName'];
    $productDescription = $_POST['productDescription'];
    $price = $_POST['price'];
    $active = $_POST['active'] ? 1 : 0; // Checkbox handling
    $productCreation = $_POST['productCreation'];

    try {
        // Prepare SQL statement
        $stmt = $conn->prepare("INSERT INTO product (p_brand, p_name, p_desc, p_price, p_active, p_creation) VALUES (?, ?, ?, ?, ?, ?)");

        // Bind parameters safely
        $stmt->bind_param(
            "ssssss",
            $brand,
            $productName,
            $productDescription,
            $price,
            $active,
            $productCreation
        );

        // Execute the query
        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'Product added successfully!';
        } else {
            $_SESSION['error_message'] = 'Failed to add product.';
        }
        $stmt->close();
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error: {$e->getMessage()}";
    }
    
    header('Location: ./admin_dashboard.php?page=product');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Include Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Include Bootstrap 4 CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <title>Product Manager</title>
</head>

<body>
    <h1 class="text-center mt-4">Product Manager</h1>
    <!-- Main Section -->
    <section class="container mt-4">
        <div class="card p-4">
            <h3 class="mb-4">Products</h3>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <label for="searchInput" class="sr-only">Search</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchInput" placeholder="Search">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary btn-hover">Search</button>
                        </div>
                    </div>
                </div>
                <div>
                    <button class="btn btn-success" data-toggle="modal" data-target="#addProductModal">Add
                        Product</button>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered text-center modern-table">
                    <thead class="thead-dark">
                        <tr>
                            <!-- Select Column -->
                            <th class="text-center align-middle">Select</th>
                            <th class="text-center align-middle">ID</th>
                            <th class="text-center align-middle">Brand</th>
                            <th class="text-center align-middle">Product Name</th>
                            <th class="text-center align-middle">Product Description</th>
                            <!-- Active Column -->
                            <th class="text-center align-middle">Active</th>
                            <th class="text-center align-middle">Price</th>
                            <th class="text-center align-middle">Edit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <!-- Select Checkbox -->
                            <td><input type="checkbox" class="select-checkbox"></td>
                            <td>001</td>
                            <td>Brand A</td>
                            <td>Product X</td>
                            <td>Product X Description</td>
                            <!-- Active Checkbox -->
                            <td><input type="checkbox" class="active-checkbox" checked></td>
                            <td>$100</td>
                            <td>
                                <button class="btn btn-link text-primary" data-toggle="modal" data-target="#editModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" class="select-checkbox"></td>
                            <td>002</td>
                            <td>Brand B</td>
                            <td>Product Y</td>
                            <td>Product Y Description</td>
                            <td><input type="checkbox" class="active-checkbox"></td>
                            <td>$150</td>
                            <td>
                                <button class="btn btn-link text-primary" data-toggle="modal" data-target="#editModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" class="select-checkbox"></td>
                            <td>003</td>
                            <td>Brand C</td>
                            <td>Product Z</td>
                            <td>Product Z Description</td>
                            <td><input type="checkbox" class="active-checkbox" checked></td>
                            <td>$200</td>
                            <td>
                                <button class="btn btn-link text-primary" data-toggle="modal" data-target="#editModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end mt-2">
                <button class="btn btn-danger" data-toggle="modal" data-target="#">Delete</button>
                <button class="btn btn-info ml-2" data-toggle="modal" data-target="#viewAllModal">View All</button>
            </div>
        </div>
    </section>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST">
                        <div class="form-group">
                            <label for="brand">Product Brand</label>
                            <input type="text" class="form-control" id="brand" name="brand" placeholder="Enter brand"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="productName">Product Name</label>
                            <input type="text" class="form-control" id="productName" name="productName"
                                placeholder="Enter product name" required>
                        </div>
                        <div class="form-group">
                            <label for="productDescription">Product Description</label>
                            <textarea name="productDescription" rows="5" class="form-control"
                                placeholder="Enter your text here..." required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="price">Product Price</label>
                            <input type="number" class="form-control" id="price" name="price" placeholder="Enter price"
                                required>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="active" name="active" checked>
                            <label class="form-check-label" for="active">Is Active?</label>
                        </div>
                        <div class="form-group">
                            <label for="productCreation">Product Creation Date</label>
                            <input type="date" class="form-control" id="productCreation" name="productCreation"
                                value="<?php echo date('Y-m-d'); ?>" readonly>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="save_product" class="btn btn-primary">Save Product</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Product</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="editBrand">Product Brand</label>
                            <input type="text" class="form-control" id="editBrand" placeholder="Enter brand">
                        </div>
                        <div class="form-group">
                            <label for="editProductName">Product Name</label>
                            <input type="text" class="form-control" id="editProductName"
                                placeholder="Enter product name">
                        </div>
                        <div class="form-group">
                            <label for="editProductDescription">Product Description</label>
                            <input type="text" class="form-control" id="editProductDescription"
                                placeholder="Enter description">
                        </div>
                        <div class="form-group">
                            <label for="editPrice">Product Price</label>
                            <input type="number" class="form-control" id="editPrice" placeholder="Enter price">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success">Save Changes</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View All Modal -->
    <div class="modal fade" id="viewAllModal" tabindex="-1" aria-labelledby="viewAllModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewAllModalLabel">All Products</h5>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Brand</th>
                                    <th>Product Name</th>
                                    <th>Product Description</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>001</td>
                                    <td>Brand A</td>
                                    <td>Product X</td>
                                    <td>Product X Description</td>
                                    <td>$100</td>
                                </tr>
                                <tr>
                                    <td>002</td>
                                    <td>Brand B</td>
                                    <td>Product Y</td>
                                    <td>Product Y Description</td>
                                    <td>$150</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Load JS and CSS dependencies -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Get current date
            const today = new Date();

            // Format date in YYYY-MM-DD for the date input
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');

            const currentDate = `${year}-${month}-${day}`;

            // Set the value of the productCreation input
            document.getElementById('productCreation').value = currentDate;
        });
    </script>

</body>

</html>