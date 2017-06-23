<?php
require('secret/connect.php');
include('inc/functions.php');
session_start();

//********* en attendant une vraie gestion de droits... :)
if (!isset($_SESSION['user_id'])) {
	if (isset($_GET["user_id"])) { 
		switch ($_GET["user_id"]) {
			case "1":
				$_SESSION['user_id'] = 1; $_SESSION['user_statut'] = "administrateur"; $_SESSION['territoire_id'] = 0; 
				$_SESSION['user_droits'] = array('territoire' => '1',  'professionnel' => '1', 'offre' => '1', 'theme' => '1', 'utilisateur' => '1', 'demande' => '1', 'stats' => '1', 'critere' => '1');
				break;
			case "2":
				$_SESSION['user_id'] = 2; $_SESSION['user_statut'] = "animateur territorial"; $_SESSION['territoire_id'] = 1;
				$_SESSION['user_droits'] = array('territoire' => '1',  'professionnel' => '1', 'offre' => '1', 'theme' => '0', 'utilisateur' => '1', 'demande' => '1', 'stats' => '1', 'stats' => '1', 'critere' => '0');
				break;
			case "3": //attention, l'utilisateur 3 (pro) n'a pas le même lien d'accès aux professionnels (cf. plus bas)
				$_SESSION['user_id'] = 3; $_SESSION['user_statut'] = "professionnel"; $_SESSION['user_pro_id'] = 1;
				$_SESSION['user_droits'] = array('territoire' => '0',  'professionnel' => '1', 'offre' => '1', 'theme' => '0', 'utilisateur' => '0', 'demande' => '1', 'stats' => '1', 'stats' => '1', 'critere' => '0');
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
if ($_SESSION['user_statut'] == "administrateur"){
	$sql = "SELECT count(`id_demande`) as nb FROM `bsl_demande` 
		WHERE date_traitement IS NULL"; 
	$result = mysqli_query($conn, $sql);
	$row_dmd = mysqli_fetch_assoc($result);
	$nb_dmd = "";
	if ($row_dmd["nb"]==1) {
		$nb_dmd = "(".$row_dmd["nb"]." nouvelle)";
	}else if ($row_dmd["nb"]>1) {
		$nb_dmd = "(".$row_dmd["nb"]." nouvelles)";
	}
}

//******* construction des listes de lien
$liens_activite ="";
if ($_SESSION['user_droits']['demande']) { $liens_activite .= "<li><a href=\"demande_liste.php\">Demandes reçues</a> ".$nb_dmd."</li>"; }
if ($_SESSION['user_droits']['stats']) { $liens_activite .= "<li>Statistiques</li>"; }
if ($liens_activite) $liens_activite = "<b>Activité</b><ul style=\"line-height:2em;\">".$liens_activite."</ul>";

$liens_admin = "";
if ($_SESSION['user_droits']['professionnel']) { 
	if (isset($_SESSION['user_pro_id'])){
		$liens_admin .= "<li><a href=\"professionnel_detail.php?id=".$_SESSION['user_pro_id']."\">Mes détails</a></li>"; //lien professionnel_detail des utilisateurs "professionnels"
	} else {
		$liens_admin .= "<li><a href=\"professionnel_liste.php\">Professionnels</a></li>";
	}
}
if ($_SESSION['user_droits']['offre']) { $liens_admin .= "<li><a href=\"offre_liste.php\">Offres de service</a></li>"; }
if ($_SESSION['user_droits']['utilisateur']) { $liens_admin .= "<li><a href=\"gestionnaire_liste.php\">Gestionnaires</a></li>"; }
if ($liens_admin) $liens_admin = "<b>Administration</b><ul style=\"line-height:2em;\">".$liens_admin."</ul>";

$liens_reference ="";
if ($_SESSION['user_droits']['territoire']) { $liens_reference .= "<li><a href=\"territoire.php\">Territoires</a></li>"; }
if ($_SESSION['user_droits']['theme']) { $liens_reference .= "<li><a href=\"theme.php\">Thèmes et sous-thèmes</a></li>"; } 
if ($_SESSION['user_droits']['critere']) { $liens_reference .= "<li>Critères</li>"; }
if ($liens_reference) $liens_reference = "<b>Données de référence</b><ul style=\"line-height:2em;\">".$liens_reference."</ul>";

?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width" />
	<link rel="stylesheet" href="css/style_backoffice.css" />
    <link rel="icon" type="image/png" href="img/compass-icon.png" />
	<title>Boussole des jeunes</title>
</head>

<body>
<a href="../web/" target="_blank"><img src="img/external-link.png" class="retour_boussole"></a>
<h1 class="bandeau"><a href="accueil.php">Administration de la boussole des jeunes</a></h1>
<div class="statut"><?php echo $_SESSION['accroche']; ?> (<a href="index.php">déconnexion</a>)</div> 

<div class="container">
<?php echo $select_territoire; ?>

<h2>Modules disponibles</h2>
	<div style="width:100%; text-align:center;">
		<div class="colonne_accueil">
			<?php echo $liens_activite; ?>
		</div>
		
		<div class="colonne_accueil">
			<?php echo $liens_admin; ?>
		</div>
		
		<div class="derniere colonne_accueil">
			<?php echo $liens_reference; ?>
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