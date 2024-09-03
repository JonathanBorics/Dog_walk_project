<?php

session_start();

// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve és admin-e
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

// Adatbázis kapcsolat
require '../../db.php';

$db = new Database();
$conn = $db->getConnection();

// Kutyasétáltatók lekérdezése
$stmt = $conn->prepare("SELECT users.user_id, users.username, users.first_name, users.last_name, users.email, walker_profiles.bio, walker_profiles.favorite_breed, users.is_active, users.is_approved
                        FROM users 
                        JOIN walker_profiles ON users.user_id = walker_profiles.user_id 
                        WHERE users.is_walker = 1");
$stmt->execute();
$walkers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Kutyasétáltató törlése
if (isset($_GET['delete_walker'])) {
    $walker_id_to_delete = $_GET['delete_walker'];
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $walker_id_to_delete, PDO::PARAM_INT);
    $stmt->execute();
    header("Location: manage_walkers.php");
    exit();
}

// Profil frissítése (pl. bio és kedvenc kutyafajta)
if (isset($_POST['update_walker'])) {
    $walker_id_to_update = $_POST['walker_id'];
    $bio = $_POST['bio'];
    $favorite_breed = $_POST['favorite_breed'];

    $stmt = $conn->prepare("UPDATE walker_profiles SET bio = :bio, favorite_breed = :favorite_breed WHERE user_id = :user_id");
    $stmt->bindParam(':bio', $bio, PDO::PARAM_STR);
    $stmt->bindParam(':favorite_breed', $favorite_breed, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $walker_id_to_update, PDO::PARAM_INT);
    $stmt->execute();
    header("Location: manage_walkers.php");
    exit();
}

// Kutyasétáltató aktiválása/deaktiválása
if (isset($_POST['toggle_status'])) {
    $walker_id_to_toggle = $_POST['walker_id'];
    $new_status = $_POST['is_active'] == 1 ? 0 : 1;

    $stmt = $conn->prepare("UPDATE users SET is_active = :is_active WHERE user_id = :user_id");
    $stmt->bindParam(':is_active', $new_status, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $walker_id_to_toggle, PDO::PARAM_INT);
    $stmt->execute();
    header("Location: manage_walkers.php");
    exit();
}

$conn = null;
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kutyasétáltatók kezelése</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../style/admin_manage_walkers.css"  rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Kutyasétáltatók kezelése</h1>

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Név</th>
                    <th>Email</th>
                    <th>Bio</th>
                    <th>Kedvenc Kutyafajta</th>
                    <th>Státusz</th>
                    <th>Jóváhagyás</th>
                    <th>Műveletek</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($walkers) > 0): ?>
                    <?php foreach ($walkers as $walker): ?>
                        <tr>
                            <td><?php echo $walker['user_id']; ?></td>
                            <td><?php echo htmlspecialchars($walker['first_name'] . ' ' . $walker['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($walker['email']); ?></td>
                            <td><?php echo htmlspecialchars($walker['bio']); ?></td>
                            <td><?php echo htmlspecialchars($walker['favorite_breed']); ?></td>
                            <td><?php echo $walker['is_active'] ? 'Aktív' : 'Inaktív'; ?></td>
                            <td><?php echo $walker['is_approved'] ? 'Jóváhagyva' : 'Nincs jóváhagyva'; ?></td>
                            <td>
                                <!-- Aktiválás/Deaktiválás form -->
                                <form method="post" action="manage_walkers.php" class="d-inline">
                                    <input type="hidden" name="walker_id" value="<?php echo $walker['user_id']; ?>">
                                    <input type="hidden" name="is_active" value="<?php echo $walker['is_active']; ?>">
                                    <button type="submit" name="toggle_status" class="btn btn-sm <?php echo $walker['is_active'] ? 'btn-warning' : 'btn-success'; ?>">
                                        <?php echo $walker['is_active'] ? 'Deaktiválás' : 'Aktiválás'; ?>
                                    </button>
                                </form>

                                <!-- Profil frissítése form -->
                                <form method="post" action="manage_walkers.php" class="d-inline">
                                    <input type="hidden" name="walker_id" value="<?php echo $walker['user_id']; ?>">
                                    <textarea name="bio" class="form-control" placeholder="Bio frissítése"><?php echo htmlspecialchars($walker['bio']); ?></textarea>
                                    <input type="text" name="favorite_breed" class="form-control" placeholder="Kedvenc kutyafajta frissítése" value="<?php echo htmlspecialchars($walker['favorite_breed']); ?>">
                                    <button type="submit" name="update_walker" class="btn btn-sm btn-primary">Frissítés</button>
                                </form>

                                <!-- Törlés link -->
                                <a href="manage_walkers.php?delete_walker=<?php echo $walker['user_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Biztosan törölni szeretnéd ezt a kutyasétáltatót?');">Törlés</a>

                                <!-- Jóváhagyás form -->
                                <?php if (!$walker['is_approved']): ?>
                                    <a href="toggle_walker_status.php?user_id=<?php echo $walker['user_id']; ?>&approve=1" class="btn btn-sm btn-success">Jóváhagyás</a>
                                <?php else: ?>
                                    <a href="toggle_walker_status.php?user_id=<?php echo $walker['user_id']; ?>&approve=0" class="btn btn-sm btn-danger">Visszavonás</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">Nincs regisztrált kutyasétáltató.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="admin_dashboard.php" class="btn btn-secondary">Vissza a Dashboardra</a>
    </div>
</body>
</html>
