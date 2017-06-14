<?php
require('../secret/connect.php');
include('../inc/functions.php');
session_start();

//********* gestion des droits
if (!isset($_SESSION['user_id'])) header('Location: index.php');
if (isset($_SESSION['user_pro_id']) && isset($_GET["id"]) && $_SESSION['user_pro_id']!=$_GET["id"]) header('Location: accueil.php'); //si tu es un professionnel qui essaie de voir une autre fiche, tu retournes à l'accueil

//********* variables
$last_id = null;
$msg = "";
$req = "";
$liste2 = "";

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
	//prise en compte du choix multiple themes
	if(isset($_POST['theme'])){
		foreach ($_POST['theme'] as $selected_option) {
			$themes .= $selected_option.";";
		}
		$themes = substr($themes, 0, -1);
	}

	//requête d'ajout
	if (!$_POST["maj_id"]) {
		$req= "INSERT INTO `bsl_professionnel`(`nom_pro`, `type_pro`, `description_pro`, `theme_pro`, `adresse_pro`, `code_postal_pro`, `ville_pro`, `code_insee_pro`, `courriel_pro`, `telephone_pro`, `site_web_pro`, `delai_pro`, `competence_geo`, `id_competence_geo`, `user_derniere_modif`) VALUES (\"".$_POST["nom"]."\",\"".$_POST["type"]."\",\"".$_POST["desc"]."\",\"".$themes."\",\"".$_POST["adresse"]."\",\"".$code_postal."\",\"".$ville."\",\"".$code_insee."\",\"".$_POST["courriel"]."\",\"".$_POST["tel"]."\",\"".$_POST["site"]."\",\"".$_POST["delai"]."\",\"".$_POST["competence_geo"]."\",\"".$id_competence_geo."\",\"".$_SESSION['user_id']."\")";
		
		$result=mysqli_query($conn, $req);
		$last_id=mysqli_insert_id($conn);

	//requête de modification
	}else{
		$req = "UPDATE `bsl_professionnel` SET `nom_pro` = \"".$_POST["nom"]."\", `type_pro` = \"".$_POST["type"]."\", `description_pro` = \"".$_POST["desc"]."\", `theme_pro` = \"".$themes."\", `adresse_pro` = \"".$_POST["adresse"]."\", `code_postal_pro` = \"".$code_postal."\", `ville_pro` = \"".$ville."\", `code_insee_pro` = \"".$code_insee."\", `courriel_pro` = \"".$_POST["courriel"]."\", `telephone_pro` = \"".$_POST["tel"]."\", `site_web_pro` = \"".$_POST["site"]."\", `delai_pro` = \"".$_POST["delai"]."\", `actif_pro` = \"".$_POST["actif"]."\" ";
		if (isset($_POST["competence_geo"])) { $req .= ", `competence_geo` = \"".$_POST["competence_geo"]."\", `id_competence_geo` = \"".$id_competence_geo."\" "; }
		$req .= " WHERE `id_professionnel` = ".$_POST["maj_id"];

		$result=mysqli_query($conn, $req);
		$last_id=$_POST["maj_id"];
	}
		
	//********** mise à jour des villes, si compétence villes => désactivé 
	//script sql à rejouer si on veut réactiver => old/bsl_professionnel_villes.sql
	/*
	$result2 = true;
	$req2 = "";
	if (isset($_POST['list2'])) {
		$req3= "DELETE FROM `bsl_professionnel_villes` WHERE `id_professionnel` = ".$_POST["maj_id"];
		mysqli_query($conn, $req3);		
		$req2 = "INSERT INTO `bsl_professionnel_villes`(`id_professionnel`, `code_insee`) VALUES ";
		foreach ($_POST['list2'] as $selected_option) {
			$req2 .= "(".$last_id.", \"".$selected_option."\"), ";
		}
		$req2 = substr ($req2, 0, -2);
		$result2=mysqli_query($conn, $req2);
	}*/
	
	if ($result) { 
		$msg = "Modification bien enregistrée.";
	} else { 
		$msg = "Il y a eu un problème à l'enregistrement (<small>".$req."</small>). Contactez l'administration centrale si le problème perdure.";
	}
	$msg = "<div class=\"soustitre\" style=\"margin-top:1%\">".$msg."</div>";
}

