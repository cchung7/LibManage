<!-- PHP CONNECTION -->
<?php
$servername = "localhost";
$username = "u779102717_cwc130330";
$password = "142773Cc";
$dbname = "u779102717_jaydemo";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

?>