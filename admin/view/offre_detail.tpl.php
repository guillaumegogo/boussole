<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Boussole des jeunes</title>
    <link rel="icon" type="image/png" href="img/compass-icon.png" />
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
	<link rel="stylesheet" href="css/style_backoffice.css" />
	<script type="text/javascript" language="javascript" src="js/jquery-1.12.0.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery-ui-1.12.0.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery.filterByText.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery-ui-1.12.0.js"></script>
	<script type="text/javascript" language="javascript" src="js/datepicker-fr.js"></script>
	<script type="text/javascript" language="javascript" src="js/selectbox.js"></script>
	<script type="text/javascript">
//******** jquery
//fonction autocomplete commune
$( function() {
	var listeVilles = [<?php include('inc/villes_index.inc');?>];
	$( "#villes" ).autocomplete({
		minLength: 2,
		source: function( request, response ) {
			//adaptation fichier insee
			request.term = request.term.replace("-", " ");
			request.term = request.term.replace(/^saint /gi, "St ");
			//recherche sur les premiers caractères de la ville ou sur le code postal
			var matcher1 = new RegExp( "^" + $.ui.autocomplete.escapeRegex( request.term ), "i" );
			var matcher2 = new RegExp( " " + $.ui.autocomplete.escapeRegex( request.term ) + "[0-9]*$", "i" );
			response( $.grep( listeVilles, function( item ){
				return (matcher1.test( item ) || matcher2.test( item ));
			}) );
		}
	});
});
$(function() {
	$('#list1').filterByText($('#textbox'));
});
function checkall(){
	var sel= document.getElementById('list2'); 
	for(i=0;i<sel.options.length;i++){
		sel.options[i].selected=true;
	}
}
$(document).ready(function() {     
	$.datepicker.setDefaults($.datepicker.regional["fr"]);
	$('.datepick').datepicker({ dateFormat: "dd/mm/yy"});
});

//******* javascript
/*montrer ou non la liste des villes*/
function cacheVilles() {
    var x = document.getElementById('liste_villes');
    if (x.style.display === 'none') {
        x.style.display = 'block';
    } else {
        x.style.display = 'none';
    }
}
/*editeur de texte*/
function commande(nom, argument) {
  if (typeof argument === 'undefined') {
    argument = '';
  }
  switch (nom) {
    case "createLink":
      argument = prompt("Quelle est l'adresse du lien ?");
      break;
    case "insertImage":
      argument = prompt("Quelle est l'adresse de l'image ?");
      break;
  }
  // Exécuter la commande
  document.execCommand(nom, false, argument);
  if (nom == "createLink") {
	var selection = document.getSelection();
	selection.anchorNode.parentElement.target = '_blank';
  }
}

function htmleditor() {
  document.getElementById("resultat").value = document.getElementById("editeur").innerHTML;
}

function choixTheme(that){
  var x = that.value;
  var tab = [];
<?php
if (isset($tab_select_soustheme)){
	foreach ($tab_select_soustheme as $key => $value) {
		echo "tab[".$key ."] = \"".$value."\";\r\n";
	}
}
?>
  document.getElementById("select_sous_themes").innerHTML = tab[x];
}
</script>
</head>

<body>
<h1 class="bandeau"><a href="accueil.php">Administration de la boussole des jeunes</a></h1>
<div class="statut"><?php echo $_SESSION['accroche']; ?> (<a href="index.php">déconnexion</a>)</div> 

<div class="container">
<h2><?php if ($id_offre) { echo "Modification"; } else { echo "Ajout"; } ?> d'une offre de service</h2>
<?php echo $msg; ?>

<form method="post" class="detail" onsubmit='checkall(); htmleditor()'>

<input type="hidden" name="maj_id" value="<?php echo $id_offre; ?>">

