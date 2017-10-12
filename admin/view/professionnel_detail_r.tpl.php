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
					<div style="display:inline-block"><select name="type_id">
						<option value=""></option>
					<?php foreach ($types as $type) { ?>
						<option value="<?= $type['id'] ?>" <?= ($id_professionnel && (isset($pro['type_id']) && $pro['type_id'] == $type['id'])) ? ' selected ' : '' ?> ><?= $type['libelle'] ?></option>
					<?php } ?>
					</select><?php if (isset($pro['type_pro']) && $pro['type_pro'] && !$pro['type_id']) { ?>
					<br/><span style="font-size:0.7em">(<?= $pro['type_pro'] ?>)</span>
					<?php } ?></div>
				</div>
				<div class="lab">
					<label for="statut_id">Statut :</label>
					<select name="statut_id">
						<option value=""></option>
					<?php foreach ($statuts as $statut) { ?>
						<option value="<?= $statut['id'] ?>" <?= ($id_professionnel && (isset($pro['statut_id']) && $pro['statut_id'] == $statut['id'])) ? ' selected ' : '' ?> ><?= $statut['libelle'] ?></option>
					<?php } ?>
					</select>
				</div>
				<div class="lab">
					<label for="desc">Description du professionnel :</label>
					<div style="display:inline-block;">
						<input type="button" value="G" style="font-weight: bold;" onclick="commande('bold');"/>
						<input type="button" value="I" style="font-style: italic;" onclick="commande('italic');"/>
						<input type="button" value="S" style="text-decoration: underline;" onclick="commande('underline');"/>
						<input type="button" value="Lien" onclick="commande('createLink');"/>
						<input type="button" value="Image" onclick="commande('insertImage');"/>
						<div id="editeur" contentEditable><?= ($id_professionnel) ? bbcode2html($pro['description_pro']):'' ?></div>
						<input id="resultat" type="hidden" name="desc"/>
					</div>
				</div>
				<div class="lab">
					<label for="theme[]">Thème(s) :</label>
					<div style="display:inline-table;">
					<?php foreach($themes as $rowt) { ?>
						<input type="checkbox" name="theme[]" value="<?= $rowt['id_theme'] ?>" <?= (isset($rowt['id_professionnel']) && $rowt['id_professionnel']) ? ' checked ':'' ?>> <?= $rowt['libelle_theme'] ?></br>
					<?php } ?>
					</div>
				</div>
				<!--<div class="lab">
					<label for="editeur">Éditeur :</label>
					<input type="checkbox" name="check_editeur" value="1" <?= (isset($pro['editeur']) && $pro['editeur']) ? ' checked ':'' ?>> Oui <img src="img/help.png" height="16px" title="L'éditeur a le droit de saisir des mesures.">
				</div>-->
			</div>
			<div class="deux_colonnes">
				<div class="lab">
					<label for="adresse">Adresse :</label>
					<input type="text" name="adresse" value="<?php if ($id_professionnel) {
						echo $pro['adresse_pro'];
					} ?>"/>
				</div>
				<div class="lab">
					<label for="code_postal">Code postal :</label>
					<input type="text" name="commune" required id="villes" value="<?php if ($id_professionnel) {
						echo $pro['ville_pro'] . " " . $pro['code_postal_pro'];
					} ?>"/>
				</div>
				<div class="lab">
					<label for="site">Site internet :</label>
					<input type="text" name="site" value="<?php if ($id_professionnel) {
						echo $pro['site_web_pro'];
					} ?>"/>
				</div>
				<div class="lab">
					<label for="courriel">Courriel de gestion :</label>
					<input type="email" name="courriel" required value="<?php if ($id_professionnel) {
						echo $pro['courriel_pro'];
					} ?>"/>
				</div>
				<div class="lab">
					<label for="tel">Téléphone :</label>
					<div style="display:inline-table;">
					<input type="text" name="tel" value="<?php if ($id_professionnel) {
						echo $pro['telephone_pro'];
					} ?>"/>
					<br/><input type="checkbox" name="visibilite" value="1" <?= (isset($pro['visibilite_coordonnees']) && $pro['visibilite_coordonnees']) ? ' checked ':'' ?>> <small><i>Afficher les courriel et téléphone sur le site.</i></small>
					</div>
				</div>
				<div class="lab">
					<label for="courriel">Courriel référent Boussole :</label>
					<input type="email" name="courriel_ref" required value="<?php if ($id_professionnel) {
						echo $pro['courriel_referent_boussole'];
					} ?>"/>
				</div>
				<div class="lab">
					<label for="tel">Téléphone référent Boussole :</label>
					<div style="display:inline-table;">
					<input type="text" name="tel_ref" required value="<?php if ($id_professionnel) {
						echo $pro['telephone_referent_boussole'];
					} ?>"/>
					<br/><small><i>Ces informations ne sont pas affichées sur le site.</i></small>
					</div>
				</div>
				<div class="lab">
					<label for="delai">Délai de réponse aux offres :</label>
					<select name="delai">
					<?php for($i = 1; $i <= 7 ;$i++) { ?>
						<option value="<?= $i ?>" 
						<?php if ($id_professionnel) {
							if ($pro['delai_pro'] == $i) {
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

					<select name="competence_geo" required onchange="displayGeo(this);" style="display:block; margin-bottom:0.5em;">
						<option value="">A choisir</option>
					<?php foreach ($competences_geo as $key => $value) { ?>
						<option value="<?= $key ?>" <?= ($id_professionnel && (isset($pro['competence_geo']) && $pro['competence_geo'] == $key)) ? ' selected ' : '' ?> ><?= $value ?></option>
					<?php } ?>
					</select>

<?php //liste déroulante des régions
if (isset($regions)){ 
	$display_r = ($id_professionnel && ($pro['competence_geo'] == 'regional')) ? 'block' : 'none';
?>
<select name="liste_regions" id="liste_regions" style="display:<?= $display_r ?>" >
	<option value="">A choisir</option>
	<?php foreach ($regions as $row_r) { ?>
	<option value="<?= $row_r['id_region'] ?>" <?= ((isset($pro['competence_geo']) && $pro['competence_geo'] == 'regional') && ($row_r['id_region'] == $pro['id_competence_geo'])) ? ' selected ' : '' ?> ><?= $row_r['nom_region'] ?></option>
	<?php } ?>
</select>
<?php } ?>

<?php //liste déroulante des départements
if (isset($departements)){ 
	$display_d = ($id_professionnel && ($pro['competence_geo'] == 'departemental')) ? 'block' : 'none';
?>
<select name="liste_departements" id="liste_departements" style="display:<?= $display_d ?>" >
	<option value="">A choisir</option>
	<?php foreach ($departements as $row_d) { ?>
	<option value="<?= $row_d['id_departement'] ?>" <?= ((isset($pro['competence_geo']) && $pro['competence_geo'] == 'departemental') && ($row_d['id_departement'] == $pro['id_competence_geo'])) ? ' selected ' : '' ?> ><?= $row_d['nom_departement'] ?></option>
	<?php } ?>
</select>
<?php } ?>

<?php //liste déroulante des territoires
if (isset($territoires)){ 
	$display_t = ($id_professionnel && ($pro['competence_geo'] == 'territoire')) ? 'block' : 'none';
?>
<select name="liste_territoires" id="liste_territoires" style="display:<?= $display_t ?>" >
	<option value="">A choisir</option>
	<?php foreach ($territoires as $row_t) { ?>
	<option value="<?= $row_t['id_territoire'] ?>" <?= ((isset($pro['competence_geo']) && $pro['competence_geo'] == 'territoire') && ($row_t['id_territoire'] == $pro['id_competence_geo'])) ? ' selected ' : '' ?> ><?= $row_t['nom_territoire'] ?></option>
	<?php } ?>
</select></div>

<div style="margin-top:1em"><span id="zone_personnalisee" style="display:<?= $display_t ?>" >
	<input type="checkbox" name="check_zone" id="check_zone" value="1" <?= (isset($pro['zone_selection_villes']) && $pro['zone_selection_villes']) ? 'checked' : '' ?> onchange="displayZone(this);" > Personnaliser la zone de compétence territoriale</span>
	
	<div class="lab" id="div_liste_villes" style="display:<?= (isset($pro['zone_selection_villes']) && $pro['zone_selection_villes']) ? 'block' : 'none' ?>">
		<div style="margin-bottom:1em;">Filtre : 
			<input id="textbox"
				value="nom de ville, code postal ou département..."
				type="text" style="width:20em;"
				onFocus="javascript:this.value='';">
		</div>

		<div style="display:inline-block; vertical-align:top;">
			<small><i>Villes correspondant au filtre :</i></small><br/>
			<select id="list1" MULTIPLE SIZE="20" style=" min-width:20em;">
				<?php include('../src/admin/villes_options_insee.inc'); //la liste des villes de France... todo : à remplacer par $("#villes").autocomplete ?>
			</select>
		</div>

		<div style="display:inline-block; margin-top:1em; vertical-align: top;">
			<INPUT TYPE="button" style="display:block; margin:1em 0.2em;" NAME="right" VALUE="&gt;&gt;"
				   ONCLICK="moveSelectedOptions(this.form['list1'],this.form['list2'],true)">

			<INPUT TYPE="button" style="display:block; margin:1em 0.2em;" NAME="left" VALUE="&lt;&lt;"
				   ONCLICK="moveSelectedOptions(this.form['list2'],this.form['list1'],true)">
		</div>

		<div style="display:inline-block;  vertical-align:top;">
			<small><i>Villes de compétence du professionnel :</i></small><br/>
			<select name="list2[]" id="list2" MULTIPLE SIZE="20"
					style=" min-width:14em;">
			<?php 
			if(isset($liste_villes_pro)){
				foreach($liste_villes_pro as $rowl){ 
			?>
				<option value="<?= $rowl['code_insee'] ?>"><?= $rowl['nom_ville']. ' ' . $rowl['code_postal'] ?></option>
			<?php 
				}
			} ?>
			</select>
		</div>
	</div>
<?php } ?>

					</div>
				</div>

			</div>
		</fieldset>

		<div class="button">
			<input type="button" value="Retour" onclick="javascript:location.href='professionnel_liste.php'">
		<?php if (!$id_professionnel) { ?>
			<input type="reset" value="Reset">
		<?php }else{ if($pro['actif_pro'] == 0){ ?>
			<input type="submit" name="restaurer" value="Restaurer">
		<?php }else{ ?>
			<input type="submit" name="archiver" value="Désactiver">
		<?php } } ?>
			<input type="submit" name="enregistrer" value="Enregistrer">
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