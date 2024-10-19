<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit;
}

include "config.php";

// Check if the book ID is provided in the URL - GET Request
if (isset($_GET['id'])) {
    $Bo_id = $_GET['id'];

    // Delete the book from the database
    $sql = "DELETE FROM books WHERE Bo_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $Bo_id);

    if ($stmt->execute()) {
        echo "Book deleted successfully";

        // Reset the AUTO_INCREMENT value
        $sql = "SET @autoid := 0";
        $conn->query($sql);
        $sql = "UPDATE books SET Bo_id = @autoid := (@autoid + 1)";
        $conn->query($sql);
        $sql = "ALTER TABLE books AUTO_INCREMENT = 1";
        $conn->query($sql);

        // Redirect to the books list page after successful deletion
        header("Location: index.php");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
    exit();
}
?>
