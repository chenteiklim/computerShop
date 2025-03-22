<?php
    session_start(); // Start session to access $_SESSION
    include_once $_SERVER['DOCUMENT_ROOT'] . '/inti/gadgetShop/db_connection.php';

    if (!isset($_SESSION['username'])) {
        die("User not logged in.");
    }

    $username = $_SESSION['username'];
    echo "Username: $username <br>";

    // Fetch rider ID based on username
    $selectQuery = $conn->prepare("SELECT rider_id FROM rider WHERE username = ?");
    $selectQuery->bind_param("s", $username);
    $selectQuery->execute();
    $result = $selectQuery->get_result();

    if ($result->num_rows === 0) {
        die("Rider not found.");
    }

    // Fetch the rider_id
    $row = $result->fetch_assoc();
    $rider_id = $row['rider_id'];
    echo "Rider ID: $rider_id <br>";

    // Update the order status for the rider
    $stmt = $conn->prepare("UPDATE orders SET order_status = 'arrived' WHERE assigned_rider = ?");
    $stmt->bind_param("i", $rider_id);

    if ($stmt->execute()) {
        $message = "Order status update successfully.";
        header("Location: order.php?message2=" . urlencode($message));
    } else {
        echo "Error updating order: " . $conn->error;
    }

    // Close statements and connection
    $stmt->close();
    $selectQuery->close();
    $conn->close();
?>