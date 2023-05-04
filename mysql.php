<?php
$servername = "localhost";
$username = "proj";
$password = "proj102030";
$dbname = "proj";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
