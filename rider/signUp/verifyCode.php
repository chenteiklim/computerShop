<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gearUp";

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
$stmt = $conn->prepare("SELECT emailCode FROM rider WHERE email = ? AND username = ?");
echo($email);
echo $havenotencryptemail;
$stmt->bind_param("ss", $email, $username);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($hashedEmailCode);
$stmt->fetch();
if ($stmt->num_rows > 0) {
    // Verify the primary email code
    if (password_verify($primaryCode, $hashedEmailCode)) {

        // Close the first statement before preparing a new one
        $stmt->close();

        // Check if another user exists with the same email but a different username
        $stmt = $conn->prepare("SELECT 1 FROM rider WHERE email = ? AND username <> ?");
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Another user exists with the same email but different username
            $stmt->close();

            // Prepare DELETE statement
            $delete_stmt = $conn->prepare("DELETE FROM rider WHERE email = ? AND username = ?");
            $delete_stmt->bind_param("ss", $email, $username);

            if ($delete_stmt->execute()) {
                $delete_stmt->close();
                header("Location: ../login/login.html?success=2");
                exit();
            } else {
                echo "Error deleting user.";
            }
        }
        else {   
            // Prepare the update query
            $updateSql = "UPDATE rider SET emailCode = '1' WHERE email = ?";
            $updateStmt = $conn->prepare($updateSql);
        
            if ($updateStmt === false) {
                die("Error in preparing statement: " . $conn->error);
            }
        
            // Bind the email parameter
            $updateStmt->bind_param("s", $email);
        
            // Execute the update
            if ($updateStmt->execute()) {
                if ($updateStmt->affected_rows > 0) {
                    header("Location: ../mainpage/riderMainpage.php");
                } else {
                    echo "No rows updated. Email might not exist.<br>";
                }
            } else {
                echo "Error executing update: " . $updateStmt->error . "<br>";
            }
        
            // Close the statement
            $updateStmt->close();
        
            // Redirect or exit
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