<?php
session_start();

require('secret/connect.php');
include('inc/functions.php');

//********* en attendant une vraie gestion de droits... :) (droit=1 => accès à la page listant l'objet correspondant)
if (!isset($_SESSION['user_id'])) {
	if (isset($_GET['user_id'])) { 
		switch ($_GET['user_id']) {
			case '1':
				$_SESSION['user_id'] = 1; $_SESSION['user_statut'] = 'administrateur'; $_SESSION['territoire_id'] = 0; 
				$_SESSION['user_droits'] = array('territoire' => '1', 'professionnel' => '1', 'offre' => '1', 'theme' => '1', 'utilisateur' => '1', 'demande' => '1', 'critere' => '1');
				break;
			case '2':
				$_SESSION['user_id'] = 2; $_SESSION['user_statut'] = 'animateur territorial'; $_SESSION['territoire_id'] = 1;
				$_SESSION['user_droits'] = array('territoire' => '1', 'professionnel' => '1', 'offre' => '1', 'theme' => '0', 'utilisateur' => '1', 'demande' => '1', 'critere' => '0');
				break;
			case '3': //attention, l'utilisateur 3 (pro) n'a pas le même lien d'accès aux professionnels (cf. plus bas)
				$_SESSION['user_id'] = 3; $_SESSION['user_statut'] = 'professionnel'; $_SESSION['user_pro_id'] = 1;
				$_SESSION['user_droits'] = array('territoire' => '0', 'professionnel' => '0', 'offre' => '1', 'theme' => '0', 'utilisateur' => '0', 'demande' => '1', 'critere' => '0');
				$sql = 'SELECT `nom_pro` FROM `bsl_professionnel` WHERE `id_professionnel`='.$_SESSION['user_pro_id']; 
				$result = mysqli_query($conn, $sql);
				$row = mysqli_fetch_assoc($result);
				$_SESSION['user_nom_pro'] = $row['nom_pro'];
				break;
			default:
				 header('Location: index.php');
		}
	}else{
		header('Location: index.php');
	}
}

//********** sélection territoire
if (isset($_POST['choix_territoire'])) {
	$_SESSION['territoire_id'] = $_POST['choix_territoire'];
}
include('inc/select_territoires.inc.php');

//********** accroche statut
$_SESSION['accroche'] = 'Bonjour, vous êtes '.$_SESSION['user_statut'];
if ($_SESSION['user_statut'] == 'animateur territorial') $_SESSION['accroche'] .= ' ('.$nom_territoire_choisi.')';
if ($_SESSION['user_statut'] == 'professionnel') $_SESSION['accroche'] .= ' ('.$_SESSION['user_nom_pro'].')';

//******** nb de demandes 
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

//view
require 'view/accueil.tpl.php';