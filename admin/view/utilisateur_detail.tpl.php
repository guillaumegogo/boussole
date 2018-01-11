<!DOCTYPE html>
<html>
<head>
	<?php include('view/inc.head.php'); ?>
	
	<link rel="stylesheet" href="../src/js/jquery-ui.min.css">
	<?php if($droit_ecriture) { ?>
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
			if (that.value == <?=ROLE_ANIMATEUR ?> || that.value == <?=ROLE_CONSULTANT ?> )
			{
				w.style.display = "block";
			} else if (that.value == <?=ROLE_PRO ?>)
			{
				x.style.display = "block";
			}
		}
	</script>
	<?php } else { ?>
	<link rel="stylesheet" type="text/css" href="css/readonlyform.css" media="screen" />
	<script type="text/javascript" language="javascript" src="js/readonlyform.js"></script>
	<?php } ?>	
</head>

<body>
<?php include('view/inc.bandeau.php'); ?>

<div class="container">
	<h2><small><a href="accueil.php">Accueil</a> > <a href="utilisateur_liste.php">Liste des utilisateurs</a> ></small> 
		<?= ($id_utilisateur) ? "Détail" : "Ajout" ?> d'un utilisateur <?= ($id_utilisateur && $user['actif_utilisateur'] == 0) ? '<span style="color:red">(désactivé)</span>':'' ?></h2>

	<div class="soustitre"><?= $msg ?></div>

	<form method="post" class="detail" autocomplete="off" >

<!-- The text and password here are to prevent FF from auto filling my login credentials because it ignores autocomplete="off"-->
<input type="text" style="display:none">
<input type="password" style="display:none">

		<input type="hidden" name="maj_id" value="<?= $id_utilisateur ?>">
		<fieldset <?= (!$droit_ecriture) ? 'disabled="disabled"':'' ?>>
			<legend>Description de l'utilisateur</legend>

			<div class="une_colonne">
				<div class="lab">
					<label for="courriel">Courriel <?= ($vue != 'creation') ? '(login)' : '' ?> :</label>
					<input type="text" name="courriel" required placeholder="Le courriel sert de login"
						value="<?= ($id_utilisateur) ? $user['email'] : '' ?>" <?= ($vue == 'motdepasse') ? 'disabled':'' ?> />
				</div>
				<?php if ($vue == "modif" || $vue == "creation") { ?>
					<div class="lab">
						<label for="nom_pouet">Nom :</label>
						<input type="text" name="nom_pouet" required value="<?= ($id_utilisateur) ? $user['nom_utilisateur']:'' ?>" />
					</div>
					<div class="lab">
						<label for="statut">Statut :</label>
						<select name="statut" <?= ($vue == 'modif') ? 'disabled':''; ?> onchange="displayAttache(this);">
							<option value="">A choisir</option>
						<?php foreach($liste_statuts as $key=>$statut) { ?>
							<option value="<?= $key ?>" <?= ($id_utilisateur && ($user['id_statut'] == $key)) ? 'selected':'' ?>>
								<?= $statut ?></option>
						<?php } ?>
						</select>
					</div>
					<div class="lab" style="display:block;">
						<label for="attache">Attache :</label>
						<div style="display:inline-block;">
							<select name="attache" id="liste_territoires" 
								<?= ($id_utilisateur && $user['id_statut'] == ROLE_ANIMATEUR) ? 'disabled' : 'style="display:none"' ?>>
							<option value="">A choisir</option>
						<?php foreach($liste_territoires as $user2) { ?>
							<option value="<?= $user2['id_territoire'] ?>" <?= (isset($user['id_territoire']) && ($user2['id_territoire'] == $user['id_territoire'])) ? 'selected':'' ?>><?= $user2['nom_territoire'] ?></option>
						<?php } ?>
							</select>
							
							<select name="attache_p" id="liste_professionnels" 
								<?= ($id_utilisateur && $user['id_statut'] == ROLE_PRO) ? 'disabled' : 'style="display:none"'; ?>>
							<option value="">A choisir</option>
						<?php foreach($liste_pro as $user3) { ?>
							<option value="<?= $user3['id_professionnel'] ?>" <?= (isset($user['id_professionnel']) && ($user3['id_professionnel'] == $user['id_professionnel'])) ? 'selected':'' ?>><?= $user3['nom_pro'] ?></option>
						<?php } ?>
							</select></div>
					</div>
					<?php if ($vue == "modif") { ?>
						<div class="lab">
							<label for="date">Date d'inscription :</label>
							<input type="text" name="date" class="datepick"
								   value="<?php echo date_format(date_create($user['date_inscription']), 'd/m/Y'); ?>"
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
			<?php } else if ($vue == "creation") { ?>
				<div style="font-style: italic; font-size:small; text-align:center">Un email sera transmis à l'adresse indiquée pour que l'utilisateur puisse renseigner son mot de passe.</div>
			<?php } ?>
		</fieldset>

		<div class="button">
			<input type="button" value="Retour"
				   onclick="javascript:location.href='utilisateur<?php if ($vue == "motdepasse") {
					   echo "_detail.php?id=" . $id_utilisateur;
				   } else {
					   echo "_liste.php";
				   } ?>'">
		<?php if($droit_ecriture) { 
			if ($id_utilisateur && $vue != "motdepasse") { 
				if($user['actif_utilisateur'] == 0){ ?>
			<input type="submit" name="restaurer" value="Restaurer">
		<?php }else{ 
				if($id_utilisateur!=$_SESSION['admin']['user_id']){?>
			<input type="submit" name="archiver" value="Désactiver">
				<?php } } } ?>
			<input type="submit" name="enregistrer" value="Enregistrer">
		<?php } ?>
		</div>
	</form>
</div>
</body>
</html>