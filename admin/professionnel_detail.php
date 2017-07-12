<?php
session_start();
require('secret/connect.php');
include('inc/functions.php');
include('inc/variables.php');

//********* verif des droits
if (!isset($_SESSION['user_id'])) header('Location: index.php'); //1. doit être connecté 
/*if (!$_SESSION['user_droits']['professionnel']){ // si on a les droits, on fait juste un test sur le territoire (cas des animateurs territoriaux notamment)
	if($_SESSION['territoire_id']){
		$sql = 'SELECT competence_geo, id_competence_geo FROM `bsl_professionnel`
		WHERE competence_geo="territoire" AND id_competence_geo='.$_SESSION['territoire_id'].' AND id_professionnel='.$_GET['id'];
		$result = mysqli_query($conn, $sql);
		if (mysqli_num_rows($result) == 0) { header('Location: professionnel_liste.php'); }
	}
}else{ //autrement, le seul cas possible est la consultation de ses propres infos
	$_GET['id'] = $_SESSION['user_pro_id'];
}*/

//********* variables
$last_id = null;
$msg = '';
$req = '';
$liste2 = '';
$row = [];

//si post du formulaire interne
if (isset($_POST['maj_id'])) {
	
	//récupération du code insee correspondant à la saisie
	$code_insee = '';
	$themes = '';
	$code_postal=substr($_POST['commune'], -5);
	$ville=substr($_POST['commune'],0,-6);
	$sql = 'SELECT code_insee FROM `bsl__ville` WHERE code_postal="'.$code_postal.'" AND nom_ville LIKE "'.$ville.'"';
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_assoc($result);
		$code_insee = $row['code_insee'];
	}
	//si choix d'une compétence région/département/territoire, récupération de l'id correspondant (région/département/territoire)
	$id_competence_geo = "NULL";
	if (isset($_POST['competence_geo'])) {
		if ($_POST['competence_geo']=='regional' && $_POST['liste_regions']){
			$id_competence_geo = $_POST['liste_regions'];
		}else if ($_POST['competence_geo']=='departemental' && $_POST['liste_departements']){
			$id_competence_geo = $_POST['liste_departements'];
		}else if ($_POST['competence_geo']=='territoire' && $_POST['liste_territoires']){
			$id_competence_geo = $_POST['liste_territoires'];
		}
	}	

	//requête d'ajout
	if (!$_POST['maj_id']) {
		$req= 'INSERT INTO `bsl_professionnel`(`nom_pro`, `type_pro`, `description_pro`, `adresse_pro`, `code_postal_pro`, `ville_pro`, `code_insee_pro`, `courriel_pro`, `telephone_pro`, `site_web_pro`, `delai_pro`, `competence_geo`, `id_competence_geo`, `user_derniere_modif`) VALUES ("'.$_POST['nom'].'","'.$_POST['type'].'","'.$_POST['desc'].'","'.$_POST['adresse'].'","'.$code_postal.'","'.$ville.'","'.$code_insee.'","'.$_POST['courriel'].'","'.$_POST['tel'].'","'.$_POST['site'].'",'.$_POST['delai'].',"'.$_POST['competence_geo'].'",'.$id_competence_geo.','.$_SESSION['user_id'].')';
		$result=mysqli_query($conn, $req);
		$last_id=mysqli_insert_id($conn);

	//requête de modification
	}else{
		$req = 'UPDATE `bsl_professionnel` SET `nom_pro` = "'.$_POST['nom'].'", `type_pro` = "'.$_POST['type'].'", `description_pro` = "'.$_POST['desc'].'", `adresse_pro` = "'.$_POST['adresse'].'", `code_postal_pro` = "'.$code_postal.'", `ville_pro` = "'.$ville.'", `code_insee_pro` = "'.$code_insee.'", `courriel_pro` = "'.$_POST['courriel'].'", `telephone_pro` = "'.$_POST['tel'].'", `site_web_pro` = "'.$_POST['site'].'", `delai_pro` = '.$_POST['delai'].', `actif_pro` = '.$_POST['actif'].' ';
		if ($_POST['competence_geo']) { $req .= ', `competence_geo` = "'.$_POST['competence_geo'].'" '; }
		if ($id_competence_geo) { $req .= ', `id_competence_geo` = '.$id_competence_geo.' '; }
		$req .= ' WHERE `id_professionnel` = '.$_POST['maj_id'];
		$result=mysqli_query($conn, $req);
		$last_id=$_POST['maj_id'];
	}
	
	//prise en compte du choix multiple themes
	if(isset($_POST['theme'])){
		//mise à jour des critères
		$reqd= 'DELETE FROM `bsl_professionnel_themes` WHERE `id_professionnel` = '.$last_id;
		mysqli_query($conn, $reqd);
		
		$reqt = 'INSERT INTO `bsl_professionnel_themes`(`id_professionnel`, `id_theme`) VALUES ';
		foreach ($_POST['theme'] as $selected_option) {
			$reqt .= '('.$last_id.', \''.$selected_option.'\'), ';
		}
		$reqt = substr($reqt, 0, -2);
		$result2=mysqli_query($conn, $reqt);
	}
	
	if ($result) { 
		$msg = 'Modification bien enregistrée.';
	} else { 
		$msg = 'Il y a eu un problème à l\'enregistrement . Contactez l\'administration centrale si le problème perdure.';
	}
}

