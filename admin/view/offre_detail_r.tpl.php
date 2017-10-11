<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Boussole des jeunes</title>
	<link rel="icon" type="image/png" href="img/compass-icon.png"/>
	<link rel="stylesheet" href="css/jquery-ui.css">
	<link rel="stylesheet" href="css/style_backoffice.css"/>
</head>

<body>
<h1 class="bandeau"><img src="../web/img/marianne.png" width="93px"> Administration de la boussole</h1>
<div class="statut"><?php echo $_SESSION['accroche']; ?> (<a href="index.php">déconnexion</a>)</div>

<div class="container">

	<h2><small><a href="accueil.php">Accueil</a> > <a href="offre_liste.php">Liste des offres de service</a> ></small> 
		<?= ($id_offre) ? 'Détail' : 'Création' ?> d'une offre <?= ($id_offre && $row['actif_offre'] == 0) ? '<span style="color:red">(archivée)</span>':'' ?> </h2>

	<form class="detail" >

		<fieldset>	
			<legend>Description de l'offre de service</legend>

			<div class="deux_colonnes">
				<div class="lab">
					<label for="nom">Nom de l'offre de service :</label>
					<?= $row['nom_offre']; ?>
				</div>
				<div class="lab">
					<label for="desc">Description de l'offre :</label>
					<div style="display:inline-block; padding:0.25em; margin:0.5em 0; border:1px solid #CCC; ">
						<?= ($id_offre) ? bbcode2html($row['description_offre']):'' ?>
					</div>
				</div>
				<div class="lab">
					<label for="du">Dates de validité :</label>
					<?= $row['date_debut']; ?>
					au 
					<?= $row['date_fin']; ?>
				</div>
					<div class="lab">
						<label for="theme">Thème :</label>
						<?php 
						foreach($themes as $rowt){
							if (!isset($rowt['id_theme_pere'])) {
								if ($rowt['id_professionnel'] == $row['id_professionnel']) { 
									if($rowt['id_theme'] == $row['id_theme_pere']) {
										echo $rowt['libelle_theme']; 
									}		
								}
							}
						}
						?>
					</div>
					<div class="lab">
						<label for="sous_theme">Sous-thème(s) :</label>
						<?php 
						foreach($themes as $rowt){
							if (isset($rowt['id_theme_pere'])) {
								if ($rowt['id_theme_pere'] == $row['id_theme_pere']) { 
									if ($rowt['id_theme'] == $row['id_sous_theme']) { 
										echo $rowt['libelle_theme']; 
									}
								}
							}
						}
						?>
					</div>
			</div>
			<div class="deux_colonnes">
				<div class="lab">
					<label for="pro">Professionnel :</label>
					<?= $row['nom_pro'] ?>
				</div>
				<div class="lab">
					<label for="adresse">Adresse :</label>
					<?= $row['adresse_offre'] ?>
				</div>
				<div class="lab">
					<label for="code_postal">Code postal (& commune) :</label>
					<?=  $row['ville_offre'] . " " . $row['code_postal_offre'] ?>
				</div>
				<div class="lab">
					<label for="courriel">Courriel :</label>
					<?=  $row['courriel_offre'] ?>
				</div>
				<div class="lab">
					<label for="tel">Téléphone :</label>
					<?=  $row['telephone_offre'] ?>
				</div>
				<div class="lab">
					<label for="site">Site internet :</label>
					<?=  $row['site_web_offre'] ?>
				</div>
				<div class="lab">
					<label for="delai">Délai garanti de réponse :</label>
					<?= $row['delai_offre'] ?> jours
				</div>
				<div class="lab">
					<label for="zone">Zone concernée :</label>
					<?= ($row['zone_offre']) ? 'Sélection de villes':'Compétence géographique du pro.' ?>
					
					<div class="lab" style="display:inline-block; font-size:0.8em; border:1px solid #CCC; padding:0.25em;">
					<?php 
					if(isset($willes)){
						foreach($willes as $roww){ 
							echo $roww['nom_ville']. ' ' . $roww['code_postal'].', ';
						}
					} ?>
					</div>
				</div>
			</div>
		</fieldset>

		<?php
		//si création d'une offre de service -> on n'affiche pas. si modification -> on affiche.
		if (isset($row['id_theme_pere']) && $row['id_theme_pere']) {
		?>

			<fieldset>
				<legend>Liste des critères de l'offre de service</legend>
				<div class="colonnes">

			<?php
			foreach ($questions as $question) {
			?>
						<div class="lab">
							<label for="critere[<?= $question['name'] ?>][]"><?= $question['libelle'] ?></label>
							<select disabled name="critere[<?= $question['name'] ?>][]" multiple
									size="<?= min(count($reponses[$question['name']]), 10) ?>">
				<?php
				foreach ($reponses[$question['name']] as $reponse) {
					if ($reponse['valeur']) {
				?>
							<option value="<?= $reponse['valeur'] ?>" <?= $reponse['selectionne'] ?>><?= $reponse['libelle'] ?></option>
				<?php
					}
				}
				?>
							</select>
						</div>
			<?php
			}
			?>
				</div>
			</fieldset>

		<?php
		}
		?>

		<div class="button">
			<input type="button" value="Retour à la liste" onclick="javascript:location.href='offre_liste.php'">
		</div>
	</form>
</div>
</body>
</html>