<!DOCTYPE html>
<html>
<head>
	<?php include('view/inc.head.php'); ?>
</head>

<body>
<a href="../web/" target="_blank"><img src="img/ex-link-w.png" class="retour_boussole"></a>
<h1 class="bandeau"><img src="../web/img/marianne.png" width="93px"> Administration de la boussole</h1>

<div class="container">

	<h2><small><a href="accueil.php">Accueil</a> ></small> Connexion</h2>

	<div class="soustitre"><?= $msg ?></div>

	<form method="post" class="detail auth">
		<div class="une_colonne" style="border:1px solid grey; padding:1em; text-align:center;">
			<div class="lab">
				<label for="login">Utilisateur :</label>
				<input type="text" name="login" placeholder="votre courriel de contact"/>
			</div>
			<div class="lab">
				<label for="motdepasseactuel">Mot de passe :</label>
				<input type="password" name="motdepasseactuel"/>
			</div>
			<a href="motdepasseoublie.php" style="display:block;font-size:small; margin:1em 0;">J'ai oublié mon mot de
				passe</a>
			<!--<input name="cookie" value="1" type="checkbox"> Garder ma session active-->
			<input type="submit" value="Se connecter">
		</div>
	</form>
	
</div>
</body>
</html>