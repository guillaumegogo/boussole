<?php
$servername = "srv-sql";
$username = "apache";
$password = "cpvaprplf";
$nom_base_donnees = "boussole";

//Sur la prod on désactive l'affichage des erreurs
if (ENVIRONMENT === ENV_PROD)
    error_reporting(0);

// connection à la base de données
$conn = @mysqli_connect($servername, $username, $password, $nom_base_donnees);
if (!$conn || !$conn instanceof mysqli)
    throw new Exception("Connection failed: " . mysqli_connect_error());

mysqli_set_charset($conn, "utf8");

setlocale(LC_ALL, "fr_FR");
setlocale(LC_TIME, 'fra_fra');
