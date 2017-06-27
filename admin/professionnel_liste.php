<?php
require('secret/connect.php');
include('inc/functions.php');
session_start();

//********* verif des droits
if (!isset($_SESSION['user_id'])) header('Location: index.php'); //si pas connecté, retour à la page de connection
if (!$_SESSION['user_droits']['professionnel']) header('Location: accueil.php'); //si pas les droits, retour à l'accueil

//********* territoire sélectionné
if (isset($_POST["choix_territoire"])) { $_SESSION['territoire_id'] = securite_bdd($conn, $_POST["choix_territoire"]); }
include('inc/select_territoires.inc.php');

//********page actif ou désactivés ?
$flag_actif = (isset($_GET['actif']) && $_GET['actif']=="non") ? 0 : 1;

//********* affichage liste résultats 
//tous les professionnel actifs, du territoire si choisi
$sql = "SELECT `bsl_professionnel`.`id_professionnel`,nom_pro,type_pro,ville_pro,code_postal_pro,GROUP_CONCAT(libelle_theme separator ', ') as themes, competence_geo,  nom_departement, nom_region, nom_territoire  
FROM `bsl_professionnel` 
JOIN `bsl_professionnel_themes` ON `bsl_professionnel_themes`.`id_professionnel`=`bsl_professionnel`.`id_professionnel`
JOIN `bsl_theme` ON `bsl_theme`.`id_theme`=`bsl_professionnel_themes`.`id_theme`
LEFT JOIN `bsl__departement` ON `bsl_professionnel`.`competence_geo`=\"departemental\" AND `bsl__departement`.`id_departement`=`bsl_professionnel`.`id_competence_geo`
LEFT JOIN `bsl__region` ON `bsl_professionnel`.`competence_geo`=\"regional\" AND `bsl__region`.`id_region`=`bsl_professionnel`.`id_competence_geo`
LEFT JOIN `bsl_territoire` ON `bsl_professionnel`.`competence_geo`=\"territoire\" AND `bsl_territoire`.`id_territoire`=`bsl_professionnel`.`id_competence_geo`
WHERE `actif_pro`='".$flag_actif."' ";
if ($_SESSION['territoire_id']) {
	$sql .= "AND `competence_geo`=\"territoire\" AND `id_competence_geo`= ".$_SESSION['territoire_id'];
}
$sql .= " GROUP BY `bsl_professionnel`.`id_professionnel`,nom_pro,type_pro,ville_pro,code_postal_pro,competence_geo";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
	$tableau= "<table id=\"sortable\"><thead> <tr><th>Nom</th><th>Type</th><th>Ville</th><th>Thème(s)</th><th>Compétence géographique</th></tr></thead> <tbody> ";
	
	while($row = mysqli_fetch_assoc($result)) {
		//colonne "compétence géographique"
		$geo = $row["competence_geo"];
		switch ($row["competence_geo"]) {
			case "territoire":
				$geo .= " (".$row["nom_territoire"].")"; break;
			case "departemental":
				$geo .= " (".$row["nom_departement"].")"; break;
			case "regional":
				$geo .= " (".$row["nom_region"].")"; break;
		}
		$tableau.= "<tr><td><a href=\"professionnel_detail.php?id=". $row["id_professionnel"]."\">". $row["nom_pro"]. "</a></td><td>" . $row["type_pro"]. "</td><td>" . $row["ville_pro"]. " (" . $row["code_postal_pro"]. ")</td><td>" . $row["themes"]. "</td><td>" . $geo. "</td></tr>";
	}
	$tableau.= "</tbody></table>";

} else {
	$tableau = "<div style=\"margin:1em;text-align:center\">Aucun résultat</div>";
}

//********** lien actifs/désactivés
$lien_desactives = ($flag_actif) ? "<a href=\"professionnel_liste.php?actif=non\">Liste des professionnels désactivés</a>" : "<a href=\"professionnel_liste.php\">Liste des professionnels actifs</a>";
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="css/style_backoffice.css" />
	<link rel="icon" type="image/png" href="img/compass-icon.png" />	
	<link rel="stylesheet" href="css/jquery.dataTables.min.css" />
	<script type="text/javascript" language="javascript" src="js/jquery-1.12.0.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
	<script type="text/javascript" charset="utf-8">
		$(document).ready(function() {
			$('#sortable').dataTable();
		} );
	</script>

	<title>Boussole des jeunes</title>
</head>

<body>
<h1 class="bandeau"><a href="accueil.php">Administration de la boussole des jeunes</a></h1>
<div class="statut"><?php echo $_SESSION['accroche']; ?> (<a href="index.php">déconnexion</a>)</div> 

<div class="container">
	<?php echo $select_territoire; ?>

	<h2>Liste des professionnels</h2>

	<?php echo $tableau; ?>

	<div style="text-align:left"><?php echo $lien_desactives; ?></div>
</div>

<div class="button">
	<input type="button" value="Ajouter un professionnel" onclick="javascript:location.href='professionnel_detail.php'">
</div>
 
<?php 
if ($ENVIRONNEMENT=="LOCAL") {
	echo "<pre>"; echo @$sql; echo "</pre>"; 
}
?>
</body>
</html>