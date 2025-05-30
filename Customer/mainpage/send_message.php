<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/inti/gearUp/db_connection.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/inti/gearUp/pusher.php';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $message = $_POST['message'];
    $senderName = $_POST['senderName'];  // Now refers to the seller
    $receiverName = $_POST['receiverName']; // Now refers to the customer
    // Generate chat room identifier
    // Get sender role
$stmt = $conn->prepare("SELECT role FROM users WHERE usernames = ?");
$stmt->bind_param("s", $senderName);
$stmt->execute();
$result = $stmt->get_result();
$sender = $result->fetch_assoc();
if (!$sender) {
    echo "Sender not found!";
    exit();
}

session_start();
$sellerName= $_SESSION['sellerName'];
$customerName =$_SESSION['customerName'];$sender_role = $sender['role'];
        // Get receiver role
        $stmt = $conn->prepare("SELECT role FROM users WHERE usernames = ?");
        $stmt->bind_param("s", $receiverName);
        $stmt->execute();
        $result = $stmt->get_result();
        $receiver = $result->fetch_assoc();
        if (!$receiver) {
            echo "Receiver not found!";
            exit();
        }
          
    $chat_room = ($senderName < $receiverName) ? "{$senderName}_{$receiverName}" : "{$receiverName}_{$senderName}";
       
    // Insert message into database
    $stmt = $conn->prepare("INSERT INTO messages (chat_room, senderName, receiverName, senderRole, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $chat_room, $senderName, $receiverName, $sender_role, $message);
    if ($stmt->execute()) {
        // Notify Pusher
        $pusher->trigger($chat_room, 'new-message', [
            'sender_name' => $senderName,
            'message' => $message,
            'sender_role' => $sender_role
        ]);
        echo "Message sent!";
    } else {
        echo "Error saving message: " . $stmt->error;
    }
}
?>