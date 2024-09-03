<?php
session_start();

// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve és admin-e
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}

// Adatbázis kapcsolat
require '../../db.php';

$db = new Database();
$conn = $db->getConnection();

// Felhasználók lekérdezése
$stmt = $conn->query("SELECT user_id, first_name, last_name, email, is_walker, is_admin, is_active FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Felhasználó törlése
if (isset($_GET['delete_user'])) {
    $user_id_to_delete = $_GET['delete_user'];
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id_to_delete);
    $stmt->execute();
    header("Location: manage_users.php");
}

// Felhasználói jogok módosítása (admin, walker, és aktiválás/deaktiválás)
if (isset($_POST['update_roles'])) {
    $user_id_to_update = $_POST['user_id'];
    $is_walker = isset($_POST['is_walker']) ? 1 : 0;
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE users SET is_walker = :is_walker, is_admin = :is_admin, is_active = :is_active WHERE user_id = :user_id");
    $stmt->bindParam(':is_walker', $is_walker);
    $stmt->bindParam(':is_admin', $is_admin);
    $stmt->bindParam(':is_active', $is_active);
    $stmt->bindParam(':user_id', $user_id_to_update);
    $stmt->execute();
    header("Location: manage_users.php");
}

$conn = null;
?>


<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Felhasználók kezelése</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../style/admin_user_manage.css" rel="stylesheet" >
</head>
<body>
    <div class="container">
        <h1>Felhasználók kezelése</h1>

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Név</th>
                    <th>Email</th>
                    <th>Kutyasétáltató</th>
                    <th>Admin</th>
                    <th>Aktív</th>
                    <th>Műveletek</th>
                </tr>
            </thead>
            <tbody>
    <?php if (count($users) > 0): ?>
        <?php foreach ($users as $user): ?>
            <tr>
                <td data-label="ID"><?php echo $user['user_id']; ?></td>
                <td data-label="Név"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                <td data-label="Email"><?php echo htmlspecialchars($user['email']); ?></td>
                <td data-label="Kutyasétáltató"><?php echo $user['is_walker'] ? 'Igen' : 'Nem'; ?></td>
                <td data-label="Admin"><?php echo $user['is_admin'] ? 'Igen' : 'Nem'; ?></td>
                <td data-label="Aktív"><?php echo $user['is_active'] ? 'Igen' : 'Nem'; ?></td>
                <td data-label="Műveletek">
                    <form method="post" action="manage_users.php" class="d-inline">
                        <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                        <input type="checkbox" name="is_walker" <?php echo $user['is_walker'] ? 'checked' : ''; ?>> Walker
                        <input type="checkbox" name="is_admin" <?php echo $user['is_admin'] ? 'checked' : ''; ?>> Admin
                        <input type="checkbox" name="is_active" <?php echo $user['is_active'] ? 'checked' : ''; ?>> Aktív
                        <button type="submit" name="update_roles" class="btn btn-sm btn-primary">Frissítés</button>
                    </form>
                    <a href="manage_users.php?delete_user=<?php echo $user['user_id']; ?>" class="btn btn-sm btn-danger">Törlés</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="7">Nincs regisztrált felhasználó.</td>
        </tr>
    <?php endif; ?>
</tbody>

        </table>

        <a href="admin_dashboard.php" class="btn btn-secondary">Vissza a Dashboardra</a>
    </div>
</body>
</html>
