<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit;
}

include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $Bo_title = $_POST["Bo_title"];
    $Au_name = $_POST["Au_name"];
    $Bo_genre = $_POST["Bo_genre"];
    $Bo_pubyear = $_POST["Bo_pubyear"];

    // Insert the author into the authors table
    $sql_author = "INSERT INTO authors (Au_name) VALUES (?)";
    $stmt_author = $conn->prepare($sql_author);
    $stmt_author->bind_param("s", $Au_name);
    $stmt_author->execute();

    // Get the last inserted author ID (Au_id)
    $Au_id = $conn->insert_id;

    // Insert the book into the books table
    $sql_book = "INSERT INTO books (Bo_title, Bo_genre, Bo_pubyear) VALUES (?, ?, ?)";
    $stmt_book = $conn->prepare($sql_book);
    $stmt_book->bind_param("ssi", $Bo_title, $Bo_genre, $Bo_pubyear);
    $stmt_book->execute();

    // Get the last inserted book ID (Bo_id)
    $Bo_id = $conn->insert_id;

    // Insert the relationship into the writes table
    $sql_writes = "INSERT INTO writes (Bo_id, Au_id) VALUES (?, ?)";
    $stmt_writes = $conn->prepare($sql_writes);
    $stmt_writes->bind_param("ii", $Bo_id, $Au_id);
    $stmt_writes->execute();

    // Check if everything is successful
    if ($stmt_book->affected_rows > 0 && $stmt_writes->affected_rows > 0) {
        // Book added successfully, redirect to the books list page
        header("Location: index.php");
        exit();
    } else {
        echo "Error: Could not add the book and/or link to the author.";
    }

    // Close the statements and connection
    $stmt_author->close();
    $stmt_book->close();
    $stmt_writes->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book - Library System</title>
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

<?php
    include "navbar.php";
?>

    <div class="container mt-5">
        <!-- Back Button -->
        <div class="mb-3">
            <a href="javascript:history.back()" class="btn btn-secondary btn-sm">&lt;</a>
        </div>

        <h1>Add New Book</h1>

        <!-- Add Book Form -->
        <form action="add_books.php" method="POST">
            <div class="mb-3">
                <label for="Bo_title" class="form-label">Title</label>
                <input type="text" class="form-control" id="Bo_title" name="Bo_title" required>
            </div>

            <div class="mb-3">
                <label for="Au_name" class="form-label">Author</label>
                <input type="text" class="form-control" id="Au_name" name="Au_name" required>
            </div>

            <div class="mb-3">
                <label for="Bo_genre" class="form-label">Genre</label>
                <input type="text" class="form-control" id="Bo_genre" name="Bo_genre" required>
            </div>

            <div class="mb-3">
                <label for="Bo_pubyear" class="form-label">Published Year</label>
                <input type="number" class="form-control" id="Bo_pubyear" name="Bo_pubyear" required>
            </div>

            <button type="submit" class="btn btn-primary">Add Book</button>
        </form>
    </div>
</body>

</html>
