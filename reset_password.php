<?php
// Adatbázis kapcsolat beállítása
$servername = "localhost";
$username = "netwalker";
$password = "OAOJM80ovv20biq";
$dbname = "netwalker";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kapcsolat sikertelen: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

    // Token ellenőrzése és érvényesítése
    $sql = "SELECT user_id FROM tokens WHERE token = '$token' AND token_type = 'password_reset' AND used = 0 AND expires_at > NOW()";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_id = $row['user_id'];

        // Jelszó frissítése
        $sql_update_password = "UPDATE users SET password_hash = '$new_password' WHERE user_id = '$user_id'";
        if ($conn->query($sql_update_password) === TRUE) {
            // Token jelölése használtként
            $sql_update_token = "UPDATE tokens SET used = 1 WHERE token = '$token'";
            $conn->query($sql_update_token);

            echo "A jelszavad sikeresen frissült.";
        } else {
            echo "Hiba történt a jelszó frissítése során.";
        }
    } else {
        echo "Érvénytelen vagy lejárt token.";
    }
} else {
    if (isset($_GET['token'])) {
        $token = $_GET['token'];
        echo '<form method="post" action="reset_password.php">
                <input type="hidden" name="token" value="'.$token.'">
                <div class="mb-3">
                    <label for="new_password" class="form-label">Új jelszó</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                </div>
                <button type="submit" class="btn btn-primary">Jelszó visszaállítás</button>
              </form>';
    } else {
        echo "Érvénytelen kérés.";
    }
}

$conn->close();
?>
