<!-- NAVBAR TEMPLATE -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
    <a class="navbar-brand disabled-link" href="#">T10 LMS</a>
    
    <!-- T10 LMS Non-clickable Nav -->
    <style>
        .disabled-link {
            pointer-events: none; /* Makes the link non-clickable */
            text-decoration: none; /* Removes underline */
        }
    </style>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
    <?php
        if (!isset($_SESSION['loggedin'])) {
    ?>
            <li class="nav-item">
               <a class="nav-link" href="login.php">Login</a>
            </li>
    <?php
        }
    ?>
            <!-- BOOKS -->
            <li class="nav-item">
                <a class="nav-link" href="index.php">View Books</a>
            </li>
            <!-- USERS -->
            <li class="nav-item">
                <a class="nav-link" href="view_users.php">View Users</a>
            </li>
            <!-- RENTALS -->
            <li class="nav-item">
                <a class="nav-link" href="view_rentals.php">View Rentals</a>
            </li>

    <?php
        if (isset($_SESSION['loggedin'])) {
    ?>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a>
            </li>
    <?php
        }
    ?>
            </ul>
        </div>
    </div>
</nav>
