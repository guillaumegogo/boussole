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
			$('#sortable').dataTable( {
				stateSave: true
			} );
		});
	</script>

	<title>Boussole des jeunes</title>
</head>

<body>
<h1 class="bandeau"><img src="../web/img/marianne.png" width="93px"> Administration de la boussole</h1>
<div class="statut"><?php echo $_SESSION["accroche"]; ?> (<a href="index.php">déconnexion</a>)</div>

<div class="container">
	<?php include('view/select_territoires.inc.php'); ?>

	<h2><small><a href="accueil.php">Accueil</a> ></small> 
		Liste des utilisateurs <?php if (!$flag_actif) echo "inactifs"; ?></h2>

	<?php
	if (count($users) > 0) {
		?>
		<table id="sortable" class="display compact">
			<thead>
			<tr>
				<th>Nom</th>
				<th>Courriel</th>
				<th>Statut</th>
				<th>Attache</th>
			</tr>
			</thead>
			<tbody>

			<?php
			foreach ($users as $row) {
				
				$attache = '';
				switch ($row['id_statut']) {
					case '2':
						$attache = $row['nom_territoire'];
						break;
					case '3':
						$attache = $row['nom_pro'];
						break;
				}
				?>
				<tr>
					<td><a href="utilisateur_detail.php?id=<?= $row['id_utilisateur'] ?>"><?= $row['nom_utilisateur'] ?></a></td>
					<td><?= $row['email'] ?></td>
					<td><?= $row['libelle_statut'] ?></td>
					<td><?= $attache ?></td>
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
	<input type="button" value="Ajouter un utilisateur" onclick="javascript:location.href='utilisateur_detail.php'">
</div>
</body>
</html>