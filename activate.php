<?php
$servername = "localhost";
$username = "netwalker";
$password = "OAOJM80ovv20biq";
$dbname = "netwalker";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kapcsolódási hiba: " . $conn->connect_error);
}

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Token ellenőrzése
    $sql = "SELECT * FROM tokens WHERE token = '$token' AND token_type = 'activation' AND used = 0 AND expires_at > NOW()";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_id = $row['user_id'];

        // Felhasználó aktiválása
        $sql_activate = "UPDATE users SET is_active = 1 WHERE user_id = '$user_id'";
        if ($conn->query($sql_activate) === TRUE) {
            // Token használtként jelölése
            $sql_update_token = "UPDATE tokens SET used = 1 WHERE token = '$token'";
            $conn->query($sql_update_token);

            echo "A fiókod sikeresen aktiválva lett.";
        } else {
            echo "Hiba történt a fiók aktiválása során.";
        }
    } else {
        echo "Érvénytelen vagy lejárt token.";
    }
} else {
    echo "Token hiányzik.";
}

$conn->close();
?>
