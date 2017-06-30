<?php
//http://php.net/manual/fr/function.password-hash.php
//https://openclassrooms.com/courses/concevez-votre-site-web-avec-php-et-mysql/tp-creer-un-espace-membres

require('secret/connect.php');
include('inc/functions.php');
session_start();

//********* verif des droits
if (!isset($_SESSION['user_id'])) header('Location: index.php'); //1. doit être connecté 
if ($_SESSION['user_droits']['utilisateur']){ // si on a les droits, on fait juste un test sur le territoire (cas des animateurs territoriaux notamment)
	if($_SESSION['territoire_id']){
		$sql = "SELECT competence_geo, id_competence_geo FROM `bsl_utilisateur` 
			WHERE competence_geo=\"territoire\" AND id_competence_geo=\"".$_SESSION['territoire_id']."\" AND id_utilisateur=".$_GET["id"];
		$result = mysqli_query($conn, $sql);
		if (mysqli_num_rows($result) == 0) { header('Location: utilisateur_liste.php'); }
	}
}else{ //autrement, le seul cas possible est la consultation de ses propres infos
	$_GET["id"] = $_SESSION['user_id'];
}

//********* variables
$last_id = null;
$msg = "";
$req = "";
$row = [];
$attache = "";
$liste_attache_metier = "<option value=\"\" >A choisir</option>";

//si post du formulaire interne
if (isset($_POST["maj_id"])) {
	/*
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
		$req= "INSERT INTO `bsl_utilisateur`(`nom_pro`, `type_pro`, `description_pro`, `adresse_pro`, `code_postal_pro`, `ville_pro`, `code_insee_pro`, `courriel_pro`, `telephone_pro`, `site_web_pro`, `delai_pro`, `competence_geo`, `id_competence_geo`, `user_derniere_modif`) VALUES (\"".$_POST["nom"]."\",\"".$_POST["type"]."\",\"".$_POST["desc"]."\",\"".$_POST["adresse"]."\",\"".$code_postal."\",\"".$ville."\",\"".$code_insee."\",\"".$_POST["courriel"]."\",\"".$_POST["tel"]."\",\"".$_POST["site"]."\",\"".$_POST["delai"]."\",\"".$_POST["competence_geo"]."\",\"".$id_competence_geo."\",\"".$_SESSION['user_id']."\")";
		
		
		$result=mysqli_query($conn, $req);
		$last_id=mysqli_insert_id($conn);

	//requête de modification
	}else{
		$req = "UPDATE `bsl_utilisateur` SET `nom_pro` = \"".$_POST["nom"]."\", `type_pro` = \"".$_POST["type"]."\", `description_pro` = \"".$_POST["desc"]."\", `adresse_pro` = \"".$_POST["adresse"]."\", `code_postal_pro` = \"".$code_postal."\", `ville_pro` = \"".$ville."\", `code_insee_pro` = \"".$code_insee."\", `courriel_pro` = \"".$_POST["courriel"]."\", `telephone_pro` = \"".$_POST["tel"]."\", `site_web_pro` = \"".$_POST["site"]."\", `delai_pro` = \"".$_POST["delai"]."\", `actif_pro` = \"".$_POST["actif"]."\" ";
		if (isset($_POST["competence_geo"])) { $req .= ", `competence_geo` = \"".$_POST["competence_geo"]."\", `id_competence_geo` = \"".$id_competence_geo."\" "; }
		$req .= " WHERE `id_utilisateur` = ".$_POST["maj_id"];

		$result=mysqli_query($conn, $req);
		$last_id=$_POST["maj_id"];
	}
	
	//prise en compte du choix multiple themes
	if(isset($_POST['theme'])){
		//mise à jour des critères
		$reqd= "DELETE FROM `bsl_utilisateur_themes` WHERE `id_utilisateur` = ".$last_id;
		mysqli_query($conn, $reqd);
		
		$reqt = "INSERT INTO `bsl_utilisateur_themes`(`id_utilisateur`, `id_theme`) VALUES ";
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
	$msg = "<div class=\"soustitre\">".$msg."</div>";*/
}

//*********** affichage de l'utilisateur demandé ou nouvellement créé
$id_utilisateur = $last_id;
if(isset($_GET["id"])){
	$id_utilisateur = $_GET["id"];
}
if(isset($id_utilisateur)) {
	$sql = "SELECT * FROM `bsl_utilisateur` 
	JOIN `bsl__statut` on `bsl__statut`.`id_statut`=`bsl_utilisateur`.`id_statut`
	LEFT JOIN `bsl_territoire` ON `bsl_territoire`.`id_territoire`=`bsl_utilisateur`.`id_metier`
	LEFT JOIN `bsl_professionnel` ON `bsl_professionnel`.`id_professionnel`=`bsl_utilisateur`.`id_metier`
	WHERE id_utilisateur=".$id_utilisateur;
	$result = mysqli_query($conn, $sql);
	
	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_assoc($result);
		
		if($row["id_statut"]==2) { $attache = $row["nom_territoire"]; }
		else if($row["id_statut"]==3) { $attache = $row["nom_pro"]; }
		
	}else{
		$msg = "<div class=\"soustitre\">Cet utilisateur est inconnu.</div>";
	}
}

