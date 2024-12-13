<?php
//Add Customer(s)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_customer'])) {
    // Sanitize inputs
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_EMAIL);  // Use FILTER_SANITIZE_EMAIL for username (email)
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);  // Use full special chars for password
    $c_fname = filter_input(INPUT_POST, 'c_fname', FILTER_SANITIZE_FULL_SPECIAL_CHARS);  // Use full special chars for first name
    $c_sname = filter_input(INPUT_POST, 'c_sname', FILTER_SANITIZE_FULL_SPECIAL_CHARS);  // Use full special chars for surname
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_FULL_SPECIAL_CHARS);  // Use full special chars for address
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);  // Sanitize email
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_NUMBER_INT);  // Sanitize phone number
    $bday = filter_input(INPUT_POST, 'bday', FILTER_SANITIZE_FULL_SPECIAL_CHARS);  // Leave birthday as string if needed (no deprecation warning)

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $creation_day = date('Y-m-d');  // Current date for creation

    // Check if username already exists
    $stmt = $conn->prepare("SELECT * FROM customer WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Username already exists. Please choose another.";
    } else {
        // Insert new customer into the database, including phone, bday, and c_creation
        $stmt = $conn->prepare("INSERT INTO customer (username, password, c_fname, c_sname, address, email, phone, bday, c_creation) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", $username, $hashedPassword, $c_fname, $c_sname, $address, $email, $phone, $bday, $creation_day);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Customer added successfully!";
        } else {
            $_SESSION['error'] = "Error during registration.";
        }
        $stmt->close();
    }
}

// Update Customer(s)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_customer'])) {
    // Sanitize customer ID
    $customerId = filter_input(INPUT_POST, 'customerId', FILTER_SANITIZE_NUMBER_INT);
    $fname = filter_input(INPUT_POST, 'fname', FILTER_SANITIZE_FULL_SPECIAL_CHARS);  // First name
    $sname = filter_input(INPUT_POST, 'sname', FILTER_SANITIZE_FULL_SPECIAL_CHARS);  // Last name (surname)
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_NUMBER_INT);  // Sanitize phone number
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $bday = filter_input(INPUT_POST, 'bday', FILTER_SANITIZE_FULL_SPECIAL_CHARS);  // Sanitize the birthday

    // Update customer in the database
    $stmt = $conn->prepare("UPDATE customer SET c_fname = ?, c_sname = ?, email = ?, phone = ?, address = ?, bday = ? WHERE customer_id = ?");
    $stmt->bind_param("ssssssi", $fname, $sname, $email, $phone, $address, $bday, $customerId);  // Correct binding for first and last name

    if ($stmt->execute()) {
        $_SESSION['success'] = "Customer updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating customer.";
    }
    $stmt->close();
}

// Delete Customer(s)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_customers'])) {
    if (!empty($_POST['customerIds'])) {
        $customerIds = implode(",", array_map('intval', $_POST['customerIds']));  // Ensure customerIds are integers
        $stmt = $conn->prepare("DELETE FROM customer WHERE customer_id IN ($customerIds)");
        if ($stmt->execute()) {
            $_SESSION['success'] = "Customer(s) deleted successfully!";
        } else {
            $_SESSION['error'] = "Error deleting customer(s).";
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "No customers selected.";
    }
}

