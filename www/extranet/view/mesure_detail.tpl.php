<!DOCTYPE html>
<html>
<head>
	<?php include('view/inc.head.php'); ?>
	
	<link rel="stylesheet" href="../src/js/jquery-ui.min.css">
	<?php if($droit_ecriture) { ?>
	<script type="text/javascript" language="javascript" src="../src/js/external/jquery/jquery.js"></script>
	<script type="text/javascript" language="javascript" src="../src/js/jquery-ui.min.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery.filterByText.js"></script>
	<script type="text/javascript" language="javascript" src="js/selectbox.js"></script>
	<script type="text/javascript" language="javascript" src="js/datepicker-fr.js"></script>
	<script type="text/javascript" language="javascript" src="js/fix-ie.js"></script>
	<script type="text/javascript">
		//fonction autocomplete commune
		$(function () {
			var listeVilles = [<?php include('../src/villes_index.inc');?>];
			$("#villes").autocomplete({ // non utilisé encore -> à reprendre de la page index du front office
				minLength: 2,
				source: function (request, response) {
					//adaptation fichier insee
					request.term = request.term.replace("-", " ");
					request.term = request.term.replace(/^saint /gi, "St ");
					//recherche sur les premiers caractères de la ville ou sur le code postal
					var matcher1 = new RegExp("^" + $.ui.autocomplete.escapeRegex(request.term), "i");
					var matcher2 = new RegExp(" " + $.ui.autocomplete.escapeRegex(request.term) + "[0-9]*$", "i");
					response($.grep(listeVilles, function (item) {
						return (matcher1.test(item) || matcher2.test(item));
					}));
				}
			});
		});
		$(function () {
			$('#list1').filterByText($('#textbox'));
		});

		$(document).ready(function () {
			$.datepicker.setDefaults($.datepicker.regional["fr"]);
			$('.datepick').datepicker({dateFormat: "dd/mm/yy"});
		});

		/*validation formulaire*/
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

		//fonction affichage listes
		function displayGeo(that)
		{
			var w = document.getElementById('liste_regions');
			var x = document.getElementById('liste_departements');
			var y = document.getElementById('liste_territoires');
			var y2 = document.getElementById('div_liste_villes');
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
			if (y2 != null)
			{
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
			} else if (that.value == 'communes')
			{
				y2.style.display = 'block';
			}
		}

		/*montrer ou non la liste des villes*/
		function cacheVilles()
		{
			var x = document.getElementById('liste_villes');
			if (x.style.display === 'none')
			{
				x.style.display = 'block';
			} else
			{
				x.style.display = 'none';
			}
		}

		/*editeur de texte*/
		function commande(nom, argument)
		{
			if (typeof argument === 'undefined')
			{
				argument = '';
			}
			switch (nom)
			{
				case "createLink":
					argument = prompt("Quelle est l'adresse du lien ?");
					break;
				case "insertImage":
					argument = prompt("Quelle est l'adresse de l'image ?");
					break;
			}
			// Exécuter la commande
			document.execCommand(nom, false, argument);
			if (nom == "createLink")
			{
				var selection = document.getSelection();
				selection.anchorNode.parentElement.target = '_blank';
			}
		}

		function htmleditor()
		{
			document.getElementById("resultat").value = document.getElementById("editeur").innerHTML;
		}

		function choixTheme(that)
		{
			var x = that.value;
			var tab = [];
			<?php
			//on imprime en javascript le contenu du tableau des sous_thèmes
			if (isset($tab_js_soustheme)) {
				foreach ($tab_js_soustheme as $key => $value) {
					echo "tab[" . $key . "] = \"" . $value . "\";\r\n";
				}
			}
			?>
			document.getElementById("select_sous_themes").innerHTML = tab[x];
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
	<h2><small><a href="accueil.php">Accueil</a> > <a href="mesure_liste.php">Liste des mesures</a> ></small> 
		<?= ($id_mesure) ? 'Détail' : 'Création' ?> d'une mesure  </h2>

	<div class="soustitre"><?= $msg ?></div>

	<form method="post" class="detail" onsubmit='htmleditor(); checkall();'>

		<input type="hidden" name="maj_id" value="<?php echo $id_mesure; ?>">

		<fieldset <?= (!$droit_ecriture) ? 'disabled="disabled"':'' ?>>	
			<legend>Description de la mesure <?= ($id_mesure && $row['actif_mesure'] == 0) ? '<span style="color:red">(archivée)</span>':'' ?></legend>

			<div class="deux_colonnes">
				<div class="lab">
					<label for="nom">Nom de la mesure :</label>
					<input type="text" name="nom" required value="<?php if ($id_mesure) {
						echo $row['nom_mesure'];
					} ?>"/>
				</div>
				<div class="lab">
					<label for="desc">Description de la mesure :</label>
					<div style="display:inline-block;">
						<input type="button" value="G" style="font-weight: bold;" onclick="commande('bold');"/>
						<input type="button" value="I" style="font-style: italic;" onclick="commande('italic');"/>
						<input type="button" value="S" style="text-decoration: underline;" onclick="commande('underline');"/>
						<input type="button" value="Lien" onclick="commande('createLink');"/>
						<input type="button" value="Image" onclick="commande('insertImage');"/>
						<div id="editeur" contentEditable><?= ($id_mesure) ? bbcode2html($row['description_mesure']):'' ?></div>
						<input id="resultat" type="hidden" name="desc"/>
					</div>
				</div>
				<div class="lab">
					<label for="du">Dates de validité :</label>
					<input type="text" name="du" size="10" class="datepick" value="<?php if ($id_mesure) {
						echo $row['date_debut'];
					} else echo date("d/m/Y"); ?>"/>
					au <input type="text" name="au" size="10" class="datepick" value="<?php if ($id_mesure) {
						echo $row['date_fin'];
					} else echo date("d/m/Y", strtotime("+1 year")); ?>"/>
				</div>
				<?php //si création d'une mesure -> on n'affiche pas. si modification -> on affiche
				if ($id_mesure) {
					?>
					<div class="lab">
						<label for="theme">Thème :</label>
						<select id="select_themes" name="theme" onchange="choixTheme(this)">
							<?php echo $select_theme; ?>
						</select>
					</div>
					<div class="lab">
						<label for="sous_theme"><abbr title="La liste des sous-thèmes dépend du thème choisi.">Sous-thème(s)</abbr>
							:</label>
						<select id="select_sous_themes" name="sous_theme">
							<?php echo $select_sous_theme; ?>
						</select>
					</div>
				<?php } ?>
			</div>
			<div class="deux_colonnes">
				<div class="lab">
					<label for="pro">Editeur :</label>
					<?php //si création d'une mesure -> liste déroulante. si modification -> juste le nom (on ne peut plus changer).
					if (isset($id_mesure)) { ?>
						<input type="text" name="pro" value="<?= $row['nom_pro'] ?>" disabled />
					<?php } else { ?>
						<select name="pro" required ><?= $liste_pro ?></select>
					<?php } ?>
				</div>
				<?php if ($id_mesure) { //si création d'une mesure -> on n'affiche pas. si modification -> on affiche. ?>
				<div class="lab">
					<label for="adresse">Adresse :</label>
					<input type="text" name="adresse" value="<?php if ($id_mesure) {
						echo $row['adresse_mesure'];
					} ?>"/>
				</div>
				<div class="lab">
					<label for="code_postal">Code postal (& commune) :</label>
					<input type="text" name="commune" id="villes" value="<?php if ($id_mesure) {
						echo $row['ville_mesure'] . " " . $row['code_postal_mesure'];
					} ?>"/>
				</div>
				<div class="lab">
					<label for="courriel">Courriel :</label>
					<input type="text" name="courriel" value="<?php if ($id_mesure) {
						echo $row['courriel_mesure'];
					} ?>"/>
				</div>
				<div class="lab">
					<label for="tel">Téléphone :</label>
					<input type="text" name="tel" value="<?php if ($id_mesure) {
						echo $row['telephone_mesure'];
					} ?>"/>
				</div>
				<div class="lab">
					<label for="site">Site internet :</label>
					<input type="text" name="site" value="<?php if ($id_mesure) {
						echo $row['site_web_mesure'];
					} ?>"/>
				</div>
				<div class="lab">
					<label for="zone">Zone concernée :</label>
					<div style="display:inline-block;">

