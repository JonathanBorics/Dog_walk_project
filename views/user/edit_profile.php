<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Adatbázis kapcsolat beállítása
$servername = "localhost";
$username = "netwalker";
$password = "OAOJM80ovv20biq";
$dbname = "netwalker";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kapcsolódási hiba: " . $conn->connect_error);
}

// Felhasználói adatok lekérdezése
$user_id = $_SESSION['user_id'];
$sql = "SELECT first_name, last_name, email, phone_number, address FROM users WHERE user_id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $first_name = $row['first_name'];
    $last_name = $row['last_name'];
    $email = $row['email'];
    $phone_number = $row['phone_number'];
    $address = $row['address'];
} else {
    echo "Hiba történt a felhasználói adatok lekérdezésekor.";
    exit();
}

// Profil frissítése
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];

    $sql_update = "UPDATE users SET first_name = '$first_name', last_name = '$last_name', email = '$email', phone_number = '$phone_number', address = '$address' WHERE user_id = $user_id";

    if ($conn->query($sql_update) === TRUE) {
        echo "Profil frissítve!";
    } else {
        echo "Hiba történt a profil frissítésekor: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil szerkesztése</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../style/user_edit_profile.css"  rel="stylesheet" >
</head>
<body>
    <div class="container edit-profile-container">
        <h2>Profil szerkesztése</h2>
        <form method="post" action="">
            <div class="mb-3">
                <label for="first_name" class="form-label">Keresztnév</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $first_name; ?>" required>
            </div>
            <div class="mb-3">
                <label for="last_name" class="form-label">Vezetéknév</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $last_name; ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone_number" class="form-label">Telefonszám</label>
                <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo $phone_number; ?>">
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Cím</label>
                <input type="text" class="form-control" id="address" name="address" value="<?php echo $address; ?>">
            </div>
            <button type="submit" class="btn btn-primary">Profil frissítése</button>
        </form>
        <a href="user_dashboard.php" class="back-link">Vissza a Dashboardra</a>
    </div>
</body>
</html>
