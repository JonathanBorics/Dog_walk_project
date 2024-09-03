<div id="section2" class="container-fluid bg-warning py-5">
      <div class="container">
        <h1 class="mb-4 text-center">Kutyasétáltatók</h1>
        <div class="row">
            <?php
           // Adatbázis kapcsolat
           $servername = "localhost";
$username = "netwalker";
$password = "OAOJM80ovv20biq";
$dbname = "netwalker";

            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Kapcsolódási hiba: " . $conn->connect_error);
            }

            // Dog-walker-ek lekérdezése
            $sql = "SELECT users.user_id, users.first_name, users.last_name, walker_profiles.bio, walker_profiles.photo_url 
                    FROM users 
                    JOIN walker_profiles ON users.user_id = walker_profiles.user_id 
                    WHERE users.is_walker = 1";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $user_id = $row['user_id'];
                    $name = $row['first_name'] . ' ' . $row['last_name'];
                    $bio = $row['bio'];
                    $photo_url = $row['photo_url'] ? $row['photo_url'] : './pic/default-dogwalker.jpg';

                    echo '
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="' . $photo_url . '" class="card-img-top" alt="Kutyasétáltató" style="height: 200px; object-fit: cover"/>
                            <div class="card-body">
                                <h5 class="card-title">' . $name . '</h5>
                                <p class="card-text">' . $bio . '</p>
                                <a href="profile.php?id=' . $user_id . '" class="btn btn-primary">További információ</a>';
                                
                    // Csak akkor jelenítjük meg az értékelés lehetőségét, ha a felhasználó be van jelentkezve
                    if (isset($_SESSION['user_id'])) {
                        echo '
                                <form method="post" action="rate_walker.php">
                                    <input type="hidden" name="walker_id" value="' . $user_id . '">
                                    <button type="submit" class="btn btn-success">Értékelés</button>
                                </form>';
                    }

                    echo '</div>
                        </div>
                    </div>';
                }
            } else {
                echo "<p>Nincsenek elérhető kutyasétáltatók.</p>";
            }

            $conn->close();
            ?>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>