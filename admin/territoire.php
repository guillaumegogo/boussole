<?php
session_start();
require('secret/connect.php');
include('inc/functions.php');
include('inc/variables.php');

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

//********** mise à jour des villes
if (isset($_POST["submit_villes"])) {
	//********* on efface
	$req3= "DELETE FROM `bsl_territoire_villes` WHERE `id_territoire` = ".$_POST["maj_id_territoire"];
	mysqli_query($conn, $req3);
	
	//********* puis on met à jour (chaque code insee ne peut être lié qu'une fois à un territoire)
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
}

//si territoire sélectionné -> on va chercher les listes de villes
if (isset($_SESSION["territoire_id"])) {
	if ($_SESSION['territoire_id']) {
		//********* liste des villes liées au territoire
		$sql = "SELECT `bsl__ville`.`code_insee`, `bsl__ville`.`code_postal`, `bsl__ville`.`nom_ville` 
			FROM `bsl__ville` JOIN `bsl_territoire_villes` ON `bsl_territoire_villes`.`code_insee`=`bsl__ville`.`code_insee` 
			WHERE `id_territoire`=".$_SESSION['territoire_id']."
			ORDER BY nom_ville";
		$result = mysqli_query($conn, $sql);
		$liste2 = "";
		if (mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_assoc($result)) {
				$liste2 .= "<option value=\"".$row['code_insee']."\">".$row['nom_ville']." ".$row['code_postal']. "</option>";
			}
		}

		//********* liste des les villes de France (remplacée par un fichier pour des questions de perf)
		/*$sql = "SELECT DISTINCT nom_ville, code_postal, code_insee FROM `bsl__ville` 
			WHERE `code_insee` NOT IN (SELECT DISTINCT code_insee FROM `bsl_territoire_villes` WHERE `id_territoire`=".$_SESSION['territoire_id'].") ORDER BY nom_ville";
		$result = mysqli_query($conn, $sql);
		$liste1 = "";
		while($row = mysqli_fetch_assoc($result)) {
			$liste1 .= "<option value=\"".$row['code_insee']."\">".$row['nom_ville']." ".$row['code_postal']. "</option>";
		}*/
	}
}

include('inc/select_territoires.inc.php');

//view
require 'view/territoire.tpl.php';