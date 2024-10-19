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

    // Fetch the existing book details with its author
    $sql = "SELECT books.Bo_id, Bo_title, Bo_genre, Bo_pubyear, authors.Au_name, authors.Au_id
            FROM books
            JOIN writes ON books.Bo_id = writes.Bo_id
            JOIN authors ON writes.Au_id = authors.Au_id
            WHERE books.Bo_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $Bo_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $books = $result->fetch_assoc();
    } else {
        echo "Book not found";
        exit();
    }
} else {
    echo "Invalid request.";
    exit();
}

// Handle the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $Bo_title = $_POST["Bo_title"];
    $Au_name = $_POST["Au_name"];
    $Bo_genre = $_POST["Bo_genre"];
    $Bo_pubyear = $_POST["Bo_pubyear"];
    $Au_id = $books['Au_id'];  // Fetch Au_id from existing data

    // Step 1: Update the book details in the `books` table
    $sql_update_book = "UPDATE books SET Bo_title = ?, Bo_genre = ?, Bo_pubyear = ? WHERE Bo_id = ?";
    $stmt_update_book = $conn->prepare($sql_update_book);
    $stmt_update_book->bind_param("ssii", $Bo_title, $Bo_genre, $Bo_pubyear, $Bo_id);
    
    // Step 2: Update the author details in the `authors` table
    $sql_update_author = "UPDATE authors SET Au_name = ? WHERE Au_id = ?";
    $stmt_update_author = $conn->prepare($sql_update_author);
    $stmt_update_author->bind_param("si", $Au_name, $Au_id);

    // Execute both updates
    if ($stmt_update_book->execute() && $stmt_update_author->execute()) {
        echo "Book and author updated successfully";
        // Redirect back to the main page after the update
        header("Location: index.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }

    $stmt_update_book->close();
    $stmt_update_author->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Book - Library System</title>
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

    <h1 class="heading-spacing">Update Book</h1>

    <!-- Update Book Form -->
    <form action="update_books.php?id=<?php echo $Bo_id; ?>" method="POST">
        <div class="mb-3">
            <label for="Bo_title" class="form-label">Title</label>
            <input type="text" class="form-control" id="Bo_title" name="Bo_title" value="<?php echo htmlspecialchars($books['Bo_title']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="Au_name" class="form-label">Authors</label>
            <input type="text" class="form-control" id="Au_name" name="Au_name" value="<?php echo htmlspecialchars($books['Au_name']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="Bo_genre" class="form-label">Genre</label>
            <input type="text" class="form-control" id="Bo_genre" name="Bo_genre" value="<?php echo htmlspecialchars($books['Bo_genre']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="Bo_pubyear" class="form-label">Published Year</label>
            <input type="number" class="form-control" id="Bo_pubyear" name="Bo_pubyear" value="<?php echo htmlspecialchars($books['Bo_pubyear']); ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Update Book</button>
    </form>
</div>

</body>
</html>
