<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width" />
	<link rel="stylesheet" href="css/style_backoffice.css" />
	<link rel="icon" type="image/png" href="img/compass-icon.png" />
	<title>Boussole des jeunes</title>
</head>

<body>
<a href="../web/" target="_blank"><img src="img/external-link.png" class="retour_boussole"></a>
<h1 class="bandeau">Administration de la boussole des jeunes</h1>

<div class="container">

<h2>Connexion</h2>

	<form method="post" class="detail">
		<div class="une_colonne" style="border:1px solid grey; padding:1em;">
			<div class="lab">
				<label for="courriel">Utilisateur (courriel) :</label>
				<input type="text" name="courriel" />
			</div>
			<div class="lab">
				<label for="motdepasseactuel">Mot de passe :</label>
				<input type="password" name="motdepasseactuel" />
			</div>
			<!--<input name="cookie" value="1" type="checkbox"> Garder ma session active-->
			<input type="submit" value="Se connecter">
		</div>
	</form>

	<div style="width:100%; margin:1em auto;">
		<div class="une_colonne" style="border:1px solid grey; padding:1em;">
			<b>Se connecter en tant que</b>
			<ul style="line-height:2em;">
				<li><a href="accueil.php?user_id=1">Administrateur national</a></li>
				<li><a href="accueil.php?user_id=2">Animateur territorial (Grand Reims)</a></li>
				<li><a href="accueil.php?user_id=3">Professionnel (Mission locale de Reims)</a></li>
			</ul>
		</div>
	</div>
</div>

</body>
</html>