<select name="competence_geo" onchange="displayGeo(this);" style="display:block; margin-bottom:0.5em;">
	<option value="">A choisir</option>
<?php foreach ($competences_geo as $key => $value) { ?>
	<option value="<?= $key ?>" <?= ($id_mesure && (isset($row['competence_geo']) && $row['competence_geo'] == $key)) ? ' selected ' : '' ?> ><?= $value ?></option>
<?php } ?>
</select>
						
<?php //liste déroulante des régions
if (isset($regions)){ 
	$display_r = ($id_mesure && ($row['competence_geo'] == 'regional')) ? 'block' : 'none';
?>
<select name="liste_regions" id="liste_regions" style="display:<?= $display_r ?>" >
	<option value="">A choisir</option>
	<?php foreach ($regions as $row_r) { ?>
	<option value="<?= $row_r['id_region'] ?>" <?= ((isset($row['competence_geo']) && $row['competence_geo'] == 'regional') && ($row_r['id_region'] == $row['id_competence_geo'])) ? ' selected ' : '' ?> ><?= $row_r['nom_region'] ?></option>
	<?php } ?>
</select>
<?php } ?>

<?php //liste déroulante des départements
if (isset($departements)){ 
	$display_d = ($id_mesure && ($row['competence_geo'] == 'departemental')) ? 'block' : 'none';
?>
<select name="liste_departements" id="liste_departements" style="display:<?= $display_d ?>" >
	<option value="">A choisir</option>
	<?php foreach ($departements as $row_d) { ?>
	<option value="<?= $row_d['id_departement'] ?>" <?= ((isset($row['competence_geo']) && $row['competence_geo'] == 'departemental') && ($row_d['id_departement'] == $row['id_competence_geo'])) ? ' selected ' : '' ?> ><?= $row_d['nom_departement'] ?></option>
	<?php } ?>
</select>
<?php } ?>

