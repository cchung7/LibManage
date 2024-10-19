<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit;
}

include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $Us_name = $_POST["Us_name"];
    $Us_email = $_POST["Us_email"];
    $Us_username = $_POST["Us_username"];
    $Us_password = $_POST["Us_password"]; // Storing the password as-is without hashing

    // Prepare the SQL statement with placeholders
    $sql = "INSERT INTO users (Us_name, Us_email, Us_username, Us_password, Us_joindate) 
            VALUES (?, ?, ?, ?, NOW())";

    // Prepare the statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind the parameters to the statement
        $stmt->bind_param("ssss", $Us_name, $Us_email, $Us_username, $Us_password);

        // Execute the statement
        if ($stmt->execute()) {
            // Success message and redirect
            echo "New User added successfully";
            header("Location: view_users.php");
            exit();
        } else {
            // Error during insertion
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        // Error during statement preparation
        echo "Error preparing statement: " . $conn->error;
    }

    // Close the connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User - Library System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"></script>
    <style>
    .heading-spacing {
        margin-bottom: 2rem; /* Adjust the spacing as needed */
    }
    </style>
</head>

<body>

<?php include "navbar.php"; ?>

<div class="container mt-5">
    <!-- Back Button -->
    <div class="mb-3">
        <a href="javascript:history.back()" class="btn btn-secondary btn-sm">&lt;</a>
    </div>

    <h1>Add New User</h1>

    <!-- Add User Form -->
    <form action="add_users.php" method="POST">
        <div class="mb-3">
            <label for="Us_name" class="form-label">User Name</label>
            <input type="text" class="form-control" id="Us_name" name="Us_name" required>
        </div>

        <div class="mb-3">
            <label for="Us_email" class="form-label">User Email</label>
            <input type="email" class="form-control" id="Us_email" name="Us_email" required>
        </div>

        <div class="mb-3">
            <label for="Us_username" class="form-label">Username</label>
            <input type="text" class="form-control" id="Us_username" name="Us_username" required>
        </div>

        <div class="mb-3">
            <label for="Us_password" class="form-label">Password</label>
            <input type="password" class="form-control" id="Us_password" name="Us_password" required>
        </div>

        <button type="submit" class="btn btn-primary">Add User</button>
    </form>
</div>

</body>

</html>
