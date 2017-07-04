<?php
session_start();
require('secret/connect.php');
include('inc/functions.php');
include('inc/variables.php');

//********* verif des droits
if (!isset($_SESSION['user_id'])) header('Location: index.php');
if (!$_SESSION['user_droits']['theme']) header('Location: accueil.php'); //si pas les droits, retour à l'accueil

//********* variable
$msg = "";
$libelle_theme_choisi = "";

$id_theme_choisi = 1;
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
			$msg = "Modification bien enregistrée.";
		}
	}
}

//********** mise à jour/création du theme
if (isset($_POST["submit_nouveau_sous_theme"])) {
	$id_theme_choisi = $_POST["maj_id_theme"];
	if ($id_theme_choisi) {
		$req= "INSERT INTO `bsl_theme` (`libelle_theme`, `id_theme_pere`, `actif_theme`) VALUES ( \"".$_POST["libelle_nouveau_sous_theme"]."\", '".$id_theme_choisi."', '0')";
		$result=mysqli_query($conn, $req);
		if ($result) { 
			$msg = "Modification bien enregistrée.";
		}
	}
}

//********* liste déroulante des thèmes (en haut à droite)
$select_theme = "";
$sql = "SELECT * FROM `bsl_theme` WHERE `id_theme_pere` IS NULL";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
	while($rows = mysqli_fetch_assoc($result)) {
		$select_theme .= "<option value=\"".$rows['id_theme']."\" ";
		if ($rows['id_theme']==$id_theme_choisi) {
			$select_theme .= "selected";
			$libelle_theme_choisi = $rows['libelle_theme'];
			$actif_theme_choisi = $rows['actif_theme'];
		}
		$select_theme .= ">".$rows['libelle_theme']."</option>";
	}
}

//si theme selectionné
$tableau = "";
$i=0;
if ($id_theme_choisi) {
	$sql2 = "SELECT * FROM `bsl_theme` 
		WHERE `id_theme_pere`=".$id_theme_choisi." 
		ORDER BY actif_theme DESC, ordre_theme";
	$result_st = mysqli_query($conn, $sql2);
}else{
	$msg = "Merci de sélectionner un thème dans la liste.";
}

//view
require 'view/theme.tpl.php';