// Fetch customers from the database
$customers = [];
$stmt = $conn->prepare("SELECT * FROM customer");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $customers[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="container mt-4">
        <!-- Show success or error messages -->
        <!-- Show success or error messages -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger" id="error-message"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success" id="success-message"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); endif; ?>

        <h2 class="text-center mt-4">Customer Manager</h2>

        <section class="mt-4">
            <div class="card p-4">
                <h3 class="mb-4">Customers</h3>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="input-group" style="max-width: 200px; font-size: 0.75rem;">
                        <input type="text" class="form-control form-control-sm" id="searchInput" placeholder="Search">
                        <button type="button" class="btn btn-primary btn-sm ms-2">Search</button>
                    </div>
                    <div>
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addCustomerModal">Add Customer</button>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <form method="POST" action="">
                        <table class="table table-bordered table-sm text-center">
                            <thead class="thead-dark">
                                <tr>
                                    <th><input type="checkbox" class="form-check-input" id="selectAll" /></th>
                                    <th>Customer ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Birthday</th>
                                    <th>Account Creation</th>
                                    <th>Edit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($customers as $customer): ?>
                                    <tr>
                                        <td><input type="checkbox" name="customerIds[]"
                                                value="<?= $customer['customer_id'] ?>" class="form-check-input"></td>
                                        <td><?= $customer['customer_id'] ?></td>
                                        <td><?= $customer['c_fname'] ?>     <?= $customer['c_sname'] ?></td>
                                        <td><?= $customer['email'] ?></td>
                                        <td><?= $customer['phone'] ?></td>
                                        <td><?= $customer['address'] ?></td>
                                        <td><?= $customer['bday'] ?></td>
                                        <td><?= $customer['c_creation'] ?></td>
                                        <td>
                                            <button type="button" class="btn btn-link text-primary p-0"
                                                data-bs-toggle="modal" data-bs-target="#editCustomerModal"
                                                onclick="populateEditCustomerModal(<?= $customer['customer_id'] ?>, '<?= $customer['c_fname'] ?>', '<?= $customer['c_sname'] ?>', '<?= $customer['email'] ?>', '<?= $customer['phone'] ?>', '<?= $customer['address'] ?>', '<?= $customer['bday'] ?>')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <div class="d-flex justify-content-end mt-2">
                            <button type="submit" name="delete_customers" class="btn btn-danger btn-sm">Delete</button>
                        </div>
                    </form>
                </div>

                <!-- Pagination -->
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center pagination-sm">
                        <li class="page-item"><a class="page-link" href="#">« Previous</a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item"><a class="page-link" href="#">Next &raquo;</a></li>
                    </ul>
                </nav>

                <!-- Add Customer Modal -->
                <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addCustomerModalLabel">Add New Customer</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" class="form-control" id="username" name="username"
                                                required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="password" class="form-label">Password</label>
                                            <input type="password" class="form-control" id="password" name="password"
                                                required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="c_fname" class="form-label">First Name</label>
                                            <input type="text" class="form-control" id="c_fname" name="c_fname"
                                                required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="c_sname" class="form-label">Last Name</label>
                                            <input type="text" class="form-control" id="c_sname" name="c_sname"
                                                required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="phone" class="form-label">Phone</label>
                                            <input type="text" class="form-control" id="phone" name="phone" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="address" class="form-label">Address</label>
                                            <input type="text" class="form-control" id="address" name="address"
                                                required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="bday" class="form-label">Birthday</label>
                                            <input type="date" class="form-control" id="bday" name="bday" required>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <button type="submit" name="save_customer" class="btn btn-primary">Save
                                            Customer</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Edit Customer Modal -->
                <div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="editCustomerModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editCustomerModalLabel">Edit Customer</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="">
                                    <!-- Hidden customer ID -->
                                    <input type="hidden" name="customerId" id="edit_customerId">
                                    <div class="mb-3">
                                        <label for="edit_fname" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="edit_fname" name="fname" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_sname" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="edit_sname" name="sname" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="edit_email" name="email" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_phone" class="form-label">Phone</label>
                                        <input type="text" class="form-control" id="edit_phone" name="phone" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_address" class="form-label">Address</label>
                                        <input type="text" class="form-control" id="edit_address" name="address"
                                            required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_bday" class="form-label">Birthday</label>
                                        <input type="date" class="form-control" id="edit_bday" name="bday" required>
                                    </div>
                                    <div class="mb-3">
                                        <button type="submit" name="update_customer" class="btn btn-primary">Update
                                            Customer</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js"></script>
    <script>
        // Function to populate the edit customer modal
        function populateEditCustomerModal(id, fname, sname, email, phone, address, bday) {
            document.getElementById('edit_customerId').value = id;
            document.getElementById('edit_fname').value = fname;  // First name
            document.getElementById('edit_sname').value = sname;  // Last name (surname)
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_phone').value = phone;
            document.getElementById('edit_address').value = address;
            document.getElementById('edit_bday').value = bday;
        }

        // Add a 3-second timer to hide success and error messages
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                const successMessage = document.getElementById('success-message');
                const errorMessage = document.getElementById('error-message');
                if (successMessage) {
                    successMessage.style.display = 'none';
                }
                if (errorMessage) {
                    errorMessage.style.display = 'none';
                }
            }, 3000); // Hide after 3 seconds
        });
    </script>
</body>

</html>