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

	<div class="soustitre"><?= $msg ?></div>

	<form method="post" class="detail">
		<div class="une_colonne" style="border:1px solid grey; padding:1em; text-align:center;">
			<div class="lab">
				<label for="login">Utilisateur :</label>
				<input type="text" name="login" placeholder="votre courriel de contact"/>
			</div>
			<div class="lab">
				<label for="motdepasseactuel">Mot de passe :</label>
				<input type="password" name="motdepasseactuel" />
			</div>
			<a href="motdepasseoublie.php" style="display:block;font-size:small; margin:1em 0;">J'ai oublié mon mot de passe</a>.
			<!--<input name="cookie" value="1" type="checkbox"> Garder ma session active-->
			<input type="submit" value="Se connecter">
		</div>
	</form>

	<div style="text-align:center; color:red; margin:2em auto; font-size:0.8em;">
		ML Reims : boussole@mission-locale-reims.jeunes-ca.fr<br/>
		Animateur Grand Reims : boussole@grandreims.fr
	</div>
	<!-- le temps des tests
	<div style="width:100%; margin:1em auto;">
		<div class="une_colonne" style="border:1px solid grey; padding:1em;">
			<b>Se connecter en tant que</b>
			<ul style="line-height:2em;">
				<li><a href="?user_id=1">Utilisateur #1</a> (administrateur national)</li>
				<li><a href="?user_id=2">Utilisateur #2</a> (animateur territorial Grand Reims)</li>
				<li><a href="?user_id=3">Utilisateur #3</a> (professionnel Mission locale de Reims)</li>
			</ul>
		</div>
	</div> -->
</div>

<?php 
if ($ENVIRONNEMENT=="LOCAL") {
	echo "<pre>"; print_r($_POST); echo "</pre>"; 
}
?>
</body>
</html>