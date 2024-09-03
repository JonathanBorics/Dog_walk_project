<?php
session_start();
session_unset();
session_destroy();

// Eredeti átirányítás
// header("Location: login.php?logout=1");

// Módosított átirányítás az index.php oldalra
header("Location: index.php");
exit();
?>
