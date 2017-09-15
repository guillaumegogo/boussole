<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="icon" type="image/png" href="img/compass-icon.png"/>
	<link rel="stylesheet" href="css/style_backoffice.css"/>
	<link rel="stylesheet" href="css/jquery.dataTables.min.css"/>
	<script type="text/javascript" language="javascript" src="js/jquery-1.12.0.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
	<script type="text/javascript" charset="utf-8">
		$(document).ready(function () {
			$('#sortable').dataTable( {
				stateSave: true
			} );
		});
	</script>

	<title>Boussole des jeunes</title>
</head>

<body>
<h1 class="bandeau">Administration de la boussole des jeunes</a></h1>
<div class="statut"><?php echo $_SESSION['accroche']; ?> (<a href="index.php">déconnexion</a>)</div>

<div class="container">
	<?php echo $select_territoire; ?>

	<h2><small><a href="accueil.php">Accueil</a> ></small> 
		Liste des offres de service <?php if (!$flag_actif) echo "archivées"; ?></h2>

	<?php
	if (count($offres) > 0) {
		?>
		<table id="sortable" class="display compact">
			<thead>
			<tr>
				<th>Nom</th><!--<th>Début</th>-->
				<th nowrap>Fin de validité</th>
				<th>Thème</th>
				<th>Professionnel</th>
				<th>Zone</th>
			</tr>
			</thead>
			<tbody>

			<?php
			foreach ($offres as $row) {
				//affichage de la compétence géo du pro (si pas sélection de villes)
				$zone = '';
				switch ($row['competence_geo']) {
					case "territoire":
						$zone = $row['nom_territoire'];
						break;
					case "departemental":
						$zone = "dépt " . $row['nom_departement'];
						break;
					case "regional":
						$zone = "région " . $row['nom_region'];
						break;
				}
				if ($row['zone_selection_villes']) {
					$zone .= "&nbsp;<sup><abbr title=\"sélection de villes\">sv</abbr></sup>";
				}
				?>
				<tr>
					<td><a href="offre_detail.php?id=<?= $row['id_offre'] ?>"><?= $row['nom_offre'] ?></a></td>
					<!--<td>" . $row['date_debut']. "</td>-->
					<td><?= $row['date_fin'] ?></td>
					<td><?= $row['libelle_theme_court'] ?></td>
					<td><a href="professionnel_detail.php?id=<?= $row['id_professionnel'] ?>"><?= $row['nom_pro'] ?></a></td>
					<td><?= $zone ?></td>
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

	<div style="text-align:left"><a href="offre_liste.php<?= ($flag_actif) ? '?actif=non">Liste des offres archivées' : '">Liste des offres actives'; ?></a></div>
</div>

<div class="button">
	<input type="button" value="Ajouter une offre de service" onclick="javascript:location.href='offre_detail.php'">
</div>
</body>
</html>