<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="icon" type="image/png" href="img/compass-icon.png"/>
	<link rel="stylesheet" href="css/style_backoffice.css"/>
	<link rel="stylesheet" href="css/jquery.dataTables.min.css"/>
	<link rel="stylesheet" type="text/css" href="css/buttons.dataTables.min.css"/>
	<script type="text/javascript" language="javascript" src="js/jquery-1.12.0.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
	<script type="text/javascript" language="javascript" src="js/dataTables.buttons.min.js"></script>
	<script type="text/javascript" language="javascript" src="js/buttons.html5.min.js"></script>
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

	<title>Boussole des jeunes</title>
</head>

<body>
<h1 class="bandeau"><img src="../web/img/marianne.png" width="93px"> Administration de la boussole</h1>
<div class="statut"><?php xecho($_SESSION['accroche']); ?> (<a href="index.php">déconnexion</a>)</div>

<div class="container">
	<?php include('view/select_perimetre.inc.php'); ?>

	<h2><small><a href="accueil.php">Accueil</a> ></small> 
		Liste des demandes <?= ($flag_traite) ? 'traitées' : 'à traiter' ?></h2>

	<?php
	if (count($demandes) > 0) {
		?>
		<table id="sortable" class="display compact">
			<thead>
			<tr>
				<th>Date de la demande</th>
				<th>Coordonnées</th>
				<th>Offre de service</th>
				<th>Professionnel</th><?php echo ($flag_traite) ? "<th>Date de traitement</th>" : ""; ?></tr>
			</thead>
			<tbody>
			<?php
			foreach ($demandes as $demande) {
				?>
				<tr>
					<td>
						<span style="display:none"><?=strtotime($demande['date_demande']) ?></span><!--clé de tri-->
						<a href="demande_detail.php?id=<?= (int) $demande['id_demande'] ?>"><?= date_format(date_create($demande['date_demande']), 'd/m/Y à H\hi') ?>
					</td>
					<td><?php xecho($demande['contact_jeune']) ?></td>
					<td><?php xecho($demande['nom_offre']) ?></td>
					<td><?php xecho($demande['nom_pro']) ?></td>
					<?php echo ($flag_traite) ? "<td>" . date_format(date_create($demande['date_traitement']), 'd/m/Y à H\hi') . "</td>" : ""; ?>
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

	<div style="text-align:left"><a href="demande_liste.php<?= ($flag_traite) ? '">Liste des demandes à traiter' : '?etat=traite">Liste des demandes traitées' ?></a></div>

</div>
</body>
</html>