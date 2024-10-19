<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit;
}

include "config.php";

if (isset($_GET['id'])) {
    // Get the user_id from the URL
    $Us_id = $_GET['id'];

    // SQL query to remove the user
    $sql = "DELETE FROM users WHERE Us_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $Us_id);

    if ($stmt->execute()) {
        echo "User removed";

        // Reset the AUTO_INCREMENT value
        $sql = "SET @autoid := 0";
        $conn->query($sql);
        $sql = "UPDATE users SET Us_id = @autoid := (@autoid + 1)";
        $conn->query($sql);
        $sql = "ALTER TABLE users AUTO_INCREMENT = 1";
        $conn->query($sql);

        // Redirect back to the main page after deletion
        header("Location: view_users.php");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
