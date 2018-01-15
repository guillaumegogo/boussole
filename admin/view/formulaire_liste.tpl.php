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
	<?php include('../src/admin/select_perimetre.inc.php'); ?>

	<h2><small><a href="accueil.php">Accueil</a> ></small> Liste des formulaires <?= ($flag_actif) ? '' : 'inactifs'; ?></h2>
	
	<?php
	if (count($formulaires) > 0) {
	?>
	
		<table id="sortable" class="display compact">
			<thead>
			<tr>
				<th>#</th>
				<th>Thème</th>
				<th>Territoire</th>
				<th>Pages</th>
				<th>Questions</th>
			</tr>
			</thead>
			<tbody>
			<?php
			foreach ($formulaires as $row) { ?>
				<tr>
					<td><a href="formulaire_detail.php?id=<?= (int) $row['id'] ?>">Formulaire <?= (int) $row['id'] ?></a></td>
					<td><?php xecho($row['theme']) ?></td>
					<td><?php xecho(isset($row['nom_territoire'])?$row['nom_territoire']:'national') ?> <?php if(isset($row['ancien_territoire']) && $row['ancien_territoire']!=$row['nom_territoire']){?> <span style="color:red; font-size:smaller">(anciennement <?= isset($row['ancien_territoire'])?$row['ancien_territoire']:'national' ?>)</span><?php } ?></td>
					<td style="text-align:center"><?php xecho($row['nb_pages']) ?></td>
					<td style="text-align:center"><?php xecho($row['nb_questions']) ?></td>
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

	<div style="text-align:left"><a href=<?= ($flag_actif) ? '"?actif=non">Liste des formulaires inactifs' : '"?actif=oui">Liste des formulaires actifs'; ?></a></div>

</div>

<div class="button">
	<input type="button" value="Créer un formulaire" onclick="javascript:location.href='formulaire_detail.php'"> 
</div>
</body>
</html>