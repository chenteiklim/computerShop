<?php

session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gadgetShop";
// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
require '../vendor/autoload.php'; // Include Composer's autoload file

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


if (isset($_POST['submit'])) {
  $usernames = $_POST['username'];
  $email = $_POST['email'];
  $backupEmail = $_POST['backupEmail'];
  $passwords = $_POST['passwords'];
  $confirm_password = $_POST['confirm_password'];
  $address=$_POST['address'];
  $_SESSION['address'] = $address;
  $_SESSION['email'] = $email;
  $_SESSION['backupEmail'] = $backupEmail;
  $token = bin2hex(random_bytes(16)); // Generate a random token
  $_SESSION['token'] = $token;
  date_default_timezone_set('Asia/Kuala_Lumpur'); // Set your desired time zone
            $current_time = time();
            $expiry_time = $current_time + 300;
            
        
 // Define separate arrays for common sequences
 $commonLowerSequences = [
    '1234', '2345', '3456', '4567', '5678', '6789', '7890', '0123',
    '1111', '2222', '3333', '4444', '5555', '6666', '7777', '8888', '9999', '0000',
    'bbbb', 'cccc', 'dddd', 'eeee', 'ffff', 'gggg', 'hhhh', 'iiii', 'jjjj',
    'kkkk', 'llll', 'mmmm', 'nnnn', 'oooo', 'pppp', 'qqqq', 'aaaa',
    'rrrr', 'ssss', 'tttt', 'uuuu', 'vvvv', 'wwww', 'xxxx', 'yyyy', 'zzzz',
    'abcd', 'bcde', 'cdef', 'defg', 'efgh', 'fghi',
    'ghij', 'hijk', 'ijkl', 'jklm', 'klmn',
    'lmno', 'mnop', 'nopq', 'qrst', 'rstu',
    'stuv', 'tuvw', 'uvwx', 'vwxy', 'wxyz'
];

$commonUpperSequences = [
    'ABCD', 'BCDE', 'CDEF', 'DEFG', 'EFGH', 'FGHI', 'GHIJ', 'HIJK',
    'IJKL', 'JKLM', 'JKLMN', 'KLMO', 'LMNOP', 'MNOPQ', 'NOPQR', 'OPQRS', 'PQRST',
    'QRSTU', 'RSTUV', 'STUVW', 'TUVWX', 'UVWXY', 'VWXYZ','BBBB', 'CCCC', 'DDDD', 'EEEE', 'FFFF', 'GGGG', 'HHHH', 'IIII', 'JJJJ',
'KKKK', 'LLLL', 'MMMM', 'NNNN', 'OOOO', 'PPPP', 'QQQQ', 'AAAA'
];

// Function to check if password contains any common sequence
function containsCommonSequence($passwords, $lowerSequences, $upperSequences) {
    // Convert the password to lowercase for checking against lower sequences
    
    
    // Check against lowercase common sequences
    foreach ($lowerSequences as $sequence) {
        if (strpos($passwords, $sequence) !== false) {
            return true; // Match found in lower sequences
        }
    }

    // Check against uppercase common sequences
    foreach ($upperSequences as $sequence) {
        if (strpos($passwords, $sequence) !== false) {
            return true; // Match found in upper sequences
        }
    }

    return false; // No matches found in either array
}

function hasRepetitivePattern($passwords) {
    $length = strlen($passwords);

    // Loop through possible substring lengths
    for ($i = 1; $i <= $length / 2; $i++) {
        $substring = substr($passwords, 0, $i);
        $repeatCount = 1; // Start with the first instance of the pattern

        // Loop to check for repetitive patterns
        for ($j = $i; $j < $length; $j += $i) {
            $nextSubstring = substr($passwords, $j, $i);

            // If the next substring matches, increase the repeat count
            if ($substring === $nextSubstring) {
                $repeatCount++;
            } else {
                break; // Stop if the next part of the string doesn't match
            }

            // If the pattern repeats more than twice, return true (too many repetitions)
            if ($repeatCount > 2) {
                return true;
            }
        }
    }
    return false; // No excessive repetitive pattern found
}

    mysqli_select_db($conn, $dbname); 

    $sql = "SELECT * FROM users WHERE usernames = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usernames);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Redirect with an error message
        header("Location: register.html?success=1");
        exit();
    }

    $sql2 = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql2);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Redirect with an error message
        header("Location: register.html?success=2");
        exit();
    }


    else if ($email == $backupEmail) {
        header("Location: register.html?success=3");
        exit();
    } 

    
    // Check if password matches confirm password
    else if ($passwords != $confirm_password) {
        header("Location: register.html?success=4");
        exit();
    } 

    // Validate email format
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: register.html?success=5");
        exit();
    }
    // Validate email format
    else if (!filter_var($backupEmail, FILTER_VALIDATE_EMAIL)) {
        header("Location: register.html?success=6");
        exit();
    }

    // Check minimum length
    else if (strlen($passwords) < 10) {
        header("Location: register.html?success=7");
    }

    // Check for at least 1 special characters
    else if (preg_match_all('/[\W_]/', $passwords) < 4) {
        header("Location: register.html?success=8");
    }

    // Check for at least one uppercase letter
    else if (!preg_match('/[A-Z]/', $passwords)) {
        header("Location: register.html?success=9");
    }

    // Check for at least one lowercase letter
    else if (!preg_match('/[a-z]/', $passwords)) {
        header("Location: register.html?success=10");
    }

    // Check for at least one number
    else if (!preg_match('/[0-9]/', $passwords)) {
        header("Location: register.html?success=11");
    }

    elseif (containsCommonSequence($passwords, $commonLowerSequences, $commonUpperSequences)) {
        header("Location: register.html?success=12");
        exit(); // Ensure no further script execution
    }
    elseif (hasRepetitivePattern($passwords)) {
        header("Location: register.html?success=13");
        exit();
    }
        
   

