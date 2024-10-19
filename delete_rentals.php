<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit;
}

include "config.php";

if (isset($_GET['id'])) {
    // Get the Rt_id (Rental id & primary key) from the URL
    $Rt_id = $_GET['id'];

    // SQL query to remove the rental
    $sql = "DELETE FROM rentals WHERE Rt_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $Rt_id);

    if ($stmt->execute()) {
        echo "Rental removed successfully";

        // Reset the AUTO_INCREMENT value
        $sql = "SET @autoid := 0";
        $conn->query($sql);
        $sql = "UPDATE rentals SET Rt_id = @autoid := (@autoid + 1)";
        $conn->query($sql);
        $sql = "ALTER TABLE rentals AUTO_INCREMENT = 1";
        $conn->query($sql);

        // Redirect back to the main page after deletion
        header("Location: view_rentals.php");
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
