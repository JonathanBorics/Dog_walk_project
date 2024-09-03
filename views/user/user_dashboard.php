<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Adatbázis kapcsolat beállítása
require '../../db.php';

$db = new Database();
$conn = $db->getConnection();

// Felhasználói adatok lekérdezése
$user_id = $_SESSION['user_id'];
$sql = "SELECT first_name, last_name, email, phone_number, address FROM users WHERE user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $full_name = $row['first_name'] . ' ' . $row['last_name'];
    $email = $row['email'];
    $phone_number = $row['phone_number'];
    $address = $row['address'];
} else {
    echo "Hiba történt a felhasználói adatok lekérdezésekor.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
   <link href="../../style/user_dashboard.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">User Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dog_registration.php">Kutyaregisztráció</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="edit_profile.php">Profil szerkesztése</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="messages.php">Üzenetek</a>
                    
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

    <!-- Dashboard Container -->
    <div class="container-dashboard">
        <div class="user-info text-center">
            <h1>Üdvözöljük, <?php echo $full_name; ?>!</h1>
            <p>Email: <?php echo $email; ?></p>
            <p>Telefonszám: <?php echo $phone_number; ?></p>
            <p>Cím: <?php echo $address; ?></p>
          
        </div>
    </div>

    <div id="section2" class="container-fluid bg-warning py-5">
    <div class="container">
        <h1 class="mb-4 text-center">Kutyasétáltatók</h1>
        <div class="row">
            <?php
            $stmt = $conn->prepare("SELECT users.user_id, users.first_name, users.last_name, walker_profiles.bio, walker_profiles.photo_url 
                                    FROM users 
                                    JOIN walker_profiles ON users.user_id = walker_profiles.user_id 
                                    WHERE users.is_walker = 1");
            $stmt->execute();
            $walkers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($walkers) > 0) {
                foreach ($walkers as $row) {
                    $user_id = $row['user_id'];
                    $name = $row['first_name'] . ' ' . $row['last_name'];
                    $bio = $row['bio'];
              
    
                    
                
                         
                    echo '
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 text-center p-3">
                            <div class="card-icon mb-3">
                                <i class="bi bi-person-circle" style="font-size: 4rem;"></i>
                            </div>
                            <div class="card-body">
                           
                                <h5 class="card-title">' . $name . '</h5>
                                <p class="card-text">' . $bio . '</p>
                                <a href="walker_profile.php?id=' . $user_id . '" class="btn btn-primary">További információ</a>';
                                
                 

                    echo '</div>
                        </div>
                    </div>';
                }
            } else {
                echo "<p>Nincsenek elérhető kutyasétáltatók.</p>";
            }
            ?>
        </div>
    </div>
</div>
<?php include '../../footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