<fieldset>
	<legend>Détail de l'offre de service</legend>

    <div class="deux_colonnes">
		<div class="lab">
			<label for="nom">Nom de l'offre de service :</label>
			<input type="text" name="nom" value="<?php if ($id_offre) { echo $row['nom_offre']; } ?>"/>
		</div>
		<div class="lab">
			<label for="desc">Description de l'offre :</label>
			<div style="display:inline-block;">
				<input type="button" value="G" style="font-weight: bold;" onclick="commande('bold');" />
				<input type="button" value="I" style="font-style: italic;" onclick="commande('italic');" />
				<input type="button" value="S" style="text-decoration: underline;" onclick="commande('underline');" />
				<input type="button" value="Lien" onclick="commande('createLink');" />
				<input type="button" value="Image" onclick="commande('insertImage');" />
				<div id="editeur" contentEditable><?php if ($id_offre) { echo $row['description_offre']; } ?></div>
				<input id="resultat" type="hidden" name="desc"/>
			</div>
		</div>
		<div class="lab">
			<label for="du">Dates de validité :</label>
			<input type="text" name="du" size="10" class="datepick" value="<?php if ($id_offre) { echo $row['date_debut']; } else echo date("d/m/Y");?>" />
			au <input type="text" name="au" size="10" class="datepick" value="<?php if ($id_offre) { echo $row['date_fin']; } else echo date("d/m/Y", strtotime("+1 year"));?>"/> 
		</div>
<?php //si création d'une offre de service -> on n'affiche pas. si modification -> on affiche
if ($id_offre) { 
?>
		<div class="lab">  
			<label for="theme">Thème :</label>
			<select id="select_themes" name="theme" onchange="choixTheme(this)">
				<?php echo $select_theme; ?>
			</select> 
		</div>
		<div class="lab">
			<label for="sous_theme"><abbr title="La liste des sous-thèmes dépend du thème choisi.">Sous-thème(s)</abbr> :</label>
			<select id="select_sous_themes" name="sous_theme">
				<?php echo $select_sous_theme; ?>
			</select> 
		</div>
<?php } ?>
	</div>
    <div class="deux_colonnes">
		<div class="lab">
			<label for="pro">Professionnel :</label>
<?php //si création d'une offre de service -> liste déroulante. si modification -> juste le nom (on ne peut plus changer).
if(isset($id_offre)) { 
			echo"<input type=\"text\" name=\"pro\" value=\"".$row['nom_pro']."\" disabled/>";
} else {
			echo"<select name=\"pro\">".$liste_pro."</select>";
} 
?>
		</div>
