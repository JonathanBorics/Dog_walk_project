<?php
session_start();

// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve és walker-e
if (!isset($_SESSION['user_id']) || !$_SESSION['is_walker']) {
    header("Location: login.php");
    exit();
}

// Adatbázis kapcsolat
require '../../db.php';

$db = new Database();
$conn = $db->getConnection();

// Lekérdezzük a walker profilját az adatbázisból
$user_id = $_SESSION['user_id'];
$sql_walker = "SELECT bio, photo_url, favorite_breed FROM walker_profiles WHERE user_id = :user_id";
$stmt_walker = $conn->prepare($sql_walker);
$stmt_walker->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt_walker->execute();
$walker_profile = $stmt_walker->fetch(PDO::FETCH_ASSOC);

// Lekérdezzük a kutyákat, akikhez hozzáférést kap a walker
$sql_dogs = "SELECT dogs.name, dogs.breed, dogs.gender, dogs.age, dogs.description, users.first_name, users.last_name, users.user_id 
             FROM dogs 
             JOIN users ON dogs.owner_id = users.user_id";
$stmt_dogs = $conn->prepare($sql_dogs);
$stmt_dogs->execute();
$dogs = $stmt_dogs->fetchAll(PDO::FETCH_ASSOC);

// Lekérdezzük a walker értékeléseit
$sql_ratings = "SELECT rating, comment, created_at FROM ratings WHERE walker_id = :walker_id ORDER BY created_at DESC";
$stmt_ratings = $conn->prepare($sql_ratings);
$stmt_ratings->bindParam(':walker_id', $user_id, PDO::PARAM_INT);
$stmt_ratings->execute();
$ratings = $stmt_ratings->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Walker Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../style/walker_dashboard.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Walker Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="view_dogs.php">Kutyák megtekintése</a>
                    </li>
                   
                    <li class="nav-item">
                        <a class="nav-link" href="messages.php">Üzenetek</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="edit.php">Profil szerkesztése</a>
                    </li>
                    <li class="nav-item">
                    <a href="../common/report.php?user_id=<?php echo $user_id; ?>" class="nav-link">Jelentés</a>

                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../logout.php">Kijelentkezés</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container dashboard-container">
        <!-- Walker Profil -->
        <div class="card shadow-lg">
            <div class="card-header text-center">
                <h1>Profilod</h1>
            </div>
            <div class="card-body text-center">
                <p><strong>Bemutatkozás:</strong> <?php echo $walker_profile['bio']; ?></p>
                <p><strong>Kedvenc kutyafajta:</strong> <?php echo $walker_profile['favorite_breed']; ?></p>
                <?php if ($walker_profile['photo_url']): ?>
                    <img src="../../<?php echo $walker_profile['photo_url']; ?>" alt="Profilkép" style="max-width: 150px; border-radius: 50%;">
                <?php endif; ?>
            </div>
        </div>

        <!-- Kutyák Listája -->
        <div class="card shadow-lg">
            <div class="card-header text-center">
                <h1>Kutyák</h1>
            </div>
            <div class="card-body">
                <?php if (count($dogs) > 0): ?>
                    <ul class="list-group">
                        <?php foreach ($dogs as $dog): ?>
                            <li class="list-group-item">
                                <div class="dog-info">
                                    <div class="dog-details">
                                        <strong>Kutya neve:</strong> <?php echo htmlspecialchars($dog['name']); ?><br>
                                        <strong>Fajta:</strong> <?php echo htmlspecialchars($dog['breed']); ?><br>
                                        <strong>Nem:</strong> <?php echo htmlspecialchars($dog['gender']); ?><br>
                                        <strong>Kor:</strong> <?php echo htmlspecialchars($dog['age']); ?> év<br>
                                        <strong>Leírás:</strong> <?php echo htmlspecialchars($dog['description']); ?>
                                    </div>
                                    <div class="owner-info">
                                        <strong>Tulajdonos:</strong> <?php echo htmlspecialchars($dog['first_name'] . ' ' . $dog['last_name']); ?><br>
                                        <form action="messages.php" method="get">
                                            <input type="hidden" name="receiver_id" value="<?php echo $dog['user_id']; ?>">
                                            <button type="submit">Üzenet küldése</button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Nincs regisztrált kutya.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Értékelések Listája -->
        <div class="card shadow-lg">
            <div class="card-header text-center">
                <h1>Értékelések</h1>
            </div>
            <div class="card-body">
                <?php if (count($ratings) > 0): ?>
                    <ul class="list-group">
                        <?php foreach ($ratings as $rating): ?>
                            <li class="list-group-item rating-container">
                                <div>
                                    <strong>Értékelés:</strong> <?php echo $rating['rating']; ?>/5<br>
                                    <strong>Megjegyzés:</strong> <?php echo $rating['comment']; ?>
                                </div>
                                <span class="text-muted"><?php echo $rating['created_at']; ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Még nincs értékelésed.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php include '../../footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
