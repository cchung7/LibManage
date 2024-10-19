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
    <title>View Rentals - T10 Library Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" 
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"></script>
    
    <style>
        html, body {
            height: 100%; /* Ensure full height */
            overflow: auto; /* Allow scrolling */
        }

        .container {
            min-height: 100vh; /* Ensure the container takes up full viewport height */
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
            max-height: 70vh; /* Allow scrolling inside the table container if needed */
            overflow-y: auto;
        }
    </style>
    
    <script>
        // Function to handle the back button click
        function goBack() {
            var previousSearchUrl = document.querySelector('input[name="previous_search"]').value;
            if (previousSearchUrl) {
                window.location.href = previousSearchUrl;
            }
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
    <?php include "navbar.php"; ?>

    <div class="container mt-5">
        <h1 class="heading-spacing">User Book Rentals</h1>

        <!-- Search bar -->
        <div class="mb-4">
            <form class="d-flex" method="GET" action="">
                <input class="form-control me-2" type="search" placeholder="Search rentals by user name or title" aria-label="Search" name="search" value="<?php echo htmlspecialchars(isset($_GET['search']) ? $_GET['search'] : ''); ?>">
                <input type="hidden" name="search_submitted" value="<?php echo isset($_GET['search_submitted']) ? 'true' : 'false'; ?>">
                <input type="hidden" name="previous_search" value="<?php echo htmlspecialchars(isset($_GET['previous_search']) ? $_GET['previous_search'] : ''); ?>">
                <button class="btn btn-outline-primary" type="submit">Search</button>
            </form>
        </div>

        <!-- Back Button, shown only if a search term has been submitted -->
        <div class="mb-4 back-button">
            <a href="javascript:goBack()" class="btn btn-secondary btn-sm">&lt; Back</a>
        </div>

        <!-- Table for displaying rental records -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th scope="col">Book Title:</th>
                        <th scope="col">Author:</th>
                        <th scope="col">Rented By:</th>
                        <th scope="col">Rental Date:</th>
                        <th scope="col">Return Date:</th>
                        <th scope="col">Rental Status:</th>
                        <th scope="col">Actions:</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include "config.php";

                    // Get search term from query parameters
                    $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

                    // SQL query to join rentals, books, and users tables with search functionality
                    $sql = "
                        SELECT rentals.Rt_id, employees.Em_id, users.Us_name, books.Bo_title, authors.Au_name, rentals.Rent_date, rentals.Rtrn_date, rentals.Rent_status
                        FROM rentals
                        JOIN books ON rentals.Bo_id = books.Bo_id
                        JOIN writes ON books.Bo_id = writes.Bo_id
                        JOIN authors ON writes.Au_id = authors.Au_id
                        JOIN users ON rentals.Us_id = users.Us_id
                        JOIN employees ON rentals.Em_id = employees.Em_id
                        WHERE users.Us_name LIKE ? OR books.Bo_title LIKE ? OR authors.Au_name
                    ";

                    $stmt = $conn->prepare($sql);
                    $searchTermWithWildcards = '%' . $searchTerm . '%';
                    $stmt->bind_param('ss', $searchTermWithWildcards, $searchTermWithWildcards);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["Bo_title"] . "</td>";
                            echo "<td>" . $row["Au_name"] . "</td>";
                            echo "<td>" . $row["Us_name"] . "</td>";
                            echo "<td>" . $row["Rent_date"] . "</td>";
                            echo "<td>" . $row["Rtrn_date"] . "</td>";
                            echo "<td>" . $row["Rent_status"] . "</td>";
                            echo "<td>";
                            echo "<a href='confirm_rentals.php?id=" . $row["Rt_id"] . "' class='btn btn-warning btn-sm me-2'>Confirm</a>";
                            echo "<a href='delete_rentals.php?id=" . $row["Rt_id"] . "' class='btn btn-danger btn-sm'>Remove</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8' class='text-center'>No rentals found</td></tr>";
                    }

                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
