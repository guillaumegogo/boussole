<!DOCTYPE html>
<html>
<head>
	<?php include('view/inc.head.php'); ?>
	
	<script type="text/javascript" language="javascript" src="../src/js/external/jquery/jquery.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
	<script type="text/javascript" language="javascript" src="js/dataTables.buttons.min.js"></script>
	<script type="text/javascript" language="javascript" src="js/buttons.html5.min.js"></script>
	<!-- export -->
	<script type="text/javascript" language="javascript" src="//cdn.datatables.net/buttons/1.4.2/js/buttons.flash.min.js"></script>
	<script type="text/javascript" language="javascript" src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
	<script type="text/javascript" language="javascript" src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
	<script type="text/javascript" language="javascript" src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
	<script type="text/javascript" language="javascript" src="//cdn.datatables.net/buttons/1.4.2/js/buttons.print.min.js"></script>
	<script type="text/javascript" charset="utf-8">
		$(document).ready(function () {
			$('#sortable').dataTable( {
				stateSave: true,
				dom: 'lfrtipB',
				buttons: [
					'csv', 'excel', 'pdf', 'print'
				]
			} );
		});
	</script>
	<style>div.dt-buttons { clear: both; float:none; margin: 0 auto !important; width: 20em;  }</style>
	<link rel="stylesheet" type="text/css" href="css/buttons.dataTables.min.css"/>
</head>

<body>
<?php include('view/inc.bandeau.php'); ?>

<div class="container">
	<?php include($path_from_extranet_to_web.'/src/admin/select_perimetre.inc.php'); ?>

	<h2><small><a href="accueil.php">Accueil</a> ></small> 
		Demandes <?= ($flag_traite) ? 'traitées' : 'à traiter' ?></h2>

	<?php
	if (count($demandes) > 0) {
		?>
		<table id="sortable" class="display compact">
			<thead>
			<tr>
				<th>Date de la demande</th>
				<th>Coordonnées</th>
				<th>Offre de service</th>
				<th>Professionnel</th>
				<th><?= ($flag_traite) ? 'Date de traitement' : 'Date de délai'; ?></th>
			</tr>
			</thead>
			<tbody>
			<?php
			foreach ($demandes as $demande) {
				$date_der = ($flag_traite) ? $demande['date_traitement'] : $demande['date_delai'];
				?>
				<tr>
					<td>
						<span style="display:none"><?=strtotime($demande['date_demande']) ?></span><!--clé de tri-->
						<a href="demande_detail.php?id=<?= (int) $demande['id_demande'] ?>"><?= date_format(date_create($demande['date_demande']), 'd/m/Y à H\hi') ?>
					</td>
					<td><?php xecho($demande['contact_jeune']) ?></td>
					<td><?php xecho($demande['nom_offre']) ?></td>
					<td><?php xecho($demande['nom_pro']) ?></td>
					<td <?= (!$flag_traite && time()>strtotime($date_der)) ? 'style="color:red"' : '' ?> >
						<span style="display:none"><?=strtotime($date_der) ?></span><!--clé de tri-->
						<?= date_format(date_create($date_der), 'd/m/Y') ?>
					</td>
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

	<div style="text-align:left"><a href="demande_liste.php<?= ($flag_traite) ? '">Liste des demandes à traiter' : '?etat=traite">Liste des demandes traitées' ?></a></div>

</div>
</body>
</html>