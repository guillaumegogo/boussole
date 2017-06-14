<?php
require('../secret/connect.php');
include('../inc/functions.php');
session_start();

//********* verif des droits
if (!isset($_SESSION['user_id'])) header('Location: index.php');

//******** liste des thèmes
$sql = "SELECT * FROM `bsl_theme` WHERE id_theme_pere IS NULL ORDER BY actif_theme DESC, libelle_theme";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
	$tableau = "<table id=\"sortable\"><thead><tr><th>Libellé</th><th>Actif</th></tr></thead><tbody>";
	while($row = mysqli_fetch_assoc($result)) {
		$flag = $row["actif_theme"] ? "oui" : "non";
		$tableau .= "<tr><td><a href=\"theme_detail.php?id=". $row["id_theme"]."\">". $row["libelle_theme"]. "</a></td><td>" . $flag. "</td></tr>";
	}
	$tableau .= "</tbody></table>";

} else {
    $tableau = "<div style=\"margin:1em;text-align:center\">Aucun résultat</div>";
}
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
	<h2>Liste des thèmes</h2>

	<?php echo $tableau; ?>
</div>

<div class="button">
	<input type="button" value="Ajouter un thème" onclick="javascript:location.href='theme_detail.php'">
</div>
 
<?php 
if ($ENVIRONNEMENT=="LOCAL") {
	echo "<pre>"; echo @$sql;echo "</pre>"; 
}
?>
</body>
</html>