<?php
session_start();

$_SESSION['admin_id'] = $admin_id; // Amikor az admin bejelentkezik

// Ellenőrizzük, hogy az admin be van-e jelentkezve
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../../login.php");
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

// Jóváhagyás vagy visszavonás kezelése
if (isset($_GET['user_id']) && isset($_GET['approve'])) {
    $user_id = (int)$_GET['user_id'];
    $approve = (int)$_GET['approve'];

    $stmt = $conn->prepare("UPDATE users SET is_approved = ? WHERE user_id = ?");
    $stmt->bind_param("ii", $approve, $user_id);

    if ($stmt->execute() === TRUE) {
        header("Location: manage_walkers.php");
        exit();
    } else {
        echo "Hiba történt a frissítés során: " . $stmt->error;
    }
    $stmt->close();
}

// Kutyasétáltató státuszának frissítése
if (isset($_POST['user_id']) && isset($_POST['action'])) {
    $user_id = (int)$_POST['user_id'];
    $action = $_POST['action'];

    if ($action == 'activate') {
        $is_active = 1;
    } elseif ($action == 'deactivate') {
        $is_active = 0;
    } else {
        echo "Érvénytelen művelet.";
        exit();
    }

    $stmt = $conn->prepare("UPDATE users SET is_active = ? WHERE user_id = ?");
    $stmt->bind_param("ii", $is_active, $user_id);

    if ($stmt->execute() === TRUE) {
        header("Location: manage_walkers.php");
        exit();
    } else {
        echo "Hiba történt a frissítés során: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>
