<?php
session_start();
require('secret/connect.php');
include('inc/functions.php');
include('inc/variables.php');

//********* verif des droits
if (!isset($_SESSION['user_id'])) header('Location: index.php');

//******** calcul du nb de demandes (todo : à adapter pour pros et animateurs)
$nb_dmd = '';
if ($_SESSION['user_statut'] == 'administrateur'){
	$sql = 'SELECT count(`id_demande`) as nb FROM `bsl_demande` 
		WHERE date_traitement IS NULL'; 
	$result = mysqli_query($conn, $sql);
	$row_dmd = mysqli_fetch_assoc($result);
	$nb_dmd = '';
	if ($row_dmd['nb']==1) {
		$nb_dmd = '('.$row_dmd['nb'].' nouvelle)';
	}else if ($row_dmd['nb']>1) {
		$nb_dmd = '('.$row_dmd['nb'].' nouvelles)';
	}
}

//******* construction des listes de lien
$liens_activite ='';
if ($_SESSION['user_droits']['offre']) { $liens_activite .= '<li><a href=\'offre_liste.php\'>Offres de service</a></li>'; }
if ($_SESSION['user_droits']['demande']) { $liens_activite .= '<li><a href=\'demande_liste.php\'>Demandes reçues</a> '.$nb_dmd.'</li>'; }

$liens_admin = '';
if ($_SESSION['user_droits']['professionnel']) { 
	$liens_admin .= '<li><a href=\'professionnel_liste.php\'>Professionnels</a></li>';
}else if (isset($_SESSION['user_pro_id'])){
	$liens_admin .= '<li><a href=\'professionnel_detail.php?id='.$_SESSION['user_pro_id'].'\'>Détails de mon organisation</a></li>'; //lien professionnel_detail des utilisateurs 'professionnels'
}
if ($_SESSION['user_droits']['utilisateur']) { 
	$liens_admin .= '<li><a href=\'utilisateur_liste.php\'>Utilisateurs</a></li>';
}else if (isset($_SESSION['user_pro_id'])){
	$liens_admin .= '<li><a href=\'professionnel_detail.php?id='.$_SESSION['user_pro_id'].'\'>Mon compte</a></li>'; //lien professionnel_detail des utilisateurs 'professionnels'
}

$liens_reference ='';
if ($_SESSION['user_droits']['territoire']) { $liens_reference .= '<li><a href=\'territoire.php\'>Territoires</a></li>'; }
if ($_SESSION['user_droits']['theme']) { $liens_reference .= '<li><a href=\'theme.php\'>Thèmes et sous-thèmes</a></li>'; } 
if ($_SESSION['user_droits']['critere']) { $liens_reference .= '<li>Critères</li>'; }

//********** sélection territoire
if (isset($_POST['choix_territoire'])) {
	$_SESSION['territoire_id'] = $_POST['choix_territoire'];
}
include('inc/select_territoires.inc.php');

//view
require 'view/accueil.tpl.php';