<?php
require('secret/connect.php');
include('inc/functions.php');
session_start();

//********* verif des droits
if (!isset($_SESSION['user_id'])) header('Location: index.php');

//********* territoire sélectionné
if (isset($_POST["choix_territoire"])) { $_SESSION['territoire_id'] = securite_bdd($conn, $_POST["choix_territoire"]); }
include('inc/select_territoires.php');

//********page des demandes traitées ou à traiter ?
$flag_traite = (isset($_GET['etat']) && $_GET['etat']=="traite") ? 1 : 0;

//******** liste de demandes
$sql = "SELECT `id_demande`, `date_demande`, `date_traitement`, `contact_jeune`, `profil`, bsl_offre.nom_offre, bsl_offre.id_professionnel, bsl_professionnel.nom_pro   
    FROM `bsl_demande` JOIN bsl_offre ON bsl_offre.id_offre=bsl_demande.id_offre JOIN bsl_professionnel ON bsl_offre.id_professionnel=bsl_professionnel.id_professionnel
	WHERE 1 ";
if ($flag_traite) {
	$sql .= "AND date_traitement IS NOT NULL ";
}else{
	$sql .= "AND date_traitement IS NULL ";
}
if (isset($_SESSION['territoire_id']) && $_SESSION['territoire_id']) {
	$sql .= "AND bsl_professionnel.`competence_geo`=\"territoire\" AND bsl_professionnel.`id_competence_geo`= ".$_SESSION['territoire_id'];
}
if (isset($_SESSION['user_pro_id'])) {
	$sql .= "AND `bsl_offre`.id_professionnel = ".$_SESSION['user_pro_id'];
}
$sql .= " ORDER BY date_demande DESC";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
	$titretraite = ($flag_traite) ? "<th>Date de traitement</th>" : "";
    $tableau = "<table id=\"sortable\"><thead><tr><th>Date de la demande</th><th>Coordonnées</th><th>Offre de service</th><th>Professionnel</th>".$titretraite."</tr></thead><tbody>";
    
    while($row = mysqli_fetch_assoc($result)) {
        $traitele = ($flag_traite) ? "<td>" . date_format(date_create($row["date_traitement"]), 'd-m-Y à H\hi'). "</td>" : "";        
		$tableau .= "<tr><td><a href=\"demande_detail.php?id=". $row["id_demande"]."\">" . date_format(date_create($row["date_demande"]), 'd-m-Y à H\hi'). "</td><td>" . $row["contact_jeune"]. "</td><td>" . $row["nom_offre"]. "</td><td>" . $row["nom_pro"]. "</td>" . $traitele . "</tr>";
    }
    $tableau .= "</tbody></table>";
    
} else {
    $tableau = "<div style=\"margin:1em;text-align:center\">Aucun résultat</div>";
}

//********** lien actifs/désactivés
$titre_page = ($flag_traite) ? "Liste des demandes traitées" : "Liste des demandes à traiter";
$lien_traites = ($flag_traite) ? "<a href=\"demande_liste.php\">Liste des demandes à traiter</a>" : "<a href=\"demande_liste.php?etat=traite\">Liste des demandes traitées</a>";
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="css/style_backoffice.css" />
    <link rel="icon" type="image/png" href="../img/compass-icon.png" />
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

<h2><?php echo $titre_page; ?></h2>

<?php echo $tableau; ?>

<div style="text-align:left"><?php echo $lien_traites; ?></div>

</div>
<?php 
if ($ENVIRONNEMENT=="LOCAL") {
	echo "<pre>"; echo @$sql;echo "</pre>"; 
}
?>
</body>
</html>
