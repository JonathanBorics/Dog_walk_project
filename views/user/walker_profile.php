<?php
session_start();

// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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

// Ellenőrizzük, hogy van-e megadott walker ID
if (isset($_GET['id'])) {
    $walker_id = $_GET['id'];
} else {
    echo "Nincs megadva kutyasétáltató ID.";
    exit();
}

// Lekérdezzük a walker adatait
$sql_walker = "SELECT users.user_id, users.first_name, users.last_name, walker_profiles.bio, walker_profiles.photo_url 
               FROM users 
               JOIN walker_profiles ON users.user_id = walker_profiles.user_id 
               WHERE users.user_id = $walker_id";

$result = $conn->query($sql_walker);

if ($result->num_rows > 0) {
    $walker = $result->fetch_assoc();
} else {
    echo "Nem található a kutyasétáltató.";
    exit();
}

// Lekérdezzük a feladó (jelenlegi bejelentkezett felhasználó) adatait
$sql_sender = "SELECT email, first_name, last_name FROM users WHERE user_id = {$_SESSION['user_id']}";
$result_sender = $conn->query($sql_sender);

if ($result_sender->num_rows > 0) {
    $sender = $result_sender->fetch_assoc();
} else {
    echo "Nem található a feladó adatai.";
    exit();
}

// Ha az üzenet formot elküldték
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    $message = $_POST['message'];

    // Adjuk hozzá az üzenet mentését az adatbázisba
    $sql_insert_message = "INSERT INTO inquiries (owner_id, walker_id, message, created_at, is_read) 
                           VALUES ({$_SESSION['user_id']}, $walker_id, '$message', NOW(), 0)";
    
    if ($conn->query($sql_insert_message) === TRUE) {
        echo 'Az üzenet sikeresen elküldve!';
    } else {
        echo "Hiba történt az üzenet elküldése során: " . $conn->error;
    }
}


// Kutyás adatok kezelése
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['dog_name'])) {
    $dog_name = $_POST['dog_name'];
    $dog_breed = $_POST['dog_breed'];
    $dog_gender = $_POST['dog_gender'];
    $dog_age = $_POST['dog_age'];
    $dog_description = $_POST['dog_description'];

    $sql_insert_dog = "INSERT INTO dogs (owner_id, walker_id, name, breed, gender, age, description) 
                       VALUES ({$_SESSION['user_id']}, $walker_id, '$dog_name', '$dog_breed', '$dog_gender', $dog_age, '$dog_description')";
    
    if ($conn->query($sql_insert_dog) === TRUE) {
        echo 'A kutyás adatok sikeresen elmentve!';
    } else {
        echo "Hiba történt a mentés során: " . $conn->error;
    }
}

// Értékelés és megjegyzés kezelése
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rating'])) {
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    $sql_insert_rating = "INSERT INTO ratings (user_id, walker_id, rating, comment) 
                          VALUES ({$_SESSION['user_id']}, $walker_id, $rating, '$comment')";
    
    if ($conn->query($sql_insert_rating) === TRUE) {
        echo 'Az értékelés sikeresen elmentve!';
    } else {
        echo "Hiba történt a mentés során: " . $conn->error;
    }
}

$conn->close();
?>





<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $walker['first_name'] . " " . $walker['last_name']; ?> - Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
   <link href="../../style/user_walker_profile.css" rel="stylesheet">
</head>
<body>

<script>
        // PHP változók JavaScript-be ágyazása
        const walker = <?php echo json_encode($walker); ?>;
        
        // Kiíratás a konzolba
        console.log("Walker adatok: ", walker);
    </script>
<div class="container mt-5">
        <h1><?php echo $walker['first_name'] . " " . $walker['last_name']; ?></h1>
        <img src="../../<?php echo $walker['photo_url']; ?>" alt="Profilkép" class="img-fluid mb-3" style="max-width: 200px;">
        <p><strong>Bemutatkozás:</strong> <?php echo $walker['bio']; ?></p>

        <a href="user_dashboard.php" class="btn btn-back">Vissza az irányítópulthoz</a>
        
        <!-- Üzenet küldése -->
        <div class="mt-4">
            <h3>Üzenet küldése</h3>
            <form method="post" action="">
                <div class="mb-3">
                    <label for="message" class="form-label">Üzenet:</label>
                    <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Küldés</button>
            </form>
        </div>
        
      
        
        <!-- Értékelés -->
        <div class="mt-4">
            <h3>Értékelés</h3>
            <form method="post" action="">
                <div class="mb-3">
                    <label for="rating" class="form-label">Értékelés (1-5):</label>
                    <input type="number" class="form-control" id="rating" name="rating" min="1" max="5" required>
                </div>
                <div class="mb-3">
                    <label for="comment" class="form-label">Megjegyzés:</label>
                    <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-success">Értékelés küldése</button>
            </form>
        </div>
    </div>
</body>
</body>
</html>