//*********** affichage du professionnel demandé ou nouvellement créé
$id_professionnel = $last_id;
if(isset($_GET["id"])){
	$id_professionnel = $_GET["id"];
}
if(isset($id_professionnel)) {
	$sql = "SELECT * FROM `bsl_professionnel` WHERE id_professionnel=".$id_professionnel;
	$result = mysqli_query($conn, $sql);
	
	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_assoc($result);
	}
}

$soustitre = ($id_professionnel) ? "Modification d'un professionnel" : "Ajout d'un professionnel";

//********** génération des listes des compétences géographiques
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

if ($_SESSION['user_statut']==1) { // choix accessibles uniquement aux admins
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

$sql = "SELECT `id_territoire`, `nom_territoire` FROM `bsl_territoire` WHERE 1 ";
if ($_SESSION['user_statut']==2) { $sql .= " AND `id_territoire`=".$_SESSION['territoire_id']; }
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
if ($_SESSION['user_statut']>2) $choix_territoire .= " disabled "; 
$choix_territoire .= ">".$select_territoire."</select>";

$affiche_listes_geo .= $choix_territoire;
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="css/style_backoffice.css" />
    <link rel="icon" type="image/png" href="../img/compass-icon.png" />
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
	<script type="text/javascript" language="javascript" src="js/jquery-1.12.0.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery-ui-1.12.0.js"></script>
	<script type="text/javascript">
function displayGeo(that) {
	var w = document.getElementById('liste_regions');
	var x = document.getElementById('liste_departements');
	var y = document.getElementById('liste_territoires');
	//var z = document.getElementById('liste_villes');
	if (w != null) { w.style.display = 'none'; }
	if (x != null) { x.style.display = 'none'; }
	if (y != null) { y.style.display = 'none'; }
	//if (z != null) { z.style.display = 'none'; }	
	if (that.value == "regional") {
		w.style.display = "block";
	} else if (that.value == "departemental") {
		x.style.display = "block";
	} else if (that.value == "territoire") {
		y.style.display = "block";
	} /*else if (that.value == "villes") {
		z.style.display = "block";
	}*/
}
</script>
	<title>Boussole des jeunes</title>
</head>

<body>
<h1 class="bandeau"><a href="accueil.php">Administration de la boussole des jeunes</a></h1>
<div class="statut"><?php echo $_SESSION['accroche']; ?> (<a href="index.php">déconnexion</a>)</div> 

<h2><?php echo $soustitre; ?></h2>

<?php echo $msg; ?>

<form method="post" class="detail" onsubmit='checkall()'>

<input type="hidden" name="maj_id" value="<?php echo $id_professionnel; ?>">
<fieldset>
	<legend>Détail du professionnel</legend>

    <div class="col">
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
				<option value="emploi" <?php if ($id_professionnel) {if (in_array("emploi", explode(';',$row["theme_pro"]))) { echo "selected"; }} ?> >Emploi</option>
				<option value="logement" <?php if ($id_professionnel) {if (in_array("logement", explode(';',$row["theme_pro"]))) { echo "selected"; }} ?> >Logement</option>
			</select> 
		</div>
		<div class="lab">
			<label for="actif">Actif :</label>
			<input type="radio" name="actif" value="1" <?php if ($id_professionnel) {if ($row["actif_pro"]=="1") { echo "checked"; }} else echo "checked"; ?>> Oui <input type="radio" name="actif" value="0" <?php if ($id_professionnel) {if ($row["actif_pro"]=="0") { echo "checked"; }} ?>> Non
			</select> 
		</div>
	</div>
    <div class="col">
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
				<select name="competence_geo" onchange="displayGeo(this);" style="display:block; margin-bottom:0.5em;" <?php if ($_SESSION['user_statut']>2) echo "disabled"; ?>>
					<?php echo $liste_competence_geo; ?>
				</select>
				<?php 
				//listes déroulantes (affichées ou pas) des régions, départements et territoires
				echo $affiche_listes_geo;
				?>
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

<?php 
if ($ENVIRONNEMENT=="LOCAL") {
	echo "<pre>"; print_r(@$_POST); echo "<br/>"; print_r(@$row); echo "<br/>";echo @$req; echo "</pre>"; 
}
?>
</body>
</html>