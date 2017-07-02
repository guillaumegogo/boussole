<?php
require('secret/connect.php');
include('inc/functions.php');
session_start();

//********* verif des droits
if (!isset($_SESSION['user_id'])) header('Location: index.php');
if (!$_SESSION['user_droits']['offre']) header('Location: accueil.php'); //si pas les droits, retour à l'accueil

//********* territoire sélectionné
if (isset($_POST["choix_territoire"])) { $_SESSION['territoire_id'] = securite_bdd($conn, $_POST["choix_territoire"]); }
include('inc/select_territoires.inc.php');

//********page des offres actives ou désactivées ?
$flag_actif = (isset($_GET['actif']) && $_GET['actif']=="non") ? 0 : 1;

//******** liste des offres de service
$sql = "SELECT id_offre, nom_offre, DATE_FORMAT(`debut_offre`, '%d/%m/%Y') AS date_debut, DATE_FORMAT(`fin_offre`, '%d/%m/%Y') AS date_fin, `theme_pere`.libelle_theme, zone_selection_villes, nom_pro, `competence_geo`, `id_competence_geo`, nom_departement, nom_region, nom_territoire  
	FROM `bsl_offre` 
	JOIN `bsl_professionnel` ON `bsl_professionnel`.id_professionnel=`bsl_offre`.`id_professionnel`
	LEFT JOIN `bsl_theme` ON bsl_theme.id_theme=`bsl_offre`.`id_sous_theme`
	LEFT JOIN `bsl_theme` AS `theme_pere` ON `theme_pere`.id_theme=`bsl_theme`.`id_theme_pere`
	LEFT JOIN `bsl__departement` ON `bsl_professionnel`.`competence_geo`=\"departemental\" AND `bsl__departement`.`id_departement`=`bsl_professionnel`.`id_competence_geo`
	LEFT JOIN `bsl__region` ON `bsl_professionnel`.`competence_geo`=\"regional\" AND `bsl__region`.`id_region`=`bsl_professionnel`.`id_competence_geo`
	LEFT JOIN `bsl_territoire` ON `bsl_professionnel`.`competence_geo`=\"territoire\" AND `bsl_territoire`.`id_territoire`=`bsl_professionnel`.`id_competence_geo`
	WHERE actif_offre='".$flag_actif."' ";
if (isset($_SESSION['territoire_id']) && $_SESSION['territoire_id']) {
	$sql .= "AND `competence_geo`=\"territoire\" AND `id_competence_geo`= ".$_SESSION['territoire_id'];
}
if (isset($_SESSION['user_pro_id'])) {
	$sql .= "AND `bsl_professionnel`.id_professionnel = ".$_SESSION['user_pro_id'];
}
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $tableau = "<table id=\"sortable\"><thead><tr><th>Nom</th><!--<th>Début</th>--><th nowrap>Fin de validité</th><th>Thème</th><th>Professionnel</th><th>Zone</th></tr></thead><tbody>";

    while($row = mysqli_fetch_assoc($result)) {
		//affichage de la compétence géo du pro (si pas sélection de villes)
		if ($row["zone_selection_villes"]) {
			$zone = "sélection de villes";
		} else {
			switch ($row["competence_geo"]) {
				case "territoire":
					$zone = "territoire (".$row["nom_territoire"].")"; break;
				case "departemental":
					$zone = "département (".$row["nom_departement"].")"; break;
				case "regional":
					$zone = "région (".$row["nom_region"].")"; break;
			}
		}
		$tableau .= "<tr><td><a href=\"offre_detail.php?id=". $row["id_offre"]."\">". $row["nom_offre"]. "</a></td><!--<td>" . $row["date_debut"]. "</td>--><td>" . $row["date_fin"]. "</td><td>" . $row["libelle_theme"]. "</td><td>" . $row["nom_pro"]. "</td><td>" . $zone. "</td></tr>";
    }
    $tableau .= "</tbody></table>";

} else {
    $tableau = "<div style=\"margin:1em;text-align:center\">Aucun résultat</div>";
}

//********** lien actifs/désactivés
$lien_desactives = ($flag_actif) ? "<a href=\"offre_liste.php?actif=non\">Liste des offres désactivées</a>" : "<a href=\"offre_liste.php\">Liste des offres actives</a>";
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

	<h2>Liste des offres <?php if(!$flag_actif) echo "désactivées"; ?></h2>

	<?php echo $tableau; ?>

	<div style="text-align:left"><?php echo $lien_desactives; ?></div>
</div>

<div class="button">
	<input type="button" value="Ajouter une offre de service" onclick="javascript:location.href='offre_detail.php'">
</div>
 
<?php 
if ($ENVIRONNEMENT=="LOCAL") {
	echo "<pre>"; echo @$sql;echo "</pre>"; 
}
?>
</body>
</html>