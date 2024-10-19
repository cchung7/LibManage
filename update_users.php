<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit;
}

include "config.php";

// Check if the user ID is provided in the URL - GET Request
if (isset($_GET['id'])) {
    $Us_id = $_GET['id'];

    // Fetch the existing user details
    $sql = "SELECT Us_id, Us_name, Us_email, Us_username, Us_password FROM users WHERE Us_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $Us_id);  // Bind Us_id as an integer
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $users = $result->fetch_assoc();  // Fetch the user details
    } else {
        echo "User not found";
        exit();
    }
} else {
    echo "Invalid request.";
    exit();
}

// Handle the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $Us_name = $_POST["Us_name"];
    $Us_email = $_POST["Us_email"];
    $Us_username = $_POST["Us_username"];
    $Us_password = $_POST["Us_password"];

    // Update the user details in the `users` table
    $sql_update_user = "UPDATE users SET Us_name = ?, Us_email = ?, Us_username = ?, Us_password = ? WHERE Us_id = ?";
    $stmt_update_user = $conn->prepare($sql_update_user);
    $stmt_update_user->bind_param("ssssi", $Us_name, $Us_email, $Us_username, $Us_password, $Us_id);  // Bind the variables

    // Execute the update query
    if ($stmt_update_user->execute()) {
        echo "User updated successfully";
        // Redirect back to the user list after the update
        header("Location: view_users.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }

    $stmt_update_user->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User - Library System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
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

    <h1 class="heading-spacing">Update User</h1>

    <!-- Update User Form -->
    <form action="update_users.php?id=<?php echo $Us_id; ?>" method="POST">
        <div class="mb-3">
            <label for="Us_name" class="form-label">Name</label>
            <input type="text" class="form-control" id="Us_name" name="Us_name" value="<?php echo htmlspecialchars($users['Us_name']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="Us_email" class="form-label">Email</label>
            <input type="email" class="form-control" id="Us_email" name="Us_email" value="<?php echo htmlspecialchars($users['Us_email']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="Us_username" class="form-label">Username</label>
            <input type="text" class="form-control" id="Us_username" name="Us_username" value="<?php echo htmlspecialchars($users['Us_username']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="Us_password" class="form-label">Password</label>
            <input type="password" class="form-control" id="Us_password" name="Us_password" value="<?php echo htmlspecialchars($users['Us_password']); ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Update User</button>
    </form>
</div>

</body>
</html>
