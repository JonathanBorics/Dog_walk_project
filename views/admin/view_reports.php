<?php
session_start();

// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve és admin-e
if (!isset($_SESSION['user_id']) || (!$_SESSION['is_admin'])) {
    header("Location: login.php");
    exit();
}

// Adatbázis kapcsolat
$servername = "localhost";
$username = "netwalker";
$password = "OAOJM80ovv20biq";
$dbname = "netwalker";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kapcsolódási hiba: " . $conn->connect_error);
}

// Jelentés törlése
if (isset($_GET['delete_report'])) {
    $report_id_to_delete = $_GET['delete_report'];
    $sql_delete_report = "DELETE FROM reports WHERE report_id = $report_id_to_delete";
    $conn->query($sql_delete_report);
    header("Location: view_reports.php");
    exit();
}

// Jelentések lekérdezése
$sql_reports = "SELECT reports.report_id, users.first_name, users.last_name, reports.report_reason, reports.report_description, reports.created_at, reports.is_resolved 
                FROM reports 
                JOIN users ON reports.reported_user_id = users.user_id";

$result_reports = $conn->query($sql_reports);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jelentések megtekintése</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../style/admin_view-report.css"   >
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-custom bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand text-white" href="#">Admin Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link text-white" href="admin_dashboard.php">Vissza a Dashboardra</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h1>Jelentések megtekintése</h1>

    <?php if ($result_reports->num_rows > 0): ?>
        <?php while ($report = $result_reports->fetch_assoc()): ?>
            <div class="report-card">
                <div class="report-header">
                    <div class="row">
                        <div class="col-md-2">Felhasználó</div>
                        <div class="col-md-2">Jelentés oka</div>
                        <div class="col-md-4">Tartalom</div>
                        <div class="col-md-2">Dátum</div>
                        <div class="col-md-1">Megoldva</div>
                        <div class="col-md-1">Művelet</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2"><?php echo $report['first_name'] . ' ' . $report['last_name']; ?></div>
                    <div class="col-md-2"><?php echo $report['report_reason']; ?></div>
                    <div class="col-md-4"><?php echo htmlspecialchars($report['report_description']); ?></div>
                    <div class="col-md-2"><?php echo $report['created_at']; ?></div>
                    <div class="col-md-1">
                        <span class="<?php echo $report['is_resolved'] ? 'badge-resolved' : 'badge-unresolved'; ?>">
                            <?php echo $report['is_resolved'] ? 'Igen' : 'Nem'; ?>
                        </span>
                    </div>
                    <div class="col-md-1">
                        <a href="view_reports.php?delete_report=<?php echo $report['report_id']; ?>" class="btn btn-danger btn-sm">Törlés</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Nincs elérhető jelentés.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

