<?php
$servername = "localhost";
$Username = "root";
$Password = "";
$dbname = "gearUp";
// Create a database connection
$conn = new mysqli($servername, $Username, $Password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//include_once $_SERVER['DOCUMENT_ROOT'] . '/inti/gearUp/db_connection.php';
?>