//*********** affichage du professionnel demandé ou nouvellement créé
$id_professionnel = $last_id;
if(isset($_GET['id'])){
	$id_professionnel = $_GET['id'];
}
if(isset($id_professionnel)) {
	$sql = 'SELECT * FROM `bsl_professionnel` 
	WHERE id_professionnel='.$id_professionnel;
	$result = mysqli_query($conn, $sql);
	
	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_assoc($result);
	}else{
		$msg = '<div class=\'soustitre\'>Ce professionnel est inconnu.</div>';
	}
}

$soustitre = ($id_professionnel) ? 'Modification d\'un professionnel' : 'Ajout d\'un professionnel';

//************************* génération des listes des compétences géographiques (régions, départements et/ou territoires )
$affiche_listes_geo = ''; 

$liste_competence_geo ='<option value=\'\'>A choisir</option>';
$tabgeo = array ('territoire'=>'Territoire');
if ($_SESSION['user_statut']=='administrateur') {
	$tabgeo += array('national'=>'National', 'regional'=>'Régional', 'departemental'=>'Départemental');
}
foreach ($tabgeo as $key => $value) {
	$liste_competence_geo .='<option value=\''.$key.'\' ';
	if ($id_professionnel) {if ($row['competence_geo']==$key) { $liste_competence_geo .=' selected '; }} 
	$liste_competence_geo .='>'.$value.'</option>';
}

$select_region = '';
$select_dep = '';
$choix_territoire = '';
if ($_SESSION['user_statut']=='administrateur') { // choix accessibles uniquement aux admins
	//liste déroulante des régions
	$sql = 'SELECT * FROM `bsl__region` WHERE 1 ';
	$result = mysqli_query($conn, $sql);
	$select_region = '<option value=\'\' >A choisir</option>';
	while($row2 = mysqli_fetch_assoc($result)) {
		$select_region .= '<option value=\''.$row2['id_region'].'\' ';
		if ($id_professionnel) {
			if (($row['competence_geo']=='regional') && ($row2['id_region']==$row['id_competence_geo'] )) {
				$select_region .= 'selected';
			}
		}
		$select_region .= '>'.$row2['nom_region'].'</option>';
	}
	$choix_region = '<select name=\'liste_regions\' id=\'liste_regions\' style=\'display:';
	if ($id_professionnel) {if ($row['competence_geo']=='regional') { $choix_region .= 'block\''; } else { $choix_region .= 'none\''; }} else { $choix_region .= 'none\''; }
	$choix_region .= ' >'.$select_region.'</select>';

	//liste déroulante des départements
	$sql = 'SELECT `id_departement`, `nom_departement` FROM `bsl__departement` WHERE 1 ';
	$result = mysqli_query($conn, $sql);
	$select_dep = '<option value=\'\' >A choisir</option>';
	while($row2 = mysqli_fetch_assoc($result)) {
		$select_dep .= '<option value=\''.$row2['id_departement'].'\' ';
		if ($id_professionnel) {
			if (($row['competence_geo']=='departemental') && ($row2['id_departement']==$row['id_competence_geo'])) {
				$select_dep .= 'selected';
			}
		}
		$select_dep .= '>'.$row2['id_departement'].' '.$row2['nom_departement'].'</option>';
	}
	$choix_dep = '<select name=\'liste_departements\' id=\'liste_departements\' style=\'display:';
	if ($id_professionnel) {if ($row['competence_geo']=='departemental') { $choix_dep .= 'block\''; } else { $choix_dep .= 'none\''; }} else { $choix_dep .= 'none\''; }
	$choix_dep .= '>'.$select_dep.'</select>';
	$affiche_listes_geo .= $choix_region.$choix_dep;
}

