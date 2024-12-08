<?php
session_start(); // Start session at the top
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debugging function
function debugLog($message)
{
    error_log($message); // Log message to server's error log
    echo "<pre>$message</pre>"; // Also display for easier visibility during debugging
}

require_once 'database.php'; // Include your database connection
$conn = Database::getInstance();

// Check database connection
if ($conn->connect_error) {
    debugLog("Database connection failed: " . $conn->connect_error);
    die("Connection failed.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    // Capture and sanitize form data
    $username = filter_input(INPUT_POST, 'usernamelog', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'passwordlog', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Username or password cannot be empty!";
    } else {
        // Query the database to check if the user exists
        $stmt = $conn->prepare("SELECT * FROM customer WHERE username = ? LIMIT 1");

        if ($stmt) {
            $stmt->bind_param("s", $username);

            if ($stmt->execute()) {
                $result = $stmt->get_result();

                if ($result && $result->num_rows > 0) {
                    $user = $result->fetch_assoc();

                    // Verify the password
                    if (password_verify($password, $user['password'])) {
                        $_SESSION['customer_id'] = $user['customer_id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['logged_in'] = true;

                        header("Location: Home.php");
                        exit();
                    } else {
                        $_SESSION['error'] = "Incorrect password!";
                    }
                } else {
                    $_SESSION['error'] = "Username does not exist!";
                }
                $stmt->close();
            } else {
                $_SESSION['error'] = "Error executing statement.";
            }
        } else {
            $_SESSION['error'] = "Error preparing statement.";
        }
    }
}

// Handle the signup logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Username and password cannot be empty.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM customer WHERE username = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $_SESSION['error'] = "Username already exists. Please choose another.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO customer (username, password) VALUES (?, ?)");
                if ($stmt) {
                    $stmt->bind_param("ss", $username, $hashedPassword);
                    if ($stmt->execute()) {
                        $_SESSION['success'] = "Registration successful!";
                    } else {
                        $_SESSION['error'] = "Error during registration.";
                    }
                    $stmt->close();
                } else {
                    $_SESSION['error'] = "Failed to prepare the database query.";
                }
            }
            $stmt->close();
        } else {
            $_SESSION['error'] = "Database error while processing signup.";
        }
    }
    // Return to the main signup tab UI
    header("Location: userreg.php#pills-profile");
    exit();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <title>User Log-in</title>
</head>

