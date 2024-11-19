<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gadgetShop";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
mysqli_select_db($conn, $dbname); 

session_start();

// Check if the session variables are set
$email = $_SESSION['email'] ?? null;
$havenotencryptemail = $_SESSION['haveNotEncrypt'] ?? null;
$username =$_SESSION['username'];

if (!$email) {
    echo "<h1>This Website is Not Accessible</h1>";
    echo "<p>Sorry, but you do not have permission to access this page. Please ensure you are logged in and have registered your email.</p>";
    exit;  // Stop further execution of the script
}

// Concatenate primary email verification code input
$primaryCode = implode('', $_POST['primaryCode']);

// Retrieve the hashed codes from the database
$stmt = $conn->prepare("SELECT emailCode FROM users WHERE email = ? AND usernames = ?");
$stmt->bind_param("ss", $email, $username);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($hashedEmailCode);
$stmt->fetch();

if ($stmt->num_rows > 0) {
    // Verify the primary email code
    if (password_verify($primaryCode, $hashedEmailCode)) {

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND usernames <> ?");

        // Bind the email parameter to the query (bind_param data type: 's' for string)
        $stmt->bind_param("ss", $email, $username);

        // Execute the statement
        $stmt->execute();

        // Get the result
        $result = $stmt->get_result();

        // Check if the email exists
        if ($result->num_rows > 0) {
            // Check if the email exists
            $stmt->close();  // Close the select statement

            // Prepare the DELETE query to remove the record
            $delete_stmt = $conn->prepare("DELETE FROM users WHERE email = ? AND usernames = ?");
            $delete_stmt->bind_param("ss", $email, $username);
        
            // Execute the DELETE query
            if ($delete_stmt->execute()) {
                // If the deletion is successful, redirect to login page with success message
                header("Location: ../login/login.html?success=2");
                exit();
            }
            else{
                echo('error delete user');
            }
        }

        else {
            $updateSql = "UPDATE users SET emailCode = '1' WHERE email = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("s", $email);
            // Email does not exist, redirect to mainpage.html
            header("Location: mainpage.html");
            exit();
        }
    } 
  
    else {
         header("Location: checkRegister.php?success=1");
    }
} 

else {
    // User not found
    header("Location: checkRegister.php?success=2");
}
?>