//+ liste déroulante des territoires
$sql = 'SELECT `id_territoire`, `nom_territoire` FROM `bsl_territoire` WHERE 1 ';
if ($_SESSION['user_statut']=='animateur territorial') { 
	$sql .= ' AND `id_territoire`='.$_SESSION['territoire_id']; 
}
$result = mysqli_query($conn, $sql);
$select_territoire = '<option value=\'\' >A choisir</option>';
while($row2 = mysqli_fetch_assoc($result)) {
	$select_territoire .= '<option value=\''.$row2['id_territoire'].'\' ';
	if ($id_professionnel) {
		if (($row['competence_geo']=='territoire') && ($row2['id_territoire']==$row['id_competence_geo'])) {
			$select_territoire .= 'selected';
		}
	}else{
		if(isset($_SESSION['territoire_id'])){
			if ($row2['id_territoire']==$_SESSION['territoire_id']) {
				$select_territoire .= 'selected';
			}
		}
	}
	$select_territoire .= '>'.$row2['nom_territoire'].'</option>';
}
$choix_territoire = '<select name="liste_territoires" id="liste_territoires" style="display:';
if ($id_professionnel) {
	if ($row['competence_geo']=='territoire') { 
		$choix_territoire .= 'block'; 
	} else { 
		$choix_territoire .= 'none'; 
	}
} else { 
	$choix_territoire .= 'none'; 
}
if (!in_array($_SESSION['user_statut'], array('administrateur','animateur territorial'))) $choix_territoire .= ' disabled '; 
$choix_territoire .= '">'.$select_territoire.'</select>';
$affiche_listes_geo .= $choix_territoire;

//********* liste déroulante des thèmes
$select_theme = '';
$sqlt = 'SELECT `bsl_theme`.`id_theme`, `libelle_theme` FROM `bsl_theme`';
if ($id_professionnel) {
	$sqlt = 'SELECT `bsl_theme`.`id_theme`, `libelle_theme`,`id_professionnel` FROM `bsl_theme`'
		. ' LEFT JOIN `bsl_professionnel_themes` ON `bsl_professionnel_themes`.`id_theme`=`bsl_theme`.`id_theme` '
		. ' AND `bsl_professionnel_themes`.`id_professionnel`='.$id_professionnel;
}
$sqlt .= ' WHERE actif_theme=1 AND `id_theme_pere` IS NULL';
$result = mysqli_query($conn, $sqlt);
while($rowt = mysqli_fetch_assoc($result)) {
	$select_theme .= '<option value="'.$rowt['id_theme'].'" ';
	if (isset($rowt['id_professionnel']) && $rowt['id_professionnel']) { $select_theme .= ' selected '; }
	$select_theme .= '>'.$rowt['libelle_theme'].'</option>';
}

//view
require 'view/professionnel_detail.tpl.php';