<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h2 class="text-center mt-4">Customer Manager</h2>
    <!-- Main Section -->
    <section class="container mt-4">
        <div class="card p-4">
            <h3 class="mb-4">Customers</h3>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" id="searchInput" placeholder="Search Customers">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary btn-sm ms-2">Search</button>
                        </div>
                    </div>
                </div>
                <div>
                    <button class="btn btn-success btn-sm ms-2" data-bs-toggle="modal"
                        data-bs-target="#addCustomerModal">Add Customer</button>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-sm text-center">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center align-middle">Select</th>
                            <th class="text-center align-middle">ID</th>
                            <th class="text-center align-middle">Name</th>
                            <th class="text-center align-middle">Email</th>
                            <th class="text-center align-middle">Phone</th>
                            <th class="text-center align-middle">Address</th>
                            <th class="text-center align-middle">Active</th>
                            <th class="text-center align-middle">Edit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Customer Rows (example) -->
                        <tr>
                            <td><input type="checkbox" class="form-check-input"></td>
                            <td>1</td>
                            <td>John Doe</td>
                            <td>johndoe@example.com</td>
                            <td>123-456-7890</td>
                            <td>123 Elm Street</td>
                            <td><input type="checkbox" class="form-check-input" checked disabled></td>
                            <td><button class="btn btn-link text-primary p-0" data-bs-toggle="modal"
                                    data-bs-target="#editCustomerModal"
                                    onclick="populateEditCustomerModal(1, 'John Doe', 'johndoe@example.com', '123-456-7890', '123 Elm Street', '1')"><i
                                        class="fas fa-edit"></i></button></td>
                        </tr>
                        <!-- More rows can be added here -->
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center pagination-sm">
                    <!-- Previous button -->
                    <li class="page-item">
                        <a class="page-link" href="#">« Previous</a>
                    </li>

                    <!-- Page number links -->
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>

                    <!-- Next button -->
                    <li class="page-item">
                        <a class="page-link" href="#">Next &raquo;</a>
                    </li>
                </ul>
            </nav>

            <div class="d-flex justify-content-end mt-2">
                <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                    data-bs-target="#deleteCustomerModal">Delete</button>
                <button class="btn btn-info btn-sm ms-2" data-bs-toggle="modal"
                    data-bs-target="#viewAllCustomersModal">View All</button>
            </div>

            <!-- Add Customer Modal -->
            <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-m">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addCustomerModalLabel">Add New Customer</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="POST">
                                <div class="form-group">
                                    <label for="name">Customer Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Enter customer name" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Customer Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="Enter customer email" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Customer Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone"
                                        placeholder="Enter customer phone" required>
                                </div>
                                <div class="form-group">
                                    <label for="address">Customer Address</label>
                                    <input type="text" class="form-control" id="address" name="address"
                                        placeholder="Enter customer address" required>
                                </div>
                                <div class="form-group form-check">
                                    <input type="checkbox" class="form-check-input" id="active" name="active" checked>
                                    <label class="form-check-label" for="active">Is Active?</label>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" name="save_customer" class="btn btn-primary btn-sm ms-2">Save
                                        Customer</button>
                                    <button type="button" class="btn btn-secondary btn-sm ms-2"
                                        data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Customer Modal -->
            <div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="editCustomerModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-m">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editCustomerModalLabel">Edit Customer</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="">
                                <!-- Hidden ID field -->
                                <input type="hidden" id="editCustomerId" name="customerId">

                                <div class="form-group">
                                    <label for="editName">Customer Name</label>
                                    <input type="text" class="form-control" id="editName" name="name"
                                        placeholder="Enter customer name" required>
                                </div>
                                <div class="form-group">
                                    <label for="editEmail">Customer Email</label>
                                    <input type="email" class="form-control" id="editEmail" name="email"
                                        placeholder="Enter customer email" required>
                                </div>
                                <div class="form-group">
                                    <label for="editPhone">Customer Phone</label>
                                    <input type="text" class="form-control" id="editPhone" name="phone"
                                        placeholder="Enter customer phone" required>
                                </div>
                                <div class="form-group">
                                    <label for="editAddress">Customer Address</label>
                                    <input type="text" class="form-control" id="editAddress" name="address"
                                        placeholder="Enter customer address" required>
                                </div>
                                <div class="form-group form-check">
                                    <input type="checkbox" class="form-check-input" id="editActive" name="active">
                                    <label class="form-check-label" for="editActive">Is Active?</label>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" name="update_customer" class="btn btn-success">Save
                                        Changes</button>
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- View All Customers Modal -->
            <div class="modal fade" id="viewAllCustomersModal" tabindex="-1"
                aria-labelledby="viewAllCustomersModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="viewAllCustomersModalLabel">All Customers</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Similar table as above for all customers -->
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <script>
        // Populate the Edit Modal with customer details
        function populateEditCustomerModal(id, name, email, phone, address, active) {
            document.getElementById('editCustomerId').value = id;
            document.getElementById('editName').value = name;
            document.getElementById('editEmail').value = email;
            document.getElementById('editPhone').value = phone;
            document.getElementById('editAddress').value = address;
            document.getElementById('editActive').checked = active === "1";
        }
    </script>

</body>

</html>