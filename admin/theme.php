<?php
require('../secret/connect.php');
include('../inc/functions.php');
session_start();

//********* verif des droits
if (!isset($_SESSION['user_id'])) header('Location: index.php');
if (!in_array($_SESSION['user_statut'], array("administrateur"))) header('Location: accueil.php'); //si pas admin ou... ?

$msg = "";
$libelle_theme_choisi = "";

$id_theme_choisi = 0;
if (isset($_POST['choix_theme'])) $id_theme_choisi = $_POST['choix_theme'];

//********** mise à jour/création du theme
if (isset($_POST["submit_meta"])) {
	$id_theme_choisi = $_POST["maj_id_theme"];
	if ($id_theme_choisi) {
		$req= "UPDATE `bsl_theme` SET `libelle_theme`=\"".$_POST["libelle_theme"]."\", `actif_theme`=\"".$_POST["actif"]."\" WHERE `id_theme`=".$id_theme_choisi;
		$result=mysqli_query($conn, $req);
		foreach($_POST['sthemes'] as $selected_option => $foo) {
			foreach($foo as $selected_option) {
				$rreq= "UPDATE `bsl_theme` SET `libelle_theme`=\"".$foo[1]."\", `ordre_theme`=\"".$foo[2]."\", `actif_theme`=\"".$foo[3]."\" WHERE `id_theme`=".$foo[0];
				$rresult=mysqli_query($conn, $rreq);
			}
		}
		if ($result) { 
			$msg = "<div class=\"soustitre\">Modification bien enregistrée.</div>";
		}
	}
}

//********* liste déroulante thèmes
$select_theme = "";
$sql = "SELECT * FROM `bsl_theme` WHERE `id_theme_pere` IS NULL";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
	$select_theme = "<label for=\"choix_theme\">Thème :</label>
	<select name=\"choix_theme\" onchange=\"this.form.submit()\" ><option value=\"\">A choisir</option>";
	while($rows = mysqli_fetch_assoc($result)) {
		$select_theme .= "<option value=\"".$rows['id_theme']."\" ";
		if ($rows['id_theme']==$id_theme_choisi) {
			$select_theme .= "selected";
			$libelle_theme_choisi = $rows['libelle_theme'];
			$actif_theme_choisi = $rows['actif_theme'];
		}
		$select_theme .= ">".$rows['libelle_theme']."</option>";
	}
	$select_theme .= "\r\n</select>\r\n";
}
$select_theme = "<form method=\"post\" class=\"liste_territoire\">".$select_theme."</form>";

//si theme selectionné
$tableau = "";
$i=0;
if ($id_theme_choisi) {
	$sql2 = "SELECT * FROM `bsl_theme` 
		WHERE `id_theme_pere`=".$id_theme_choisi." 
		ORDER BY actif_theme DESC, ordre_theme";
	$result = mysqli_query($conn, $sql2);
	if (mysqli_num_rows($result) > 0) {
		$tableau .= "<table><thead><tr><th>Libellé</th><th>Ordre d'affichage</th><th>Actif</th></tr></thead><tbody>";

		while($row = mysqli_fetch_assoc($result)) {
			if ($row["id_theme"]==$id_theme_choisi) {
			}else{
				$tableau .= "<tr><td><input type=\"hidden\" name=\"sthemes[".$i."][]\" value=\"". $row["id_theme"]. "\" />
					<input type=\"text\" name=\"sthemes[".$i."][]\" value=\"". $row["libelle_theme"]. "\" style=\"width:60em;\"/></td>
					<td><input type=\"text\" name=\"sthemes[".$i."][]\" value=\"". $row["ordre_theme"]. "\" style=\"width:3em\"/></td>
					<td><input type=\"radio\" name=\"sthemes[".$i."][]\" value=\"1\" " .(($row["actif_theme"]==="1") ? "checked" : ""). "> Oui 
					<input type=\"radio\" name=\"sthemes[".$i."][]\" value=\"0\" " .(($row["actif_theme"]==="0") ? "checked" : ""). "> Non</td></tr>";
				$i++;
				}
		}
		$tableau .= "</tbody></table>";

	} else {
		$tableau = "<div class=\"soustitre\">Aucun sous-thème</div>";
	}
}else{
	$msg = "<div class=\"soustitre\">Merci de sélectionner un thème dans la liste.</div>";
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="css/style_backoffice.css" />
	<link rel="icon" type="image/png" href="../img/compass-icon.png" />	
	<script type="text/javascript" language="javascript" src="js/jquery-1.12.0.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery.filterByText.js"></script>
	<script type="text/javascript" language="javascript" src="js/selectbox.js"></script>
	<title>Boussole des jeunes</title>
</head>

<body>
<h1 class="bandeau"><a href="accueil.php">Administration de la boussole des jeunes</a></h1>
<div class="statut"><?php echo $_SESSION['accroche']; ?> (<a href="index.php">déconnexion</a>)</div> 

<div class="container">
<?php echo $select_theme; ?>

<h2>Gestion des thèmes</h2>
<?php echo $msg; ?>

<?php
if ($id_theme_choisi) {
?>
<form method="post"  class="detail">
	<fieldset style="margin-bottom:2em;">
	<legend>Gérer le theme</legend>
		<div class="col">
			<div class="lab">
				<label for="libelle_theme">Libellé :</label>
				<input type="text" name="libelle_theme" value="<?php echo $libelle_theme_choisi; ?>">
			</div>
			<div class="lab">
				<label for="actif">Actif :</label>
				<input type="radio" name="actif" value="1" <?php if ($actif_theme_choisi=="1") { echo "checked"; } ?>> Oui 
				<input type="radio" name="actif" value="0" <?php if ($actif_theme_choisi=="0") { echo "checked"; } ?>> Non
			</div>
			<input type="hidden" name="maj_id_theme" value="<?php echo $id_theme_choisi; ?>">
		</div>
	</fieldset>
	<fieldset>
	<legend>Sous-thèmes</legend>
<?php echo $tableau; ?>

	</fieldset>
	
	<div class="button">
		<input type="submit" name="submit_meta" value="Valider">
	</div>
	
</form>

<?php } ?>
</div>
 
<?php 
if ($ENVIRONNEMENT=="LOCAL") {
	echo "<pre>"; print_r(@$_POST); echo @$sql; echo @$sql2; echo "</pre>"; 
}
?>
</body>
</html>