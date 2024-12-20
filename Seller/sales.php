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

$sellerName=$_SESSION['username'];

mysqli_select_db($conn, $dbname);


$sql2 = "SELECT user_id FROM users";
$result2 = $conn->query($sql2);

if ($result2->num_rows > 0) {
    // Fetch the user ID from the result
    $row = $result2->fetch_assoc();
    $user_id = $row['user_id'];
}
mysqli_select_db($conn, $dbname);
$maxIdQuery = "SELECT MAX(order_id) AS max_id FROM orders";
$maxIdResult = $conn->query($maxIdQuery);

if ($maxIdResult && $maxIdResult->num_rows > 0) {
    $row9 = $maxIdResult->fetch_assoc();
    $maxId = $row9['max_id'];
}

// Query to retrieve all rows in ascending order
$selectRowsQuery = "SELECT * FROM orders ORDER BY order_id ASC";
$selectRowsResult = $conn->query($selectRowsQuery);

$rows = []; // Initialize an empty array to store the rows

if ($selectRowsResult && $selectRowsResult->num_rows > 0) {
    while ($row = $selectRowsResult->fetch_assoc()) {
        $rows[] = $row; // Add each row to the array
    }
}

// Loop through the array of rows
foreach ($rows as $row) {
    $product_id = $row['product_id'];
    $product_name = $row['product_name'];
    $price = $row['price'];
    $image = $row['image'];
    $quantity=$row['quantity'];
    $total_price=$row['total_price'];
    $imageUrl = "/inti/gadgetShop/assets/" . $image;

}


// Query to count the total number of rows in the table
$countQuery = "SELECT COUNT(*) AS total FROM orders";
$countResult = $conn->query($countQuery);

if ($countResult && $countResult->num_rows > 0) {
    $row6 = $countResult->fetch_assoc();
    $total_rows = $row6['total'];
} else {
    $total_rows = 0;
}



?>

<head>
    <style>

#container {
width:100%px;
background-color: #CDCDCD;
display: flex;
flex-direction:column;
height: auto;

 
}

.item{
    margin-left:20px;
 width:100px;
 height:100px;
}

.title{
    margin-left:40px;

    display: grid;
    grid-template-columns: repeat(13, 1fr);
    width:1400px;
    grid-gap: 3px;
    margin-top: 50px;
    margin-bottom:40px;
    font-size:18px;
   
}
.total_price{
    text-align:center;
    color:red;
}
.content{
    margin-left:40px;

    width:1400px;
    font-size:16px;
    display: grid;
    grid-template-columns: repeat(13, 1fr);
    grid-gap: 3px;
    align-items:center;
    margin-bottom: 50px;
    

}

.Product{
    font-size:20px;
    text-align:center;
}

.product_name {
    text-align:center;
    color: black;
}

.price {
    text-align:center;
    color: red;
}

.quantity {
    text-align:center;
}

#prices{
    text-align:center;
    color:red;
}
#checkOut{
    background-color:white;
    display:flex;
    font-size: 20px;
    width:1500px;
    margin-top:480px;
    height:400px;
    position: fixed;
}


#total_item{
    padding-left:10px;
}
#price{
    display:flex;
    align-items:center;
    justify-content:center;
    
}

#total_price{
    text-align:center;
}

#quantity{
    text-align:center;
}



    
    body{
        font-size:12px;
        background-color: bisque;
        height: auto;
        
    }
      
  
    #logOut{
        margin-left: 200px;
    }

    .total{
        margin-left:800px;
    }

    .user-info{
        display:flex;
        flex-direction:column;
    }
    .title2{
        margin-top:20px;
        font-size:22px;
        color:red;
        margin-right:500px;
    }

    .content2{
        margin-top:10px;
        font-size:18px;
        margin-right:200px;
        display:flex;
    }

    

    .item10{
        margin-left:100px
    }

    .row{
        margin-top:10px;
        display:flex;
    }

    .row2{
        margin-left:20px;
    }
    .row3{
        margin-left:82px;
    }
    .row4{
        margin-left:78px;
    }
    .text{
        margin-top:30px;
        margin-left:600px;
    }
    #checkOutbtn{
        margin-top:20px;
        margin-left:50px;
    }

    .payment{
        margin-left:100px;
    }
    html, body {
        margin: 0;
        padding: 0;
        width: 100%; /* Ensure full width */
        height: 100%; /* Ensure full height */
    }
    
    #navContainer {
        display: flex;
        background-color: black;
        width: 100%; /* Adjust width as needed */
        height: 80px; /* Adjust height as needed */
        
        /* Ensure it remains visible within the container */
      
      }

    .button {
        background-color: black;
        color: white;
        cursor: pointer;
        padding-left: 30px;
        padding-right: 30px;
        padding-top: 10px;
        padding-bottom: 10px;
        font-size: 12px;
        }
        #home{
            margin-left: 10px;
        }
    #names{
        margin-left: 850px;
    }
    #logout{
      height: 80px;    
    }
    #logoImg{
        margin-top: 25px;
        width: 35px;
        height: 35px;
        border-radius: 5px;
        margin-left: 100px;
    }
    
        button:hover{
            transform: scale(0.9);
            background: radial-gradient( circle farthest-corner at 10% 20%,  rgba(255,94,247,1) 17.8%, rgba(2,245,255,1) 100.2% );
          }
    </style>
