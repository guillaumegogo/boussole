<?php
session_start();

require('secret/connect.php');
include('inc/functions.php');

//********* verif des droits
if (!isset($_SESSION['user_id'])) header('Location: index.php'); //1. doit être connecté 
/*
if ($_SESSION['user_droits']['utilisateur']){ // si on a les droits, on fait juste un test sur le territoire (cas des animateurs territoriaux notamment)
	if($_SESSION['territoire_id']){
		$sql = 'SELECT competence_geo, id_competence_geo FROM `bsl_utilisateur` 
			WHERE competence_geo="territoire" AND id_competence_geo='.$_SESSION['territoire_id'].' AND id_utilisateur='.$_GET['id'];
		$result = mysqli_query($conn, $sql);
		if (mysqli_num_rows($result) == 0) { header('Location: utilisateur_liste.php'); }
	}
}else{ //autrement, le seul cas possible est la consultation de ses propres infos
	$_GET['id'] = $_SESSION['user_id'];
}*/

//********* variables
$last_id = null;
$msg = '';
$req = '';
$row = [];
$attache = '';

//si post du formulaire interne
if (isset($_POST['maj_id'])) {

	$maj_attache = "NULL";
	if ($_POST["statut"]==2) $maj_attache=$_POST["attache"];
	else if ($_POST["statut"]==3) $maj_attache=$_POST["attache_p"];
			
	//requête d'ajout
	if (!$_POST["maj_id"]) {
		if($_POST["nouveaumotdepasse"]==$_POST["nouveaumotdepasse2"]){
			
			$req= "INSERT INTO `bsl_utilisateur`(`nom_utilisateur`, `email`, `motdepasse`, `date_inscription`, `id_statut`, `id_metier`) VALUES (\"".$_POST["nom"]."\",\"".$_POST["courriel"]."\",\"".password_hash($_POST["nouveaumotdepasse"], PASSWORD_DEFAULT)."\",NOW(),\"".$_POST["statut"]."\",\"".$maj_attache."\")";
			$result=mysqli_query($conn, $req);
			$last_id=mysqli_insert_id($conn);
			
		}else{
			$msg = 'Les deux mots de passe ne correspondent pas.';
		}

	//requête de modification
	}else{
		if (!($_POST["nouveaumotdepasse"]||$_POST["nouveaumotdepasse2"]||$_POST["motdepasseactuel"])){
			$req = "UPDATE `bsl_utilisateur` SET `nom_utilisateur` = \"".$_POST["nom"]."\", `email` = \"".$_POST["courriel"]."\", `id_statut` = \"".$_POST["statut"]."\", `id_metier` = \"".$maj_attache."\", `actif_utilisateur` = \"".$_POST["actif"]."\" WHERE `id_utilisateur` = ".$_POST["maj_id"];
		}else{
			if ($_POST["nouveaumotdepasse"]!=$_POST["nouveaumotdepasse2"]){
				$msg = 'Les deux nouveaux mots de passe ne correspondent pas.';
			}else{
				$sql = "SELECT `id_statut` FROM `bsl_utilisateur` 
					WHERE `id_utilisateur`=".$_POST["maj_id"]." AND `motdepasse`=\"".password_hash($_POST["motdepasseactuel"], PASSWORD_DEFAULT)."\"";
				$result = mysqli_query($conn, $sql);
				if (!mysqli_num_rows($result)) {
					$msg = 'Le mot de passe indiqué n\'est pas le bon.';
				}else {//mdp actuel correct
					$req = "UPDATE `bsl_utilisateur` SET `nom_utilisateur` = \"".$_POST["nom"]."\", `email` = \"".$_POST["courriel"]."\", `id_statut` = \"".$_POST["statut"]."\", `id_metier` = \"".$maj_attache."\", `actif_utilisateur` = \"".$_POST["actif"]."\", `motdepasse` = \"".password_hash($_POST["nouveaumotdepasse"], PASSWORD_DEFAULT)."\" WHERE `id_utilisateur` = ".$_POST["maj_id"];
					$result=mysqli_query($conn, $req);
					$last_id=$_POST["maj_id"];
				}
			}
		}
	}
	
	if ($result) { 
		$msg = "Modification bien enregistrée.";
	} else { 
		if (!$msg) $msg = "Il y a eu un problème à l'enregistrement . Contactez l'administration centrale si le problème perdure.";
	}
}

//*********** affichage de l'utilisateur demandé ou nouvellement créé
$id_utilisateur = $last_id;
if(isset($_GET['id'])){
	$id_utilisateur = $_GET['id'];
}
if(isset($id_utilisateur)) {
	$sql = 'SELECT `bsl_utilisateur`.`id_statut`, `nom_utilisateur`, `email`, `date_inscription`, `actif_utilisateur`, `id_professionnel`, `nom_pro`, `id_territoire` , `nom_territoire`
	FROM `bsl_utilisateur` 
	JOIN `bsl__statut` on `bsl__statut`.`id_statut`=`bsl_utilisateur`.`id_statut`
	LEFT JOIN `bsl_territoire` ON `bsl_territoire`.`id_territoire`=`bsl_utilisateur`.`id_metier`
	LEFT JOIN `bsl_professionnel` ON `bsl_professionnel`.`id_professionnel`=`bsl_utilisateur`.`id_metier`
	WHERE `id_utilisateur`='.$id_utilisateur;
	$result = mysqli_query($conn, $sql);
	
	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_assoc($result);
		
		if($row['id_statut']==2) { $attache = $row['nom_territoire']; }
		else if($row['id_statut']==3) { $attache = $row['nom_pro']; }
		
	}else{
		if (!$msg) $msg = '<div class="soustitre">Cet utilisateur est inconnu.</div>';
	}
}

