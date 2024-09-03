<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';

function generateToken() {
    return bin2hex(random_bytes(16)); // 32 karakteres hexadecimális token
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);

    // Ellenőrizzük, hogy az email létezik-e
    $sql = "SELECT user_id FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Token generálása
        $token = generateToken();
        $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour')); // 1 óra érvényesség
        $row = $result->fetch_assoc();
        $user_id = $row['user_id'];

        // Token tárolása az adatbázisban
        $sql_token = "INSERT INTO tokens (user_id, token, token_type, expires_at, used) 
                      VALUES ('$user_id', '$token', 'password_reset', '$expires_at', 0)";
        $conn->query($sql_token);

        // Email küldése a jelszó visszaállítási linkkel
        $mail = new PHPMailer(true);

        try {
            // SMTP beállítások
            $mail->isSMTP();
            $mail->Host = 'mail.netwalker.stud.vts.su.ac.rs'; // SMTP szerver beállítása
            $mail->SMTPAuth = true;
            $mail->Username = 'netwalker'; // SMTP felhasználónév
            $mail->Password = 'OAOJM80ovv20biq'; // SMTP jelszó
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Email beállítások
            $mail->setFrom('netwalker@netwalker.stud.vts.su.ac.rs', 'Dog Walk App'); // Feladó email és név
            $mail->addAddress($email); // Címzett email cím
            $mail->isHTML(true);
            $mail->Subject = 'Jelszó visszaállítás';

            // Itt kell beállítani a helyes URL-t
            $reset_link = "https://netwalker.stud.vts.su.ac.rs/Project/reset_password.php?token=$token";


            $mail->Body = "Kedves Felhasználó,<br><br>Kérlek, kattints az alábbi linkre a jelszavad visszaállításához:<br>
                           <a href='$reset_link'>$reset_link</a><br><br>Ez a link 1 órán keresztül érvényes.<br><br>Üdvözlettel,<br>Dog Walk Team";

            $mail->send();
            echo 'Egy emailt küldtünk a jelszó visszaállítási információkkal.';
        } catch (Exception $e) {
            echo "Hiba történt az email küldése során: {$mail->ErrorInfo}";
        }
    } else {
        echo "Hiba: Nincs ilyen email cím regisztrálva.";
    }
}

$conn->close();
?>
