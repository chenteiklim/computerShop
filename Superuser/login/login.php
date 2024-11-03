<?php
$servername = "localhost";
$Username = "root";
$Password = "";
$dbname = "gadgetShop";  

$conn = new mysqli($servername, $Username, $Password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
require '../../vendor/autoload.php'; // Include Composer's autoload file

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

session_start();

if (isset($_POST['submit'])) {
  $email = $_POST['email'];
  $password = $_POST['passwords'];

  // Select database
  mysqli_select_db($conn, $dbname); 

  // Retrieve the user's data from the database based on the provided email
  $sql = "SELECT * FROM superuser WHERE email = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    // Fetch the user's data
    $row = $result->fetch_assoc();
    $hashed_password = $row['passwords'];
   

    if (!password_verify($password, $hashed_password)) {
      header("Location: login.html?success=1");
      exit(); // Stop further script execution
    } 
   
    else if (password_verify($password, $hashed_password)) {
      $_SESSION['isLoginAdmin'] = true;
      $_SESSION['emailAdmin']=$email;
      header("Location: ../mainpage/mainpage.php");

    } 
  }
  else {
    // No user found with the provided email
    header("Location: login.html?success=2");
    exit();
  }
  $stmt->close();
}
$conn->close();
?>