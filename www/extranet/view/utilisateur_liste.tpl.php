<!DOCTYPE html>
<html>
<head>
	<?php include('view/inc.head.php'); ?>
	
	<script type="text/javascript" language="javascript" src="../src/js/external/jquery/jquery.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
	<script type="text/javascript" charset="utf-8">
		$(document).ready(function () {
			$('#sortable').dataTable( {
				stateSave: true
			} );
		});
	</script>
</head>

<body>
<?php include('view/inc.bandeau.php'); ?>

<div class="container">
	<?php include($path_from_extranet_to_web.'/src/admin/select_perimetre.inc.php'); ?>

	<h2><small><a href="accueil.php">Accueil</a> ></small> 
		Utilisateurs <?php if (!$flag_actif) echo "inactifs"; ?></h2>

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
		<div class="centre margin1">Aucun résultat</div>
		<?php
	}
	?>

	<div style="text-align:left"><a href="utilisateur_liste.php<?= ($flag_actif) ? '?actif=non':''?>">Liste des utilisateurs <?= ($flag_actif) ? 'inactifs':''?></a></div>
</div>

<div class="button">
	<input type="button" value="Ajouter un utilisateur" onclick="javascript:location.href='utilisateur_detail.php'">
</div>
</body>
</html>