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

// Kutyaregisztráció kezelése
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $owner_id = $_SESSION['user_id'];
    $name = $_POST['name'];
    $breed = $_POST['breed'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $description = $_POST['description'];

    $sql = "INSERT INTO dogs (owner_id, name, breed, gender, age, description) 
            VALUES ('$owner_id', '$name', '$breed', '$gender', '$age', '$description')";

    if ($conn->query($sql) === TRUE) {
        echo "Kutyaregisztráció sikeres!";
    } else {
        echo "Hiba történt a kutyaregisztráció során: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kutyaregisztráció</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
   <link href="../../style/user_dog_reg.css"  rel="stylesheet"  >
</head>
<body>
    <div class="container registration-container">
        <h2>Kutyaregisztráció</h2>
        <form method="post" action="">
            <div class="mb-3">
                <label for="name" class="form-label">Kutya neve</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="breed" class="form-label">Fajta</label>
                <input type="text" class="form-control" id="breed" name="breed" required>
            </div>
            <div class="mb-3">
                <label for="gender" class="form-label">Nem</label>
                <select class="form-control" id="gender" name="gender" required>
                    <option value="male">Hím</option>
                    <option value="female">Nőstény</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="age" class="form-label">Kor (években)</label>
                <input type="number" class="form-control" id="age" name="age" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Leírás</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Regisztráció</button>
        </form>
        <a href="user_dashboard.php" class="back-link">Vissza a Dashboardra</a>
    </div>
</body>
</html>
