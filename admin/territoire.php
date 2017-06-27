<?php
require('secret/connect.php');
include('inc/functions.php');
session_start();

//********* verif des droits
if (!isset($_SESSION['user_id'])) header('Location: index.php');
if (!$_SESSION['user_droits']['territoire']) header('Location: accueil.php'); //si pas les droits, retour à l'accueil

//********* variables
$msg = "";

//********* territoire sélectionné
if (isset($_POST["choix_territoire"])) { $_SESSION['territoire_id'] = securite_bdd($conn, $_POST["choix_territoire"]); }

//********** mise à jour/création du territoire
if (isset($_POST["submit_meta"])) {
	if ($_POST["maj_id_territoire"]) {
		$req= "UPDATE `bsl_territoire` SET `nom_territoire`=\"".$_POST["libelle_territoire"]."\" WHERE `id_territoire`=".$_POST["maj_id_territoire"];
		$result=mysqli_query($conn, $req);
		$id_territoire_choisi=$_POST["maj_id_territoire"];
	}else {
		$req= "INSERT INTO `bsl_territoire`(`nom_territoire`) VALUES (\"".$_POST["libelle_territoire"]."\")";
		$result=mysqli_query($conn, $req);
		$id_territoire_choisi=mysqli_insert_id($conn);
	}
}

include('inc/select_territoires.inc.php');

//********** mise à jour des villes
if (isset($_POST["submit_villes"])) {
	//********* on efface
	$req3= "DELETE FROM `bsl_territoire_villes` WHERE `id_territoire` = ".$_POST["maj_id_territoire"];
	mysqli_query($conn, $req3);
	
	//mise à jour des critères (chaque code insee ne peut être lié qu'une fois à un territoire)
	$tab_code_insee = array();
	$req2 = "INSERT INTO `bsl_territoire_villes` (`id_territoire`, `code_insee`) VALUES ";
	if (isset($_POST['list2'])){
		foreach ($_POST['list2'] as $selected_option) {
			if (!in_array($selected_option, $tab_code_insee)) {
				$req2 .= "(".$_POST["maj_id_territoire"].", \"".$selected_option."\"), ";
				$tab_code_insee[] = $selected_option;
			}
		}
	}
	$req2 = substr ($req2, 0, -2);
	$result2=mysqli_query($conn, $req2);
	
	if ($result2) { 
		$msg = "Modification bien enregistrée.";
	} else { 
		$msg = "Il y a eu un problème à l'enregistrement (<small>".$req2."</small>). Contactez l'administration centrale si le problème perdure.";
	}
	$msg = "<div class=\"soustitre\">".$msg."</div>";
}

//si territoire_id=0 -> pas de territoire sélectionné -> pas besoin d'aller chercher la liste des villes liées au territoire
if (isset($_SESSION["territoire_id"])) {
	if ($_SESSION['territoire_id']) {
		$sql = "SELECT `bsl__ville`.`code_insee`, `bsl__ville`.`code_postal`, `bsl__ville`.`nom_ville` 
			FROM `bsl__ville` JOIN `bsl_territoire_villes` ON `bsl_territoire_villes`.`code_insee`=`bsl__ville`.`code_insee` 
			WHERE `id_territoire`=".$_SESSION['territoire_id']."
			ORDER BY nom_ville";
		$result = mysqli_query($conn, $sql);
		$liste2 = "";
		if (mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_assoc($result)) {
				$liste2 .= "<option value=\"".$row["code_insee"]."\">".$row["nom_ville"]." ".$row["code_postal"]. "</option>";
			}
		}

		//********* liste des villes en base (remplacée par un fichier pour des questions de perf)
		/*$sql = "SELECT DISTINCT nom_ville, code_postal, code_insee FROM `bsl__ville` 
			WHERE `code_insee` NOT IN (SELECT DISTINCT code_insee FROM `bsl_territoire_villes` WHERE `id_territoire`=".$_SESSION['territoire_id'].") ORDER BY nom_ville";
		$result = mysqli_query($conn, $sql);
		$liste1 = "";
		while($row = mysqli_fetch_assoc($result)) {
			$liste1 .= "<option value=\"".$row["code_insee"]."\">".$row["nom_ville"]." ".$row["code_postal"]. "</option>";
		}*/
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="css/style_backoffice.css" />
	<link rel="icon" type="image/png" href="img/compass-icon.png" />	
	<script type="text/javascript" language="javascript" src="js/jquery-1.12.0.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery.filterByText.js"></script>
	<script type="text/javascript" language="javascript" src="js/selectbox.js"></script>
	<script type="text/javascript">
	$(function() {
		$('#list1').filterByText($('#textbox'));
	});
	function checkall(){
		var sel= document.getElementById('list2') 
		for(i=0;i<sel.options.length;i++){
			sel.options[i].selected=true;
		}
	}
	</script>
	<title>Boussole des jeunes</title>
</head>

<body>
<h1 class="bandeau"><a href="accueil.php">Administration de la boussole des jeunes</a></h1>
<div class="statut"><?php echo $_SESSION['accroche']; ?> (<a href="index.php">déconnexion</a>)</div> 

<div class="container">
<?php echo $select_territoire; ?>

<h2>Gestion des territoires</h2>
<?php echo $msg; ?>

<form method="post" class="detail" onsubmit='checkall()'>
	<fieldset class="centre">
	<legend><?php echo ($_SESSION['territoire_id']) ? "Modification du" : "Création d'un nouveau"; ?> territoire</legend>
		<div class="une_colonne">
			<label for="libelle_territoire" class="court">Libellé :</label>
			<input type="text" name="libelle_territoire" value="<?php echo $nom_territoire_choisi; ?>">
			<input type="hidden" name="maj_id_territoire" value="<?php echo $_SESSION['territoire_id']; ?>">
		</div>
		<input type="submit" name="submit_meta" value="Valider">
	</fieldset>

<?php if ($_SESSION['territoire_id']){ ?>

	<fieldset class="centre">
	<legend>Sélection des villes du territoire</legend>

		<div style="width:auto; text-align:left; clear:both; display: inline-block; vertical-align: middle; height: 100%;">
			<div style="margin-bottom:1em;">Filtre : <input id="textbox" placeholder="nom de ville, code postal ou département..." type="text" style="width:20em;"></div>
			
			<div style="display:inline-block; vertical-align:top;">		
				<select id="list1" MULTIPLE SIZE="20" style=" min-width:20em;"><?php include('inc/villes_options_insee.inc');?></select>
			</div>

			<div style="display:inline-block; margin-top:1em; vertical-align: top;">
				<INPUT TYPE="button" style="display:block; margin:1em;" NAME="right" VALUE="&gt;&gt;" ONCLICK="moveSelectedOptions(this.form['list1'],this.form['list2'],true)">
				
				<INPUT TYPE="button" style="display:block; margin:1em;" NAME="left" VALUE="&lt;&lt;" ONCLICK="moveSelectedOptions(this.form['list2'],this.form['list1'],true)">
			</div>

			<div style="display:inline-block;  vertical-align:top;">
				<select name="list2[]" id="list2" MULTIPLE SIZE="20" style=" min-width:20em;"><?php echo $liste2;?></select>
			</div>
			
			<input style="display:block; margin:2em auto 0 auto;" type="submit" name="submit_villes" value="Enregistrer le périmètre du territoire">
		</div>
	</fieldset>

<?php } ?>
	
</form>
</div>
 
<?php 
if ($ENVIRONNEMENT=="LOCAL") {
	echo "<pre>"; print_r(@$_POST); echo "</pre>"; 
}
?>
</body>
</html>