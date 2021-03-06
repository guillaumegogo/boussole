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
	<script type="text/javascript" language="javascript" src="js/fix-ie.js"></script>
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
	<?php } else { ?>
	<link rel="stylesheet" type="text/css" href="css/readonlyform.css" media="screen" />
	<script type="text/javascript" language="javascript" src="js/readonlyform.js"></script>
	<?php } ?>	
</head>

<body>
<?php include('view/inc.bandeau.php'); ?>

<div class="container">
	<h2><small><a href="accueil.php">Accueil</a> > <a href="professionnel_liste.php">Liste des organismes</a> ></small> 
		<?= ($id_professionnel) ? 'Détail' : 'Création'; ?> d'un organisme </h2>

	<div class="soustitre"><?= $msg ?></div>

	<?php
	if ($pro !== null) {
	?>

	<form method="post" class="detail" onsubmit='htmleditor(); checkall();'>

		<input type="hidden" name="maj_id" value="<?= $id_professionnel ?>">
		<fieldset <?= (!$droit_ecriture) ? 'disabled="disabled"':'' ?>>
			<legend>Description de l'organisme <?= ($id_professionnel && $pro['actif_pro'] == 0) ? '<span style="color:red">(désactivé)</span>':'' ?></legend>

			<div class="deux_colonnes">
				<div class="lab">
					<label for="nom">Nom de l'organisme :</label>
					<input type="text" name="nom" required value="<?php if ($id_professionnel) {
						echo $pro['nom_pro'];
					} ?>"/>
				</div>
				<div class="lab">
					<label for="type_id">Type :</label>
					<select name="type_id">
						<option value=""></option>
					<?php foreach ($types as $type) { ?>
						<option value="<?= $type['id'] ?>" <?= ($id_professionnel && (isset($pro['type_id']) && $pro['type_id'] == $type['id'])) ? ' selected ' : '' ?> ><?= $type['libelle'] ?></option>
					<?php } ?>
					</select><?php if (isset($pro['type_pro']) && $pro['type_pro'] && !$pro['type_id']) { ?>
					<?php } ?>
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
				<?php if(isset($pro['type_pro']) && $pro['type_pro'] && !$pro['statut_id'] && !$pro['type_id']) {?>
				<span style="font-size:smaller; margin-left:10em; color:red;">(anciennement : "<?= $pro['type_pro'] ?>")</span>
				<?php }?>
				<div class="lab">
					<label for="desc">Description de l'organisme :</label>
					<div style="display:inline-block;" id="div-editeur">
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
					<?php if (isset($pro['site_web_pro']) && filter_var($pro['site_web_pro'], FILTER_VALIDATE_URL)) { ?> 
						<a href="<?= $pro['site_web_pro'] ?>" target="_blank">Vérifier</a>
					<?php } ?>
				</div>
			</div>
			<div class="deux_colonnes">
				<div class="lab">
					<label for="courriel">Courriel de gestion :</label>
					<input type="text" name="courriel" required value="<?php if ($id_professionnel) {
						echo $pro['courriel_pro'];
					} ?>"/>
				</div>
				<div class="lab">
					<label for="tel">Téléphone :</label>
					<div style="display:inline-table;">
					<input type="text" name="tel" value="<?php if ($id_professionnel) {
						echo $pro['telephone_pro'];
					} ?>"/>
					<br/><input type="checkbox" name="visibilite" value="1" <?= (isset($pro['visibilite_coordonnees']) && $pro['visibilite_coordonnees']) ? ' checked ':'' ?>> <span class="notice">Afficher ces deux dernières informations sur le site.</span>
					</div>
				</div>
				<div class="lab">
					<label for="courriel">Courriel référent Boussole :</label>
					<input type="text" name="courriel_ref" required value="<?php if ($id_professionnel) {
						echo $pro['courriel_referent_boussole'];
					} ?>"/>
				</div>
				<div class="lab">
					<label for="tel">Téléphone référent Boussole :</label>
					<div style="display:inline-table;">
					<input type="text" name="tel_ref" required value="<?php if ($id_professionnel) {
						echo $pro['telephone_referent_boussole'];
					} ?>"/>
					<br/><span class="notice" style="font-style: italic; ">Ces deux dernières informations ne sont pas affichées sur le site.</span>
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
						} ?>><?= $i ?> jour<?= ($i>1)?'s':'' ?>
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
</select>

	<span id="zone_personnalisee" class="notice" style="display:<?= $display_t ?>;" ><input type="checkbox" name="check_zone" id="check_zone" value="1" <?= (isset($pro['zone_selection_villes']) && $pro['zone_selection_villes']) ? 'checked' : '' ?> onchange="displayZone(this);" > Personnaliser la zone de compétence territoriale</span>
</div>

