<?php
require('secret/connect.php');
include('inc/functions.php');
session_start();

//********* verif des droits
if (!isset($_SESSION['user_id'])) header('Location: index.php'); //1. doit être connecté 
if ($_SESSION['user_statut'] != "administrateur" && isset($_GET["id"])) { //2. si tu n'es pas un admin, ton territoire_id ou ton user_pro_id doit correspondre à celui du pro
	if (isset($_SESSION['user_pro_id'])) {
		if ($_SESSION['user_pro_id']!=$_GET["id"]) header('Location: accueil.php');
	}else{
		$sql = "SELECT competence_geo, id_competence_geo FROM `bsl_professionnel` 
		WHERE competence_geo=\"territoire\" AND id_competence_geo=\"".$_SESSION['territoire_id']."\" AND id_professionnel=".$_GET["id"];
		$result = mysqli_query($conn, $sql);
		if (mysqli_num_rows($result) == 0) { header('Location: accueil.php'); }
	}
}

//********* variables
$last_id = null;
$msg = "";
$req = "";
$liste2 = "";
$row = [];

//si post du formulaire interne
if (isset($_POST["maj_id"])) {
	
	//récupération du code insee correspondant à la saisie
	$code_insee = "";
	$themes = "";
	$code_postal=substr($_POST["commune"], -5);
	$ville=substr($_POST["commune"],0,-6);
	$sql = "SELECT code_insee FROM `bsl__ville` WHERE code_postal='".$code_postal."' AND nom_ville LIKE '".$ville."'";
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_assoc($result);
		$code_insee = $row['code_insee'];
	}
	//si choix d'une compétence région/département/territoire, récupération de l'id correspondant (région/département/territoire)
	$id_competence_geo = null;
	if (isset($_POST["competence_geo"])) {
		if ($_POST["competence_geo"]=="regional" && $_POST["liste_regions"]){
			$id_competence_geo = $_POST["liste_regions"];
		}else if ($_POST["competence_geo"]=="departemental" && $_POST["liste_departements"]){
			$id_competence_geo = $_POST["liste_departements"];
		}else if ($_POST["competence_geo"]=="territoire" && $_POST["liste_territoires"]){
			$id_competence_geo = $_POST["liste_territoires"];
		}
	}	

	//requête d'ajout
	if (!$_POST["maj_id"]) {
		$req= "INSERT INTO `bsl_professionnel`(`nom_pro`, `type_pro`, `description_pro`, `adresse_pro`, `code_postal_pro`, `ville_pro`, `code_insee_pro`, `courriel_pro`, `telephone_pro`, `site_web_pro`, `delai_pro`, `competence_geo`, `id_competence_geo`, `user_derniere_modif`) VALUES (\"".$_POST["nom"]."\",\"".$_POST["type"]."\",\"".$_POST["desc"]."\",\"".$_POST["adresse"]."\",\"".$code_postal."\",\"".$ville."\",\"".$code_insee."\",\"".$_POST["courriel"]."\",\"".$_POST["tel"]."\",\"".$_POST["site"]."\",\"".$_POST["delai"]."\",\"".$_POST["competence_geo"]."\",\"".$id_competence_geo."\",\"".$_SESSION['user_id']."\")";
		
		
		$result=mysqli_query($conn, $req);
		$last_id=mysqli_insert_id($conn);

	//requête de modification
	}else{
		$req = "UPDATE `bsl_professionnel` SET `nom_pro` = \"".$_POST["nom"]."\", `type_pro` = \"".$_POST["type"]."\", `description_pro` = \"".$_POST["desc"]."\", `adresse_pro` = \"".$_POST["adresse"]."\", `code_postal_pro` = \"".$code_postal."\", `ville_pro` = \"".$ville."\", `code_insee_pro` = \"".$code_insee."\", `courriel_pro` = \"".$_POST["courriel"]."\", `telephone_pro` = \"".$_POST["tel"]."\", `site_web_pro` = \"".$_POST["site"]."\", `delai_pro` = \"".$_POST["delai"]."\", `actif_pro` = \"".$_POST["actif"]."\" ";
		if (isset($_POST["competence_geo"])) { $req .= ", `competence_geo` = \"".$_POST["competence_geo"]."\", `id_competence_geo` = \"".$id_competence_geo."\" "; }
		$req .= " WHERE `id_professionnel` = ".$_POST["maj_id"];

		$result=mysqli_query($conn, $req);
		$last_id=$_POST["maj_id"];
	}
	
	//prise en compte du choix multiple themes
	if(isset($_POST['theme'])){
		//mise à jour des critères
		$reqd= "DELETE FROM `bsl_professionnel_themes` WHERE `id_professionnel` = ".$last_id;
		mysqli_query($conn, $reqd);
		
		$reqt = "INSERT INTO `bsl_professionnel_themes`(`id_professionnel`, `id_theme`) VALUES ";
		foreach ($_POST['theme'] as $selected_option) {
			$reqt .= "(".$last_id.", \"".$selected_option."\"), ";
		}
		$reqt = substr($reqt, 0, -2);
		$result2=mysqli_query($conn, $reqt);
	}
	
	if ($result) { 
		$msg = "Modification bien enregistrée.";
	} else { 
		$msg = "Il y a eu un problème à l'enregistrement . Contactez l'administration centrale si le problème perdure.";
	}
	$msg = "<div class=\"soustitre\">".$msg."</div>";
}

