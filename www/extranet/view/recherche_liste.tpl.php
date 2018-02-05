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

	<h2><small><a href="accueil.php">Accueil</a> > <a href="statistiques.php">Statistiques</a> ></small> 
		Liste des recherches</h2>
		
	<?php
	if (count($recherches) > 0) {
	?>
		<table id="sortable" class="display compact">
			<thead>
			<tr>
				<th>Date de la recherche</th>
				<th>Code INSEE du demandeur</th>
				<th>Besoin</th>
				<th>Liste des critères</th>
				<th>Nombre d'offres présentées</th>
				<th>Demandes déposées</th>
			</thead>
			<tbody>
			<?php
			foreach ($recherches as $recherche) {
				?>
				<tr>
					<td nowrap>
						<span style="display:none"><?=strtotime($recherche['date_recherche']) ?></span><!--clé de tri-->
						<?= date_format(date_create($recherche['date_recherche']), 'd/m/Y à H\hi') ?> </td>
					<td class="centre"><?php xecho($recherche['code_insee']) ?></td>
					<td nowrap><?php xecho($recherche['besoin']) ?></td>
					<td><div style="font-size:0.7em;"><?= str_replace(["_", "\"", "\\", "{", "}"], [" ", " ", "", "", ""], preg_replace('/u([\da-fA-F]{4})/', '&#x\1;', $recherche['criteres'])) //pretty_json_print($recherche['criteres'], 50) ?></div></td> 
					<td class="centre"><?php xecho($recherche['nb_offres']) ?></td>
					<td><?php 
					$liste_demande_id = explode(",", $recherche['demandes']);
					foreach($liste_demande_id as $id) { ?>
						<a href="demande_detail.php?id=<?= $id ?>"><?= $id ?></a> 
					<?php } ?></td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>

		<?php
	}
	?>

</div>
</body>
</html>