<div style="margin-top:1em">	
	<div class="lab" id="div_liste_villes" style="display:<?= (isset($pro['zone_selection_villes']) && $pro['zone_selection_villes']) ? 'block' : 'none' ?>">
		<?php if($droit_ecriture) { ?>
		<div style="margin-bottom:1em;">Filtre : 
			<input id="textbox"
				value="nom de ville, code postal ou département..."
				type="text" style="width:20em;"
				onFocus="javascript:this.value='';">
		</div>

		<div style="display:inline-block; vertical-align:top;">
			<small><i>Villes correspondant au filtre :</i></small><br/>
			<select id="list1" MULTIPLE SIZE="20" style=" min-width:20em;">
				<?php // include('../src/admin/villes_options_insee.inc'); //<-- si on veut laisser la possibilité de choisir n'importe quelle ville en France... ?>
				<?php 
				if (isset($villes_accessibles)){
					foreach($villes_accessibles as $row){ ?>
						<option value="<?= $row['code_insee'] ?>"><?= $row['nom_ville'].' '. $row['code_postal'] ?></option>
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
				<?php if(isset($pro['competence_geo'])) { //si pas de compétence géo, impossible de déterminer les thèmes?>
				<div class="lab">
					<label for="theme[]">Thème(s) :</label>
					<div style="display:inline-table;">
					<?php if(count($themes_proposes)){
						foreach($themes_proposes as $rowt) { ?>
						<input type="checkbox" name="theme[]" value="<?= $rowt['id_theme'] ?>" 
						<?php 
						foreach($themes_coches as &$rowc) { 
							if($rowc['id_theme'] == $rowt['id_theme']) { 
								echo ' checked '; $rowc['checked']=1; break; 
							}
							unset($rowc);
						}?>
						> <?= $rowt['libelle_theme_court'] ?>  <?= (isset($rowt['actif_theme']) && $rowt['actif_theme']==0) ? ' (inactif) ':'' ?></br>
					<?php }
					} else { ?>
						<span class="notice">Aucun thème n'est déclaré sur ce territoire.</span>
					<?php } ?>
					</div>
				</div>
				<?php } ?>
					<?php //************* le temps de redresser les données pré-v1
					if (DEBUG) { echo '<!--'; print_r($themes_proposes); print_r($themes_coches); echo '-->';  }
					$themes_a_maj = null;
					foreach($themes_coches as $rowc) { 
						if (!$rowc['checked']) $themes_a_maj .= $rowc['libelle_theme_court'].'/'.($rowc['nom_territoire']?$rowc['nom_territoire']:'national').', ';
					}
					?>
					<?= ($themes_a_maj ? '<br/><span style="font-size:smaller; margin-left:10em; color:red;">(anciennement : '.substr($themes_a_maj,0,-2).')</span></br>' : '') 
					/****************************************************************/ ?>
				<div class="lab">
					<label for="editeur">Editeur du mesurier :</label>
					<?php if(secu_check_role(ROLE_ADMIN)){ ?>
					<input type="checkbox" name="check_editeur" value="1" <?= (isset($pro['editeur']) && $pro['editeur']) ? ' checked ':'' ?> > 
					<?php }else{ ?>
					<input type="hidden" name="check_editeur" value="<?= (isset($pro['editeur']) && $pro['editeur']) ? '1':'0' ?>" > 
					<input type="checkbox" disabled readonly <?= (isset($pro['editeur']) && $pro['editeur']) ? ' checked ':'' ?> > 
					<?php } ?>
					Oui <img src="img/help.png" height="16px" title="Cet organisme peut être indiqué comme l'éditeur de mesures." >
				</div>

			</div>
		</fieldset>

		<div class="button">
			<input type="button" value="Retour" onclick="javascript:location.href='professionnel_liste.php'">
		<?php if($droit_ecriture) {
			if ($id_professionnel) {
				if(isset($pro['actif_pro']) && $pro['actif_pro'] == 0){ ?>
			<input type="submit" name="restaurer" value="Restaurer">
		<?php }else{ ?>
			<input type="submit" name="archiver" value="Désactiver">
		<?php } } ?>
			<input type="submit" name="enregistrer" value="Enregistrer">
		<?php } ?>
		</div>
		
<?php if(count($offres)+count($incoherences_themes)+count($incoherences_villes)>0){ ?>
		<fieldset>
			<legend>Offres de service de l'organisme</legend>
			<div>
<?php if(count($incoherences_themes)>0){ ?>
		<span style="color:red; font-weight: bold;">offres en incohérence : thème inactif</span><ul>
		<?php 
		foreach ($incoherences_themes as $row){ ?>
			<li><a href="offre_detail.php?id=<?=$row['id_offre']?>"><?=$row['nom_offre']?></a></li>
		<?php
		}?>
		</ul><br>
<?php } 
if(count($incoherences_villes)>0){ ?>
		<span style="color:red; font-weight: bold;">offres en incohérence : ville hors périmètre</span><ul>
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