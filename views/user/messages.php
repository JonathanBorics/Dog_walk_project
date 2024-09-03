<?php
session_start();

// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Felhasználó ID-jének beállítása a session alapján
$user_id = $_SESSION['user_id'];

// Adatbázis kapcsolat
$servername = "localhost";
$username = "netwalker";
$password = "OAOJM80ovv20biq";
$dbname = "netwalker";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kapcsolódási hiba: " . $conn->connect_error);
}

// Jelöld olvasottként az üzeneteket
$sql_mark_as_read = "UPDATE inquiries SET is_read = 1 WHERE owner_id = '$user_id' AND is_read = 0";
$conn->query($sql_mark_as_read);

// Üzenet küldése
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['receiver_name']) && isset($_POST['message'])) {
    $receiver_name = $conn->real_escape_string($_POST['receiver_name']);
    $message = $conn->real_escape_string($_POST['message']);
    $sender_id = $_SESSION['user_id'];

    // Lekérdezzük a fogadó user_id-t a megadott név alapján
    $sql_get_receiver_id = "SELECT user_id FROM users WHERE CONCAT(first_name, ' ', last_name) = '$receiver_name'";
    $result_receiver = $conn->query($sql_get_receiver_id);

    if ($result_receiver->num_rows > 0) {
        $row_receiver = $result_receiver->fetch_assoc();
        $receiver_id = $row_receiver['user_id'];

        $sql_send_message = "INSERT INTO inquiries (owner_id, walker_id, message) VALUES ('$sender_id', '$receiver_id', '$message')";
        $conn->query($sql_send_message);
    } else {
        echo "Nem található felhasználó ezzel a névvel.";
    }
}

// Lekérdezzük az üzeneteket
$sql_messages = "SELECT inquiries.message, inquiries.created_at, users.first_name, users.last_name 
                 FROM inquiries 
                 JOIN users ON inquiries.walker_id = users.user_id 
                 WHERE inquiries.owner_id = '$user_id' 
                 ORDER BY inquiries.created_at DESC";
$result_messages = $conn->query($sql_messages);

// Lekérdezzük az olvasatlan üzenetek számát
$sql_unread_messages = "SELECT COUNT(*) as unread_count FROM inquiries WHERE owner_id = '$user_id' AND is_read = 0";
$result_unread = $conn->query($sql_unread_messages);
$unread_count = $result_unread->fetch_assoc()['unread_count'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Üzenetek</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
   <link href="../../style/user_massages.css" rel="stylesheet" >
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="messages.php">Üzenetek 
                        <?php if ($unread_count > 0): ?>
                            <span class="badge bg-danger"><?php echo $unread_count; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="user_dashboard.php">Vissza a Dashboardra</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Bootstrap JS és Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <div class="container dashboard-container">
        <div class="row">
            <div class="col-md-4">
                <div class="message-form">
                    <h4>Üzenet küldése</h4>
                    <form method="post" action="">
                        <div class="mb-3">
                            <label for="receiver_name" class="form-label">Címzett neve:</label>
                            <input type="text" class="form-control" id="receiver_name" name="receiver_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Üzenet:</label>
                            <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Küldés</button>
                    </form>
                </div>
            </div>
            <div class="col-md-8">
                <div class="message-list">
                    <h4>Beérkezett üzenetek</h4>
                    <ul class="list-group">
                        <?php if ($result_messages->num_rows > 0): ?>
                            <?php while ($message = $result_messages->fetch_assoc()): ?>
                                <li class="list-group-item">
                                    <strong><?php echo $message['first_name'] . ' ' . $message['last_name']; ?>:</strong>
                                    <p><?php echo $message['message']; ?></p>
                                    <span class="text-muted"><?php echo $message['created_at']; ?></span>
                                </li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <li class="list-group-item">Nincs üzenet.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
