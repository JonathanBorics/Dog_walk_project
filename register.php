<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="./style/register.css">

</head>
<body>
<div class="container mt-5">
    <h1>Registration</h1>
        <!-- Vissza gomb az index.php-re -->
<div class="container mt-3">
    <a href="index.php" class="btn btn-primary">Vissza a főoldalra</a>
</div>
<div id="message"></div>
    <form method="post" id="registrationForm" action="register.php" enctype="multipart/form-data">

        <div class="mb-3">
            <label for="username" class="form-label">Felhasználónév</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Jelszó</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
            <label for="first_name" class="form-label">Keresztnév</label>
            <input type="text" class="form-control" id="first_name" name="first_name">
        </div>
        <div class="mb-3">
            <label for="last_name" class="form-label">Vezetéknév</label>
            <input type="text" class="form-control" id="last_name" name="last_name">
        </div>
        <div class="mb-3">
            <label for="phone_number" class="form-label">Telefonszám</label>
            <input type="text" class="form-control" id="phone_number" name="phone_number">
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Cím</label>
            <input type="text" class="form-control" id="address" name="address">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>

        <!-- Checkbox, ha walker-ként regisztrál -->
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_walker" name="is_walker">
            <label class="form-check-label" for="is_walker">Regisztráció mint walker</label>
        </div>


        <div class="mb-3">
        <label for="admin_code" class="form-label">Admin kód</label>
        <input type="text" class="form-control" id="admin_code" name="admin_code">
    </div>

        <!-- Walker extra adatok -->
        <div id="walker_info" style="display: none;" class="mt-3">
            <div class="mb-3">
                <label for="bio" class="form-label">Rövid bemutatkozás</label>
                <textarea class="form-control" id="bio" name="bio"></textarea>
            </div>
            <div class="mb-3">
                <label for="photo_url" class="form-label">Fénykép URL</label>
                <input type="text" class="form-control" id="photo_url" name="photo_url">
            </div>
            <div class="mb-3">
    <label for="photo" class="form-label">Profilkép</label>
    <input type="file" class="form-control" id="photo" name="photo">
</div>

            <div class="mb-3">
                <label for="favorite_breed" class="form-label">Kedvenc kutyafajta</label>
                <input type="text" class="form-control" id="favorite_breed" name="favorite_breed">
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Regisztráció</button>
    </form>
    

    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Regisztráció Sikeres</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Bezárás"></button>
            </div>
            <div class="modal-body">
                A regisztrációd sikeresen megtörtént!
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

</div>

<script src="./scripts/registration.js"></script>
</body>
</html>
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
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
    die("Kapcsolódási hiba: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $is_walker = isset($_POST['is_walker']) ? 1 : 0;
    
    // Admin kód ellenőrzés
    $admin_code = $_POST['admin_code'];
    $is_admin = 0; // Alapértelmezett admin státusz
    if ($admin_code === 'mauni') { // Cseréld le egy általad ismert kódra
        $is_admin = 1; // Admin státusz beállítása
    }

    // Ellenőrizd, hogy az email cím már létezik-e
    $check_email_sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($check_email_sql);

    if ($result->num_rows > 0) {
        echo "Hiba: Ez az email cím már használatban van.";
        exit();
    }

    // Felhasználó beszúrása a 'users' táblába
    $sql = "INSERT INTO users (username, password_hash, first_name, last_name, phone_number, address, email, is_walker, is_admin) 
            VALUES ('$username', '$password', '$first_name', '$last_name', '$phone_number', '$address', '$email', '$is_walker', '$is_admin')";

    if ($conn->query($sql) === TRUE) {
        $user_id = $conn->insert_id;
        
        // Kép feltöltésének kezelése
        $photo_url = ""; // Alapértelmezett üres érték
        if ($is_walker && isset($_FILES["photo"]) && $_FILES["photo"]["error"] == 0) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["photo"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $check = getimagesize($_FILES["photo"]["tmp_name"]);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                echo "A fájl nem kép.";
                $uploadOk = 0;
            }

            if (file_exists($target_file)) {
                echo "Sajnáljuk, a fájl már létezik.";
                $uploadOk = 0;
            }

            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                echo "Csak JPG, JPEG, PNG és GIF fájlok engedélyezettek.";
                $uploadOk = 0;
            }

            if ($uploadOk == 0) {
                echo "Sajnáljuk, a fájl feltöltése sikertelen.";
            } else {
                if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                    $photo_url = $target_file;
                } else {
                    echo "Hiba történt a fájl feltöltése során.";
                }
            }
        }

        if ($is_walker) {
            $bio = $_POST['bio'];
            $favorite_breed = $_POST['favorite_breed'];
            
            $sql_walker = "INSERT INTO walker_profiles (user_id, bio, photo_url, favorite_breed) 
                           VALUES ('$user_id', '$bio', '$photo_url', '$favorite_breed')";
            $conn->query($sql_walker);
        }

        // Token generálása és tárolása
        $token = generateToken();
        $token_type = 'activation';
        $expires_at = date('Y-m-d H:i:s', strtotime('+1 day')); // 24 óra érvényesség
        $sql_token = "INSERT INTO tokens (user_id, token, token_type, expires_at, used) 
                      VALUES ('$user_id', '$token', '$token_type', '$expires_at', 0)";
        $conn->query($sql_token);

        // Email küldése a sikeres regisztrációról és az aktivációs linkről
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = 0; // SMTP debug kikapcsolása
            $mail->isSMTP(); // SMTP használata
            $mail->Host = 'mail.netwalker.stud.vts.su.ac.rs';
            $mail->SMTPAuth = true;
            $mail->Username = 'netwalker';
            $mail->Password = 'OAOJM80ovv20biq';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('netwalker@netwalker.stud.vts.su.ac.rs', 'Dog Walk App');
            $mail->addAddress($email); // Címzett email cím

            // Content
            $mail->isHTML(true); // Email formátum HTML
            $mail->Subject = 'Sikeres regisztráció - Aktiváld a fiókodat';
            $activation_link = "https://netwalker.stud.vts.su.ac.rs/Project/activate.php?token=$token";

            $mail->Body = 'Kedves ' . $first_name . ',<br><br>Köszönjük, hogy regisztráltál a Dog Walk alkalmazásba!<br>Kérlek, <a href="'.$activation_link.'">kattints ide</a> a fiókod aktiválásához.<br><br>Üdvözlettel,<br>Dog Walk Team';

            $mail->send();
            echo 'Sikeres regisztráció! Ellenőrizd az email fiókodat az aktivációs linkért.';
        } catch (Exception $e) {
            echo "Az email küldése sikertelen. Hiba: {$mail->ErrorInfo}";
        }
    } else {
        echo "Hiba: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

