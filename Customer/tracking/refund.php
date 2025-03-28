<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/inti/gadgetShop/db_connection.php';



if (!isset($_POST['refund']) || !isset($_FILES['proof'])) {
    die("Invalid request.");
}

$order_id = $_POST['refund'];
$productName= $_POST['productName'];
$username = $_SESSION['username'];
$reason = $_POST['reason'] ?? ''; // Get reason if available

// File upload handling
$targetDir = $_SERVER['DOCUMENT_ROOT'] . "/inti/gadgetShop/assets/"; // Store in assets folder

if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

$fileName = basename($_FILES["proof"]["name"]);
$targetFile = $targetDir . $fileName;
$fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

$allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'mov', 'avi']; // Allow images & videos

if (!in_array($fileType, $allowedTypes)) {
    die("Only JPG, PNG, GIF images or MP4, MOV, AVI videos are allowed.");
}

if (move_uploaded_file($_FILES["proof"]["tmp_name"], $targetFile)) {
    $fileUrl = "/inti/gadgetShop/assets/" . $fileName; // Relative path for database

    // Insert refund request into the database
    $stmt = $conn->prepare("INSERT INTO refund_requests (order_id, usernames, productName, reason, proof, status, date) 
                            VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
    $stmt->bind_param("issss", $order_id, $username, $productName, $reason, $fileUrl);

    if ($stmt->execute()) {
        echo "Refund request submitted successfully!";
    } else {
        echo "Database error: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "Error uploading file.";
}

$conn->close();
?>
