
<?php

$servername = "localhost";
$Username = "root";
$Password = "";
$dbname = "gadgetShop";

$conn = new mysqli($servername, $Username, $Password);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

session_start();
mysqli_select_db($conn, $dbname);
$selectNameQuery = "SELECT * FROM seller";
// Execute the query
$result = $conn->query($selectNameQuery);

if ($result->num_rows > 0) {
    // Fetch the row from the result
    $row = $result->fetch_assoc();
}
    // Get the address value from the fetched row
    $name = $row['usernames'];

  ?>
<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product</title>
    <link rel="stylesheet" href="mainpage.css">


</head>

<div id="navContainer"> 
    <img id="logoImg" src="../../assets/logo.jpg" alt="" srcset="">
    <button class="button" id="home">Pit Stop</button>

    <button class="button" id="name"><?php echo $name ?></button>
    <form action="../login/logout.php" method="POST">
      <button type="submit" id="logout" class="button">Log Out</button>
    </form> 
</div>
<div>

</div>
<div id='content'>
  <div>
  <p id='title'>Welcome to be a seller. Boost your income by providing best quality product and service. </p>
  <img id='gadget' src="../../assets/deco.png" alt="">
  </div>
  <div id="container">
    <div id="messageContainer"></div>
    <div class="view">
    <button id="view" class="btn"><?php echo 'View All Products' ?></button>
    </div>
    <div class="sell">
    <button id="sell" class="btn"><?php echo 'Create Product' ?></button>
    </div>
    <div class="edit">
    <button id="edit" class="btn"><?php echo 'Edit Product' ?></button>
    </div>
    <div class="edit">
    <button id="delete" class="btn"><?php echo 'delete Product' ?></button>
    </div>
    <div class="sales">
    <button id="sales" class="btn"><?php echo 'Sales' ?></button>
    </div>
</div>

</div>

<script src="mainpage.js"></script>
  
   