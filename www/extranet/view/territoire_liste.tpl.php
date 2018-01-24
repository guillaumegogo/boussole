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
		Liste des territoires <?php if (!$flag_actif) echo "inactifs"; ?></h2>

	<?php
	if (count($territoires) > 0) {
		?>
		<table id="sortable" class="display compact">
			<thead>
			<tr>
				<th>Nom</th>
				<th>Département(s)</th>
				<th>Nombre de villes</th>
			</tr>
			</thead>
			<tbody>

			<?php
			foreach ($territoires as $row) {
				?>
				<tr>
					<td><a href="territoire_detail.php?id=<?= $row['id_territoire'] ?>"><?= $row['nom_territoire'] ?></a></td>
					<td><?= $row['dep'] ?></td>
					<td><?= $row['c'] ?></td>
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

	<div style="text-align:left"><a href=<?= ($flag_actif) ? '"?actif=non">Liste des territoires désactivés' : '"?actif=oui">Liste des territoires actifs' ?></a></div>
</div>

<?php if($check_ajout){ ?>
<div class="button">
	<input type="button" value="Créer un nouveau territoire" onclick="javascript:location.href='territoire_detail.php'">
</div>
<?php } ?>
</body>
</html>