//*********** affichage du professionnel demandé ou nouvellement créé
$id_professionnel = $last_id;
if(isset($_GET["id"])){
	$id_professionnel = $_GET["id"];
}
if(isset($id_professionnel)) {
	$sql = "SELECT * FROM `bsl_professionnel` 
	WHERE id_professionnel=".$id_professionnel;
	$result = mysqli_query($conn, $sql);
	
	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_assoc($result);
	}
}

$soustitre = ($id_professionnel) ? "Modification d'un professionnel" : "Ajout d'un professionnel";

//************************* génération des listes des compétences géographiques
$liste_competence_geo ="<option value=\"\">A choisir</option>";
$select_region = "";
$select_dep = "";
$choix_territoire = "";
$affiche_listes_geo = ""; 

$tabgeo = array ( 
	array("national", "National", array("administrateur")),
	array("regional", "Régional", array("administrateur")), 
	array("departemental", "Départemental", array("administrateur")), 
	array("territoire", "Territoire", array("administrateur", "animateur territorial", "professionnel"))
);
foreach ($tabgeo as $row_tabgeo) {
	if (in_array($_SESSION['user_statut'], $row_tabgeo[2])) { //si l'utilisateur a les droits
		$liste_competence_geo .="<option value=\"".$row_tabgeo[0]."\" ";
		if ($id_professionnel) {if ($row["competence_geo"]==$row_tabgeo[0]) { $liste_competence_geo .=" selected "; }} 
		$liste_competence_geo .=">".$row_tabgeo[1]."</option>";
	}
}

if ($_SESSION['user_statut']=="administrateur") { // choix accessibles uniquement aux admins
	//liste déroulante des régions
	$sql = "SELECT * FROM `bsl__region` WHERE 1 ";
	$result = mysqli_query($conn, $sql);
	$select_region = "<option value=\"\" >A choisir</option>";
	while($row2 = mysqli_fetch_assoc($result)) {
		$select_region .= "<option value=\"".$row2['id_region']."\" ";
		if ($id_professionnel) {
			if (($row["competence_geo"]=="regional") && ($row2['id_region']==$row['id_competence_geo'] )) {
				$select_region .= "selected";
			}
		}
		$select_region .= ">".$row2['nom_region']."</option>";
	}
	$choix_region = "<select name=\"liste_regions\" id=\"liste_regions\" style=\"display:";
	if ($id_professionnel) {if ($row["competence_geo"]=="regional") { $choix_region .= "block\""; } else { $choix_region .= "none\""; }} else { $choix_region .= "none\""; }
	$choix_region .= "\">".$select_region."</select>";

	//liste déroulante des départements
	$sql = "SELECT `id_departement`, `nom_departement` FROM `bsl__departement` WHERE 1 ";
	$result = mysqli_query($conn, $sql);
	$select_dep = "<option value=\"\" >A choisir</option>";
	while($row2 = mysqli_fetch_assoc($result)) {
		$select_dep .= "<option value=\"".$row2['id_departement']."\" ";
		if ($id_professionnel) {
			if (($row["competence_geo"]=="departemental") && ($row2['id_departement']==$row['id_competence_geo'])) {
				$select_dep .= "selected";
			}
		}
		$select_dep .= ">".$row2['id_departement']." ".$row2['nom_departement']."</option>";
	}
	$choix_dep = "<select name=\"liste_departements\" id=\"liste_departements\" style=\"display:";
	if ($id_professionnel) {if ($row["competence_geo"]=="departemental") { $choix_dep .= "block\""; } else { $choix_dep .= "none\""; }} else { $choix_dep .= "none\""; }
	$choix_dep .= ">".$select_dep."</select>";
	$affiche_listes_geo .= $choix_region.$choix_dep;
}