<?php //liste déroulante des territoires
if (isset($territoires)){ 
	$display_t = ($id_mesure && ($row['competence_geo'] == 'territoire')) ? 'block' : 'none';
?>
<select name="liste_territoires" id="liste_territoires" style="display:<?= $display_t ?>" >
	<option value="">A choisir</option>
	<?php foreach ($territoires as $row_t) { ?>
	<option value="<?= $row_t['id_territoire'] ?>" <?= ((isset($row['competence_geo']) && $row['competence_geo'] == 'territoire') && ($row_t['id_territoire'] == $row['id_competence_geo'])) ? ' selected ' : '' ?> ><?= $row_t['nom_territoire'] ?></option>
	<?php } ?>
</select>
<?php } ?>
</div>

<div style="margin-top:1em">
	<div class="lab" id="div_liste_villes" style="display:<?= ($id_mesure && ($row['competence_geo'] == 'communes')) ? 'block' : 'none' ?>">
		<?php if($droit_ecriture) { ?>
		<div style="margin-bottom:1em;">Filtre : 
			<input id="textbox"
				value="nom de ville, code postal ou département..."
				type="text" style="width:20em;"
				onFocus="javascript:this.value='';">
		</div>

		<div style="display:inline-block; vertical-align:top;">
			<small><i>Villes correspondant au filtre :</i></small><br/>
			<select id="list1" MULTIPLE SIZE="10" style=" min-width:20em;">
				<?php include($path_from_extranet_to_web.'/src/admin/villes_options_insee.inc'); //la liste des villes de France... todo : à remplacer par $("#villes").autocomplete ?>
			</select>
		</div>

		<div style="display:inline-block; margin-top:1em; vertical-align: top;">
			<INPUT TYPE="button" style="display:block; margin:1em 0.2em;" NAME="right" VALUE="&gt;&gt;"
				   ONCLICK="moveSelectedOptions(this.form['list1'],this.form['list2'],true)">

			<INPUT TYPE="button" style="display:block; margin:1em 0.2em;" NAME="left" VALUE="&lt;&lt;"
				   ONCLICK="moveSelectedOptions(this.form['list2'],this.form['list1'],true)">
		</div>
		<?php } ?>

		<div style="display:inline-block;  vertical-align:top;">
			<small><i>Villes couvertes par la mesure :</i></small><br/>
			<select name="list2[]" id="list2" MULTIPLE SIZE="10"
					style=" min-width:14em;">
			<?php 
			if(isset($liste_villes_mesure)){
				foreach($liste_villes_mesure as $rowl){ 
			?>
				<option value="<?= $rowl['code_insee'] ?>"><?= $rowl['nom_ville']. ' ' . $rowl['code_postal'] ?></option>
			<?php 
				}
			} ?>
			</select>
		</div>
	</div>
</div>
			<?php } ?>

				</div>
			</div>
		</fieldset>

		<?php
		//si création d'une mesure -> on n'affiche pas. si modification -> on affiche.
		if (isset($row['id_theme_pere']) && $row['id_theme_pere']) {
		?>

			<fieldset <?= (!$droit_ecriture) ? 'disabled="disabled"':'' ?>>
				<legend>Liste des critères de la mesure</legend>
				<input type="hidden" name="maj_criteres" value="oui">
 				<div class="colonnes">

			<?php
			foreach ($questions as $question) {
			?>
				<div class="lab">
					<label for="critere[<?= $question['name'] ?>][]"><?= $question['libelle'] ?></label>
					<select name="critere[<?= $question['name'] ?>][]" multiple
							size="<?= min(count($reponses[$question['name']]), 10) ?>" class="criteres">
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
			<input type="button" value="Retour à la liste" onclick="javascript:location.href='mesure_liste.php'">
		<?php if($droit_ecriture) {
			if ($id_mesure) {
				if($row['actif_mesure'] == 0){ ?>
			<input type="submit" name="restaurer" value="Restaurer">
		<?php }else{ ?>
			<input type="submit" name="archiver" value="Archiver">
		<?php } } ?>
			<input type="submit" name="enregistrer" value="Enregistrer">
		<?php } ?>
		</div>
	</form>
</div>
</body>
</html>

<?php if (DEBUG) { ?>
<!--<pre><?php print_r($questions); print_r($reponses)?></pre>-->
<?php } ?>