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
	<script type="text/javascript" language="javascript" src="js/jquery.filterByText.js"></script>
	<script type="text/javascript" language="javascript" src="js/selectbox.js"></script>
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
		$(function () {
			$('#list1').filterByText($('#textbox'));
		});

		//fonction affichage listes
		function displayGeo(that)
		{
			var w = document.getElementById('liste_regions');
			var x = document.getElementById('liste_departements');
			var y = document.getElementById('liste_territoires');
			var y2 = document.getElementById('zone_personnalisee');
			
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
				y2.style.display = 'none';
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
		
		function displayZone(that)
		{
			var y2 = document.getElementById('div_liste_villes');

			if (that.checked == true)
			{
				y2.style.display = 'block';
			} else
			{
				y2.style.display = 'none';
			}
		}
		
		function checkall()
		{
			var sel = document.getElementById('list2');
			if (sel != null && sel.value == '')
			{
				for (i = 0; i < sel.options.length; i++)
				{
					sel.options[i].selected = true;
				}
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
	if ($pro !== null) {
	?>

	<form method="post" class="detail" onsubmit='checkall();'>

		<input type="hidden" name="maj_id" value="<?= $id_professionnel ?>">
		<fieldset>
			<legend>Détail du professionnel</legend>

			<div class="deux_colonnes">
				<div class="lab">
					<label for="nom">Nom du professionnel :</label>
					<input type="text" name="nom" value="<?php if ($id_professionnel) {
						echo $pro['nom_pro'];
					} ?>"/>
				</div>
				<div class="lab">
					<label for="type">Type :</label>
					<input type="text" name="type" value="<?php if ($id_professionnel) {
						echo $pro['type_pro'];
					} ?>"/>
				</div>
				<div class="lab">
					<label for="desc">Description du professionnel :</label>
					<textarea rows="5" name="desc"><?php if ($id_professionnel) {
							echo $pro['description_pro'];
						} ?></textarea>
				</div>
				<div class="lab">
					<label for="theme[]">Thème(s) :</label>
					<div style="display:inline-table;">
					<?php foreach($themes as $rowt) { ?>
						<input type="checkbox" name="theme[]" value="<?= $rowt['id_theme'] ?>" <?= (isset($rowt['id_professionnel']) && $rowt['id_professionnel']) ? ' checked ':'' ?>> <?= $rowt['libelle_theme'] ?></br>
					<?php } ?>
					</div>
				</div>
				<div class="lab">
					<label for="actif">Actif :</label>
					<input type="radio" name="actif" value="1" <?php if ($id_professionnel) {
						if ($pro['actif_pro'] == "1") {
							echo "checked";
						}
					} else echo "checked"; ?>> Oui
					<input type="radio" name="actif" value="0" <?php if ($id_professionnel) {
						if ($pro['actif_pro'] == "0") {
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
						echo $pro['adresse_pro'];
					} ?>"/>
				</div>
				<div class="lab">
					<label for="code_postal">Code postal :</label>
					<input type="text" name="commune" id="villes" value="<?php if ($id_professionnel) {
						echo $pro['ville_pro'] . " " . $pro['code_postal_pro'];
					} ?>"/>
				</div>
				<div class="lab">
					<label for="courriel">Courriel :</label>
					<input type="email" name="courriel" value="<?php if ($id_professionnel) {
						echo $pro['courriel_pro'];
					} ?>"/>
				</div>
				<div class="lab">
					<label for="tel">Téléphone :</label>
					<input type="text" name="tel" value="<?php if ($id_professionnel) {
						echo $pro['telephone_pro'];
					} ?>"/>
				</div>
				<div class="lab">
					<label for="site">Site internet :</label>
					<input type="text" name="site" value="<?php if ($id_professionnel) {
						echo $pro['site_web_pro'];
					} ?>"/>
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

<select name="competence_geo" onchange="displayGeo(this);" style="display:block; margin-bottom:0.5em;">
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
	<input type="checkbox" name="check_zone" id="check_zone" value="1" <?= ($pro['zone_selection_villes']) ? 'checked' : '' ?> onchange="displayZone(this);" > Personnaliser la zone de compétence</span>
	
	<div class="lab" id="div_liste_villes" style="display:<?= ($pro['zone_selection_villes']) ? 'block' : 'none' ?>">
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
				foreach($liste_villes_pro as $row){ 
			?>
				<option value="<?= $row['code_insee'] ?>"><?= $row['nom_ville']. ' ' . $row['code_postal'] ?></option>
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
			<input type="reset" value="Reset">
			<input type="submit" value="Enregistrer">
		</div>
		
		<fieldset>
			<legend>Offres de service du professionnel</legend>
<?php if(count($incoherences_themes)+count($incoherences_villes)>0){ ?>
		<div><span style="color:red; font-weight: bold;">offres incohérentes :</span><br>
<?php if(count($incoherences_themes)>0){ ?>
		<span style="color:red; font-weight: bold;">> thèmes</span><ul>
		<?php 
		foreach ($incoherences_themes as $row){ ?>
			<li><a href="offre_detail.php?id=<?=$row['id_offre']?>"><?=$row['nom_offre']?></a></li>
		<?php
		}?>
		</ul><br>
<?php } 
if(count($incoherences_villes)>0){ ?>
		<span style="font-weight: bold;">> villes</span><ul>
		<?php 
		foreach ($incoherences_villes as $row){ ?>
			<li><a href="offre_detail.php?id=<?=$row['id_offre']?>"><?=$row['nom_offre']?></a></li>
		<?php
		}?>
		</ul></div>
<?php }
} ?>
		
<?php if(count($offres)>0){ ?>
		<div><span style="font-weight: bold;">offres actives :</span><ul>
		<?php 
		foreach ($offres as $row){ ?>
			<li><a href="offre_detail.php?id=<?=$row['id_offre']?>"><?=$row['nom_offre']?></a></li>
		<?php
		}?>
		</ul></div>
<?php } ?>
		</fieldset>

	</form>
	<?php
	} else {
		echo "Professionnel inconnu.";
	}
	?>
</div>
</body>
</html>