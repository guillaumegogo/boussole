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

	<h2><small><a href="accueil.php">Accueil</a> ></small> 
		Listes de références</h2>

	<?php
	if (count($rows) > 0) {
		?>
		<table id="sortable" class="display compact">
			<thead>
			<tr>
				<th>Liste</th>
				<th>Libellé</th>
			</tr>
			</thead>
			<tbody>

			<?php
			foreach ($rows as $row) {
				?>
				<tr>
					<td><?= $row['liste'] ?></td>
					<td><?= $row['libelle'] ?> <!--<?= $row['id'] ?>--></td>
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

</div>

<div class="button">
	<input type="button" value="Ajouter une valeur aux listes de référence" onclick="javascript:location.href='reference_detail.php'">
</div>
</body>
</html>