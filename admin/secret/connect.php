<?php
$servername = "localhost";
$username = "root";
$password = "";
$nom_base_donnees = "boussole";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $nom_base_donnees);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");

setlocale (LC_ALL, "fr_FR");
setlocale(LC_TIME, 'fra_fra');

$ENVIRONNEMENT="LOCAL";
?> 