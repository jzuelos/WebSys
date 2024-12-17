<?php
// Assuming you have a database connection $conn
function getAllProducts($conn)
{
    $query = "SELECT * FROM product";  // Query to get all products
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        return $result->fetch_all(MYSQLI_ASSOC);  // Return results as an associative array
    } else {
        return [];  // Return an empty array if no products are found
    }
}

// Insert model into the database when the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['model_name']) && isset($_POST['product_id'])) {
    $model_name = $_POST['model_name'];
    $product_id = $_POST['product_id'];

    // Check if the model already exists for the given product_id
    $checkQuery = $conn->prepare("SELECT COUNT(*) FROM model WHERE model_name = ? AND product_id = ?");
    $checkQuery->bind_param('si', $model_name, $product_id);
    $checkQuery->execute();
    $checkQuery->bind_result($exists);
    $checkQuery->fetch();
    $checkQuery->close();

    // If the model already exists, don't insert
    if ($exists > 0) {
        $message = "This model already exists for the selected product.";
    } else {
        // Insert model into the models table
        $stmt = $conn->prepare("INSERT INTO model (model_name, product_id) VALUES (?, ?)");
        $stmt->bind_param('si', $model_name, $product_id);
        $stmt->execute();

        // Check if the insert was successful
        if ($stmt->affected_rows > 0) {
            $message = "Model added successfully!";
        } else {
            $message = "Failed to add model.";
        }
        $stmt->close();
    }
}

// Fetch the products
$products = getAllProducts($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Table</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        /* Equalize column widths, but make the Select column smaller */
        th,
        td {
            text-align: center;
            vertical-align: middle;
        }

        .select-width {
            width: 10%;
            /* Make the Select column smaller */
        }

        .equal-width {
            width: 22.5%;
            /* Equal width for the other columns */
        }

        /* Make the table scrollable and increase height */
        .table-responsive {
            max-height: 600px;
            /* Increase the max height for the table */
            overflow-y: auto;
        }

        /* Style for the message */
        .alert {
            position: fixed;
            top: 10%;
            right: 10%;
            z-index: 1000;
        }
    </style>
</head>

<body>
    <h2 class="text-center mt-4">Color Variation</h2>
    <div class="container mt-5 bg-white p-4 shadow-sm rounded">
        <?php if (isset($message)) {
            echo "<div class='alert alert-info'>$message</div>";
        } ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="select-width">Product ID</th>
                        <th class="equal-width">Product Image</th>
                        <th class="equal-width">Brand</th>
                        <th class="equal-width">Product Name</th>
                        <th class="equal-width">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Loop through the fetched products and display them
                    foreach ($products as $product) {
                        echo "<tr>";
                        // Product ID column (instead of radio button)
                        echo "<td>" . htmlspecialchars($product['product_id']) . "</td>";
                        // Product image column
                        echo "<td><img src='" . htmlspecialchars($product['p_image']) . "' alt='" . htmlspecialchars($product['p_name']) . "' style='width: 50px; height: auto;'></td>";
                        // Brand name column
                        echo "<td>" . htmlspecialchars($product['p_brand']) . "</td>";
                        // Product name column
                        echo "<td>" . htmlspecialchars($product['p_name']) . "</td>";
                        // Action column with form for adding models
                        echo "<td>
            <form method='POST'>
                <input type='hidden' name='product_id' value='" . $product['product_id'] . "'>
                <input type='text' name='model_name' class='form-control' placeholder='Enter model name' required>
                <button type='submit' class='btn btn-primary mt-2'>Add Model</button>
            </form>
          </td>";
                        echo "</tr>";
                    }
                    ?>

                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS and Dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <script>
        // Hide the success message after 3 seconds
        setTimeout(function () {
            var alertMessage = document.querySelector('.alert');
            if (alertMessage) {
                alertMessage.style.display = 'none';
            }
        }, 3000);
    </script>

</body>

</html>