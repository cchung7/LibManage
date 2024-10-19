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
    <title>Home Page - T10 Library Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Q2o+3dsh2BaMzHtXvsE+OdaecVVeyl8NcoHf9Jux+YAGnAZFGkmFlXofp34ZOIwA" crossorigin="anonymous"></script>

    <style>
        html, body {
            height: 100%; /* Ensure full height */
            overflow: auto; /* Allow scrolling */
        }

        .container {
            min-height: 100vh; /* Ensure container takes up full viewport height */
        }

        .heading-spacing {
            margin-bottom: 2rem; /* Adjust the spacing as needed */
        }

        .back-button {
            display: none; /* Hide by default */
            margin-bottom: 1rem;
        }

        /* Optional: Add max-height and scroll for large tables */
        .table-responsive {
            max-height: 70vh; /* Allow scrolling inside the table container */
            overflow-y: auto;
        }
    </style>

    <script>
        // Function to handle the back button click
        function goBack() {
            window.location.href = document.querySelector('input[name="previous_search"]').value;
        }

        // Function to show or hide the back button based on search submission
        function toggleBackButton() {
            const backButton = document.querySelector('.back-button');
            const previousSearch = document.querySelector('input[name="previous_search"]').value;
            backButton.style.display = previousSearch ? 'block' : 'none';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const searchForm = document.querySelector('form');
            searchForm.addEventListener('submit', function() {
                document.querySelector('input[name="previous_search"]').value = window.location.href;
            });

            // Call toggleBackButton on page load to set initial state
            toggleBackButton();
        });
    </script>
</head>

<body>

<!-- ** NAVBAR ** -->
<?php
    include "navbar.php";
?>

<div class="container mt-5">
    <h1 class="heading-spacing">Library Users</h1>

    <!-- Add button above search bar and on the right -->
    <div class="d-flex justify-content-end mb-3">
        <a href="add_users.php" class="btn btn-primary">Add New User</a>
    </div>

    <!-- Search bar -->
    <div class="mb-4">
        <form class="d-flex" method="GET" action="">
            <input class="form-control me-2" type="search" placeholder="Search users by name or email" aria-label="Search" name="search" value="<?php echo htmlspecialchars(isset($_GET['search']) ? $_GET['search'] : ''); ?>">
            <button class="btn btn-outline-primary" type="submit">Search</button>
            <input type="hidden" name="previous_search" value="<?php echo htmlspecialchars(isset($_GET['previous_search']) ? $_GET['previous_search'] : ''); ?>">
        </form>
    </div>

    <!-- Back Button, shown only if a previous search URL exists -->
    <div class="mb-4 back-button">
        <a href="javascript:goBack()" class="btn btn-secondary btn-sm">&lt; Back</a>
    </div>

    <!-- Table for displaying user records -->
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th scope="col">User Name:</th>
                    <th scope="col">User Email:</th>
                    <th scope="col">Join Date:</th>
                    <th scope="col">Actions:</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include "config.php";

                // Get search term from query parameters
                $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

                // SQL query to display user table with search functionality
                $sql = "
                    SELECT users.Us_id, users.Us_name, users.Us_email, users.Us_joindate, users.Us_username, users.Us_password
                    FROM users
                    WHERE users.Us_name LIKE ? OR users.Us_email LIKE ?
                ";

                $stmt = $conn->prepare($sql);
                $searchTermWithWildcards = '%' . $searchTerm . '%';
                $stmt->bind_param('ss', $searchTermWithWildcards, $searchTermWithWildcards);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["Us_name"] . "</td>";
                        echo "<td>" . $row["Us_email"] . "</td>";
                        echo "<td>" . $row["Us_joindate"] . "</td>";
                        echo "<td>";
                        echo "<a href='update_users.php?id=" . $row["Us_id"] . "' class='btn btn-warning btn-sm me-2'>Update</a>";
                        echo "<a href='delete_users.php?id=" . $row["Us_id"] . "' class='btn btn-danger btn-sm'>Remove</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' class='text-center'>No users found</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</div>
</body>

</html>
