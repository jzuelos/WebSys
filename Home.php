<?php
session_start(); // Start session at the top
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database.php';

$conn = Database::getInstance();
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//Check if the user is logged in by verifying if 'user_id' exists in the session
if (!isset($_SESSION['customer_id'])) {
    header("Location: userreg.php"); // Redirect to login page if user is not logged in
    exit; // Stop further execution after redirection
}
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="Home.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>MotoMagX</title>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container d-flex justify-content-center align-items-center" style="height: 70vh;">
        <div class="row w-100 position-relative">
            <!-- First column: Price -->
            <div class="col-4 d-flex justify-content-center align-items-start position-relative">
                <div class="p-3 rounded shadow-sm text-center w-80"
                    style="background-color: white; border: 1px solid black;">
                    <p id="price" class="m-0 display-4 text-black">₱2500</p>
                </div>
            </div>

            <!-- Second column: Centered Content with Oval (Hidden Square) -->
            <div class="col-4 d-flex justify-content-center align-items-center position-relative">
                <!-- Oval (acting as background) -->
                <div class="text-white d-flex justify-content-center align-items-center shadow-lg"
                    style="width: 800px; height: 400px; border-radius: 50%; background-color: #339f62; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: -1;">
                    <!-- Image inside the oval -->
                    <img id="brandImage" src="" alt="Brand Image"
                        style="max-width: 100%; max-height: 100%; object-fit: contain;">
                </div>
                <!-- Hidden square (hidden using visibility: hidden) -->
                <div class="bg-primary text-white d-flex justify-content-center align-items-center rounded shadow-lg"
                    style="width: 300px; height: 300px; visibility: hidden;">
                    <p class="m-0">pautot lang to</p>
                </div>
            </div>

            <!-- Third column: Buy Now Button with BOM X text above -->
            <div class="col-4 d-flex flex-column justify-content-center align-items-center position-relative">
                <!-- BOM X text above the button -->
                <h2 id="bomxText" class="mb-4"
                    style="font-family: 'Roboto', sans-serif; font-size: 36px; font-weight: bold; text-transform: uppercase; color: white; text-shadow: 2px 2px 0px black, -2px -2px 0px black, 2px -2px 0px black, -2px 2px 0px black;">
                    NEW!
                </h2>
                <!-- Adding more margin-bottom to create more space between the text and button -->
                <button class="btn btn-success btn-lg w-75 mb-3" style="background-color: black;">BUY NOW!</button>
            </div>
        </div>
    </div>

    <div class="features">
        <div class="feature">
            <img src="./img/shipping.png" alt="" class="featureIcon">
            <span class="featureTitle">FREE SHIPPING</span>
            <span class="featureDesc">Free worldwide shipping on all orders.</span>
        </div>
        <div class="feature">
            <img class="featureIcon" src="./img/return.png" alt="">
            <span class="featureTitle">30 DAYS RETURN</span>
            <span class="featureDesc">No question return and easy refund in 14 days.</span>
        </div>
        <div class="feature">
            <img class="featureIcon" src="./img/gift.png" alt="">
            <span class="featureTitle">GIFT CARDS</span>
            <span class="featureDesc">Buy gift cards and use coupon codes easily.</span>
        </div>
        <div class="feature">
            <img class="featureIcon" src="./img/contact.png" alt="">
            <span class="featureTitle">CONTACT US!</span>
            <span class="featureDesc">Keep in touch via email and support system.</span>
        </div>
    </div>

    <div class="product" id="product">
        <img src="./img/boomx.png" alt="" class="productImg">
        <div class="productDetails">
            <h1 class="productTitle">BOM X</h1>
            <h2 class="productPrice">₱2500</h2>
            <p class="productDesc">Lorem ipsum dolor sit amet consectetur impal adipisicing elit. Alias assumenda
                dolorum
                doloremque sapiente aliquid aperiam.</p>
            <div class="colors">
                <div class="color"></div>
                <div class="color"></div>
                <div class="color"></div>
            </div>
            <div class="brands">
                <select id="motorcycleBrands" name="motorcycleBrands">
                    <option value="" selected disabled>Select a Model</option>
                    <option value="hondaClick">Honda Click</option>
                    <option value="sniper">Sniper</option>
                    <option value="nmax">Nmax</option>
                    <option value="suzuki">Aerox</option>
                </select>
            </div>
            <button class="productButton">BUY NOW!</button>
        </div>

        <div class="payment">
            <h1 class="payTitle">Personal Information</h1>
            <label>Name and Surname</label>
            <input type="text" placeholder="John Doe" class="payInput">
            <label>Phone Number</label>
            <input type="text" placeholder="+1 234 5678" class="payInput">
            <label>Address</label>
            <input type="text" placeholder="Elton St 21 22-145" class="payInput">
            <h1 class="payTitle">Card Information</h1>
            <div class="cardIcons">
                <img src="./img/visa.png" width="40" alt="" class="cardIcon">
                <img src="./img/master.png" alt="" width="40" class="cardIcon">
            </div>
            <input type="password" class="payInput" placeholder="Card Number">
            <div class="cardInfo">
                <input type="text" placeholder="mm" class="payInput sm">
                <input type="text" placeholder="yyyy" class="payInput sm">
                <input type="text" placeholder="cvv" class="payInput sm">
            </div>
            <button class="payButton">Checkout!</button>
            <span class="close">X</span>
        </div>
    </div>
    <?php include 'footer.php'; ?>
    <script src="./Home.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>