<?php if ($id_offre) { //si création d'une offre de service -> on n'affiche pas. si modification -> on affiche. ?>
		<div class="lab">
			<label for="adresse">Adresse :</label>
			<input type="text" name="adresse" value="<?php if ($id_offre) { echo $row['adresse_offre']; } ?>" />
		</div>
		<div class="lab">
			<label for="code_postal">Commune :</label>
			<input type="text" name="commune" id="villes" value="<?php if ($id_offre) { echo $row['ville_offre']." ".$row['code_postal_offre']; } ?>" /> 
		</div>
<!--***************** cp et ville à remplacer par commune, au dessus
		<div class="lab">
			<label for="code_postal">Code postal :</label>
			<input type="text" name="code_postal" value="<?php if ($id_offre) { echo $row['code_postal_offre']; } ?>" maxlength="5" style="width:5em"/> <abbr title="Le code postal devra permettre de générer une liste de villes possibles, pour une meilleure géolocalisation.">&#9888;</abbr> <!--<input type="submit" name="villes_code_postal" value="Lister les villes" />
		</div>
		<div class="lab">
			<label for="ville">Ville :</label>
			<input type="text" name="ville" value="<?php if ($id_offre) { echo $row['ville_offre']; } ?>" />
		</div> -->
		<div class="lab">
			<label for="courriel">Courriel :</label>
			<input type="email" name="courriel" value="<?php if ($id_offre) { echo $row['courriel_offre']; } ?>"/>
		</div>
		<div class="lab">
			<label for="tel">Téléphone :</label>
			<input type="text" name="tel"  value="<?php if ($id_offre) { echo $row['telephone_offre']; } ?>" />
		</div>
		<div class="lab">
			<label for="site">Site internet :</label>
			<input type="text" name="site"  value="<?php if ($id_offre) { echo $row['site_web_offre']; } ?>" />
		</div>
		<div class="lab">
			<label for="delai">Délai garanti de réponse :</label>
			<select name="delai">
				<option value="2" <?php if ($id_offre) {if ($row['delai_offre']=="2") { echo "selected"; }} ?>>2 jours</option>
				<option value="3" <?php if ($id_offre) {if ($row['delai_offre']=="3") { echo "selected"; }} ?>>3 jours</option>
				<option value="5" <?php if ($id_offre) {if ($row['delai_offre']=="5") { echo "selected"; }} ?>>5 jours</option>
				<option value="7" <?php if ($id_offre) {if ($row['delai_offre']=="7") { echo "selected"; }} ?>>7 jours</option>
			</select> 
		</div>
		<div class="lab">
			<label for="zone">Zone concernée :</label>
			<div style="display:inline-block;">
				<input type="radio" name="zone" value="0" <?php if ($id_offre) {if (!$row['zone_selection_villes']) { echo "checked"; }} else { echo "checked"; } ?> onchange="document.getElementById('div_liste_villes').style.display = 'none';"> Compétence géographique du pro <?php echo "<small>(".$geo.")</small>"; ?><br/>
				<input type="radio" name="zone" value="1" <?php if ($id_offre) {if ($row['zone_selection_villes']) { echo "checked"; }} ?>  onchange="document.getElementById('div_liste_villes').style.display = 'block';" > Sélection de villes <abbr title="Liste des villes de la zone de compétence géographique du professionnel">&#9888;</abbr>
			</div>
		</div>
		<div class="lab" id="div_liste_villes"  style="display:<?php if ($id_offre) {if ($row['zone_selection_villes']) { echo "block"; } else { echo "none"; }} else { echo "none"; } ?>">
			<!--<label for="villes">Villes  :</label>
			<select name="villes[]" multiple size=10 >
			<?php 
			//********* liste des villes liées au pro, le cas échéant
			/*if ($row['zone_selection_villes']) { 
			$sql = "SELECT `bsl__ville`.`code_insee`, `bsl__ville`.`code_postal`, `bsl__ville`.`nom_ville` 
				FROM `bsl__ville` 
				JOIN `bsl_professionnel_villes` ON `bsl_professionnel_villes`.`code_insee`=`bsl__ville`.`code_insee` 
				WHERE `id_professionnel`=".$row['id_professionnel']." 
				ORDER BY nom_ville";
			$result = mysqli_query($conn, $sql);
			if (mysqli_num_rows($result) > 0) {
				while($row2 = mysqli_fetch_assoc($result)) {
					$liste2 .= "<option value=\"".$row2['code_insee']."\">".$row2['nom_ville']." ".$row2['code_postal']. "</option>";
				}
			}
			*/
			//en attendant la gestion des territoires
			//include('tmp_liste_villes_territoire_reims.inc'); 
			?> -->
			
<!--********************** compétence villes => désactivé -->
			<!--<div id="liste_villes" style="width:100%; vertical-align: middle; height: 100%; display:<?php if ($id_professionnel) {if ($row['competence_geo']=="villes") { echo "block"; } else { echo "none"; }} else { echo "none"; } ?>;">-->
			<div style="margin-bottom:1em;">Filtre : <input id="textbox" value="nom de ville, code postal ou département..." type="text" style="width:20em;" onFocus="javascript:this.value='';"></div>
			
			<div style="display:inline-block; vertical-align:top;">		
				<select id="list1" MULTIPLE SIZE="10" style=" min-width:14em;">
					<?php echo $liste_villes_pro ; ?>
				</select>
			</div>

			<div style="display:inline-block; margin-top:1em; vertical-align: top;">
				<INPUT TYPE="button" style="display:block; margin:1em 0.2em;" NAME="right" VALUE="&gt;&gt;" ONCLICK="moveSelectedOptions(this.form['list1'],this.form['list2'],true)">
				
				<INPUT TYPE="button" style="display:block; margin:1em 0.2em;" NAME="left" VALUE="&lt;&lt;" ONCLICK="moveSelectedOptions(this.form['list2'],this.form['list1'],true)">
			</div>

			<div style="display:inline-block;  vertical-align:top;">
				<select name="list2[]" id="list2" MULTIPLE SIZE="10" style=" min-width:14em;"><?php echo $liste2;?></select>
			</div>
		</div>
		<div class="lab">
			<label for="actif">Offre active :</label>
			<input type="radio" name="actif" value="1" <?php if ($id_offre) {if ($row['actif_offre']=="1") { echo "checked"; }} else echo "checked";  ?>> Oui <input type="radio" name="actif" value="0" <?php if ($id_offre) {if ($row['actif_offre']=="0") { echo "checked"; }} ?>> Non
		</div>
	</div>
<?php } ?>
</fieldset>