//*********************
$liste_attache_metier = "<option value=\"\" >A choisir</option>";
//si création, liste = liste du/des territoire(s) et des pros du/des territoire(s), avec tout en display none
//si modif = affichage en disabled du territoire ou de la liste des pros, en fonction de la liste

$soustitre = ($id_utilisateur) ? "Modification d'un utilisateur" : "Ajout d'un utilisateur";
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Boussole des jeunes</title>
	<link rel="icon" type="image/png" href="img/compass-icon.png" />
	<link rel="stylesheet" href="css/style_backoffice.css" />
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
	<script type="text/javascript">
//fonction affichage listes
function displayAttache() {
	/*var w = document.getElementById('liste_regions');
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
	}*/
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

<input type="hidden" name="maj_id" value="<?php echo $id_utilisateur; ?>">
<fieldset>
	<legend>Détail de l'utilisateur</legend>

	<div class="une_colonne">
		<div class="lab">
			<label for="nom">Courriel :</label>
			<input type="text" name="nom" value="<?php if ($id_utilisateur) { echo $row["email"]; } ?>"/> <abbr title="Le courriel sert de login.">&#9888;</abbr>
		</div>
		<div class="lab">
			<label for="nom">Nom :</label>
			<input type="text" name="nom" value="<?php if ($id_utilisateur) { echo $row["nom_utilisateur"]; } ?>"/>
		</div>
		<div class="lab">
			<label for="type">Statut :</label>
			<select name="type" <?php if (!$_SESSION['user_droits']['utilisateur']){ echo "disabled"; } ?> onchange="displayAttache();" >
				<option value="" >A choisir</option>
				<option value="1" <?php if ($id_utilisateur) {if ($row["id_statut"]=="1") { echo "selected"; }} ?>>Administrateur national</option>
				<option value="2" <?php if ($id_utilisateur) {if ($row["id_statut"]=="2") { echo "selected"; }} ?>>Animateur territorial</option>
				<option value="3" <?php if ($id_utilisateur) {if ($row["id_statut"]=="3") { echo "selected"; }} ?>>Professionnel</option>
			</select>
		</div>
		<div class="lab">
			<label for="competence_geo">Attache :</label>
			<select name="competence_geo" <?php if ($id_utilisateur) { echo "disabled"; } else { echo "style=\"display:none\""; } ?>>
				<?php echo $liste_attache_metier; ?>
			</select>
		</div>
		<?php if ($id_utilisateur) { ?>
		<div class="lab">
			<label for="date">Date d'inscription :</label>
			<input type="text" name="date" class="datepick" value="<?php echo date_format(date_create($row["date_inscription"]), 'd/m/Y'); ?>" disabled />
		</div>
		<?php } ?>
		<div class="lab">
			<label for="actif">Actif :</label>
			<input type="radio" name="actif" value="1" <?php if ($id_utilisateur) {if ($row["actif_utilisateur"]=="1") { echo "checked"; }} else echo "checked"; ?>> Oui 
			<input type="radio" name="actif" value="0" <?php if ($id_utilisateur) {if ($row["actif_utilisateur"]=="0") { echo "checked"; }} ?>> Non
			</select> 
		</div>
	</div>
</fieldset>

<fieldset>
	<legend>Mot de passe</legend>

	<div class="une_colonne">
		<?php if ($id_utilisateur) { ?>
		<div class="lab">
			<label for="date">Mot de passe actuel :</label>
			<input type="password" name="motdepasseactuel" />
		</div>
		<?php } ?>
		<div class="lab">
			<label for="date"><?php echo ($id_utilisateur) ? "Nouveau mot de passe" : "Mot de passe" ; ?> :</label>
			<input type="password" name="nouveaumotdepasse" />
		</div>
		<div class="lab">
			<label for="date">Confirmez le mot de passe :</label>
			<input type="password" name="nouveaumotdepasse2" />
		</div>
	</div>
</fieldset>

<div class="button">
	<input type="button" value="Retour" onclick="javascript:location.href='utilisateur_liste.php'">
	<input type="reset" value="Reset">
	<input type="submit" value="Enregistrer">
</div>
</form>
</div>

<?php 
if ($ENVIRONNEMENT=="LOCAL") {
	echo "<pre>";print_r(@$_SESSION); echo "<br/>"; print_r(@$_POST); echo "<br/>"; print_r(@$row); echo "<br/>".@$req."<br/>".@$sqlt; echo "</pre>"; 
}
?>
</body>
</html>