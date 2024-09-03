<?php
session_start();

// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
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

// Lekérdezzük a bejelentkezett felhasználó adatait
$user_id = $_SESSION['user_id'];
$sql = "SELECT users.first_name, users.last_name, users.phone_number AS phone, walker_profiles.bio, walker_profiles.photo_url, walker_profiles.favorite_breed 
        FROM walker_profiles 
        JOIN users ON walker_profiles.user_id = users.user_id 
        WHERE walker_profiles.user_id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $profile = $result->fetch_assoc();
} else {
    echo "Nem található profil.";
    exit();
}

if (isset($_POST['update_profile'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone = $_POST['phone'];
    $bio = $_POST['bio'];
    $favorite_breed = $_POST['favorite_breed'];

    // Profilkép feltöltése
    if (!empty($_FILES['photo']['name'])) {
        $photo = $_FILES['photo']['name'];
        $target_dir = "../../uploads/";  // Két szinttel vissza kell lépni
        $target_file = $target_dir . time() . "_" . basename($photo);
        
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
            $sql = "UPDATE walker_profiles SET photo_url=? WHERE user_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('si', $target_file, $user_id);
            $stmt->execute();
            $stmt->close();
        } else {
            echo "Hiba történt a fájl feltöltése közben.";
        }
        
    }
    // Adatok frissítése a walker_profiles táblában
    $sql = "UPDATE walker_profiles SET bio=?, favorite_breed=? WHERE user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssi', $bio, $favorite_breed, $user_id);

    if ($stmt->execute()) {
        echo "Profil sikeresen frissítve!";
    } else {
        echo "Hiba történt a profil frissítésekor: " . $stmt->error;
    }

    // Frissítjük a `users` táblát is a `first_name`, `last_name`, és `phone_number` értékekkel
    $sql = "UPDATE users SET first_name=?, last_name=?, phone_number=? WHERE user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssi', $first_name, $last_name, $phone, $user_id);

    if ($stmt->execute()) {
        echo "Név és telefonszám sikeresen frissítve!";
    } else {
        echo "Hiba történt a név és telefonszám frissítésekor: " . $stmt->error;
    }

    $stmt->close();
}

if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $sql = "SELECT password_hash FROM users WHERE user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->close();

    if (password_verify($current_password, $hashed_password)) {
        if ($new_password === $confirm_password) {
            $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);
            $sql = "UPDATE users SET password_hash=? WHERE user_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('si', $new_password_hash, $user_id);
            if ($stmt->execute()) {
                echo "Jelszó sikeresen megváltoztatva!";
            } else {
                echo "Hiba történt a jelszó módosítása közben: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Az új jelszó és a megerősítés nem egyezik.";
        }
    } else {
        echo "A jelenlegi jelszó hibás.";
    }
}

if (isset($_POST['delete_profile'])) {
    $sql = "DELETE FROM walker_profiles WHERE user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    if ($stmt->execute()) {
        $sql = "DELETE FROM users WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $user_id);
        if ($stmt->execute()) {
            session_destroy();
            header("Location: ../../index.php");
            exit();
        }
    } else {
        echo "Hiba történt a profil törlése közben: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Szerkesztése</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
   <link href="../../style/walker_edit.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h1>Profil Szerkesztése</h1>
    <form method="post" action="edit.php" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="first_name" class="form-label">Keresztnév:</label>
            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($profile['first_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="last_name" class="form-label">Vezetéknév:</label>
            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($profile['last_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Telefonszám:</label>
            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($profile['phone']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="bio" class="form-label">Leírás:</label>
            <textarea class="form-control" id="bio" name="bio" rows="3" required><?php echo htmlspecialchars($profile['bio']); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="photo" class="form-label">Profilkép:</label>
            <input type="file" class="form-control" id="photo" name="photo">
        </div>
        <div class="mb-3">
            <label for="favorite_breed" class="form-label">Kedvenc kutyafajta:</label>
            <input type="text" class="form-control" id="favorite_breed" name="favorite_breed" value="<?php echo htmlspecialchars($profile['favorite_breed']); ?>" required>
        </div>
        <button type="submit" name="update_profile" class="btn btn-primary">Mentés</button>
    </form>

    <div class="back-button">
        <a href="walker_dashboard.php">Vissza a Dashboard-ra</a>
    </div>
</div>

<div class="container mt-5">
    <h1>Jelszó Megváltoztatása</h1>
    <form method="post" action="edit.php">
        <div class="mb-3">
            <label for="current_password" class="form-label">Jelenlegi jelszó:</label>
            <input type="password" class="form-control" id="current_password" name="current_password" required>
        </div>
        <div class="mb-3">
            <label for="new_password" class="form-label">Új jelszó:</label>
            <input type="password" class="form-control" id="new_password" name="new_password" required>
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Új jelszó megerősítése:</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" name="change_password" class="btn btn-danger">Jelszó Megváltoztatása</button>
    </form>
</div>

<div class="container mt-5">
    <form method="post" action="edit.php">
        <button type="submit" name="delete_profile" class="btn btn-danger">Profil Törlése</button>
    </form>
</div>
</body>
</html>