<?php 
//si création d'une offre de service -> on n'affiche pas. si modification -> on affiche. 
if ($id_offre) { 
	// si theme de l'offre = emploi
	if ($row['id_theme_pere']=="1") { 
?>
<fieldset>
	<legend>Liste des critères de l'offre de service</legend>
	<input type="hidden" name="maj_criteres" value="emploi">
	<div class="deux_colonnes">
		<div class="lab">
			<label for="age_min">Age :</label>
			de <select name="age_min" class="age">
<?php
$t = null;
for ($i = 16; $i <= 30; $i++) {
	$s = ""; 
	if (isset($criteres["age_min"][$i])) { $s = " selected "; }
    $t .= "<option value=\"".$i."\"".$s.">".$i."</option>";
}
echo $t;
?>
			</select>  à 
			<select name="age_max" class="age">
<?php
$t = null;
for ($i = 16; $i <= 30; $i++) {
	$s = ""; 
	if (isset($criteres["age_max"][$i])) { $s = " selected "; }
    $t .= "<option value=\"".$i."\"".$s.">".$i."</option>";
}
echo $t;
?>
			</select>
		</div>
		<div class="lab">
			<label for="sexe[]">Je suis :</label>
			<select name="sexe[]" multiple size="2">
				<option value="h" <?php if (isset($criteres["sexe"]["h"])) { echo "selected"; } ?>>un homme</option>
				<option value="f" <?php if (isset($criteres["sexe"]["f"])) { echo "selected"; } ?>>une femme</option> 
			</select>
		</div>
		<div class="lab">
			<label for="jesais[]">Je sais ce que je veux faire :</label>
			<select name="jesais[]" multiple size="2">
				<option value="oui" <?php if (isset($criteres["jesais"]["oui"])) { echo "selected"; } ?>>Oui</option>
				<option value="non" <?php if (isset($criteres["jesais"]["non"])) { echo "selected"; } ?>>Non</option> 
			</select>
		</div>
		<div class="lab">
			<label for="situation[]">Situation :</label>
			<select name="situation[]" multiple  size="10">
				<option value="sans activite"  <?php if (isset($criteres["situation"]["sans activite"])) { echo "selected"; } ?>>Sans activité</option>
				<option value="collegien"  <?php if (isset($criteres["situation"]["collegien"])) { echo "selected"; } ?>>collégien</option>
				<option value="lyceen"  <?php if (isset($criteres["situation"]["lyceen"])) { echo "selected"; } ?>>Lycéen</option>
				<option value="etudiant"  <?php if (isset($criteres["situation"]["etudiant"])) { echo "selected"; } ?>>Etudiant</option>
				<option value="stagiaire form pro"  <?php if (isset($criteres["situation"]["stagiaire form pro"])) { echo "selected"; } ?>>Stagiaire form pro</option>
				<option value="apprenti"  <?php if (isset($criteres["situation"]["apprenti"])) { echo "selected"; } ?>>Apprenti</option>
				<option value="salarie"  <?php if (isset($criteres["situation"]["salarie"])) { echo "selected"; } ?>>Salarié</option>
				<option value="independant"  <?php if (isset($criteres["situation"]["independant"])) { echo "selected"; } ?>>Indépendant</option>
				<option value="auto entrepreneur"  <?php if (isset($criteres["situation"]["auto entrepreneur"])) { echo "selected"; } ?>>Auto entrepreneur</option>
				<option value="autre"  <?php if (isset($criteres["situation"]["autre"])) { echo "selected"; } ?>>Autre</option>
			</select>
		</div>
		<div class="lab">
			<label for="nationalite[]">Nationalité :</label>
			<select name="nationalite[]" multiple size="3">
				<option value="francais" <?php if (isset($criteres["nationalite"]["francais"])) { echo "selected"; } ?>>Français</option>
				<option value="europeen" <?php if (isset($criteres["nationalite"]["europeen"])) { echo "selected"; } ?>>Européen</option> 
				<option value="hors-ue" <?php if (isset($criteres["nationalite"]["hors-ue"])) { echo "selected"; } ?>>Hors-UE</option> 
			</select>
		</div>
		<div class="lab">
			<label for="permis[]">Permis de conduire :</label>
			<select name="permis[]" multiple size="2">
				<option value="oui" <?php if (isset($criteres["permis"]["oui"])) { echo "selected"; } ?>>Oui</option>
				<option value="non" <?php if (isset($criteres["permis"]["non"])) { echo "selected"; } ?>>Non</option> 
			</select>
		</div>
		<div class="lab">
			<label for="handicap[]">Handicap :</label>
			<select name="handicap[]" multiple size="2">
				<option value="oui" <?php if (isset($criteres["handicap"]["oui"])) { echo "selected"; } ?>>Oui</option>
				<option value="non" <?php if (isset($criteres["handicap"]["non"])) { echo "selected"; } ?>>Non</option> 
			</select>
		</div>
		<div class="lab">
			<label for="experience[]">Expérience en emploi :</label>
			<select name="experience[]" multiple size="2">
				<option value="oui" <?php if (isset($criteres["experience"]["oui"])) { echo "selected"; } ?>>Oui</option>
				<option value="non" <?php if (isset($criteres["experience"]["non"])) { echo "selected"; } ?>>Non</option> 
			</select>
		</div>
		<div class="lab">
			<label for="type_emploi[]">Type d'emploi :</label>
			<select name="type_emploi[]" multiple size="4">
				<option value="ete" <?php if (isset($criteres["type_emploi"]["ete"])) { echo "selected"; } ?>>Job d'été ou saisonnier</option>
				<option value="etudiant" <?php if (isset($criteres["type_emploi"]["etudiant"])) { echo "selected"; } ?>>Job étudiant</option>
				<option value="durable" <?php if (isset($criteres["type_emploi"]["durable"])) { echo "selected"; } ?>>Emploi "durable"</option>
				<option value="formation" <?php if (isset($criteres["type_emploi"]["formation"])) { echo "selected"; } ?>>Emploi avec une formation</option>
			</select>
		</div>
		<div class="lab">
			<label for="temps_plein[]">Temps plein :</label>
			<select name="temps_plein[]" multiple size="2">
				<option value="oui" <?php if (isset($criteres["temps_plein"]["oui"])) { echo "selected"; } ?>>Oui</option>
				<option value="non" <?php if (isset($criteres["temps_plein"]["non"])) { echo "selected"; } ?>>Non, à temps partiel</option>
			</select>
		</div>
	</div>
	<div class="deux_colonnes">
		<div class="lab">
			<label for="etudes[]">Dernières études :</label>
			<select name="etudes[]" multiple size="6">
				<option value="aucune" <?php if (isset($criteres["etudes"]["aucune"])) { echo "selected"; } ?>>Aucune</option>
				<option value="college" <?php if (isset($criteres["etudes"]["college"])) { echo "selected"; } ?>>Collège</option>
				<option value="lycee" <?php if (isset($criteres["etudes"]["lycee"])) { echo "selected"; } ?>>Lycée</option>
				<option value="etudes superieures" <?php if (isset($criteres["etudes"]["etudes superieures"])) { echo "selected"; } ?>>Etudes supérieures</option>
				<option value="aprentissage" <?php if (isset($criteres["etudes"]["aprentissage"])) { echo "selected"; } ?>>Apprentissage</option>
				<option value="formation professionnelle" <?php if (isset($criteres["etudes"]["formation professionnelle"])) { echo "selected"; } ?>>Formation professionnelle</option>
				<option value="etranger" <?php if (isset($criteres["etudes"]["etranger"])) { echo "selected"; } ?>>Etudes à l'étranger</option>
			</select>
		</div>
		<div class="lab">
			<label for="diplome[]">Diplôme :</label>
			<select name="diplome[]" multiple size="10">
				<option value="aucun" <?php if (isset($criteres["diplome"]["aucun"])) { echo "selected"; } ?>>Aucun</option>
				<option value="brevet" <?php if (isset($criteres["diplome"]["brevet"])) { echo "selected"; } ?>>Brevet des collèges</option>
				<option value="cap" <?php if (isset($criteres["diplome"]["cap"])) { echo "selected"; } ?>>CAP</option>
				<option value="bep" <?php if (isset($criteres["diplome"]["bep"])) { echo "selected"; } ?>>BEP</option>
				<option value="bac general" <?php if (isset($criteres["diplome"]["bac general"])) { echo "selected"; } ?>>Baccalauréat général</option>
				<option value="bac pro" <?php if (isset($criteres["diplome"]["bac pro"])) { echo "selected"; } ?>>Baccalauréat professionnel</option>
				<option value="bts dut" <?php if (isset($criteres["diplome"]["bts dut"])) { echo "selected"; } ?>>BTS / DUT</option>
				<option value="licence" <?php if (isset($criteres["diplome"]["licence"])) { echo "selected"; } ?>>Licence</option>
				<option value="master" <?php if (isset($criteres["diplome"]["master"])) { echo "selected"; } ?>>Master</option>
				<option value="doctorat" <?php if (isset($criteres["diplome"]["doctorat"])) { echo "selected"; } ?>>Doctorat</option>
				<option value="etranger" <?php if (isset($criteres["diplome"]["etranger"])) { echo "selected"; } ?>>Diplôme étranger</option>
			</select>
		</div>
		<div class="lab">
			<label for="secteur[]">Secteur d'activité :</label>
			<select name="secteur[]" multiple size="12">
				<option value="Agriculture" <?php if (isset($criteres["secteur"]["Agriculture"])) { echo "selected"; } ?>>Agriculture</option>
				<option value="Agroalimentaire - Alimentation" <?php if (isset($criteres["secteur"]["Agroalimentaire - Alimentation"])) { echo "selected"; } ?>>Agroalimentaire - Alimentation</option>
				<option value="Animaux" <?php if (isset($criteres["secteur"]["Animaux"])) { echo "selected"; } ?>>Animaux</option>
				<option value="Architecture - Aménagement intérieur" <?php if (isset($criteres["secteur"]["Architecture - Aménagement intérieur"])) { echo "selected"; } ?>>Architecture - Aménagement intérieur</option>
				<option value="Artisanat - Métiers d'art" <?php if (isset($criteres["secteur"]["Artisanat - Métiers d'art"])) { echo "selected"; } ?>>Artisanat - Métiers d'art</option>
				<option value="Banque - Finance - Assurance" <?php if (isset($criteres["secteur"]["Banque - Finance - Assurance"])) { echo "selected"; } ?>>Banque - Finance - Assurance</option>
				<option value="Bâtiment - Travaux publics" <?php if (isset($criteres["secteur"]["Bâtiment - Travaux publics"])) { echo "selected"; } ?>>Bâtiment - Travaux publics</option>
				<option value="Biologie - Chimie" <?php if (isset($criteres["secteur"]["Biologie - Chimie"])) { echo "selected"; } ?>>Biologie - Chimie</option>
				<option value="Commerce - Immobilier" <?php if (isset($criteres["secteur"]["Commerce - Immobilier"])) { echo "selected"; } ?>>Commerce - Immobilier</option>
				<option value="Communication - Information" <?php if (isset($criteres["secteur"]["Communication - Information"])) { echo "selected"; } ?>>Communication - Information</option>
				<option value="Culture - Spectacle" <?php if (isset($criteres["secteur"]["Culture - Spectacle"])) { echo "selected"; } ?>>Culture - Spectacle</option>
				<option value="Défense - Sécurité - Secours" <?php if (isset($criteres["secteur"]["Défense - Sécurité - Secours"])) { echo "selected"; } ?>>Défense - Sécurité - Secours</option>
				<option value="Droit" <?php if (isset($criteres["secteur"]["Droit"])) { echo "selected"; } ?>>Droit</option>
				<option value="Edition - Imprimerie - Livre" <?php if (isset($criteres["secteur"]["Edition - Imprimerie - Livre"])) { echo "selected"; } ?>>Edition - Imprimerie - Livre</option>
				<option value="Electronique - Informatique" <?php if (isset($criteres["secteur"]["Electronique - Informatique"])) { echo "selected"; } ?>>Electronique - Informatique</option>
				<option value="Enseignement - Formation" <?php if (isset($criteres["secteur"]["Enseignement - Formation"])) { echo "selected"; } ?>>Enseignement - Formation</option>
				<option value="Environnement - Nature - Nettoyage" <?php if (isset($criteres["secteur"]["Environnement - Nature - Nettoyage"])) { echo "selected"; } ?>>Environnement - Nature - Nettoyage</option>
				<option value="Gestion - Audit - Ressources humaines" <?php if (isset($criteres["secteur"]["Gestion - Audit - Ressources humaines	"])) { echo "selected"; } ?>>Gestion - Audit - Ressources humaines</option>
				<option value="Hôtellerie - Restauration - Tourisme" <?php if (isset($criteres["secteur"]["Hôtellerie - Restauration - Tourisme"])) { echo "selected"; } ?>>Hôtellerie - Restauration - Tourisme</option>
				<option value="Humanitaire" <?php if (isset($criteres["secteur"]["Humanitaire"])) { echo "selected"; } ?>>Humanitaire</option>
				<option value="Industrie - Matériaux" <?php if (isset($criteres["secteur"]["Industrie - Matériaux"])) { echo "selected"; } ?>>Industrie - Matériaux</option>
				<option value="Lettres - Sciences humaines" <?php if (isset($criteres["secteur"]["Lettres - Sciences humaines"])) { echo "selected"; } ?>>Lettres - Sciences humaines</option>
				<option value="Mécanique - Maintenance" <?php if (isset($criteres["secteur"]["Mécanique - Maintenance"])) { echo "selected"; } ?>>Mécanique - Maintenance</option>
				<option value="Numérique - Multimédia - Audiovisuel" <?php if (isset($criteres["secteur"]["Numérique - Multimédia - Audiovisuel"])) { echo "selected"; } ?>>Numérique - Multimédia - Audiovisuel</option>
				<option value="Santé" <?php if (isset($criteres["secteur"]["Santé"])) { echo "selected"; } ?>>Santé</option>
				<option value="Sciences - Maths - Physique" <?php if (isset($criteres["secteur"]["Sciences - Maths - Physique"])) { echo "selected"; } ?>>Sciences - Maths - Physique</option>
				<option value="Secrétariat - Accueil" <?php if (isset($criteres["secteur"]["Secrétariat - Accueil"])) { echo "selected"; } ?>>Secrétariat - Accueil</option>
				<option value="Social - Services à la personne" <?php if (isset($criteres["secteur"]["Social - Services à la personne"])) { echo "selected"; } ?>>Social - Services à la personne</option>
				<option value="Soins - Esthétique - Coiffure" <?php if (isset($criteres["secteur"]["Soins - Esthétique - Coiffure"])) { echo "selected"; } ?>>Soins - Esthétique - Coiffure</option>
				<option value="Sport - Animation" <?php if (isset($criteres["secteur"]["Sport - Animation"])) { echo "selected"; } ?>>Sport - Animation</option>
				<option value="Transport - Logistique" <?php if (isset($criteres["secteur"]["Transport - Logistique"])) { echo "selected"; } ?>>Transport - Logistique</option>
			</select>
		</div>
		<div class="lab">
			<label for="inscription[]">Inscription :</label>
			<select name="inscription[]" multiple size="4">
				<option value="pole emploi" <?php if (isset($criteres["inscription"]["pole emploi"])) { echo "selected"; } ?>>Pôle emploi</option>
				<option value="cap emploi" <?php if (isset($criteres["inscription"]["cap emploi"])) { echo "selected"; } ?>>Cap emploi</option>
				<option value="mission locale" <?php if (isset($criteres["inscription"]["mission locale"])) { echo "selected"; } ?>>Mission locale</option>
				<option value="apec" <?php if (isset($criteres["inscription"]["apec"])) { echo "selected"; } ?>>APEC</option>
			</select>
		</div>	
	</div>
</fieldset>
<?php 
	// si theme = logement
	} else if ($row['id_theme_pere']=="2") { 
?>
...
<?php
	}
} 
?>
	
	<div class="button">
		<input type="button" value="Retour à la liste" onclick="javascript:location.href='offre_liste.php'">
		<input type="reset" value="Reset">
		<input type="submit" value="Enregistrer">
	</div>
</form>
</div>

<?php 
if ($ENVIRONNEMENT=="LOCAL") {
	echo "<pre>";print_r(@$_POST);echo "<br/>"; echo @$req;echo "<br/>";  print_r(@$row);echo "<br/>";print_r(@$criteres); print_r(@$tab_select_soustheme); echo @$sql."<br/>".@$sqlt."<br/>".@$sqlst."<br/>".@$sqlv."<br/>".@$sqlv2."</pre>"; 
}
?>
</body>
</html>