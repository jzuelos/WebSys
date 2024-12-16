<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database.php';
$conn = Database::getInstance();

// Check for connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all products
function getAllProducts($conn)
{
    $query = "SELECT * FROM product";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        return [];
    }
}

// Handle form submission for adding a product
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['save_product'])) {
    $brand = $_POST['brand'];
    $productName = $_POST['productName'];
    $productDescription = $_POST['productDescription'];
    $price = $_POST['price'];
    $active = isset($_POST['active']) ? 1 : 0; // Checkbox handling
    $productCreation = $_POST['productCreation'];

    // Handle image upload
    $imagePath = null; // Default to null if no image
    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/"; // Directory to save uploaded images
        $imageName = basename($_FILES['productImage']['name']);
        $targetFilePath = $targetDir . uniqid() . "_" . $imageName; // Avoid name conflicts

        // Ensure the directory exists
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Move uploaded file to the target directory
        if (move_uploaded_file($_FILES['productImage']['tmp_name'], $targetFilePath)) {
            $imagePath = $targetFilePath;
        } else {
            $_SESSION['error_message'] = 'Failed to upload image.';
            header('Location: ./admin_dashboard.php?page=product');
            exit();
        }
    }

    try {
        // Prepare SQL statement
        $stmt = $conn->prepare("INSERT INTO product (p_brand, p_name, p_desc, p_price, p_active, p_creation, p_image) VALUES (?, ?, ?, ?, ?, ?, ?)");

        // Bind parameters
        $stmt->bind_param(
            "sssssss",
            $brand,
            $productName,
            $productDescription,
            $price,
            $active,
            $productCreation,
            $imagePath
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

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_product'])) {
    $productId = $_POST['productId'];
    $brand = $_POST['brand'];
    $productName = $_POST['productName'];
    $productDescription = $_POST['productDescription'];
    $price = $_POST['price'];
    $active = isset($_POST['active']) ? 1 : 0; // Checkbox handling

    try {
        // Step 1: Get the existing image path from the database (to retain it if no new image is uploaded)
        $select_query = "SELECT p_image FROM product WHERE product_id = ?";
        $stmt = $conn->prepare($select_query);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $stmt->bind_result($existingImagePath);
        $stmt->fetch();
        $stmt->close();

        // Default value for new image path (retain the old one if no new image is uploaded)
        $newImagePath = $existingImagePath;

        // Step 2: Check if a new image was uploaded
        if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === UPLOAD_ERR_OK) {
            // Validate the uploaded image (only allow images)
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileType = $_FILES['productImage']['type'];

            if (in_array($fileType, $allowedTypes)) {
                // Define the directory to save uploaded images
                $uploadDir = "uploads/";

                // Ensure the directory exists
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                // Generate a unique name for the image file (avoid overwriting)
                $imageName = uniqid('product_') . '.' . pathinfo($_FILES['productImage']['name'], PATHINFO_EXTENSION);
                $imagePath = $uploadDir . $imageName;

                // Step 3: Move the uploaded file to the upload directory
                if (move_uploaded_file($_FILES['productImage']['tmp_name'], $imagePath)) {
                    // Successfully uploaded the image, set the new image path
                    $newImagePath = $imagePath;

                    // Step 4: Delete the old image (if it exists)
                    if ($existingImagePath && file_exists($existingImagePath)) {
                        // Delete the old image file from the server
                        if (unlink($existingImagePath)) {
                            echo "Old image deleted successfully.<br>";
                        } else {
                            echo "Failed to delete old image.<br>";
                        }
                    }
                } else {
                    throw new Exception('Failed to upload the image.');
                }
            } else {
                throw new Exception('Invalid file type. Only JPEG, PNG, and GIF images are allowed.');
            }
        }

        // Step 5: Prepare SQL statement to update the product
        $updateQuery = "UPDATE product SET p_brand = ?, p_name = ?, p_desc = ?, p_price = ?, p_active = ?, p_image = ? WHERE product_id = ?";
        $stmt = $conn->prepare($updateQuery);

        // Bind parameters (including the image path)
        $stmt->bind_param("ssssssi", $brand, $productName, $productDescription, $price, $active, $newImagePath, $productId);

        // Execute the update query
        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'Product updated successfully!';
        } else {
            $_SESSION['error_message'] = 'Failed to update product.';
        }
        $stmt->close();
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error: {$e->getMessage()}";
    }

    header('Location: ./admin_dashboard.php?page=product');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_products'])) {
    if (!empty($_POST['product_ids'])) {
        // Decode JSON-encoded product IDs from the POST request
        $product_ids = json_decode($_POST['product_ids'], true);

        // Sanitize product IDs and convert to a comma-separated string
        $sanitized_ids = array_map('intval', $product_ids);
        $ids_to_delete = implode(',', $sanitized_ids);

        // Fetch the image paths for the products to delete
        $select_query = "SELECT p_image FROM product WHERE product_id IN ($ids_to_delete)";
        $select_result = $conn->query($select_query);

        if ($select_result) {
            while ($row = $select_result->fetch_assoc()) {
                // Assuming the path is already relative (e.g., 'uploads/xyz.jpg')
                $image_path = $row['p_image'];

                if (file_exists($image_path)) {
                    if (unlink($image_path)) {
                        // Optionally, you can log this success in an application log
                    } else {
                        echo "<pre>Failed to delete image: $image_path</pre>";
                    }
                } else {
                    echo "<pre>Image does not exist: $image_path</pre>";
                }
            }

            // Delete the products from the database
            $delete_query = "DELETE FROM product WHERE product_id IN ($ids_to_delete)";
            if ($conn->query($delete_query) === TRUE) {
                echo "<div class='alert alert-success'>Products and images deleted successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>Error deleting products: " . $conn->error . "</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Error fetching images for deletion: " . $conn->error . "</div>";
        }
    } else {
        echo "<div class='alert alert-warning'>No products selected for deletion.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Include Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Include Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <title>Product Manager</title>
</head>

<body>
    <h2 class="text-center mt-4">Product Manager</h2>
    <!-- Main Section -->
    <section class="container mt-4">
        <div class="card p-4">
            <h3 class="mb-4">Products</h3>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <label for="searchInput" class="sr-only">Search</label>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" id="searchInput" placeholder="Search">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary btn-sm ms-2">Search</button>
                        </div>
                    </div>
                </div>
                <div>
                    <button class="btn btn-success btn-sm ms-2" data-bs-toggle="modal"
                        data-bs-target="#addProductModal">Add Product</button>
                </div>
            </div>

            <!-- Table -->
            <!-- Table -->
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-bordered table-sm text-center" style="width: 100%; table-layout: fixed;">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center align-middle">Select</th>
                            <th class="text-center align-middle">ID</th>
                            <th class="text-center align-middle">Image</th>
                            <th class="text-center align-middle">Product Name</th>
                            <th class="text-center align-middle">Brand</th>
                            <th class="text-center align-middle">Description</th>
                            <th class="text-center align-middle">Active</th>
                            <th class="text-center align-middle">Price</th>
                            <th class="text-center align-middle">Edit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch all products from the database without any LIMIT or OFFSET
                        $query = "SELECT * FROM product";
                        $result = $conn->query($query);

                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td><input type='checkbox' class='form-check-input'></td>";
                                echo "<td>{$row['product_id']}</td>";

                                // Image with click to zoom
                                echo "<td>
                            <a href='#' onclick='zoomImage(\"{$row['p_image']}\")'>
                                <img src='{$row['p_image']}' alt='Product Image' class='img-thumbnail' style='width: 50px; height: 50px;'>
                            </a>
                        </td>";

                                echo "<td>{$row['p_name']}</td>";
                                echo "<td>{$row['p_brand']}</td>";

                                // Truncate and hover effect with inline styles and JavaScript
                                echo "<td title='{$row['p_desc']}'>
                            <span style='display:block; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;'>{$row['p_desc']}</span>
                            <span class='hover-message' style='display:none; position:absolute; background:#f9f9f9; padding:5px; border:1px solid #ddd;'>{$row['p_desc']}</span>
                        </td>";

                                echo "<td><input type='checkbox' class='form-check-input' " . ($row['p_active'] ? "checked" : "") . " disabled></td>";
                                echo "<td>₱{$row['p_price']}</td>";
                                echo "<td><button class='btn btn-link text-primary p-0' 
                            data-bs-toggle='modal' 
                            data-bs-target='#editModal' 
                            onclick='populateEditModal(
                                " . json_encode($row) . "
                            )'>
                            <i class='fas fa-edit'></i>
                        </button></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='9'>No products found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav aria-label="Page navigation">
                <div class="d-flex justify-content-end mt-2">
                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                        data-bs-target="#deleteModal">Delete</button>
                    <button class="btn btn-info btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#viewAllModal">View
                        All</button>
                </div>


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
                                <form action="" method="POST" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label for="brand">Product Brand</label>
                                        <input type="text" class="form-control" id="brand" name="brand"
                                            placeholder="Enter brand" required>
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
                                        <input type="number" class="form-control" id="price" name="price"
                                            placeholder="Enter price" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="productImage">Product Image</label>
                                        <input type="file" class="form-control" id="productImage" name="productImage"
                                            accept="image/*" required>
                                    </div>
                                    <div class="form-group form-check">
                                        <input type="checkbox" class="form-check-input" id="active" name="active"
                                            checked>
                                        <label class="form-check-label" for="active">Is Active?</label>
                                    </div>
                                    <div class="form-group">
                                        <label for="productCreation">Product Creation Date</label>
                                        <input type="date" class="form-control" id="productCreation"
                                            name="productCreation" value="<?php echo date('Y-m-d'); ?>" readonly>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" name="save_product"
                                            class="btn btn-primary btn-sm ms-2">Save
                                            Product</button>
                                        <button type="button" class="btn btn-secondary btn-sm ms-2"
                                            data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Product Modal -->
                <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-m">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel">Edit Product</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="" enctype="multipart/form-data">
                                    <!-- Hidden ID field -->
                                    <input type="hidden" id="editProductId" name="productId">

                                    <div class="form-group">
                                        <label for="editBrand">Product Brand</label>
                                        <input type="text" class="form-control" id="editBrand" name="brand"
                                            placeholder="Enter brand" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="editProductName">Product Name</label>
                                        <input type="text" class="form-control" id="editProductName" name="productName"
                                            placeholder="Enter product name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="editProductDescription">Product Description</label>
                                        <textarea class="form-control" id="editProductDescription"
                                            name="productDescription" rows="5" required></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="editPrice">Product Price</label>
                                        <input type="number" class="form-control" id="editPrice" name="price"
                                            placeholder="Enter price" required>
                                    </div>
                                    <div class="form-group form-check">
                                        <input type="checkbox" class="form-check-input" id="editActive" name="active">
                                        <label class="form-check-label" for="editActive">Is Active?</label>
                                    </div>

                                    <!-- Image Upload Section -->
                                    <div class="form-group">
                                        <label for="editImage">Product Image</label>
                                        <input type="file" class="form-control" id="editImage" name="productImage"
                                            accept="image/*">
                                        <small class="form-text text-muted">Leave blank to keep the current
                                            image.</small>
                                    </div>

                                    <!-- Display Current Image Section -->
                                    <div class="form-group">
                                        <label for="currentImage">Current Image</label>
                                        <div id="currentImage">
                                            <!-- Current image will be shown here -->
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="submit" name="update_product" class="btn btn-success">Save
                                            Changes</button>
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- View All Modal -->
                <div class="modal fade" id="viewAllModal" tabindex="-1" aria-labelledby="viewAllModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="viewAllModalLabel">All Products</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
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
                                            <?php
                                            // Fetch products from the database
                                            $products = getAllProducts($conn);
                                            $totalPrice = 0;
                                            $activeProductsCount = 0;

                                            if (count($products) > 0) {
                                                foreach ($products as $product) {
                                                    // Increment total price and active products count
                                                    $totalPrice += $product['p_price'];
                                                    if ($product['p_active'] == 1) {
                                                        // Increment active products count if the product is active
                                                        $activeProductsCount++;
                                                    }

                                                    echo "<tr>";
                                                    echo "<td>{$product['product_id']}</td>";
                                                    echo "<td>{$product['p_brand']}</td>";
                                                    echo "<td>{$product['p_name']}</td>";
                                                    // Truncate and hover effect with inline styles and JavaScript
                                                    echo "<td title='{$product['p_desc']}'>
                                            <span class='truncate' style='display:block; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;'>{$product['p_desc']}</span>
                                            <span class='hover-message' style='display:none; position:absolute; background:#f9f9f9; padding:5px; border:1px solid #ddd; max-width:300px;'>{$product['p_desc']}</span>
                                          </td>";
                                                    echo "<td>₱{$product['p_price']}</td>";
                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='5'>No products found</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer d-flex justify-content-between">
                                <!-- Display total sum and active products count on the left -->
                                <div>
                                    <p class="mb-0">Total Price of All Products:
                                        ₱<?php echo number_format($totalPrice, 2); ?></p>
                                    <p class="mb-0">Active Products: <?php echo $activeProductsCount; ?></p>
                                </div>
                                <!-- Button aligned to the right -->
                                <button type="button" class="btn btn-secondary btn-sm ms-2"
                                    data-bs-dismiss="modal">Close</button>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Delete Modal -->
                <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p id="selectedIdsText">Are you sure you want to delete the selected products?</p>
                            </div>
                            <div class="modal-footer">
                                <form method="POST">
                                    <input type="hidden" name="product_ids" id="productIdsToDelete">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" name="delete_products" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Include jQuery and Bootstrap JS -->
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script
                    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
                <script>
                    // Function to update the "Delete" button's disabled state based on checkbox selection
                    function updateDeleteButtonState() {
                        const selectedCheckboxes = document.querySelectorAll('table tbody tr td:first-child input[type="checkbox"]:checked');
                        const deleteButton = document.querySelector('.btn-danger[data-bs-target="#deleteModal"]');

                        deleteButton.disabled = selectedCheckboxes.length === 0;
                    }

                    // Call the function initially to set the correct state on page load
                    updateDeleteButtonState();

                    // Add event listeners to checkboxes to update the button state when checked/unchecked
                    document.querySelectorAll('table tbody tr td:first-child input[type="checkbox"]').forEach(checkbox => {
                        checkbox.addEventListener('change', updateDeleteButtonState);
                    });

                    // Collect selected product IDs and pass them to the modal when the "Delete" button is clicked
                    document.querySelector('.btn-danger[data-bs-target="#deleteModal"]').addEventListener('click', function (event) {
                        const selectedCheckboxes = document.querySelectorAll('table tbody tr td:first-child input[type="checkbox"]:checked');

                        if (selectedCheckboxes.length === 0) {
                            alert('No products selected for deletion.');
                            event.preventDefault();
                            return;
                        }

                        const selectedIds = Array.from(selectedCheckboxes)
                            .map(checkbox => checkbox.closest('tr').querySelector('td:nth-child(2)').textContent.trim());

                        const idsList = selectedIds.map(id => encodeURIComponent(id)).join(', ');

                        document.getElementById('selectedIdsText').textContent = `Selected product IDs: ${idsList}`;
                        document.getElementById('productIdsToDelete').value = JSON.stringify(selectedIds);
                    });

                    // Zoom Image function
                    function zoomImage(imageUrl) {
                        var zoomedImage = document.createElement('img');
                        zoomedImage.src = imageUrl;
                        zoomedImage.style.width = '40%';
                        zoomedImage.style.maxHeight = '50%';
                        zoomedImage.style.margin = 'auto';
                        zoomedImage.style.display = 'block';
                        zoomedImage.style.border = '2px solid #ddd';
                        zoomedImage.style.transition = 'transform 0.3s ease';
                        zoomedImage.style.transform = 'scale(1.2)';

                        var overlay = document.createElement('div');
                        overlay.style.position = 'fixed';
                        overlay.style.top = '0';
                        overlay.style.left = '0';
                        overlay.style.width = '100%';
                        overlay.style.height = '100%';
                        overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
                        overlay.style.display = 'flex';
                        overlay.style.justifyContent = 'center';
                        overlay.style.alignItems = 'center';
                        overlay.style.zIndex = '1000';
                        overlay.style.cursor = 'pointer';

                        overlay.appendChild(zoomedImage);
                        document.body.appendChild(overlay);

                        overlay.onclick = function () {
                            document.body.removeChild(overlay);
                        };
                    }

                    // Set current date in the productCreation input field
                    document.addEventListener("DOMContentLoaded", function () {
                        const productCreation = document.getElementById('productCreation');
                        if (productCreation) {
                            const today = new Date();
                            const year = today.getFullYear();
                            const month = String(today.getMonth() + 1).padStart(2, '0');
                            const day = String(today.getDate()).padStart(2, '0');
                            const currentDate = `${year}-${month}-${day}`;
                            productCreation.value = currentDate;
                        }
                    });

                    function populateEditModal(product) {
                        // Populate the modal with the product details
                        document.getElementById('editProductId').value = product.product_id;
                        document.getElementById('editBrand').value = product.p_brand;
                        document.getElementById('editProductName').value = product.p_name;
                        document.getElementById('editProductDescription').value = product.p_desc;
                        document.getElementById('editPrice').value = product.p_price;
                        document.getElementById('editActive').checked = (product.p_active == 1);

                        // Set the image preview if available
                        const imagePath = product.p_image.trim();
                        if (imagePath) {
                            document.getElementById('currentImage').innerHTML = `<img src="${imagePath}" alt="Current Image" style="width: 100px; height: 100px; object-fit: cover;">`;
                        } else {
                            document.getElementById('currentImage').innerHTML = `<img src="default-image.jpg" alt="No image" style="width: 100px; height: 100px; object-fit: cover;">`;
                        }
                    }

                </script>
</body>

</html>