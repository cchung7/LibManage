<!-- login  -->
<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page - T10 LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"></script>

    <style>
        html, body {
            height: 100%; /* Ensure body takes full height */
        }

        body {
            overflow: auto; /* Allow scrolling if content exceeds height */
        }

        .container {
            min-height: 100vh; /* Ensure container takes up full viewport height */
        }

        .table-container {
            max-height: 70vh; /* Optional: Allow scrolling inside the table if needed */
            overflow-y: auto;
        }
    </style>

    <script>
        function goBack() {
            window.history.back();
        }
    </script>
</head>

<body>

<?php include "navbar.php"; ?>

<div class="container mt-5">
    <h1>Library Books</h1>

    <div class="d-flex justify-content-end mb-3">
        <a href="add_books.php" class="btn btn-primary">Add New Book</a>
    </div>

    <div class="mb-4">
        <form class="d-flex" method="GET" action="">
            <input class="form-control me-2" type="search" placeholder="Search for books by title or author" aria-label="Search" name="search" value="<?php echo htmlspecialchars(isset($_GET['search']) ? $_GET['search'] : ''); ?>">
            <button class="btn btn-outline-primary" type="submit">Search</button>
        </form>
    </div>

    <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
        <div class="mb-4">
            <a href="javascript:goBack()" class="btn btn-secondary btn-sm">&lt; Back</a>
        </div>
    <?php endif; ?>

    <div class="table-container">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th scope="col">Title:</th>
                    <th scope="col">Authors:</th>
                    <th scope="col">Genre:</th>
                    <th scope="col">Published Year:</th>
                    <th scope="col">Actions:</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include "config.php";

                $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
                $searchTermWithWildcards = '%' . $searchTerm . '%';

                $sql = "
                    SELECT books.Bo_id, books.Bo_title, books.Bo_genre, books.Bo_pubyear, authors.Au_name
                    FROM books
                    JOIN writes ON books.Bo_id = writes.Bo_id
                    JOIN authors ON writes.Au_id = authors.Au_id
                    WHERE books.Bo_title LIKE ? OR authors.Au_name LIKE ?
                ";

                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    echo "Error: " . $conn->error;
                }

                $stmt->bind_param('ss', $searchTermWithWildcards, $searchTermWithWildcards);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row["Bo_title"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["Au_name"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["Bo_genre"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["Bo_pubyear"]) . "</td>";
                        echo "<td>";
                        echo "<a href='update_books.php?id=" . $row["Bo_id"] . "' class='btn btn-warning btn-sm me-2'>Update</a>";
                        echo "<a href='remove_books.php?id=" . $row["Bo_id"] . "' class='btn btn-danger btn-sm'>Remove</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center'>No books found</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</div>
</body>

</html>
