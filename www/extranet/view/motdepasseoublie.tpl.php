<!DOCTYPE html>
<html>
<head>
	<?php include('view/inc.head.php'); ?>
	<meta name="viewport" content="width=device-width"/>
</head>

<body>
<?php include('view/inc.bandeau.php'); ?>

<div class="container">

	<h2><small><a href="accueil.php">Accueil</a> ></small> Saisie du mot de passe</h2>

	<div class="soustitre"><?= $msg ?></div>

	<form method="post" class="detail">
		<?php if ($vue == 'normal') { ?>
			<div class="une_colonne" style="border:1px solid grey; padding:1em; text-align:center;">
				<div class="lab">
					<label for="login">Adresse de courriel :</label>
					<input type="text" name="login"/>
				</div>
				<input type="submit" value="Réinitialiser le mot de passe">
			</div>

		<?php } else if ($vue == 'reinit' && $token !== null) { ?>
			<div class="une_colonne" style="border:1px solid grey; padding:1em; text-align:center;">
				<input type="hidden" name="token" value="<?= $token ?>">
				<div class="lab">
					<label for="nouveaumotdepasse">Nouveau mot de passe :</label>
					<input type="password" name="nouveaumotdepasse"/>
				</div>
				<div class="lab">
					<label for="nouveaumotdepasse2">Confirmez le mot de passe :</label>
					<input type="password" name="nouveaumotdepasse2"/>
				</div>
				<br/><input type="submit" value="Valider">
			</div>

		<?php } ?>
	</form>
</div>
</body>
</html>