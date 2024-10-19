<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit;
}

include "config.php";

// Get rental_id from GET request
$rentalId = isset($_GET['id']) ? $_GET['id'] : '';

if ($rentalId) {
    // Prepare SQL to update rental status
    $sql = "UPDATE rentals SET Rent_status = 'CONFIRMED' WHERE Rt_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('i', $rentalId);
        if ($stmt->execute()) {
            // Redirect to view_rentals.php after successful update
            header("Location: view_rentals.php");
            exit;
        } else {
            echo 'Error updating the rental status.';
        }
        $stmt->close();
    } else {
        echo 'Error preparing the SQL statement.';
    }
}

$conn->close();
?>
