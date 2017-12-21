<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="icon" type="image/png" href="img/compass-icon.png"/>
	<link rel="stylesheet" href="css/style_backoffice.css"/>
	<link rel="stylesheet" href="css/jquery.dataTables.min.css"/>
	<script type="text/javascript" language="javascript" src="../src/js/external/jquery/jquery.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
	<script type="text/javascript" charset="utf-8">
		$(document).ready(function () {
			$('#sortable').dataTable( {
				stateSave: true
			} );
		});
	</script>
	<title>Boussole des jeunes</title>
</head>

<body>
<h1 class="bandeau"><img src="../web/img/marianne.png" width="93px"> Administration de la boussole</h1>
<div class="statut"><?php xecho($_SESSION['accroche']); ?> (<a href="index.php">déconnexion</a>)</div>

<div class="container">

	<h2><small><a href="accueil.php">Accueil</a> ></small> 
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
					<td style="text-align:center"><?php xecho($recherche['code_insee']) ?></td>
					<td nowrap><?php xecho($recherche['besoin']) ?></td>
					<td><div style="font-size:0.7em;"><?= str_replace(["_", "\"", "{", "}", "\u20ac"], [" ", " ", "", "", "€"], $recherche['criteres']) //pretty_json_print($recherche['criteres'], 50) ?></div></td> 
					<td style="text-align:center"><?php xecho($recherche['nb_offres']) ?></td>
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