<?php
include('secret/connect.php');
include('inc/functions.php');
include('inc/variables.php');

//********* permet de revenir sur les formulaires sans recharger
header('Cache-Control: no cache'); 
session_cache_limiter('private_no_expire'); 

//********* valeur de sessions
session_start();
if (isset($_POST["besoin"])) { $_SESSION['besoin'] = $_POST["besoin"]; }
if (isset($_POST["sexe"])) { $_SESSION['sexe'] = $_POST["sexe"]; }
if (isset($_POST["age"])) { $_SESSION['age'] = $_POST["age"]; }
if (isset($_POST["nationalite"])) { $_SESSION['nationalite'] = $_POST["nationalite"]; }
if (isset($_POST["jesais"])) { $_SESSION['jesais'] = $_POST["jesais"]; }
if (isset($_POST["situation"])) { $_SESSION['situation'] = $_POST["situation"]; }
if (isset($_POST["etudes"])) { $_SESSION['etudes'] = $_POST["etudes"]; }
if (isset($_POST["diplome"])) { $_SESSION['diplome'] = $_POST["diplome"]; }
if (isset($_POST["permis"])) { $_SESSION['permis'] = $_POST["permis"]; }
if (isset($_POST["handicap"])) { $_SESSION['handicap'] = $_POST["handicap"]; }
if (isset($_POST["temps_plein"])) { $_SESSION['temps_plein'] = $_POST["temps_plein"]; }
if (isset($_POST["experience"])) { $_SESSION['experience'] = $_POST["experience"]; }
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
	$etape = $_POST["etape"]; 
}
if ($etape=="fin") {
	header('Location: resultat.php');
}

//******** présentation des questions du formulaire
function affiche_formulaire($sujet, $tab){
	$t ="";
	if($tab[$sujet]["type"]=="radio"){
		foreach($tab[$sujet] as $key => $value){
			if ($key=="type") continue; //pour passer le "type"
			$t .= "<input type=\"radio\" name=\"".$sujet."\" value=\"".$key."\" ";
			if (isset($_SESSION[$sujet]) && $_SESSION[$sujet]==$key) $t .= " checked ";
			$t .= "> ".$value."\n";
		}
	}else if($tab[$sujet]["type"]=="checkbox"){
		$t ="<div style=\"display:inline-table;\">";
		foreach($tab[$sujet] as $key => $value){
			if ($key=="type") continue;
			$t .= "<input type=\"checkbox\" name=\"".$sujet."[]\" value=\"".$key."\" ";
			if (isset($_SESSION[$sujet]) && in_array($key, $_SESSION[$sujet])) $t .= " checked ";
			$t .= "> ".$value."</br>\n";
		}
	}else if($tab[$sujet]["type"]=="select"){
		$t ="<select name=\"".$sujet."\">";
		foreach($tab[$sujet] as $key => $value){
			if ($key=="type") continue;
			$t .= "<option value=\"".$key."\" ";
			if (isset($_SESSION[$sujet]) && $_SESSION[$sujet]==$key) $t .= " selected ";
			$t .= "> ".$value."</option>\n";
		}
		$t .="</select>";
	}else if($tab[$sujet]["type"]=="multiple"){
		$t ="<select name=\"".$sujet."[]\" size=\"".$tab[$sujet]["size"]."\">";
		foreach($tab[$sujet] as $key => $value){
			if ($key=="type" || $key=="size") continue;
			$t .= "<option value=\"".$key."\" ";
			if (isset($_SESSION[$sujet]) && $_SESSION[$sujet]==$key) $t .= " selected ";
			$t .= "> ".$value."</option>\n";
		}
		$t .="</select>";
	}else if($tab[$sujet]["type"]=="age"){
		$t ="<select name=\"".$sujet."\" class=\"age\">";
		for ($i = $tab[$sujet]["min"]; $i <= $tab[$sujet]["max"]; $i++) {
			$t .= "<option value=\"".$i."\"";
			if (isset($_SESSION['age']) && $i==$_SESSION['age']) $t .= " selected ";
			else if ($i==$tab[$sujet]["defaut"]) $t .= " selected ";
			$t .= ">".$i."</option>\n";
		}
		$t .= "</select> ans";
	}
	return $t;
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="css/style.css" />
	<link rel="icon" type="image/png" href="img/compass-icon.png" />
	<title><?php echo $titredusite; ?></title>
</head>

<body><div id="main">
<div class="bandeau"><div class="titrebandeau"><a href="index.php"><?php echo $titredusite; ?></a></div></div>
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

<?php include('inc/footer.inc.php'); ?>
</div>
</body>
</html>