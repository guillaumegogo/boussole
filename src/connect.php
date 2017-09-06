<?php
//Connexion à la base de données
$conn = @mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWD, DB_NAME);
if (!$conn || !$conn instanceof mysqli)
	throw new Exception("Connection failed : ".mysqli_connect_error());

mysqli_set_charset($conn, "utf8");

setlocale(LC_ALL, "fr_FR");
setlocale(LC_TIME, 'fra_fra');
