<?php
session_start();

// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve és admin-e
if (!isset($_SESSION['user_id']) || (!$_SESSION['is_admin'])) {
    header("Location: login.php");
    exit();
}



// Admin felhasználó neve
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
   <link href="../../style/admin_dashboard.css" rel="stylesheet"  >
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Admin Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="manage_users.php">Felhasználók kezelése</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_walkers.php">Kutyasétáltatók kezelése</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view_reports.php">Jelentések megtekintése</a>
                </li>
              
                <li class="nav-item">
                    <a class="nav-link btn btn-danger text-white" href="../../logout.php">Kijelentkezés</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h1>Üdvözöljük, <?php echo $username; ?>!</h1>

    <div class="row">
        <!-- Felhasználók kezelése -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Felhasználók kezelése</div>
                <div class="card-body">
                    <p>Itt kezelheted a felhasználókat.</p>
                    <a href="manage_users.php" class="btn btn-primary">Felhasználók kezelése</a>
                </div>
            </div>
        </div>

        <!-- Kutyasétáltatók kezelése -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Kutyasétáltatók kezelése</div>
                <div class="card-body">
                    <p>Itt kezelheted a kutyasétáltatókat.</p>
                    <a href="manage_walkers.php" class="btn btn-primary">Kutyasétáltatók kezelése</a>
                </div>
            </div>
        </div>

        <!-- Jelentések megtekintése -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Jelentések megtekintése</div>
                <div class="card-body">
                    <p>Itt megtekintheted a jelentéseket.</p>
                    <a href="view_reports.php" class="btn btn-primary">Jelentések megtekintése</a>
                </div>
            </div>
        </div>

 

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
