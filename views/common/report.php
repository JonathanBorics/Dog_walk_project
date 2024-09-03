<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Adatbázis kapcsolat
    $servername = "localhost";
    $username = "netwalker";
    $password = "OAOJM80ovv20biq";
    $dbname = "netwalker";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Kapcsolódási hiba: " . $conn->connect_error);
    }

    $reported_user_id = $_POST['reported_user_id'];
    $reported_by_user_id = $_SESSION['user_id'];
    $report_reason = $conn->real_escape_string($_POST['report_reason']);
    $report_description = $conn->real_escape_string($_POST['report_description']);

    $sql = "INSERT INTO reports (reported_user_id, reported_by_user_id, report_reason, report_description)
            VALUES ('$reported_user_id', '$reported_by_user_id', '$report_reason', '$report_description')";

    if ($conn->query($sql) === TRUE) {
        echo "Jelentés sikeresen beküldve.";
    } else {
        echo "Hiba történt: " . $conn->error;
    }

    $conn->close();
} else {
    $reported_user_id = $_GET['user_id'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Felhasználó Jelentése</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../style/report.css" rel="stylesheet"  >
</head>
<body>

    <div class="container">
        <h1>Felhasználó Jelentése</h1>
        <form method="post" action="report.php">
            <input type="hidden" name="reported_user_id" value="<?php echo $reported_user_id; ?>">

            <div class="mb-3">
                <label for="report_reason" class="form-label">Jelentés oka:</label>
                <input type="text" class="form-control" name="report_reason" required>
            </div>

            <div class="mb-3">
                <label for="report_description" class="form-label">Részletek:</label>
                <textarea class="form-control" name="report_description" rows="5" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary w-100">Jelentés beküldése</button>
        </form>

        <!-- Vissza gomb -->
        <a href="javascript:history.back()" class="btn btn-secondary w-100 mt-3">Vissza</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
