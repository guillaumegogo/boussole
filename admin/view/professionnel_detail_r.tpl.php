<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Boussole des jeunes</title>
	<link rel="icon" type="image/png" href="img/compass-icon.png"/>
	<link rel="stylesheet" href="css/style_backoffice.css"/>
	<link rel="stylesheet" href="css/jquery-ui.css">
</head>

<body>
<h1 class="bandeau"><img src="../web/img/marianne.png" width="93px"> Administration de la boussole</h1>
<div class="statut"><?= $_SESSION["accroche"] ?> (<a href="index.php">déconnexion</a>)</div>

<div class="container">
	<h2><small><a href="accueil.php">Accueil</a> > <a href="professionnel_liste.php">Liste des professionnels</a> ></small> 
		<?= ($id_professionnel) ? 'Détail' : 'Création'; ?> d'un professionnel  <?= ($id_professionnel && $pro['actif_pro'] == 0) ? '<span style="color:red">(désactivé)</span>':'' ?> </h2>

	<div class="soustitre"><?= $msg ?></div>

	<?php
	if ($pro !== null) {
	?>

	<form method="post" class="detail" onsubmit='htmleditor(); checkall();'>

		<input type="hidden" name="maj_id" value="<?= $id_professionnel ?>">
		<fieldset>
			<legend>Description du professionnel</legend>

			<div class="deux_colonnes">
				<div class="lab">
					<label for="nom" style="margin-right:-1em;">Nom du professionnel :</label>
					<div style="display:inline-block; margin-left:1em; "><?= $pro['nom_pro']; ?></div>
				</div>
				<div class="lab">
					<label for="type_id">Type :</label>
					<div><?= (isset($pro['type'])?$pro['type']:$pro['type_pro'] ?></div>
				</div>
				<div class="lab">
					<label for="statut_id">Statut :</label>
					<?php foreach ($statuts as $statut) {
						if(isset($pro['statut_id']) && $pro['statut_id'] == $statut['id']) echo $statut['libelle'];
					} ?>
				</div>
				<div class="lab">
					<label for="desc">Description du professionnel :</label>
					<div style="display:inline-block; margin-left:1em;">
						<?= ($id_offre) ? bbcode2html($row['description_pro']):'' ?>
					</div>
				</div>
				<div class="lab">
					<label for="theme[]">Thème(s) :</label>
					<div style="display:inline-block;">
					<?php foreach($themes as $rowt) { 
						$comma='';
						if(isset($rowt['id_professionnel']) && $rowt['id_professionnel']) {
							echo $comma.$rowt['libelle_theme'];
							$comma=', ';
						}
					} ?>
					</div>
				</div>
			</div>
			<div class="deux_colonnes">
				<div class="lab">
					<label for="adresse">Adresse :</label>
					<?= $pro['adresse_pro']; ?>
				</div>
				<div class="lab">
					<label for="code_postal">Code postal :</label>
					<?= $pro['ville_pro'] . ' ' . $pro['code_postal_pro'] ?>
				</div>
				<div class="lab">
					<label for="site">Site internet :</label>
					<?= $pro['site_web_pro'] ?>
				</div>
				<div class="lab">
					<label for="courriel">Courriel de gestion :</label>
					<?= $pro['courriel_pro'] ?>
				</div>
				<div class="lab">
					<label for="tel">Téléphone :</label>
					<?= $pro['telephone_pro'] ?>
				</div>
				<div class="lab">
					<label for="courriel">Courriel référent Boussole :</label>
					<?= $pro['courriel_referent_boussole'] ?>
				</div>
				<div class="lab">
					<label for="tel">Téléphone référent Boussole :</label>
					<?= $pro['telephone_referent_boussole'] ?>
				</div>
				<div class="lab">
					<label for="delai">Délai de réponse aux offres :</label>
					<?= $pro['delai_pro'] ?> jours
				</div>
				<div class="lab">
					<label for="competence_geo">Compétence géographique :</label>
					<?php foreach ($competences_geo as $key => $value) {
						if((isset($pro['competence_geo']) && $pro['competence_geo'] == $key)) echo $value; 
					}
					
					if (isset($regions) && ($pro['competence_geo'] == 'regional')){
						foreach ($regions as $row_r) {
							if($row_r['id_region'] == $pro['id_competence_geo']) echo $row_r['nom_region'];
						}
					} 
					if (isset($departements) && ($pro['competence_geo'] == 'departemental')){
						foreach ($departements as $row_d) {
							if($row_d['id_departement'] == $pro['id_competence_geo']) echo $row_d['nom_departement'];
						}
					} 
					if (isset($territoires) && ($pro['competence_geo'] == 'territoire')){
						foreach ($territoires as $row_t) {
							if($row_t['id_territoire'] == $pro['id_competence_geo']) echo $row_t['nom_territoire'];
						}
					} 
					?>
				</div>
				<?php if (isset($pro['zone_selection_villes']) && $pro['zone_selection_villes']) { ?>
				<div class="lab">
					<label for="competence_geo">Zone personnalisée :</label>
					<?php 
					if(isset($liste_villes_pro)){
						foreach($liste_villes_pro as $rowl){ 
							echo $rowl['nom_ville']. ' ' . $rowl['code_postal'];
						}
					} ?>
				</div>
				<?php } ?>
			</div>
		</fieldset>

		<div class="button">
			<input type="button" value="Retour" onclick="javascript:location.href='professionnel_liste.php'">
		</div>
		
<?php if(count($offres)+count($incoherences_themes)+count($incoherences_villes)>0){ ?>
		<fieldset>
			<legend>Offres de service du professionnel</legend>
			<div>
<?php if(count($incoherences_themes)>0){ ?>
		<span style="color:red; font-weight: bold;">offres incohérentes (thèmes)</span><ul>
		<?php 
		foreach ($incoherences_themes as $row){ ?>
			<li><a href="offre_detail.php?id=<?=$row['id_offre']?>"><?=$row['nom_offre']?></a></li>
		<?php
		}?>
		</ul><br>
<?php } 
if(count($incoherences_villes)>0){ ?>
		<span style="color:red; font-weight: bold;">offres incohérentes (villes)</span><ul>
		<?php 
		foreach ($incoherences_villes as $row){ ?>
			<li><a href="offre_detail.php?id=<?=$row['id_offre']?>"><?=$row['nom_offre']?></a></li>
		<?php
		}?>
		</ul><br>
<?php } 
if(count($offres)>0){ ?>
		<span style="font-weight: bold;">offres actives :</span><ul>
		<?php 
		foreach ($offres as $row){ ?>
			<li><a href="offre_detail.php?id=<?=$row['id_offre']?>"><?=$row['nom_offre']?></a></li>
		<?php
		}?>
		</ul>
<?php } ?>
			</div>
		</fieldset>
<?php } ?>

	</form>
	<?php
	} else {
		echo "Professionnel inconnu.";
	}
	?>
</div>
</body>
</html>