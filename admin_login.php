<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database.php';
$conn = Database::getInstance();

// Handle form submission logic
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$username || !$password) {
        $_SESSION['error'] = "Please fill in both fields.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM admin WHERE ad_un = ? LIMIT 1");

        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                $user = $result->fetch_assoc();

                if (password_verify($password, $user['ad_password'])) {
                    $_SESSION['admin_id'] = $user['admin_id'];
                    $_SESSION['ad_un'] = $user['ad_un'];
                    $_SESSION['logged_in'] = true;
                    header("Location: admin_dashboard.php");
                    exit();
                } else {
                    $_SESSION['error'] = "Invalid password.";
                }
            } else {
                $_SESSION['error'] = "Username does not exist.";
            }

            $stmt->close();
        } else {
            $_SESSION['error'] = "Could not prepare database query.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Admin Login</title>
</head>

<body class="bg-light d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-sm" style="width: 35rem; height: 30rem; border-radius: 20px;">
        <div class="card-header text-center text-white p-4" style="background-color: #3C3D37;">
            <h1 class="h5">ADMIN LOGIN</h1>
            <p class="mb-0">Hello there, Sign in and start managing your website</p>
        </div>
        <div class="card-body">
            <?php
            // Display session errors if they exist
            if (isset($_SESSION['error'])) {
                echo "<div id='errorMessage' class='alert alert-danger text-center'>{$_SESSION['error']}</div>";
                unset($_SESSION['error']);
            }
            ?>
            <form method="POST" action="">
                <div class="mb-3 ms-5 me-5 mt-4">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username"
                        placeholder="Enter your username">
                </div>
                <div class="mb-3 ms-5 me-5">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password"
                        placeholder="Enter your password">
                </div>
                <div class="d-flex justify-content-between align-items-center ms-5 me-5">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">Remember Me</label>
                    </div>
                    <a href="#" class="text-primary">Forgot Password?</a>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary w-25 mt-4 me-5">Login</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Load external JavaScript -->
    <script src="./admin_login.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>
