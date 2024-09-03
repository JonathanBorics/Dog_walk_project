<?php
session_start();

// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve
$walker_id = $_GET['id']; // Javítva: `$walker_id` változó helyes használata

// Adatbázis kapcsolat
$servername = "localhost";
$username = "netwalker";
$password = "OAOJM80ovv20biq";
$dbname = "netwalker";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kapcsolódási hiba: " . $conn->connect_error);
}

// Lekérdezzük a walker profilját
$sql = "SELECT users.first_name, users.last_name, walker_profiles.bio, walker_profiles.photo_url 
        FROM users 
        JOIN walker_profiles ON users.user_id = walker_profiles.user_id 
        WHERE users.user_id = $walker_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $walker = $result->fetch_assoc();
    $walker_name = $walker['first_name'] . ' ' . $walker['last_name'];
    $walker_bio = $walker['bio'];
    $walker_photo = $walker['photo_url'];
    $walker_detailed_info = isset($walker['detailed_info']) ? $walker['detailed_info'] : "Nincsenek további információk.";

    if (!isset($_SESSION['user_id'])) {
        // Ha vendég (nincs bejelentkezve)
      //  echo "<h1>" . htmlspecialchars($walker_name) . "</h1>";
       // echo "<img src='" . htmlspecialchars($walker_photo) . "' alt='Kép' style='width:200px; height:200px; object-fit:cover;'/>";
       // echo "<p>" . htmlspecialchars($walker_bio) . "</p>";
       // echo "<p><em>Jelentkezz be, hogy több információt láss!</em></p>";
    } else {
        // Ha be van jelentkezve
        echo "<h1>" . htmlspecialchars($walker_name) . "</h1>";
        echo "<img src='" . htmlspecialchars($walker_photo) . "' alt='Kép' style='width:200px; height:200px; object-fit:cover;'/>";
        echo "<p>" . htmlspecialchars($walker_bio) . "</p>";
        echo "<p>" . htmlspecialchars($walker_detailed_info) . "</p>";
    }
} else {
    echo "Érvénytelen azonosító.";
    exit();
}

// Értékelés beküldése
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id']; // A bejelentkezett felhasználó ID-je
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    $sql_rating = "INSERT INTO ratings (walker_id, user_id, rating, comment) 
                   VALUES ('$walker_id', '$user_id', '$rating', '$comment')";

    if ($conn->query($sql_rating) === TRUE) {
        echo "Értékelés sikeresen elmentve!";
    } else {
        echo "Hiba történt az értékelés mentésekor: " . $conn->error;
    }
}

// Lekérdezzük a meglévő értékeléseket
$sql_reviews = "SELECT ratings.rating, ratings.comment, users.first_name, users.last_name 
                FROM ratings 
                JOIN users ON ratings.user_id = users.user_id 
                WHERE ratings.walker_id = $walker_id";
$result_reviews = $conn->query($sql_reviews);

$conn->close();
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kutyasétáltató Profil</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
 <link rel="stylesheet" href="./style/profile.css" > 
</head>
<body>
<div class="container profile-container">
    <div class="card">
        <div class="card-header text-center">
            <h1><?php echo htmlspecialchars($walker_name); ?> Profil</h1>
        </div>
        <div class="card-body text-center">
            <img src="<?php echo htmlspecialchars($walker_photo); ?>" alt="Profil kép" class="rounded-circle mb-3" style="max-width: 150px;">
            <p><strong>Bemutatkozás:</strong> <?php echo htmlspecialchars($walker_bio); ?></p>
            <a href="index.php" class="btn btn-primary mt-3">Vissza a főoldalra</a>
        </div>
    </div>
</div>

<!-- Meglévő értékelések megjelenítése -->
<div class="container review-container mt-4">
    <div class="card">
        <div class="card-header text-center">
            <h3>Értékelések</h3>
        </div>
        <div class="card-body">
            <?php if ($result_reviews->num_rows > 0): ?>
                <ul class="list-group">
                    <?php while ($review = $result_reviews->fetch_assoc()): ?>
                        <li class="list-group-item">
                            <strong><?php echo htmlspecialchars($review['first_name']) . ' ' . htmlspecialchars($review['last_name']); ?>:</strong> 
                            <?php echo htmlspecialchars($review['rating']); ?>/5<br>
                            <p><?php echo htmlspecialchars($review['comment']); ?></p>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>Még nincsenek értékelések.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