// Check if the password contains any common sequence


// Check if the password contains a repetitive pattern



    // Hash the password
    $hashed_password = password_hash($passwords, PASSWORD_BCRYPT);

    $emailCode = rand(100000, 999999); // 6-digit code for primary email
    $backupEmailCode = rand(100000, 999999); // 6-digit code for backup email
    $hashedEmailCode = password_hash($emailCode, PASSWORD_BCRYPT);
    $hashedBackupEmailCode = password_hash($backupEmailCode, PASSWORD_BCRYPT);
    $param1 = 0;
    $param2 = 0;
    $sql = "INSERT INTO users (email, backupEmail, usernames, address, passwords, emailCode, backupEmailCode, ChangePwdEmailCode, ChangePwdbackupEmailCode) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssii", $email, $backupEmail, $usernames, $address, $hashed_password, $hashedEmailCode, $hashedBackupEmailCode, $param1, $param2);
    if ($stmt->execute()) {
        // Send verification email
         $mail = new PHPMailer(true);
        try {
            // Common settings
            $mail->isSMTP();
            $mail->Host = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Port = 2525;  // You can also use port 25, 465, or 587   
            $mail->Username = 'beb2839877c67c';  // Replace with your Mailtrap username
            $mail->Password = '42343f9bc18416';  // Replace with your Mailtrap password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Use TLS
            $mail->setFrom('PitStop@computerShop.com', 'Pit Stop');
            $mail->isHTML(true);
            $mail->Subject = 'Email Verification';
        
            // Send to primary email
            $mail->addAddress($email);
            $mail->Body = "<p>Your verification code for primary email {$email} is: <strong> $emailCode </strong></p>
            <p>Please enter this code on the verification page to verify your account.</p>";
            $mail->send();
            
            // Clear addresses for the next email
            $mail->clearAddresses();
            
            // Send to backup email
            $mail->addAddress($backupEmail);
            $mail->Body = "<p>Your verification code for backup email {$backupEmail} is: <strong> $backupEmailCode </strong></p>
            <p>Please enter this code on the verification page to verify your account.</p>";
            $mail->send();
             header("Location: checkRegister.php?token=$token&expiry=$expiry_time");
         
        } catch (Exception $e) {
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        }

    } else {
        // Handle database insert error
        echo "Error: " . $sql . "<br>" . $conn->error;
    } 
 
    $stmt->close();
    $conn->close();
    }