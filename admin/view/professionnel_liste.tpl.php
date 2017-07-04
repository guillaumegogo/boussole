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

<?php 
if (mysqli_num_rows($result) > 0) {
?>
	<table id="sortable">
	<thead> <tr><th>Nom</th><th>Type</th><th>Ville</th><th>Thème(s)</th><th>Compétence géographique</th></tr></thead> 
	<tbody>
	
<?php while($row = mysqli_fetch_assoc($result)) {
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
?>
		<tr>
			<td><a href="professionnel_detail.php?id=<?=$row["id_professionnel"];?>"><?=$row["nom_pro"];?></a></td>
			<td><?=$row["type_pro"];?></td>
			<td><?php echo $row["ville_pro"]. " (" . $row["code_postal_pro"]. ")";?></td>
			<td><?=$row["themes"];?></td>
			<td><?=$geo;?></td>
		</tr>
<?php
	}
?>
	</tbody>
	</table>
	
<?php
} else {
?>
	<div style="margin:1em;text-align:center">Aucun résultat</div>
<?php
}
?>

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