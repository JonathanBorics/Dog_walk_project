<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Dog Walker Service</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    />
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    ></script>
    <link rel="stylesheet" href="stilus.css" />
    <script src="./scripts/search.js" defer></script>
  </head>
  <body data-bs-spy="scroll" data-bs-target=".navbar" data-bs-offset="50">
    <nav class="navbar navbar-expand-sm bg-dark navbar-dark fixed-top">
      <div class="container-fluid">
        <a class="navbar-brand" href="#"><i class="bi bi-house"></i></a>
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#collapsibleNavbar"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="collapsibleNavbar">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" href="#section1">About Us <i class="bi bi-person-fill"></i></a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#section2">Dog sitters <i class="bi bi-code-slash"></i></a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#section3">Contact <i class="bi bi-envelope"></i></a>
            </li>
            
            <li class="nav-item">
              <a class="nav-link" href="register.php">Users registration <i class="bi bi-person-plus-fill"></i></a>
            </li>
          </ul>
         
          <form class="d-flex ms-auto" action="login.php" method="post">
            <a href="forgot_password.php" class="btn btn-link">Forgot?</a>
    <input class="form-control me-2" type="text" placeholder="Username" name="username" required>
    <input class="form-control me-2" type="password" placeholder="Password" name="password" required>
    <button class="btn btn-primary" type="submit">Login</button>
</form>
        </div>
      </div>
    </nav>
    
    <div id="section1" class="container-fluid bg-success text-dark py-5">
    <div class="container">
        <!-- Hero Section -->
        <section class="hero-section text-center mb-5">
            <div class="hero-content">
                <h1 class="display-4">Rólunk</h1>
                <p class="lead">Ismerd meg a Dog Walk App történetét és csapatát!</p>
            </div>
        </section>

        <!-- Mission Section -->
        <section class="mission-section bg-success py-5">
            <div class="container">
                <h2 class="section-title text-center mb-4">Küldetésünk</h2>
                <p class="text-center mx-auto" style="max-width: 800px;">
                    A Dog Walk App célja, hogy összekösse a lelkes kutyasétáltatókat a gazdikkal, biztosítva ezzel, hogy minden négylábú barátunk megkapja a szükséges törődést és mozgást. Hiszünk abban, hogy a boldog kutyák boldog gazdikat eredményeznek, és ezért törekszünk a legjobb szolgáltatást nyújtani mindkét fél számára.
                </p>
            </div>
        </section>

        <!-- Our Story Section -->
        <section class="story-section py-5">
            <div class="container">
                <h2 class="section-title text-center mb-4">Történetünk</h2>
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <p>
                            A Dog Walk App 2023-ban indult, amikor alapítóink, Anna és Péter, észrevették, hogy sok gazdi küzd azzal, hogy megfelelő időt találjon szeretett kutyája sétáltatására a rohanó mindennapokban. Ugyanakkor rengeteg állatbarát keresett lehetőséget arra, hogy több időt töltsön kutyákkal és egy kis extra jövedelemre tegyen szert.
                        </p>
                        <p>
                            Ezt a két igényt összekapcsolva született meg a Dog Walk App ötlete, amely gyorsan népszerűvé vált a kutyatartók és a sétáltatók körében is. Ma már több száz aktív felhasználóval büszkélkedhetünk, és továbbra is azon dolgozunk, hogy szolgáltatásunkat fejlesszük és bővítsük.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>



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
                    <img src="' . htmlspecialchars($photo_url) . '" class="card-img-top" alt="Kutyasétáltató" style="height: 200px; object-fit: cover"/>
                    <div class="card-body">
                        <h5 class="card-title">' . htmlspecialchars($name) . '</h5>
                        <p class="card-text">' . htmlspecialchars($bio) . '</p>
                        <a href="profile.php?id=' . htmlspecialchars($user_id) . '" class="btn btn-primary">További információ</a>
                    </div>
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
   
    <div id="section3" class="container-fluid bg-secondary text-white p-3">
     
    <section class="contact-section">
        <div class="container">
            <div class="row">
                <!-- Elérhetőségek -->
                <div class="col-md-6 mb-4 mb-md-0">
                    <h2 class="section-title">Kapcsolat</h2>
                    <ul class="list-unstyled contact-info mt-4">
                        <li><i class="fas fa-phone-alt"></i> +36 1 234 5678</li>
                        <li><i class="fas fa-envelope"></i> info@dogwalkapp.com</li>
                        <li><i class="fas fa-map-marker-alt"></i> 1234 Budapest, Kutyás utca 10.</li>
                    </ul>
                    <div class="social-icons mt-4">
                        <a href="#" class="me-3 text-decoration-none text-primary"><i class="fab fa-facebook fa-2x"></i></a>
                        <a href="#" class="me-3 text-decoration-none text-primary"><i class="fab fa-instagram fa-2x"></i></a>
                        <a href="#" class="text-decoration-none text-primary"><i class="fab fa-twitter fa-2x"></i></a>
                    </div>
                </div>

                <!-- Google Maps -->
                <div class="col-md-6">
                    <h2 class="section-title">Hol talál minket?</h2>
                    <div class="map-container mt-4" style="height: 300px;">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1383.4052062131807!2d19.66247437005319!3d46.09475632460355!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x474366d1b03dbbc5%3A0xfbb187d5a85acad0!2sVisoka%20tehni%C4%8Dka%20%C5%A1kola%20strukovnih%20studija%20-%20Subotica!5e0!3m2!1shu!2srs!4v1725378677965!5m2!1shu!2srs" 
                                width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="mt-5 text-center">
        <p>&copy; 2024 Dog Walk App. Minden jog fenntartva.</p>
        <ul class="list-inline">
            <li class="list-inline-item"><a href="#about">Rólunk</a></li>
            <li class="list-inline-item"><a href="#privacy">Adatvédelmi irányelvek</a></li>
            <li class="list-inline-item"><a href="#contact">Kapcsolat</a></li>
        </ul>
    </footer>
</div>






  </body>
</html>
