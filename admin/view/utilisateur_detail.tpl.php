<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Boussole des jeunes</title>
	<link rel="icon" type="image/png" href="img/compass-icon.png"/>
	<link rel="stylesheet" href="css/style_backoffice.css"/>
	<link rel="stylesheet" href="css/jquery-ui.css">
	<script type="text/javascript" language="javascript" src="js/fix-ie.js"></script>
	<script type="text/javascript">
		//fonction affichage listes
		function displayAttache(that)
		{
			var w = document.getElementById('liste_territoires');
			var x = document.getElementById('liste_professionnels');
			if (w != null)
			{
				w.style.display = 'none';
			}
			if (x != null)
			{
				x.style.display = 'none';
			}
			if (that.value == "2")
			{
				w.style.display = "block";
			} else if (that.value == "3")
			{
				x.style.display = "block";
			}
		}
	</script>
</head>

<body>
<h1 class="bandeau"><img src="../web/img/marianne.png" width="93px"> Administration de la boussole</h1>
<div class="statut"><?php echo $_SESSION['accroche']; ?> (<a href="index.php">déconnexion</a>)</div>

<div class="container">
	<h2><small><a href="accueil.php">Accueil</a> > <a href="utilisateur_liste.php">Liste des utilisateurs</a> ></small> 
		<?= ($id_utilisateur) ? "Détail" : "Ajout" ?> d'un utilisateur <?= ($id_utilisateur && $row['actif_utilisateur'] == 0) ? '<span style="color:red">(désactivé)</span>':'' ?></h2>

	<div class="soustitre"><?= $msg ?></div>

	<form method="post" class="detail" autocomplete="off" >

<!-- The text and password here are to prevent FF from auto filling my login credentials because it ignores autocomplete="off"-->
<input type="text" style="display:none">
<input type="password" style="display:none">

		<input type="hidden" name="maj_id" value="<?= $id_utilisateur ?>">
		<fieldset>
			<legend>Description de l'utilisateur</legend>

			<div class="une_colonne">
				<div class="lab">
					<label for="courriel">Courriel <?= ($vue != 'creation') ? '(login)' : '' ?> :</label>
					<input type="text" name="courriel" required placeholder="Le courriel sert de login"
						value="<?= ($id_utilisateur) ? $row['email'] : '' ?>" <?= ($vue == 'motdepasse') ? 'disabled':'' ?> />
				</div>
				<?php if ($vue == "modif" || $vue == "creation") { ?>
					<div class="lab">
						<label for="nom_pouet">Nom :</label>
						<input type="text" name="nom_pouet" required value="<?= ($id_utilisateur) ? $row['nom_utilisateur']:'' ?>" />
					</div>
					<div class="lab">
						<label for="statut">Statut :</label>
						<select name="statut" <?= ($vue == 'modif') ? 'disabled':''; ?> onchange="displayAttache(this);">
							<option value="">A choisir</option>
						<?php foreach($liste_statuts as $key=>$statut) { ?>
							<option value="<?= $key ?>" <?= ($id_utilisateur && ($row['id_statut'] == $key)) ? 'selected':'' ?>>
								<?= $statut ?></option>
						<?php } ?>
						</select>
					</div>
					<div class="lab">
						<label for="attache">Attache :</label>
						<div style="display:inline-block;">
							<select name="attache" id="liste_territoires" 
								<?= ($id_utilisateur && $row['id_statut'] == '2') ? 'disabled' : 'style="display:none"' ?>>
							<option value="">A choisir</option>
						<?php foreach($liste_territoires as $row2) { ?>
							<option value="<?= $row2['id_territoire'] ?>" <?= (isset($row['id_territoire']) && ($row2['id_territoire'] == $row['id_territoire'])) ? 'selected':'' ?>><?= $row2['nom_territoire'] ?></option>
						<?php } ?>
							</select>
							
							<select name="attache_p" id="liste_professionnels" 
								<?php ($id_utilisateur && $row['id_statut'] == '3') ? 'disabled' : 'style="display:none"'; ?>>
							<option value="">A choisir</option>
						<?php foreach($liste_pro as $row3) { ?>
							<option value="<?= $row3['id_professionnel'] ?>" <?= (isset($row['id_professionnel']) && ($row3['id_professionnel'] == $row['id_professionnel'])) ? 'selected':'' ?>><?= $row3['nom_pro'] ?></option>
						<?php } ?>
							</select></div>
					</div>
					<?php if ($vue == "modif") { ?>
						<div class="lab">
							<label for="date">Date d'inscription :</label>
							<input type="text" name="date" class="datepick"
								   value="<?php echo date_format(date_create($row['date_inscription']), 'd/m/Y'); ?>"
								   disabled/>
						</div>
					<?php } ?>
				<?php }
				if ($vue == "motdepasse") {
					?>
					<div class="lab">
						<label for="motdepasseactuel">Mot de passe actuel :</label>
						<input type="password" name="motdepasseactuel"/>
					</div>
				<?php }
				if ($vue == "motdepasse" || $vue == "creation") {
					?>
					<div class="lab">
						<label for="nouveaumotdepasse"><?php echo ($id_utilisateur) ? "Nouveau mot de passe" : "Mot de passe"; ?>
							:</label>
						<input type="password" name="nouveaumotdepasse" />
					</div>
					<div class="lab">
						<label for="nouveaumotdepasse2">Confirmez le mot de passe :</label>
						<input type="password" name="nouveaumotdepasse2"/>
					</div>
				<?php } ?>
			</div>
			<?php if ($vue == "modif") { ?>
				<a href="?id=<?= $id_utilisateur ?>&do=mdp" style="float:right">Changer le mot de passe</a>
			<?php } ?>
		</fieldset>

		<div class="button">
			<input type="button" value="Retour"
				   onclick="javascript:location.href='utilisateur<?php if ($vue == "motdepasse") {
					   echo "_detail.php?id=" . $id_utilisateur;
				   } else {
					   echo "_liste.php";
				   } ?>'">
		<?php if (!$id_utilisateur) { ?>
			<input type="reset" value="Reset">
		<?php }else{ if($row['actif_utilisateur'] == 0){ ?>
			<input type="submit" name="restaurer" value="Restaurer">
		<?php }else{ ?>
			<input type="submit" name="archiver" value="Désactiver">
		<?php } } ?>
			<input type="submit" name="enregistrer" value="Enregistrer">
		</div>
	</form>
</div>
</body>
</html>