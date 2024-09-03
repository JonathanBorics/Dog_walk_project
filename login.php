<?php
session_start();

// Ellenőrizzük, van-e kijelentkezési üzenet
$logout_message = "";
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    $logout_message = "Sikeresen kijelentkeztél.";
}

// Adatbázis kapcsolat beállítása
$servername = "localhost";
$username = "netwalker";
$password = "OAOJM80ovv20biq";
$dbname = "netwalker";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kapcsolat sikertelen: " . $conn->connect_error);
}

// POST metódussal küldött adatok ellenőrzése
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepared Statement használata az SQL lekérdezéshez
    $stmt = $conn->prepare("SELECT user_id, password_hash, is_walker, is_admin FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password_hash'])) {
            // Sikeres bejelentkezés
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $username;
            $_SESSION['is_walker'] = $row['is_walker'];
            $_SESSION['is_admin'] = $row['is_admin'];

            // Átirányítás a megfelelő dashboardra
            if ($row['is_admin']) {
                $_SESSION['admin_id'] = $row['user_id'];
                header("Location: ./views/admin/admin_dashboard.php");
            } elseif ($row['is_walker']) {
                header("Location: ./views/walker/walker_dashboard.php");
            } else {
                header("Location: ./views/user/user_dashboard.php");
            }
            exit();
        } else {
            echo "<div class='alert alert-danger'>Helytelen jelszó</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>A felhasználónév nem létezik</div>";
    }
    $stmt->close();
}
