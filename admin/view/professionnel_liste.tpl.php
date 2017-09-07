<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" href="css/style_backoffice.css"/>
	<link rel="icon" type="image/png" href="img/compass-icon.png"/>
	<link rel="stylesheet" href="css/jquery.dataTables.min.css"/>
	<script type="text/javascript" language="javascript" src="js/jquery-1.12.0.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
	<script type="text/javascript" charset="utf-8">
		$(document).ready(function () {
			$('#sortable').dataTable();
		});
	</script>

	<title>Boussole des jeunes</title>
</head>

<body>
<h1 class="bandeau">Administration de la boussole des jeunes</h1>
<div class="statut"><?php echo $_SESSION['accroche']; ?> (<a href="index.php">déconnexion</a>)</div>

<div class="container">
	<?php echo $select_territoire; ?>

	<h2><small><a href="accueil.php">Accueil</a> ></small> 
		Liste des professionnels <?php if (!$flag_actif) echo "inactifs"; ?></h2>

	<?php
	if (count($pros) > 0) {
		?>
		<table id="sortable">
			<thead>
			<tr>
				<th>Nom</th>
				<th>Type</th>
				<th>Siège</th>
				<th>Thème(s)</th>
				<th><abbr title="Compétence géographique">Compétence</abbr></th>
			</tr>
			</thead>
			<tbody>

			<?php
			foreach ($pros as $row) {
				//colonne "compétence géographique"
				$geo = $row['competence_geo'];
				switch ($row['competence_geo']) {
					case "territoire":
						$geo = $row['nom_territoire'];
						break;
					case "departemental":
						$geo = "dépt " . $row['nom_departement'];
						break;
					case "regional":
						$geo = "région " . $row['nom_region'];
						break;
				}
				?>
				<tr>
					<td><a href="professionnel_detail.php?id=<?= $row['id_professionnel'] ?>"><?= $row['nom_pro'] ?></a>
					</td>
					<td><?= $row['type_pro'] ?></td>
					<td><?php echo $row['ville_pro'] . " (" . $row['code_postal_pro'] . ")"; ?></td>
					<td><?= $row['themes'] ?></td>
					<td><?= $geo ?></td>
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
</body>
</html>