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
		Offres de service <?php if (!$flag_actif) echo "archivées"; ?></h2>

	<?php
	if (count($offres) > 0) {
		?>
		<table id="sortable" class="display compact">
			<thead>
			<tr>
				<th>#</th>
				<th>Nom</th><!--<th>Début</th>-->
				<th nowrap>Fin de validité</th>
				<th>Thème</th>
				<th>Professionnel</th>
				<th>Zone</th>
			</tr>
			</thead>
			<tbody>

			<?php
			foreach ($offres as $row) {
				//affichage de la compétence géo du pro (si pas sélection de villes)
				$zone = '';
				switch ($row['competence_geo']) {
					case "territoire":
						$zone = $row['nom_territoire'];
						break;
					case "departemental":
						$zone = "dépt " . $row['nom_departement'];
						break;
					case "regional":
						$zone = "région " . $row['nom_region'];
						break;
				}
				if ($row['zone_selection_villes']) {
					$zone .= "&nbsp;<sup><abbr title=\"sélection de villes\">sv</abbr></sup>";
				}
				?>
				<tr>
					<td style="font-size:smaller"><?= $row['id_offre'] ?></td>
					<td><a href="offre_detail.php?id=<?= $row['id_offre'] ?>"><?= $row['nom_offre'] ?></a></td>
					<!--<td>" . $row['date_debut']. "</td>-->
					<td><span style="display:none"><?=substr($row['date_fin'], 6, 4).substr($row['date_fin'], 3, 2).substr($row['date_fin'], 0, 2) ?></span><!--clé de tri-->
						<?= $row['date_fin'] ?></td>
					<td><?= $row['libelle_theme_court'] ?>
					<?php //recadrage données pré v1
					if($row['competence_geo']=="territoire" && $row['id_territoire']!=$row['id_competence_geo']) { ?>
						<br/><span style="font-size:smaller; color:red">incohérence/territoire</span>
					<?php } ?></td>
					<td><a href="professionnel_detail.php?id=<?= $row['id_professionnel'] ?>"><?= $row['nom_pro'] ?></a></td>
					<td><?= $zone ?></td>
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

	<div style="text-align:left"><a href="offre_liste.php<?= ($flag_actif) ? '?actif=non">Liste des offres archivées' : '">Liste des offres actives'; ?></a></div>
</div>

<div class="button">
	<input type="button" value="Ajouter une offre de service" onclick="javascript:location.href='offre_detail.php'">
</div>
</body>
</html>