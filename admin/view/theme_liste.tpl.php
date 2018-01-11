<!DOCTYPE html>
<html>
<head>
	<?php include('../src/admin/header.inc.php'); ?>
	
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
<?php include('../src/admin/bandeau.inc.php'); ?>

<div class="container">
	<?php include('../src/admin/select_perimetre.inc.php'); ?>

	<h2><small><a href="accueil.php">Accueil</a> ></small> Liste des thèmes</h2>
	
	<?php
	if (count($themes) > 0) {
	?>
	
		<table id="sortable" class="display compact">
			<thead>
			<tr>
				<th>#</th>
				<th>Thème</th>
				<th>Territoire</th>
				<th>Libellé</th>
				<th>Actif</th>
			</tr>
			</thead>
			<tbody>
			<?php
			foreach ($themes as $row) { ?>
				<tr>
					<td><a href="theme_detail.php?id=<?= (int) $row['id_theme'] ?>">Thème <?= (int) $row['id_theme'] ?></a></td>
					<td><?php xecho($row['libelle_theme_court']) ?></td>
					<td><?php xecho(isset($row['nom_territoire'])?$row['nom_territoire']:'national') ?></td>
					<td><?php xecho($row['libelle_theme']) ?></td>
					<td><?php xecho($row['actif_theme']?'oui':'non') ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>

	<?php
	} else {
	?>
		<div style="margin:1em;text-align:center">Aucun résultat</div>
	<?php
	}
	?>

</div>

<!--<div class="button">
	<input type="button" value="Créer un formulaire" onclick="javascript:location.href='formulaire_detail.php'"> 
</div>-->
</body>
</html>