//+ liste déroulante des territoires
$sql = "SELECT `id_territoire`, `nom_territoire` FROM `bsl_territoire` WHERE 1 ";
if ($_SESSION['user_statut']=="animateur territorial") { 
	$sql .= " AND `id_territoire`=".$_SESSION['territoire_id']; 
}
$result = mysqli_query($conn, $sql);
$select_territoire = "<option value=\"\" >A choisir</option>";
while($row2 = mysqli_fetch_assoc($result)) {
	$select_territoire .= "<option value=\"".$row2['id_territoire']."\" ";
	if ($id_professionnel) {
		if (($row["competence_geo"]=="territoire") && ($row2['id_territoire']==$row['id_competence_geo'])) {
			$select_territoire .= "selected";
		}
	}else{
		if(isset($_SESSION['territoire_id'])){
			if ($row2['id_territoire']==$_SESSION['territoire_id']) {
				$select_territoire .= "selected";
			}
		}
	}
	$select_territoire .= ">".$row2['nom_territoire']."</option>";
}
$choix_territoire = "<select name=\"liste_territoires\" id=\"liste_territoires\" style=\"display:";
if ($id_professionnel) {
	if ($row["competence_geo"]=="territoire") { 
		$choix_territoire .= "block\""; 
	} else { 
		$choix_territoire .= "none\""; 
	}
} else { 
	$choix_territoire .= "none\""; 
}
if (!in_array($_SESSION['user_statut'], array("administrateur","animateur territorial"))) $choix_territoire .= " disabled "; 
$choix_territoire .= ">".$select_territoire."</select>";
$affiche_listes_geo .= $choix_territoire;

//********* liste déroulante des thèmes
$select_theme = "";
$sqlt = "SELECT `bsl_theme`.`id_theme`, `libelle_theme`,`id_professionnel` 
	FROM `bsl_theme` 
	LEFT JOIN `bsl_professionnel_themes` ON `bsl_professionnel_themes`.`id_theme`=`bsl_theme`.`id_theme` 
		AND `bsl_professionnel_themes`.`id_professionnel`=\"".$id_professionnel."\" 
	WHERE actif_theme=1 AND `id_theme_pere` IS NULL ";
