<?php
session_start(); // Start session at the top
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database.php'; // Ensure database connection is established

$conn = Database::getInstance();
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    if (!$username || !$password) {
        $_SESSION['error'] = "Please fill in both fields.";
    } else {
        // Use prepared statements to prevent SQL Injection
        $stmt = $conn->prepare("INSERT INTO admin (ad_un, ad_password) VALUES (?, ?)");

        if ($stmt) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password before storing it
            $stmt->bind_param("ss", $username, $hashed_password);

            if ($stmt->execute()) {
                $_SESSION['success'] = "Data successfully inserted.";
            } else {
                $_SESSION['error'] = "Error saving data.";
            }

            $stmt->close();
        } else {
            $_SESSION['error'] = "Unable to prepare database query.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert into Admin</title>
</head>
<body>
    <?php
    if (isset($_SESSION['error'])) {
        echo "<div style='color: red;'>" . $_SESSION['error'] . "</div>";
        unset($_SESSION['error']);
    }

    if (isset($_SESSION['success'])) {
        echo "<div style='color: green;'>" . $_SESSION['success'] . "</div>";
        unset($_SESSION['success']);
    }
    ?>

    <form action="" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Submit</button>
    </form>
</body>
</html>