</head>

<div id="navContainer"> 
    <img id="logoImg" src="../assets/logo.jpg" alt="" srcset="">
    <button class="button" id="home">Computer Shop</button>
    <button class="button" id="names"><?php echo $sellerName ?></button>
</div>

<div id="container">
    <div class='item10'>
    <div class='user-info'>
        
        </div>
    </div>

<div class='title'>
    <div class="Order_id"><?php echo 'Order_id'; ?></div>
    <div class="User_id"><?php echo 'User_id'; ?></div>
    <div class="Product"><?php echo 'Product'; ?> </div>
    <div class="product_name"><?php echo 'Product Name'; ?></div>
    <div class="price"><?php echo 'Price'; ?></div>
    <div class="quantity"><?php echo 'Quantity'; ?></div>
    <div class="total_price"><?php echo 'Total Price'; ?></div>
    <div class="order_status"><?php echo 'Order Status'; ?></div>
    <div class="purchase_date"><?php echo 'Purchase date'; ?></div>
</div>

<?php

$selectNumberRows = "SELECT * FROM orders ORDER BY order_id ASC";
$selectNumberResult = $conn->query($selectRowsQuery);

$rows = []; // Initialize an empty array to store the rows

if ($selectNumberResult && $selectNumberResult->num_rows > 0) {
    while ($row = $selectNumberResult->fetch_assoc()) {
        $rows[] = $row; // Add each row to the array
    }
}

// Get the total number of rows
$total_rows = count($rows);






$grandTotal=0;
// Loop through the orders
for ($order_id = 1; $order_id <= $maxId; $order_id++) {
    $selectRowQuery = "SELECT * FROM orders WHERE order_id = $order_id";
    $selectRowResult = $conn->query($selectRowQuery);

    if ($selectRowResult && $selectRowResult->num_rows > 0) {
        // Display order details
        while ($row = $selectRowResult->fetch_assoc()) {
            $product_id = $row['product_id'];
            $product_name = $row['product_name'];
            $user_id=$row['user_id'];
            $date = $row['date'];
            $price = $row['price'];
            $image = $row['image'];
            $quantity = $row['quantity'];
            $order_status = $row['order_status'];
            $total_price = $row['total_price'];
            $grandTotal += $total_price;
            $imageUrl = "/inti/gadgetShop/assets/" . $image;


            
        ?>
            <div class="content">
            <div id="order_id"><?php echo $order_id;?></div>
            <div id="user_id"><?php echo $user_id; ?></div>
            <img class="item" src="<?php echo $imageUrl; ?>" alt="">
            <div class="product_name"><?php echo $product_name; ?></div>
            <div id="price"><?php echo 'RM'.$price; ?></div>
            <div id="quantity">x<?php echo $quantity; ?></div>
            <div id="total_price"><?php echo 'RM'.$total_price; ?></div> 
            <div id="order_status"><?php echo $order_status?></div> 
            <div id="order_date"><?php echo $date?></div> 
            <form action="editOrder.php" method="POST">
                <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                <input type="hidden" name="order_status" value="<?php echo $order_status; ?>">
                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            </form>
            </div>
        
        <?php
        }
    }
}
?>
<?php
?>
</div>

<script>
    var homeButton = document.getElementById("home");
    homeButton.addEventListener("click", function(event) {
    event.preventDefault()
    window.location.href = "./mainpage/mainpage.php";
  });
</script>