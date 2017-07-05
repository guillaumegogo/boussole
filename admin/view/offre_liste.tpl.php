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

<?php
if (mysqli_num_rows($result) > 0) {
?>
	<table id="sortable">
	<thead><tr><th>Nom</th><!--<th>Début</th>--><th nowrap>Fin de validité</th><th>Thème</th><th>Professionnel</th><th>Zone</th></tr></thead>
	<tbody>

<?php
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
?>
		<tr>
			<td><a href="offre_detail.php?id=<?=$row["id_offre"];?>"><?=$row["nom_offre"];?></a></td>
			<!--<td>" . $row["date_debut"]. "</td>--><td><?=$row["date_fin"];?></td>
			<td><?=$row["libelle_theme"];?></td>
			<td><?=$row["nom_pro"];?></td>
			<td><?=$zone;?></td>
		</tr>
<?php
	}
?>
	</tbody></table>
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
	<input type="button" value="Ajouter une offre de service" onclick="javascript:location.href='offre_detail.php'">
</div>
 
<?php 
if ($ENVIRONNEMENT=="LOCAL") {
	echo "<pre>"; echo @$sql;echo "</pre>"; 
}
?>
</body>
</html>