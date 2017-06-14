<?php
require('../secret/connect.php');
include('../inc/functions.php');
session_start();

//********* en attendant une vraie gestion de droits... :)
if (!isset($_SESSION['user_id'])) {
	if (isset($_GET["user_id"])) { 
		switch ($_GET["user_id"]) {
			case "1":
				$_SESSION['user_id'] = 1; $_SESSION['user_statut'] = "administrateur"; $_SESSION['territoire_id'] = 0; 
				$_SESSION['user_droits'] = array('territoire' => '1',  'professionnel' => '1', 'offre' => '1', 'theme' => '1', 'utilisateur' => '1', 'demande' => '1');
				break;
			case "2":
				$_SESSION['user_id'] = 2; $_SESSION['user_statut'] = "animateur territorial"; $_SESSION['territoire_id'] = 1;
				$_SESSION['user_droits'] = array('territoire' => '1',  'professionnel' => '1', 'offre' => '1', 'theme' => '0', 'utilisateur' => '1', 'demande' => '1');
				break;
			case "3": //attention, l'utilisateur 3 (pro) n'a pas le même lien d'accès aux professionnels (cf. plus bas)
				$_SESSION['user_id'] = 3; $_SESSION['user_statut'] = "professionnel"; $_SESSION['user_pro_id'] = 1;
				$_SESSION['user_droits'] = array('territoire' => '0',  'professionnel' => '1', 'offre' => '1', 'theme' => '0', 'utilisateur' => '0', 'demande' => '1');
				break;
			default:
				 header('Location: index.php');
		}
	}else{
		header('Location: index.php');
	}
}

//********** sélection territoire
if (isset($_POST["choix_territoire"])) $_SESSION['territoire_id'] = securite_bdd($conn, $_POST["choix_territoire"]); 
include('inc/select_territoires.php');

//********** pro 
if (isset($_SESSION['user_pro_id'])){ 
	$sql = "SELECT `nom_pro` FROM `bsl_professionnel` WHERE `id_professionnel`=".$_SESSION['user_pro_id']; 
	$result = mysqli_query($conn, $sql);
	$row = mysqli_fetch_assoc($result);
	$_SESSION['user_nom_pro'] = $row['nom_pro'];
}

//********** accroche statut
$_SESSION['accroche'] = "Bonjour, vous êtes ".$_SESSION['user_statut'];
if ($_SESSION['user_statut'] == "animateur territorial") $_SESSION['accroche'] .= " (".$nom_territoire_choisi.")";
if ($_SESSION['user_statut'] == "professionnel") $_SESSION['accroche'] .= " (".$_SESSION['user_nom_pro'].")";

//******** nb de demandes 
$nb_dmd = "";
if ($_SESSION['user_statut'] == "administrateur"){  //**** todo : à étendre aux autres statuts
	$sql = "SELECT count(`id_demande`) as nb FROM `bsl_demande` 
		WHERE date_traitement IS NULL"; 
	$result = mysqli_query($conn, $sql);
	$row_dmd = mysqli_fetch_assoc($result);
	$nb_dmd = "";
	if ($row_dmd["nb"]) $nb_dmd = "(".$row_dmd["nb"]." nouvelles)";
}

//******* construction des listes de lien
$liens_gauche ="";
if ($_SESSION['user_droits']['territoire']) { $liens_gauche .= "<li><a href=\"territoire.php\">Territoires</a></li>"; }
if ($_SESSION['user_droits']['professionnel']) { 
	if (isset($_SESSION['user_pro_id'])){
		$liens_gauche .= "<li><a href=\"professionnel_detail.php?id=".$_SESSION['user_pro_id']."\">Mes détails</a></li>"; //lien professionnel_detail des utilisateurs "professionnels"
	} else {
		$liens_gauche .= "<li><a href=\"professionnel_liste.php\">Professionnels</a></li>";
	}
}
if ($_SESSION['user_droits']['offre']) { $liens_gauche .= "<li><a href=\"offre_liste.php\">Offres de service</a></li>"; }
if ($_SESSION['user_droits']['theme']) { $liens_gauche .= "<li><a href=\"theme.php\">Thèmes et sous-thèmes</a></li>"; } 
if ($_SESSION['user_droits']['utilisateur']) { $liens_gauche .= "<li>Utilisateurs</li>"; } //<li>&empty; <a href=\"utilisateur_liste.php\">Utilisateurs</a></li>

$liens_droite ="";
if ($_SESSION['user_droits']['demande']) { $liens_droite .= "<li><a href=\"demande_liste.php\">Demandes reçues</a> ".$nb_dmd."</li>"; }
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width" />
	<link rel="stylesheet" href="css/style_backoffice.css" />
    <link rel="icon" type="image/png" href="../img/compass-icon.png" />
	<title>Boussole des jeunes</title>
</head>

<body>
<a href="../" target="_blank"><img src="img/external-link.png" class="retour_boussole"></a>
<h1 class="bandeau"><a href="accueil.php">Administration de la boussole des jeunes</a></h1>
<div class="statut"><?php echo $_SESSION['accroche']; ?> (<a href="index.php">déconnexion</a>)</div> 

<div class="container">
<?php echo $select_territoire; ?>

<h2>Modules disponibles</h2>
	<div style="width:70%; margin:auto;">
		<div style="display:inline-block; min-width:25em; vertical-align:top;">
			<b>Administration</b>
			<ul style="line-height:2em;">
				<?php echo $liens_gauche; ?>
			</ul>
		</div>
		
		<div style="display:inline-block; min-width:25em; vertical-align:top;">
			<b>Activité</b> 
			<ul style="line-height:2em;">
				<?php echo $liens_droite; ?>
			</ul>
		</div>
	</div>
</div>

<?php 
if ($ENVIRONNEMENT=="LOCAL") {
	echo "<pre>"; print_r(@$_POST); echo "<br/>"; echo @$req; echo "</pre>"; 
}
?>
</body>
</html>