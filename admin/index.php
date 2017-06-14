<?php
require('../secret/connect.php');

//********* valeur de sessions
session_start();
session_unset(); 
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width" />
	<link rel="stylesheet" href="css/style_backoffice.css" />
	<link rel="icon" type="image/png" href="../img/compass-icon.png" />
	<title>Boussole des jeunes</title>
</head>

<body>
<a href="../" target="_blank"><img src="img/external-link.png" class="retour_boussole"></a>
<h1 class="bandeau">Administration de la boussole des jeunes</h1>

<div class="container">

<h2>Connexion</h2>
    
	<div style="width:60%; margin:auto;">
		<b>Se connecter en tant que</b>
		<ul style="line-height:2em;">
			<li><a href="accueil.php?user_id=1">Administrateur national</a></li>
			<li><a href="accueil.php?user_id=2">Animateur territorial (Grand Reims)</a></li>
			<li><a href="accueil.php?user_id=3">Professionnel (Mission locale de Reims)</a></li>
		</ul>
	</div>
</div>

</body>
</html>