<body style="
        height: 100vh; 
        background-image: url('img/logoamg.png'); 
        background-size: cover; 
        background-position: center; 
        background-repeat: no-repeat;">
    <!-- Form Container -->
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card p-4"
            style="width: 30rem; background-color: white; border-radius: 20px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
            <!-- Pills Tabs Section -->
            <div class="container">
                <h2 class="text-center mb-4 mt-3">MotoMagX</h2>
                <!-- Pills Tabs -->
                <ul class="nav nav-pills mb-3 justify-content-center" id="pills-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab"
                            aria-controls="pills-home" aria-selected="true">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab"
                            aria-controls="pills-profile" aria-selected="false">Sign-up</a>
                    </li>
                </ul>

                <?php
                if (isset($_SESSION['error'])) {
                    echo "<div id='error-alert' class='alert alert-danger' style='text-align: center; max-width: 360px; margin: 10px auto;'>"
                        . $_SESSION['error'] . "</div>";
                    unset($_SESSION['error']);
                }
                if (isset($_SESSION['success'])) {
                    echo "<div id='success-alert' class='alert alert-success' style='text-align: center; max-width: 300px; margin: 10px auto;'>"
                        . $_SESSION['success'] . "</div>";
                    unset($_SESSION['success']);
                }
                ?>

                <!-- Tab Content -->
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active p-3" id="pills-home" role="tabpanel"
                        aria-labelledby="pills-home-tab">
                        <h5 class="ml-3 mb-4">Login</h5>
                        <form class="m-3" action="" method="POST">
                            <!-- Email input -->
                            <div data-mdb-input-init class="form-outline mb-4">
                                <input type="text" name="usernamelog" class="form-control" required />
                                <label class="form-label">Username</label>
                            </div>

                            <!-- Password input -->
                            <div data-mdb-input-init class="form-outline mb-4">
                                <input type="password" name="passwordlog" class="form-control" required />
                                <label class="form-label">Password</label>
                            </div>

                            <!-- Submit button -->
                            <button type="submit" name="login" class="btn btn-primary btn-block mb-4">Sign in</button>
                            <p>Have concerns?</p>
                            <button type="button" data-mdb-button-init data-mdb-ripple-init
                                class="btn btn-link btn-floating mx-1">
                                <i class="fab fa-facebook-f"></i>
                            </button>

                            <button type="button" data-mdb-button-init data-mdb-ripple-init
                                class="btn btn-link btn-floating mx-1">
                                <i class="fab fa-google"></i>
                            </button>

                            <button type="button" data-mdb-button-init data-mdb-ripple-init
                                class="btn btn-link btn-floating mx-1">
                                <i class="fab fa-twitter"></i>
                            </button>

                            <button type="button" data-mdb-button-init data-mdb-ripple-init
                                class="btn btn-link btn-floating mx-1">
                                <i class="fab fa-instagram"></i>
                            </button>
                        </form>
                    </div>

                    <div class="tab-pane fade p-3" id="pills-profile" role="tabpanel"
                        aria-labelledby="pills-profile-tab">
                        <h5 class="ml-3 mb-4">Sign-up</h5>
                        <form class="m-3" action="" method="POST">
                            <!-- Email input -->
                            <div data-mdb-input-init class="form-outline mb-4">
                                <input type="text" name="username" class="form-control" required />
                                <label class="form-label">Username</label>
                            </div>

                            <!-- Password input -->
                            <div data-mdb-input-init class="form-outline mb-4">
                                <input type="password" name="password" class="form-control" required />
                                <label class="form-label">Password</label>
                            </div>

                            <!-- 2 column grid layout for inline styling -->
                            <div class="row mb-4">
                                <div class="col d-flex justify-content-center">
                                    <!-- Checkbox -->
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="form2Example31"
                                            checked />
                                        <label class="form-check-label" for="form2Example31"> Remember me </label>
                                    </div>
                                </div>

                                <div class="col">
                                    <!-- Simple link -->
                                    <a href="#!">Forgot password?</a>
                                </div>
                            </div>

                            <!-- Submit button -->
                            <button type="submit" data-mdb-button-init data-mdb-ripple-init
                                class="btn btn-primary btn-block mb-4" name="signup">Register</button>
                            <p>Have concerns?</p>
                            <button type="button" data-mdb-button-init data-mdb-ripple-init
                                class="btn btn-link btn-floating mx-1">
                                <i class="fab fa-facebook-f"></i>
                            </button>

                            <button type="button" data-mdb-button-init data-mdb-ripple-init
                                class="btn btn-link btn-floating mx-1">
                                <i class="fab fa-google"></i>
                            </button>

                            <button type="button" data-mdb-button-init data-mdb-ripple-init
                                class="btn btn-link btn-floating mx-1">
                                <i class="fab fa-twitter"></i>
                            </button>

                            <button type="button" data-mdb-button-init data-mdb-ripple-init
                                class="btn btn-link btn-floating mx-1">
                                <i class="fab fa-instagram"></i>
                            </button>
                    </div>
                    <div class="tab-pane fade p-3" id="pills-profile" role="tabpanel"
                        aria-labelledby="pills-profile-tab">
                        <h4>Profile Content</h4>
                        <p>Here you can manage your user profile details.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
    </div>
    </div>
    <script src="./userreg.js"></script>
    <!-- Scripts: Include jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
</body>

</html>