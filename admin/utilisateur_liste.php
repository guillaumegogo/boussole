<?php
require('secret/connect.php');
include('inc/functions.php');
session_start();

//********* verif des droits
if (!isset($_SESSION['user_id'])) header('Location: index.php'); //si pas connecté, retour à la page de connection
if (!$_SESSION['user_droits']['utilisateur']) header('Location: accueil.php'); //si pas de droits de gestion des utilisateurs, retour à l'accueil

//********* territoire sélectionné
if (isset($_POST["choix_territoire"])) { $_SESSION['territoire_id'] = securite_bdd($conn, $_POST["choix_territoire"]); }
include('inc/select_territoires.inc.php');

//********page actif ou désactivés ?
$flag_actif = (isset($_GET['actif']) && $_GET['actif']=="non") ? 0 : 1;

//********* affichage liste résultats 
//tous les utilisateurs actifs, du territoire le cas échéant
$sql = "SELECT *
FROM `bsl_utilisateur` 
JOIN `bsl__statut` ON `bsl__statut`.`id_statut`=`bsl_utilisateur`.`id_statut`
LEFT JOIN `bsl_territoire` ON `bsl_territoire`.`id_territoire`=`bsl_utilisateur`.`id_metier`
LEFT JOIN `bsl_professionnel` ON `bsl_professionnel`.`id_professionnel`=`bsl_utilisateur`.`id_metier`
WHERE `actif_utilisateur`='".$flag_actif."' ";
if ($_SESSION['territoire_id']) {
	$sql .= "AND (`id_statut`==2 AND `id_metier`= ".$_SESSION['territoire_id'].") 
		OR (`id_statut`==3 AND `bsl_professionnel`.`competence_geo`=\"territoire\" AND id_competence_geo=".$_SESSION['territoire_id'].")";
}
$sql .= " ORDER BY `bsl_utilisateur`.`id_statut` ASC,`id_metier` ASC";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
	$tableau= "<table id=\"sortable\"><thead> <tr><th>Nom</th><th>Courriel</th><th>Statut</th><th>Attache</th></tr></thead> <tbody> ";
	
	while($row = mysqli_fetch_assoc($result)) {
		//attache = colonne "compétence géographique"
		$attache = "";
		switch ($row["id_statut"]) {
			case "2":
				$attache = $row["nom_territoire"]; break;
			case "3":
				$attache = $row["nom_pro"]; break;
		}
		$tableau.= "<tr><td><a href=\"utilisateur_detail.php?id=". $row["id_utilisateur"]."\">".$row["nom_utilisateur"]. "</a></td><td>" .$row["email"]. "</td><td>" . $row["libelle_statut"]."</td><td>" . $attache. "</td></tr>";
	}
	$tableau.= "</tbody></table>";

} else {
	$tableau = "<div style=\"margin:1em;text-align:center\">Aucun résultat</div>";
}

//********** lien actifs/désactivés
$lien_desactives = ($flag_actif) ? "<a href=\"utilisateur_liste.php?actif=non\">Liste des utilisateurs désactivés</a>" : "<a href=\"utilisateur_liste.php\">Liste des utilisateurs actifs</a>";
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

	<h2>Liste des utilisateurs</h2>

	<?php echo $tableau; ?>

	<div style="text-align:left"><?php echo $lien_desactives; ?></div>
</div>

<div class="button">
	<input type="button" value="Ajouter un utilisateur" onclick="javascript:location.href='utilisateur_detail.php'">
</div>
 
<?php 
if ($ENVIRONNEMENT=="LOCAL") {
	echo "<pre>"; echo @$sql; echo "</pre>"; 
}
?>
</body>
</html>