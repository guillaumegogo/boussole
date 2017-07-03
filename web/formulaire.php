<?php
session_start();

require('secret/connect.php');
include('inc/functions.php');
include('inc/variables.php');

//********* censé permettre de revenir sur les formulaires sans recharger
header('Cache-Control: no cache'); 
session_cache_limiter('private_no_expire');

//********* valeur de sessions
if (isset($_POST['besoin'])) { $_SESSION['besoin'] = $_POST['besoin']; }
if (isset($_POST['sexe'])) { $_SESSION['sexe'] = $_POST['sexe']; }
if (isset($_POST['age'])) { $_SESSION['age'] = $_POST['age']; }
if (isset($_POST['nationalite'])) { $_SESSION['nationalite'] = $_POST['nationalite']; }
if (isset($_POST['jesais'])) { $_SESSION['jesais'] = $_POST['jesais']; }
if (isset($_POST['situation'])) { $_SESSION['situation'] = $_POST['situation']; }
if (isset($_POST['etudes'])) { $_SESSION['etudes'] = $_POST['etudes']; }
if (isset($_POST['diplome'])) { $_SESSION['diplome'] = $_POST['diplome']; }
if (isset($_POST['permis'])) { $_SESSION['permis'] = $_POST['permis']; }
if (isset($_POST['handicap'])) { $_SESSION['handicap'] = $_POST['handicap']; }
if (isset($_POST['temps_plein'])) { $_SESSION['temps_plein'] = $_POST['temps_plein']; }
if (isset($_POST['experience'])) { $_SESSION['experience'] = $_POST['experience']; }
if (isset($_POST['secteur'])) { $_SESSION['secteur'] = $_POST['secteur']; }
if (isset($_POST['type_emploi'])) { $_SESSION['type_emploi'] = $_POST['type_emploi']; }
if (isset($_POST['inscription'])) { $_SESSION['inscription'] = $_POST['inscription']; }

//************ si accès direct à la page, renvoi vers l'accueil
if (!isset($_SESSION['ville_habitee']) || !isset($_SESSION['besoin'])) {
	header('Location: index.php');
}

//********* etape dans le formulaire : si on a validé la dernière étape on est renvoyé sur la page de résultats
$etape = 1;
if (isset($_POST['etape'])) {
	if ($_POST['etape']=='fin') {
		header('Location: resultat.php');
	}else{
		$etape = $_POST['etape'];
	}
}

//view
require 'view/formulaire.tpl.php';