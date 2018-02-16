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

<?php 
if($ecrans){ 
	$liens_ecrans=null;
	foreach($ecrans as $titre=>$id){ 
		if($ecran==$id) $liens_ecrans .= '<b>'.$titre.'</b> | ';
		else $liens_ecrans .= '<a href="?e='.$id.'">'.$titre.'</a> | ';
	} ?>
	
	<div class="centre" style="margin-top:-1em;"><?= ($ecrans) ? substr($liens_ecrans, 0, -3) : '' ?>
<?php } ?>

	<?php
	foreach($stats as $tableau){
	?>
		<table class="dataTable no-footer" style="margin:2em 0">
			<caption><?= $tableau['caption'] ?></caption>
			<thead><tr>
				<th style="width:14%"> </th>
	<?php foreach ($tableau['entetes'] as $mois) { ?>
				<th class="dt-head-center" style="width:6.5%; padding:8px 4px !important;"><?= $mois ?></th>
	<?php } ?>
				<th class="dt-head-center" style="width:8%">Total</th>
			</tr></thead>
			
			<tbody>
	<?php foreach ($tableau['totaux_territoriaux'] as $terr=>$val) { ?>
			<tr><td><?= $terr ?></td>
	<?php	foreach ($tableau['entetes'] as $mois) { ?>
				<td class="dt-body-center"><?= (isset($tableau['valeurs'][$terr][$mois])) ? $tableau['valeurs'][$terr][$mois] : '' ?></td>
	<?php	} ?>
				<td class="dt-body-center"><b><?= (isset($tableau['totaux_territoriaux'][$terr])) ? $tableau['totaux_territoriaux'][$terr] : 0 ?></b></td>
			</tr>
	<?php } ?>
			</tbody>
			
			<tfoot>
			<tr><td><b>Total</b></td>
	<?php foreach ($tableau['entetes'] as $mois) { ?>
				<td class="dt-head-center"><?= (isset($tableau['totaux_mois'][$mois])) ? $tableau['totaux_mois'][$mois] : '' ?></td>
	<?php	} ?>
				<td class="dt-head-center"><b><?= (isset($tableau['total'])) ? $tableau['total'] : 0 ?></b></td>
			</tr>
			</tfoot>
		</table>
	<?php } ?>

</div>
</body>
</html>