$soustitre = ($id_utilisateur) ? "Modification d'un utilisateur" : "Ajout d'un utilisateur";

//*********************
$select_territoire = '<option value="" >A choisir</option>';
$select_professionnel = '<option value="" >A choisir</option>';
//si création, liste = liste du/des territoire(s) et des pros du/des territoire(s), avec tout en display none
//si modif = affichage en disabled du territoire ou de la liste des pros, en fonction de la liste

$sql2 = 'SELECT `id_territoire`, `nom_territoire` FROM `bsl_territoire` WHERE 1 ';
if ($_SESSION['user_statut']=='animateur territorial') { 
	$sql2 .= ' AND `id_territoire`='.$_SESSION['territoire_id']; 
}
$result = mysqli_query($conn, $sql2);
while($row2 = mysqli_fetch_assoc($result)) {
	$select_territoire .= '<option value="'.$row2['id_territoire'].'" ';
	if(isset($row['id_territoire'])){
		if ($row2['id_territoire']==$row['id_territoire']) {
			$select_territoire .= 'selected';
		}
	}
	$select_territoire .= '>'.$row2['nom_territoire'].'</option>';
}

$sql3 = 'SELECT `id_professionnel`, `nom_pro` FROM `bsl_professionnel` WHERE 1 ';
if ($_SESSION['user_statut']=='animateur territorial') { 
	$sql3 .= ' AND `competence_geo`="territoire" AND `id_competence_geo`='.$_SESSION['territoire_id']; 
}
$result = mysqli_query($conn, $sql3);
while($row3 = mysqli_fetch_assoc($result)) {
	$select_professionnel .= '<option value="'.$row3['id_professionnel'].'" ';
	if(isset($row['id_professionnel'])){
		if ($row3['id_professionnel']==$row['id_professionnel']) {
			$select_professionnel .= 'selected';
		}
	}
	$select_professionnel .= '>'.$row3['nom_pro'].'</option>';
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
	<script type="text/javascript">
//fonction affichage listes
function displayAttache(that) {
	var w = document.getElementById('liste_territoires');
	var x = document.getElementById('liste_professionnels');
	if (w != null) { w.style.display = 'none'; }
	if (x != null) { x.style.display = 'none'; }
	if (that.value == "2") {
		w.style.display = "block";
	} else if (that.value == "3") {
		x.style.display = "block";
	}
}
</script>
</head>

<body>
<h1 class="bandeau"><a href="accueil.php">Administration de la boussole des jeunes</a></h1>
<div class="statut"><?php echo $_SESSION['accroche']; ?> (<a href="index.php">déconnexion</a>)</div> 

<div class="container">
<h2><?php echo $soustitre; ?> <span style="color:red">//en cours de dev</span></h2>

<div class="soustitre"><?=$msg; ?></div>

<form method="post" class="detail">

<input type="hidden" name="maj_id" value="<?php echo $id_utilisateur; ?>">
<fieldset>
	<legend>Détail de l'utilisateur</legend>

	<div class="une_colonne">
		<div class="lab">
			<label for="courriel">Courriel <?php if ($id_utilisateur) { echo "(login)"; } ?> :</label>
			<input type="text" name="courriel" placeholder="Le courriel sert de login" value="<?php if ($id_utilisateur) { echo $row["email"]; } ?>"/>
		</div>
		<div class="lab">
			<label for="nom">Nom :</label>
			<input type="text" name="nom" value="<?php if ($id_utilisateur) { echo $row["nom_utilisateur"]; } ?>"/>
		</div>
		<div class="lab">
			<label for="statut">Statut :</label>
			<select name="statut" <?php if (!$_SESSION['user_droits']['utilisateur']){ echo "disabled"; } ?> onchange="displayAttache(this);" >
				<option value="" >A choisir</option>
				<option value="1" <?php if ($id_utilisateur) {if ($row["id_statut"]=="1") { echo "selected"; }} ?>>Administrateur national</option>
				<option value="2" <?php if ($id_utilisateur) {if ($row["id_statut"]=="2") { echo "selected"; }} ?>>Animateur territorial</option>
				<option value="3" <?php if ($id_utilisateur) {if ($row["id_statut"]=="3") { echo "selected"; }} ?>>Professionnel</option>
			</select>
		</div>
		<div class="lab">
			<label for="attache">Attache :</label>
			<div style="display:inline-block;">
			<select name="attache" id="liste_territoires" <?php if (isset($row["id_statut"]) && $row["id_statut"]=="2") { echo "disabled"; } else { echo "style=\"display:none\""; } ?>>
				<?php echo $select_territoire; ?>
			</select> 
			<select name="attache_p" id="liste_professionnels" <?php if (isset($row["id_statut"]) && $row["id_statut"]=="3") { echo "disabled"; } else { echo "style=\"display:none\""; } ?>>
				<?php echo $select_professionnel; ?>
			</select></div>
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
		
		<div style="margin-top:2em;">
			<?php if ($id_utilisateur) { ?>
			<div class="lab">
				<label for="motdepasseactuel">Mot de passe actuel :</label>
				<input type="password" name="motdepasseactuel" />
			</div>
			<?php } ?>
			<div class="lab">
				<label for="nouveaumotdepasse"><?php echo ($id_utilisateur) ? "Nouveau mot de passe" : "Mot de passe" ; ?> :</label>
				<input type="password" name="nouveaumotdepasse" />
			</div>
			<div class="lab">
				<label for="nouveaumotdepasse2">Confirmez le mot de passe :</label>
				<input type="password" name="nouveaumotdepasse2" />
			</div>
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