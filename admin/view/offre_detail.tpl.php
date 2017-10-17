<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Boussole des jeunes</title>
	<link rel="icon" type="image/png" href="img/compass-icon.png"/>
	<link rel="stylesheet" href="css/jquery-ui.css">
	<link rel="stylesheet" href="css/style_backoffice.css"/>
	<?php if($droit_ecriture) { ?>
	<script type="text/javascript" language="javascript" src="js/jquery-1.12.0.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery-ui-1.12.0.js"></script>
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
					argument = prompt("Quelle est l'adresse du lien ?", "http://");
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
<h1 class="bandeau"><img src="../web/img/marianne.png" width="93px"> Administration de la boussole</h1>
<div class="statut"><?php echo $_SESSION['accroche']; ?> (<a href="index.php">déconnexion</a>)</div>

<div class="container">

	<h2><small><a href="accueil.php">Accueil</a> > <a href="offre_liste.php">Liste des offres de service</a> ></small> 
		<?= ($id_offre) ? 'Détail' : 'Création' ?> d'une offre <?= ($id_offre && $row['actif_offre'] == 0) ? '<span style="color:red">(archivée)</span>':'' ?> </h2>

	<div class="soustitre"><?= $msg ?></div>

	<form method="post" class="detail" onsubmit='htmleditor(); checkall();'>

		<input type="hidden" name="maj_id" value="<?= $id_offre; ?>">

		<fieldset <?= (!$droit_ecriture) ? 'disabled="disabled"':'' ?>>	
			<legend>Description de l'offre de service</legend>

			<div class="deux_colonnes">
				<div class="lab">
					<label for="nom">Nom de l'offre de service :</label>
					<input type="text" name="nom" required value="<?= ($id_offre) ? $row['nom_offre']:'' ?>"/>
				</div>
				<div class="lab">
					<label for="desc">Description de l'offre :</label>
					<div style="display:inline-block;" id="div-editeur">
						<input type="button" value="G" style="font-weight: bold;" onclick="commande('bold');"/>
						<input type="button" value="I" style="font-style: italic;" onclick="commande('italic');"/>
						<input type="button" value="S" style="text-decoration: underline;" onclick="commande('underline');"/>
						<input type="button" value="Lien" onclick="commande('createLink');"/>
						<input type="button" value="Image" onclick="commande('insertImage');"/>
						<div id="editeur" contentEditable ><?= ($id_offre) ? bbcode2html($row['description_offre']):'' ?></div>
						<input id="resultat" type="hidden" name="desc"/>
					</div>
				</div>
				<div class="lab">
					<label for="du">Dates de validité :</label>
					<input type="text" name="du" size="10" class="datepick" value="<?php if ($id_offre) {
						echo $row['date_debut'];
					} else echo date("d/m/Y"); ?>"/>
					au 
					<input type="text" name="au" size="10" class="datepick" value="<?php if ($id_offre) {
						echo $row['date_fin'];
					} else echo date("d/m/Y", strtotime("+1 year")); ?>"/>
				</div>
				<?php //si création d'une offre de service -> on n'affiche pas. si modification -> on affiche
				if ($id_offre) {
					?>
					<div class="lab">
						<label for="theme">Thème :</label>
						<select id="select_themes" name="theme" onchange="choixTheme(this)">
						<?php 
						if (!$row['id_theme_pere']) { ?>
							<option value="">A choisir</option>
						<?php }
						foreach($themes as $rowt){
							if (!isset($rowt['id_theme_pere'])) {
								if ($rowt['id_professionnel'] == $row['id_professionnel']) { ?>
							<option value="<?= $rowt['id_theme'] ?>" <?= ($rowt['id_theme'] == $row['id_theme_pere']) ? ' selected ':'' ?>> <?= $rowt['libelle_theme'] ?></option>
						<?php			
								}
							}
						}
						?>
						</select>
					</div>
					<div class="lab">
						<label for="sous_theme"><abbr title="La liste des sous-thèmes dépend du thème choisi.">Sous-thème</abbr>
							:</label>
						<select id="select_sous_themes" name="sous_theme">
						<?php 
						if (!$row['id_sous_theme']) { ?>
							<option value="">A choisir</option>
						<?php }
						foreach($themes as $rowt){
							if (isset($rowt['id_theme_pere'])) {
								if ($rowt['id_theme_pere'] == $row['id_theme_pere']) { ?>
							<option value="<?= $rowt['id_theme'] ?>" <?= ($rowt['id_theme'] == $row['id_sous_theme']) ? ' selected ':'' ?>> <?= $rowt['libelle_theme'] ?></option>
						<?php
								}
							}
						}
						?>
						</select>
					</div>
				<?php } ?>
			</div>
			<div class="deux_colonnes">
				<div class="lab">
					<label for="pro">Professionnel :</label>
					<?php //si création d'une offre de service -> liste déroulante. si modification -> juste le nom (on ne peut plus changer).
					if (isset($id_offre)) { ?>
						<input type="text" name="pro" value="<?= $row['nom_pro'] ?>" readonly />
					<?php } else { ?>
						<select name="pro" required ><?= $liste_pro ?></select>
					<?php } ?>
				</div>
				<?php if ($id_offre) { //si création d'une offre de service -> on n'affiche pas. si modification -> on affiche. ?>
				<div class="lab">
					<label for="adresse">Adresse :</label>
					<input type="text" name="adresse" value="<?php if ($id_offre) {
						echo $row['adresse_offre'];
					} ?>"/>
				</div>
				<div class="lab">
					<label for="code_postal">Code postal (& commune) :</label>
					<input type="text" name="commune" id="villes" value="<?php if ($id_offre) {
						echo $row['ville_offre'] . " " . $row['code_postal_offre'];
					} ?>"/>
				</div>
				<div class="lab">
					<label for="courriel">Courriel :</label>
					<input type="text" name="courriel" value="<?php if ($id_offre) {
						echo $row['courriel_offre'];
					} ?>"/>
				</div>
				<div class="lab">
					<label for="tel">Téléphone :</label>
					<input type="text" name="tel" value="<?php if ($id_offre) {
						echo $row['telephone_offre'];
					} ?>"/>
				</div>
				<div class="lab">
					<label for="site">Site internet :</label>
					<input type="text" name="site" value="<?php if ($id_offre) {
						echo $row['site_web_offre'];
					} ?>"/>
				</div>
				<div class="lab">
					<label for="delai">Délai garanti de réponse :</label>
					<select name="delai" >
					<?php for($i = 1; $i <= 7 ;$i++) { ?>
						<option value="<?= $i ?>" <?= ($row['delai_offre'] == $i) ? 'selected':'' ?>><?= $i ?> jour<?= ($i>1) ? 's':'' ?>
						</option>
					<?php } ?>
					</select>
				</div>
				<div class="lab">
					<label for="zone">Zone concernée :</label>
					<div style="display:inline-block;">
						<input type="radio" name="zone" value="0" <?= ($id_offre && $row['zone_offre']) ? '':'checked' ?> 
							onchange="document.getElementById('div_liste_villes').style.display = 'none';"> 
							<?= $geo ?> <small>(compétence géographique du professionnel)</small><br/>
						<input type="radio" name="zone" value="1" <?= ($id_offre && $row['zone_offre']) ? 'checked':'' ?> 
							onchange="document.getElementById('div_liste_villes').style.display = 'block';"> 
							Sélection de villes 
					</div>
				</div>
				<div class="lab" id="div_liste_villes" style="display:<?= ($id_offre && $row['zone_offre']) ? "block" : "none" ?>">
				<?php if($droit_ecriture) { ?>
					<div style="margin-bottom:1em;">Filtre <abbr title="La liste des villes proposées dépend de la zone de compétence géographique du professionnel">&#9888;</abbr> : 
						<input id="textbox"
							value="nom de ville, code postal ou département..."
							type="text" style="width:20em;"
							onFocus="javascript:this.value='';">
					</div>

					<div style="display:inline-block; vertical-align:top;">
						<small><i>Villes correspondant au filtre :</i></small><br/>
						<select id="list1" MULTIPLE SIZE="10" style="width:18em;">
					<?php 
					if(isset($villes)){
						foreach($villes as $rowv){ 
					?>
						<option value="<?= $rowv['code_insee'] ?>"><?= $rowv['nom_ville']. ' ' . $rowv['code_postal'] ?></option>
					<?php 
						} 
					} ?>
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
						<small><i>Zone couverte par l'offre :</i></small><br/>
						<select name="list2[]" id="list2" MULTIPLE SIZE="10"
								style="width:18em;">
					<?php 
					if(isset($willes)){
						foreach($willes as $roww){ 
					?>
						<option value="<?= $roww['code_insee'] ?>"><?= $roww['nom_ville']. ' ' . $roww['code_postal'] ?></option>
					<?php 
						}
					} ?>
						</select>
					</div>
				</div>
			</div>
			<?php } ?>
		</fieldset>

		<?php
		//si création d'une offre de service -> on n'affiche pas. si modification -> on affiche.
		if (isset($row['id_theme_pere']) && $row['id_theme_pere']) {
		?>

			<fieldset <?= (!$droit_ecriture) ? 'disabled="disabled"':'' ?>>
				<legend>Liste des critères de l'offre de service</legend>
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
			<input type="button" value="Retour à la liste" onclick="javascript:location.href='offre_liste.php'">
		<?php if($droit_ecriture) {
			if ($id_offre) {
				if($row['actif_offre'] == 0){ ?>
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

<?php
if (DEBUG) { 
	$timestamp_fin = microtime(true);
	$difference_ms = $timestamp_fin - $timestamp_debut;
	echo '<pre>Exécution du script : ' . substr($difference_ms,0,6) . ' secondes.</pre>';
	echo '<!--'; print_r($_POST); echo '-->';
}
?>