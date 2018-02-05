<!DOCTYPE html>
<html>
<head>
	<?php include('view/inc.head.php'); ?>
	
	<script type="text/javascript" language="javascript" src="../src/js/external/jquery/jquery.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
	<script type="text/javascript" charset="utf-8">
		$(document).ready(function () {
			$('table.dataTable').DataTable( {
				paging: false, "info": false
			} );
		});
	</script>
</head>

<body>
<?php include('view/inc.bandeau.php'); ?>

<div class="container">

	<h2><small><a href="accueil.php">Accueil</a> ></small> 
		Statistiques</h2>
		
		<div class="centre">
			<?= ($ecran=='national') ? '<b>' : '<a href="?e=n">' ?>National<?= ($ecran=='national') ? '</b>' : '</a>' ?> - 
			<?= ($ecran=='territorial') ? '<b>' : '<a href="?e=t">' ?>Territorial<?= ($ecran=='territorial') ? '</b>' : '</a>' ?>
		</div>
		
	<?php
	foreach($tableaux_stats as $k=>$tableau){
	
	if (count($tableau[1]) > 0) {
	?>
		<table class="dataTable no-footer" style="margin:2em 0">
			<caption><?= $tableau[0] ?></caption>
			<thead><tr>
				<th style="width:14%"> </th>
	<?php foreach ($entetes[$k] as $mois) { ?>
				<th class="dt-head-center" style="width:6.5%; padding:8px 4px !important;"><?= $mois ?></th>
	<?php } ?>
				<th class="dt-head-center" style="width:8%">Total</th>
			</tr></thead>
			
			<tbody>
	<?php foreach ($totaux_territoriaux[$k] as $terr=>$val) { ?>
			<tr><td><?= $terr ?></td>
	<?php	foreach ($entetes[$k] as $mois) { ?>
				<td class="dt-body-center"><?= (isset($valeurs[$k][$terr][$mois])) ? $valeurs[$k][$terr][$mois] : '' ?></td>
	<?php	} ?>
				<td class="dt-body-center"><b><?= (isset($totaux_territoriaux[$k][$terr])) ? $totaux_territoriaux[$k][$terr] : 0 ?></b></td>
			</tr>
	<?php } ?>
			</tbody>
			
			<tfoot>
			<tr><td><b>Total</b></td>
	<?php foreach ($entetes[$k] as $mois) { ?>
				<td class="dt-head-center"><?= (isset($totaux_mois[$k][$mois])) ? $totaux_mois[$k][$mois] : '' ?></td>
	<?php	} ?>
				<td class="dt-head-center"><b><?= (isset($totaux[$k])) ? $totaux[$k] : 0 ?></b></td>
			</tr>
			</tfoot>
		</table>
	<?php }
	} ?>

</div>
</body>
</html>