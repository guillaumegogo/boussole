<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Boussole des jeunes</title>
	<link rel="icon" type="image/png" href="img/compass-icon.png"/>
	<link rel="stylesheet" href="css/style_backoffice.css"/>
	<link rel="stylesheet" href="css/jquery-ui.css">
	<script type="text/javascript" language="javascript" src="js/jquery-1.12.0.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery-ui-1.12.0.js"></script>
	<script type="text/javascript">
		//fonction autocomplete commune
		$(function () {
			var listeVilles = [<?php include('../src/villes_index.inc');?>];
			$('#villes').autocomplete({
				minLength: 2,
				source: function (request, response) {
					//adaptation fichier insee
					request.term = request.term.replace('-', ' ');
					request.term = request.term.replace(/^saint /gi, 'St ');
					//recherche sur les premiers caractères de la ville ou sur le code postal
					var matcher1 = new RegExp('^' + $.ui.autocomplete.escapeRegex(request.term), 'i');
					var matcher2 = new RegExp(' ' + $.ui.autocomplete.escapeRegex(request.term) + '[0-9]*$', 'i');
					response($.grep(listeVilles, function (item) {
						return (matcher1.test(item) || matcher2.test(item));
					}));
				}
			});
		});

		//fonction affichage listes
		function displayGeo(that)
		{
			var w = document.getElementById('liste_regions');
			var x = document.getElementById('liste_departements');
			var y = document.getElementById('liste_territoires');
			if (w != null)
			{
				w.style.display = 'none';
			}
			if (x != null)
			{
				x.style.display = 'none';
			}
			if (y != null)
			{
				y.style.display = 'none';
			}
			if (that.value == 'regional')
			{
				w.style.display = 'block';
			} else if (that.value == 'departemental')
			{
				x.style.display = 'block';
			} else if (that.value == 'territoire')
			{
				y.style.display = 'block';
			}
		}
	</script>
</head>

<body>
<h1 class="bandeau">Administration de la boussole des jeunes</h1>
<div class="statut"><?= $_SESSION["accroche"] ?> (<a href="index.php">déconnexion</a>)</div>

<div class="container">
	<h2><small><a href="accueil.php">Accueil</a> > <a href="professionnel_liste.php">Liste des professionnels</a> ></small> 
		<?= ($id_professionnel) ? 'Modification' : 'Création'; ?> d'un professionnel</h2>

	<div class="soustitre"><?= $msg ?></div>

	<?php
	if ($row !== null) {
	?>

	<form method="post" class="detail">

		<input type="hidden" name="maj_id" value="<?= $id_professionnel ?>">
		<fieldset>
			<legend>Détail du professionnel</legend>

			<div class="deux_colonnes">
				<div class="lab">
					<label for="nom">Nom du professionnel :</label>
					<input type="text" name="nom" value="<?php if ($id_professionnel) {
						echo $row['nom_pro'];
					} ?>"/>
				</div>
				<div class="lab">
					<label for="type">Type :</label>
					<input type="text" name="type" value="<?php if ($id_professionnel) {
						echo $row['type_pro'];
					} ?>"/>
				</div>
				<div class="lab">
					<label for="desc">Description du professionnel :</label>
					<textarea rows="5" name="desc"><?php if ($id_professionnel) {
							echo $row['description_pro'];
						} ?></textarea>
				</div>
				<div class="lab">
					<label for="theme[]">Thème(s) :</label>
					<?= $select_theme ?>
				</div>
				<div class="lab">
					<label for="actif">Actif :</label>
					<input type="radio" name="actif" value="1" <?php if ($id_professionnel) {
						if ($row['actif_pro'] == "1") {
							echo "checked";
						}
					} else echo "checked"; ?>> Oui
					<input type="radio" name="actif" value="0" <?php if ($id_professionnel) {
						if ($row['actif_pro'] == "0") {
							echo "checked";
						}
					} ?>> Non
					</select>
				</div>
			</div>
			<div class="deux_colonnes">
				<div class="lab">
					<label for="adresse">Adresse :</label>
					<input type="text" name="adresse" value="<?php if ($id_professionnel) {
						echo $row['adresse_pro'];
					} ?>"/>
				</div>
				<div class="lab">
					<label for="code_postal">Code postal :</label>
					<input type="text" name="commune" id="villes" value="<?php if ($id_professionnel) {
						echo $row['ville_pro'] . " " . $row['code_postal_pro'];
					} ?>"/>
				</div>
				<div class="lab">
					<label for="courriel">Courriel :</label>
					<input type="email" name="courriel" value="<?php if ($id_professionnel) {
						echo $row['courriel_pro'];
					} ?>"/>
				</div>
				<div class="lab">
					<label for="tel">Téléphone :</label>
					<input type="text" name="tel" value="<?php if ($id_professionnel) {
						echo $row['telephone_pro'];
					} ?>"/>
				</div>
				<div class="lab">
					<label for="site">Site internet :</label>
					<input type="text" name="site" value="<?php if ($id_professionnel) {
						echo $row['site_web_pro'];
					} ?>"/>
				</div>
				<div class="lab">
					<label for="delai">Délai garanti de réponse :</label>
					<select name="delai">
					<?php for($i = 1; $i <= 7 ;$i++) { ?>
						<option value="<?= $i ?>" 
						<?php if ($id_professionnel) {
							if ($row['delai_pro'] == $i) {
								echo "selected";
							}
						} ?>><?= $i ?> jours
						</option>
					<?php } ?>
					</select>
				</div>
				<div class="lab">
					<label for="competence_geo">Compétence géographique :</label>
					<div style="display:inline-block;">
						<select name="competence_geo" onchange="displayGeo(this);"
								style="display:block; margin-bottom:0.5em;">
							<?= $liste_competence_geo ?>
						</select>

						<?= $affiche_listes_geo ?>
					</div>
				</div>

			</div>
		</fieldset>

		<div class="button">
			<input type="button" value="Retour" onclick="javascript:location.href='professionnel_liste.php'">
			<input type="reset" value="Reset">
			<input type="submit" value="Enregistrer">
		</div>
	</form>
	<?php
	} else {
		echo "Professionnel inconnu.";
	}
	?>
</div>
</body>
</html>