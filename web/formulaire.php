<?php
session_start();

require('secret/connect.php');
include('inc/functions.php');
include('inc/variables.php');

$msg = "";

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

//if (isset($_GET['b']) and ctype_digit((string)$_GET['b'])){ $_SESSION['id_besoin'] = $_GET['b']; } //à voir...

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

//********* récup du formulaire
$sql = 'SELECT `bsl_formulaire`.`id_formulaire`, `bsl_formulaire`.`nb_pages`, `bsl_formulaire__page`.`titre`, `bsl_formulaire__page`.`ordre` as `ordre_page`, `bsl_formulaire__page`.`aide`, `bsl_formulaire__question`.`libelle` as `libelle_question`, `bsl_formulaire__question`.`html_name`, `bsl_formulaire__question`.`type`, `bsl_formulaire__question`.`taille`, `bsl_formulaire__question`.`obligatoire`, `bsl_formulaire__valeur`.`libelle`, `bsl_formulaire__valeur`.`valeur`, `bsl_formulaire__valeur`.`defaut` FROM `bsl_formulaire` 
JOIN `bsl_theme` ON `bsl_theme`.`id_theme`=`bsl_formulaire`.`id_theme`
JOIN `bsl_formulaire__page` ON `bsl_formulaire__page`.`id_formulaire`=`bsl_formulaire`.`id_formulaire` AND `bsl_formulaire__page`.`actif`=1
JOIN `bsl_formulaire__question` ON `bsl_formulaire__question`.`id_page`=`bsl_formulaire__page`.`id_page` AND `bsl_formulaire__question`.`actif`=1
JOIN `bsl_formulaire__valeur` ON `bsl_formulaire__valeur`.`id_question`=`bsl_formulaire__question`.`id_question` AND `bsl_formulaire__valeur`.`actif`=1
WHERE `bsl_formulaire`.`actif`=1 AND `bsl_theme`.`libelle_theme`= ? AND `bsl_formulaire__page`.`ordre` = ?
ORDER BY `ordre_page`, `bsl_formulaire__question`.`ordre`, `bsl_formulaire__valeur`.`ordre`';
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'si', $_SESSION['besoin'], $etape);

if (mysqli_stmt_execute($stmt)) {
	mysqli_stmt_store_result($stmt);
	$nb=mysqli_stmt_num_rows($stmt);

	if($nb==0){
		$msg = "Nous ne trouvons pas de formulaire. Recommence s'il te plait.";
	}else {
		mysqli_stmt_bind_result($stmt, $id_formulaire, $nb_pages, $titre, $ordre_page, $aide, $question, $name, $type, $taille, $obligatoire, $libelle, $valeur, $defaut);
		$i=0;
		while (mysqli_stmt_fetch($stmt)) {
			if($i++<1){
				$meta[] = array('id'=>$id_formulaire, 'nb'=>$nb_pages, 'titre'=>$titre, 'etape'=>$ordre_page, 'aide'=>$aide, 'suite'=>($ordre_page<$nb_pages) ? ($ordre_page+1) : 'fin');
			}
			$elements[] = array('que'=>$question, 'name'=>$name, 'type'=>$type, 'tai'=>$taille, 'obl'=>$obligatoire, 'lib'=>$libelle, 'val'=>$valeur, 'def'=>$defaut);
		}
	}
}
mysqli_stmt_close($stmt);

//view
require 'view/formulaire.tpl.php';