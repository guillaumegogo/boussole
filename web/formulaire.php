<?php
include('secret/connect.php');
include('inc/functions.php');

//********* permet de revenir sur les formulaires sans recharger
header('Cache-Control: no cache'); 
session_cache_limiter('private_no_expire'); 

//********* valeur de sessions
session_start();
if (isset($_POST["besoin"])) { $_SESSION['besoin'] = securite_bdd($conn, $_POST["besoin"]); }
if (isset($_POST["sexe"])) { $_SESSION['sexe'] = securite_bdd($conn, $_POST["sexe"]); }
if (isset($_POST["age"])) { $_SESSION['age'] = securite_bdd($conn, $_POST["age"]); }
if (isset($_POST["europeen"])) { $_SESSION['europeen'] = securite_bdd($conn, $_POST["europeen"]); }
if (isset($_POST["jesais"])) { $_SESSION['jesais'] = securite_bdd($conn, $_POST["jesais"]); }
if (isset($_POST["situation"])) { $_SESSION['situation'] = securite_bdd($conn, $_POST["situation"]); }
if (isset($_POST["etudes"])) { $_SESSION['etudes'] = securite_bdd($conn, $_POST["etudes"]); }
if (isset($_POST["diplome"])) { $_SESSION['diplome'] = securite_bdd($conn, $_POST["diplome"]); }
if (isset($_POST["permis"])) { $_SESSION['permis'] = securite_bdd($conn, $_POST["permis"]); }
if (isset($_POST["handicap"])) { $_SESSION['handicap'] = securite_bdd($conn, $_POST["handicap"]); }
if (isset($_POST["temps_plein"])) { $_SESSION['temps_plein'] = securite_bdd($conn, $_POST["temps_plein"]); }
if (isset($_POST["experience"])) { $_SESSION['experience'] = securite_bdd($conn, $_POST["experience"]); }
//pas de securite_bdd() pour ces 3 là car ce sont des tableaux - on passe la fonction plus bas
if (isset($_POST["secteur"])) { $_SESSION['secteur'] = $_POST["secteur"]; }
if (isset($_POST["type_emploi"])) { $_SESSION['type_emploi'] = $_POST["type_emploi"]; }
if (isset($_POST["inscription"])) { $_SESSION['inscription'] = $_POST["inscription"]; }

//************ si accès direct à la page, renvoi vers l'accueil
if (!isset($_SESSION['ville_habitee']) || !isset($_SESSION['besoin'])) {
	header('Location: index.php');
} else {    
	$message = "<p>J'habite à <b>".$_SESSION['ville_habitee']."</b> et je souhaite <b>".strtolower ($_SESSION['besoin'])."</b>.</p>";
}

//********* etape dans le formulaire : si on a validé la dernière étape on est renvoyé sur la page de résultats
$etape = 1;
if (isset($_POST["etape"])) { 
	$etape = securite_bdd($conn, $_POST["etape"]); 
}
if ($etape=="fin") {
	header('Location: resultat.php');
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="css/style.css" />
	<link rel="icon" type="image/png" href="img/compass-icon.png" />
	<title>Boussole des jeunes</title>
</head>

<body><div id="main">
<div class="bandeau"><a href="index.php">La boussole des jeunes</a></div>
<div class="soustitre"><?php echo $message; ?></div>
    
<?php
switch ($_SESSION['besoin']) {
    case "trouver un emploi":
        include("formulaire-emploi.php");
        break;
    /*case "me loger":
        include('formulaire-logement.php');
        break;*/
    default:
		echo "<p>Ce formulaire n'est pas actif. <a href=\"index.php\">Recommence</a></p>";
}
?>

<div class="lienenbas">&nbsp;</div>

<div style="height:2em;">&nbsp;</div>  <!--tweak css-->

<!--
<?php print_r($_POST); echo "<br/>"; print_r($_SESSION); ?>
-->

<?php include('inc/footer.inc'); ?>
</div>
</body>
</html>