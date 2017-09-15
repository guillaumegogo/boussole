<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Boussole des jeunes</title>
	<link rel="icon" type="image/png" href="img/compass-icon.png"/>
	<link rel="stylesheet" href="css/jquery-ui.css">
	<link rel="stylesheet" href="css/style_backoffice.css"/>
	<script type="text/javascript" language="javascript" src="js/jquery-1.12.0.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery-ui-1.12.0.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery.filterByText.js"></script>
	<script type="text/javascript" language="javascript" src="js/selectbox.js"></script>
	<script type="text/javascript" language="javascript" src="js/datepicker-fr.js"></script>
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
</head>

<body>
<h1 class="bandeau">Administration de la boussole des jeunes</h1>
<div class="statut"><?php echo $_SESSION['accroche']; ?> (<a href="index.php">déconnexion</a>)</div>

<div class="container">
	<h2><small><a href="accueil.php">Accueil</a> > <a href="mesure_liste.php">Liste des mesures</a> ></small> 
		<?= ($id_mesure) ? 'Détail' : 'Création' ?> d'une mesure  <?= ($id_mesure && $row['actif_mesure'] == 0) ? '<span style="color:red">(archivée)</span>':'' ?></h2>

	<div class="soustitre"><?= $msg ?></div>

	<form method="post" class="detail" onsubmit='htmleditor(); checkall();'>

		<input type="hidden" name="maj_id" value="<?php echo $id_mesure; ?>">

		<fieldset>	
			<legend>Description de la mesure</legend>

			<div class="deux_colonnes">
				<div class="lab">
					<label for="nom">Nom de la mesure :</label>
					<input type="text" name="nom" value="<?php if ($id_mesure) {
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
					if (isset($id_mesure)) {
						echo "<input type=\"text\" name=\"pro\" value=\"" . $row['nom_pro'] . "\" disabled/>";
					} else {
						echo "<select name=\"pro\">" . $liste_pro . "</select>";
					}
					?>
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
					<input type="email" name="courriel" value="<?php if ($id_mesure) {
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
						<input type="radio" name="zone" value="0" <?= ($id_mesure && $row['zone_mesure']) ? '':'checked' ?> onchange="document.getElementById('div_liste_villes').style.display = 'none';"> Compétence
						géographique du pro <?php echo "<small>(" . $geo . ")</small>"; ?><br/>
						<input type="radio" name="zone" value="1" <?= ($id_mesure && $row['zone_mesure']) ? 'checked':'' ?> onchange="document.getElementById('div_liste_villes').style.display = 'block';"> Sélection
						de villes <abbr title="Liste des villes de la zone de compétence géographique du professionnel">&#9888;</abbr>
					</div>
				</div>
				<div class="lab" id="div_liste_villes" style="display:<?= ($id_mesure && $row['zone_mesure']) ? "block" : "none" ?>">
					<div style="margin-bottom:1em;">Filtre : 
						<input id="textbox"
							value="nom de ville, code postal ou département..."
							type="text" style="width:20em;"
							onFocus="javascript:this.value='';">
					</div>

					<div style="display:inline-block; vertical-align:top;">
						<small><i>Villes correspondant au filtre :</i></small><br/>
						<select id="list1" MULTIPLE SIZE="10" style=" min-width:14em;">
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

					<div style="display:inline-block;  vertical-align:top;">
						<small><i>Zone couverte par la mesure :</i></small><br/>
						<select name="list2[]" id="list2" MULTIPLE SIZE="10"
								style=" min-width:14em;">
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
		//si création d'une mesure -> on n'affiche pas. si modification -> on affiche.
		if (isset($row['id_theme_pere']) && $row['id_theme_pere']) {
		?>

			<fieldset>
				<legend>Liste des critères de la mesure</legend>
				<input type="hidden" name="maj_criteres" value="oui"> <!--est-ce bien utile ?-->
				
				<center>A DEFINIR</center>
				
			</fieldset>

		<?php
		}
		?>

		<div class="button">
			<input type="button" value="Retour à la liste" onclick="javascript:location.href='mesure_liste.php'">
		<?php if (!$id_mesure) {	?>
			<input type="reset" value="Reset">
		<?php }else{ if($row['actif_mesure'] == 0){ ?>
			<input type="submit" name="restaurer" value="Restaurer">
		<?php }else{ ?>
			<input type="submit" name="archiver" value="Archiver">
		<?php } } ?>
			<input type="submit" name="enregistrer" value="Enregistrer">
		</div>
	</form>
</div>
</body>
</html>