$result = mysqli_query($conn, $sqlt);
while($rowt = mysqli_fetch_assoc($result)) {
	$select_theme .= "<option value=\"".$rowt['id_theme']."\" ";
	if ($rowt['id_professionnel']) { $select_theme .= " selected "; }
	$select_theme .= ">".$rowt['libelle_theme']."</option>";
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Boussole des jeunes</title>
	<link rel="icon" type="image/png" href="img/compass-icon.png" />
	<link rel="stylesheet" href="css/style_backoffice.css" />
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
	<script type="text/javascript" language="javascript" src="js/jquery-1.12.0.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery-ui-1.12.0.js"></script>
	<script type="text/javascript">
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
//fonction affichage listes
function displayGeo(that) {
	var w = document.getElementById('liste_regions');
	var x = document.getElementById('liste_departements');
	var y = document.getElementById('liste_territoires');
	if (w != null) { w.style.display = 'none'; }
	if (x != null) { x.style.display = 'none'; }
	if (y != null) { y.style.display = 'none'; }
	if (that.value == "regional") {
		w.style.display = "block";
	} else if (that.value == "departemental") {
		x.style.display = "block";
	} else if (that.value == "territoire") {
		y.style.display = "block";
	}
}
</script>
</head>

<body>
<h1 class="bandeau"><a href="accueil.php">Administration de la boussole des jeunes</a></h1>
<div class="statut"><?php echo $_SESSION['accroche']; ?> (<a href="index.php">déconnexion</a>)</div> 

<div class="container">
<h2><?php echo $soustitre; ?></h2>

<?php echo $msg; ?>

<form method="post" class="detail">

<input type="hidden" name="maj_id" value="<?php echo $id_professionnel; ?>">
<fieldset>
	<legend>Détail du professionnel</legend>

	<div class="deux_colonnes">
		<div class="lab">
			<label for="nom">Nom du professionnel :</label>
			<input type="text" name="nom" value="<?php if ($id_professionnel) { echo $row["nom_pro"]; } ?>"/>
		</div>
		<div class="lab">
			<label for="type">Type :</label>
			<input type="text" name="type" value="<?php if ($id_professionnel) { echo $row["type_pro"]; } ?>"/>
		</div>
		<div class="lab">
			<label for="desc">Description du professionnel :</label>
			<textarea rows="5" name="desc"><?php if ($id_professionnel) { echo $row["description_pro"]; } ?></textarea>
		</div>
		<div class="lab">
			<label for="theme[]">Thème(s) :</label>
			<select name="theme[]" multiple size="2">
				<?php echo $select_theme; ?>
			</select> 
		</div>
		<div class="lab">
			<label for="actif">Actif :</label>
			<input type="radio" name="actif" value="1" <?php if ($id_professionnel) {if ($row["actif_pro"]=="1") { echo "checked"; }} else echo "checked"; ?>> Oui <input type="radio" name="actif" value="0" <?php if ($id_professionnel) {if ($row["actif_pro"]=="0") { echo "checked"; }} ?>> Non
			</select> 
		</div>
	</div>
	<div class="deux_colonnes">
		<div class="lab">
			<label for="adresse">Adresse :</label>
			<input type="text" name="adresse"  value="<?php if ($id_professionnel) { echo $row["adresse_pro"]; } ?>"/>
		</div>
		<div class="lab">
			<label for="code_postal">Commune :</label>
			<input type="text" name="commune" id="villes" value="<?php if ($id_professionnel) { echo $row["ville_pro"]." ".$row["code_postal_pro"]; } ?>" /> 
		</div>
		<div class="lab">
			<label for="courriel">Courriel :</label>
			<input type="email" name="courriel" value="<?php if ($id_professionnel) { echo $row["courriel_pro"]; } ?>" />
		</div>
		<div class="lab">
			<label for="tel">Téléphone :</label>
			<input type="text" name="tel"  value="<?php if ($id_professionnel) { echo $row["telephone_pro"]; } ?>" />
		</div>
		<div class="lab">
			<label for="site">Site internet :</label>
			<input type="text" name="site"  value="<?php if ($id_professionnel) { echo $row["site_web_pro"]; } ?>" />
		</div>
		<div class="lab">
			<label for="delai">Délai garanti de réponse :</label>
			<select name="delai">
				<option value="2" <?php if ($id_professionnel) {if ($row["delai_pro"]=="2") { echo "selected"; }} ?>>2 jours</option>
				<option value="3" <?php if ($id_professionnel) {if ($row["delai_pro"]=="3") { echo "selected"; }} ?>>3 jours</option>
				<option value="5" <?php if ($id_professionnel) {if ($row["delai_pro"]=="5") { echo "selected"; }} ?>>5 jours</option>
				<option value="7" <?php if ($id_professionnel) {if ($row["delai_pro"]=="7") { echo "selected"; }} ?>>7 jours</option>
			</select> 
		</div>
		<div class="lab">
			<label for="competence_geo">Compétence géographique :</label>
			<div style="display:inline-block;">
				<select name="competence_geo" onchange="displayGeo(this);" style="display:block; margin-bottom:0.5em;" <?php if (!in_array($_SESSION['user_statut'], array("administrateur","animateur territorial"))) echo "disabled"; ?>>
					<?php echo $liste_competence_geo; ?>
				</select>
				
				<?php echo $affiche_listes_geo; //listes déroulantes (affichées ou pas) des régions, départements et territoires ?>
			</div>
		</div>
		
	</div>
</fieldset>

<div class="button">
	<input type="button" value="Retour" onclick="javascript:location.href='professionnel_liste.php'">
	<input type="reset" value="Reset">
	<input type="submit" value="Enregistrer">
</div>
</form>
</div>

<?php 
if ($ENVIRONNEMENT=="LOCAL") {
	echo "<pre>"; print_r(@$_POST); echo "<br/>"; print_r(@$row); echo "<br/>".@$req."<br/>".@$sqlt; echo "</pre>"; 
}
?>
</body>
</html>