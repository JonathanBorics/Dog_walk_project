<?php
session_start();

// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve és walker-e
if (!isset($_SESSION['user_id']) || (!$_SESSION['is_walker'])) {
    header("Location: login.php");
    exit();
}

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

// Üzenetek olvasottként jelölése
$sql_mark_as_read = "UPDATE inquiries SET is_read = 1 WHERE walker_id = '$user_id' AND is_read = 0";
$conn->query($sql_mark_as_read);

// Üzenetek lekérdezése
$sql_messages = "SELECT inquiries.message, inquiries.created_at, users.first_name, users.last_name 
                 FROM inquiries 
                 JOIN users ON inquiries.owner_id = users.user_id 
                 WHERE inquiries.walker_id = '$user_id' 
                 ORDER BY inquiries.created_at DESC";
$result_messages = $conn->query($sql_messages);

// Olvasatlan üzenetek számának lekérdezése
$sql_unread_messages_walker = "SELECT COUNT(*) as unread_count FROM inquiries WHERE walker_id = '$user_id' AND is_read = 0";
$result_unread_walker = $conn->query($sql_unread_messages_walker);
$unread_count_walker = $result_unread_walker->fetch_assoc()['unread_count'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Üzenetek</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../style/walker_messages.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Dashboard</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="messages.php">Üzenetek 
                        <?php if ($unread_count_walker > 0): ?>
                            <span class="badge bg-danger"><?php echo $unread_count_walker; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="walker_dashboard.php">Vissza a Dashboardra</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

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
                                <strong><?php echo htmlspecialchars($message['first_name'] . ' ' . $message['last_name']); ?>:</strong>
                                <p><?php echo htmlspecialchars($message['message']); ?></p>
                                <span class="text-muted"><?php echo htmlspecialchars($message['created_at']); ?></span>
                            </li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <li class="list-group-item">Nincs új üzenet.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
</body>
</html>
