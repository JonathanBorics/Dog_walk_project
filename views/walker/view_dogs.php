<?php
session_start();

// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve és walker-e
if (!isset($_SESSION['user_id']) || (!$_SESSION['is_walker'])) {
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

// Lekérdezzük a kutyákat és a tulajdonosaikat
$sql_dogs = "SELECT dogs.name, dogs.breed, dogs.gender, dogs.age, dogs.description, users.first_name, users.last_name, users.user_id 
             FROM dogs 
             JOIN users ON dogs.owner_id = users.user_id";
$result_dogs = $conn->query($sql_dogs);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regisztrált Kutyák</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../style/view_dogs.css" rel="stylesheet">
    
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow-lg">
            <div class="card-header">
                <h1>Regisztrált Kutyák</h1>
            </div>
            <div class="card-body">
                <?php if ($result_dogs->num_rows > 0): ?>
                    <ul class="list-group">
                        <?php while ($dog = $result_dogs->fetch_assoc()): ?>
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
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p>Nincs regisztrált kutya.</p>
                <?php endif; ?>
            </div>
            <div class="card-footer text-center">
                <a href="walker_dashboard.php" class="btn btn-back">Vissza a dashboardra</a>
            </div>
        </div>
    </div>
</body>

</html>
