
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gadgetShop";

// Create a new connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



session_start();
mysqli_select_db($conn, $dbname);
$usernames=$_SESSION['username'];

$stmt = $conn->prepare("SELECT email FROM users WHERE usernames = ?");
$stmt->bind_param("s", $usernames);
$stmt->execute();
// Get the result
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch the result as an associative array
    $user = $result->fetch_assoc();
    $encrypted_email = $user['email']; // Access the email field
    

} else {
    echo "No user found with that username.";
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="tracking.css">
</head>
<body>
    

<div id="navContainer"> 
    <img id="logoImg" src="../../assets/logo.jpg" alt="" srcset="">
    <button class="button" id="home">Trust Toradora</button>
    <button class="button" id="cart" onclick="window.location.href = '../product/cart.php';"><?php echo 'Shopping Cart'; ?></button>
    <button class="button" id="tracking"><?php echo 'Tracking' ?></button>
    <button class="button" id="refund" type="submit" name="refund" value="">refund</button>
    <button class="button" id="name"><?php echo $usernames ?></button>
    <form action="../login/logout.php" method="POST">
      <button type="submit" id="logOut" class="button">Log Out</button>
    </form>    
</div>
<div id="container">
    
<div id="bigTitle">Order Status</div>

<div class='title'>
    <div class="Orders_id"><?php echo 'Orders_id'; ?></div>
    <div class="Order_id"><?php echo 'Order_id'; ?></div>
    <div class="email"><?php echo 'Email'; ?></div>
    <div class="Address"><?php echo 'Address'; ?> </div>
    <div class="Product"><?php echo 'Product'; ?> </div>
    <div class="product_name"><?php echo 'Product Name'; ?></div>
    <div class="Pricess"><?php echo 'Price'; ?></div>
    <div class="quantity"><?php echo 'Quantity'; ?></div>
    <div class="total_price"><?php echo 'Total Price'; ?></div>
    <div class="order_status"><?php echo 'Order Status'; ?></div>
    <div class="purchase_date"><?php echo 'Purchase date'; ?></div>
</div>
<?php

// Fetch all orders for the user, excluding those in 'cart' status
$selectOrdersQuery = "SELECT * FROM orders WHERE email='$encrypted_email' AND order_status <> 'cart' ORDER BY order_id ASC";
$selectOrdersResult = $conn->query($selectOrdersQuery);

if ($selectOrdersResult && $selectOrdersResult->num_rows > 0) {
    // Loop through the results
    while ($row = $selectOrdersResult->fetch_assoc()) {
        $product_id = $row['product_id'];
        $product_name = $row['product_name'];
        $date = $row['date'];
        $encrypted_address = $row['address'];
        include_once $_SERVER['DOCUMENT_ROOT'] . '/inti/gadgetShop/encryption_helper.php';
        $address = openssl_decrypt($encrypted_address, 'AES-256-CBC', $encryption_key, 0, $encryption_iv);

        $email = openssl_decrypt($encrypted_email, 'AES-256-CBC', $encryption_key, 0, $encryption_iv);

        $price = $row['price'];
        $image = $row['image'];
        $imageUrl = "/inti/gadgetShop/assets/" . $image;
        $quantity = $row['quantity'];
        $order_status = $row['order_status'];
        $total_price = $row['total_price'];
        $button_id = $product_id;
        
        ?>
        <div class="content">
            <div id="orders_id" class='itemContent'><?php echo $row['orders_id']; ?></div>
            <div id="order_id" class='itemContent'><?php echo $row['order_id']; ?></div>
            <div id="user_id" class='itemContent'><?php echo $email; ?></div>
            <div id="Address" class='itemContent'><?php echo $address; ?></div>
            <img class="item" class='itemContent' src="<?php echo $imageUrl; ?>" alt="">
            <div class="product_name" class='itemContent'><?php echo $product_name; ?></div>
            <div id="price" class='itemContent'><?php echo 'RM' . $price; ?></div>
            <div id="quantity" class='itemContent'>x<?php echo $quantity; ?></div>
            <div id="total_price" class='itemContent'><?php echo 'RM' . $total_price; ?></div> 
            <div id="order_status" class='itemContent'><?php echo $order_status; ?></div> 
            <div id="order_date" class='itemContent'><?php echo $date; ?></div> 
            <form action="refundRequestForm.php" method="post">
                <input type="hidden" name="orders_id" value="<?php echo $row['orders_id']; ?>">
                <input type="hidden" name="product_name" value="<?php echo $product_name; ?>">
                <button id="refunds" class="button" type="submit" name="refund">Refund</button>
            </form>
        </div>
        <?php
    }
} else {
    echo "No orders found for this user.";
}
?>

</div>

</div>

</div>

</body>
<script src="tracking.js"></script>
</html>