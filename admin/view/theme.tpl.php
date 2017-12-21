<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" href="css/style_backoffice.css"/>
	<link rel="icon" type="image/png" href="img/compass-icon.png"/>
	<script type="text/javascript" language="javascript" src="../src/js/external/jquery/jquery.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery.filterByText.js"></script>
	<script type="text/javascript" language="javascript" src="js/selectbox.js"></script>
	<script type="text/javascript" language="javascript" src="js/fix-ie.js"></script>
	<title>Boussole des jeunes</title>
</head>

<body>
<h1 class="bandeau"><img src="../web/img/marianne.png" width="93px"> Administration de la boussole</h1>
<div class="statut"><?php echo $_SESSION['accroche']; ?> (<a href="index.php">déconnexion</a>)</div>

<div class="container">
	
	<h2><small><a href="accueil.php">Accueil</a> ></small> Gestion des thèmes</h2>
	<div class="soustitre"><?php echo $msg; ?></div>

	<?php if ($select_theme) { ?>
	<form method="post" style="margin-bottom:1em;">
		<label for="choix_theme">Thème :</label>
		<select name="choix_theme" onchange="this.form.submit()">
			<option value="">A choisir</option><?php echo $select_theme; ?></select>
	</form>
	<?php } ?>

	<?php
	if ($id_theme_choisi) {
		?>
		<form method="post" class="detail">
			<fieldset style="margin-bottom:1em;">
				<legend>Description du theme</legend>
				<div class="une_colonne" style="width:auto; min-width:auto;">
					<div class="lab">
						<label for="libelle_theme">Libellé court :</label>
						<input type="text" name="libelle_theme" value="<?= $libelle_theme_court_choisi ?>">
					</div>
					<div class="lab">
						<label for="libelle_theme">Libellé du bouton web :</label>
						<input type="text" name="libelle_theme" value="<?= $libelle_theme_choisi ?>">
					</div>
					<div class="lab">
						<label for="actif">Actif :</label>
						<input type="radio" name="actif" value="1" <?php if ($actif_theme_choisi == "1") echo "checked"; ?>> Oui
						<input type="radio" name="actif" value="0" <?php if ($actif_theme_choisi == "0") echo "checked"; ?>> Non

					</div>
					<input type="hidden" name="maj_id_theme" value="<?php echo $id_theme_choisi; ?>">
				</div>
				<input type="submit" style="display:inline-block; vertical-align:bottom;" name="submit_theme" value="Valider">
			</fieldset>

			<fieldset style="margin-bottom:1em;">
				<legend>Liste des sous-thèmes</legend>
				<?php
				if (count($sous_themes) > 0) {
					?>
					<table>
						<thead>
						<tr>
							<th>Libellé</th>
							<th>Ordre d'affichage</th>
							<th>Actif</th>
						</tr>
						</thead>
						<tbody>
						<?php
						foreach($sous_themes as $row) {
							if ($row['id_theme'] != $id_theme_choisi) {
								?>
								<tr>
									<td>
										<input type="hidden" name="sthemes[<?= $i ?>][]"
											   value="<?= $row['id_theme'] ?>"/>
										<input type="text" name="sthemes[<?= $i ?>][]"
											   value="<?= $row['libelle_theme'] ?>" style="width:60em;"/>
									</td>
									<td>
										<input type="text" name="sthemes[<?= $i ?>][]"
											   value="<?= $row['ordre_theme'] ?>" style="width:3em"/>
									</td>
									<td>
										<input type="radio" name="sthemes[<?= $i ?>][]"
											   value="1" <?= ($row['actif_theme'] == 1) ? "checked" : "" ?>> Oui
										<input type="radio" name="sthemes[<?= $i ?>][]"
											   value="0" <?= ($row['actif_theme'] == 0) ? "checked" : "" ?>> Non
									</td>
								</tr>
								<?php
								$i++;
							}
						}
						?>
						</tbody>
					</table>
					<input type="submit" style="display:block; margin:0 auto;" name="submit_liste_sous_themes"
						   value="Valider">
					<?php
				} else {
					?>
					<div class="soustitre">Aucun sous-thème</div>
					<?php
				}
				?>
			</fieldset>

			<fieldset>
				<legend>Ajouter un sous-thème</legend>
				<div class="deux_colonnes" style="width:auto; min-width:auto;">
					<div class="lab">
						<label for="libelle_nouveau_sous_theme" class="court">Libellé :</label>
						<input type="text" required name="libelle_nouveau_sous_theme" value="">
					</div>
				</div>
				<input type="submit" style="display:inline-block; vertical-align:bottom;"
					   name="submit_nouveau_sous_theme" value="Valider">
			</fieldset>

		</form>

	<?php } ?>
</div>
</body>
</html>