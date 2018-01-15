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

	<h2><small><a href="accueil.php">Accueil</a> ></small> 
		Liste des organismes <?php if (!$flag_actif) echo "inactifs"; ?></h2>

	<?php
	if (count($pros) > 0) {
		?>
		<table id="sortable" class="display compact">
			<thead>
			<tr>
				<th>Nom</th>
				<th>Type</th>
				<th>Siège</th>
				<th>Thème(s)</th>
				<th><abbr title="Compétence géographique">Compétence</abbr></th>
			</tr>
			</thead>
			<tbody>

			<?php
			foreach ($pros as $row) {
				//colonne "compétence géographique"
				$geo = $row['competence_geo'];
				switch ($row['competence_geo']) {
					case "territoire":
						$geo = $row['nom_territoire'];
						break;
					case "departemental":
						$geo = "dépt " . $row['nom_departement'];
						break;
					case "regional":
						$geo = "région " . $row['nom_region'];
						break;
				}
				if ($row['zone_selection_villes']) {
					$geo .= "&nbsp;<sup><abbr title=\"sélection de villes\">sv</abbr></sup>";
				}
				?>
				<tr>
					<td><a href="professionnel_detail.php?id=<?= $row['id_professionnel'] ?>"><?= $row['nom_pro'] ?></a>
					</td>
					<td><?= ($row['type']) ? $row['type'] : $row['type_pro'] ?></td>
					<td><?php echo $row['ville_pro'] . " (" . $row['code_postal_pro'] . ")"; ?></td>
					<td><?= $row['themes'] ?>
					<?php //recadrage données pré v1
					if($row['competence_geo']=="territoire" && $row['id_competence_geo']!=$row['id_territoire']) { ?>
						<br/><span style="color:red">incohérence /territoire</span>
					<?php } ?>
					</td>
					<td><?= $geo ?></td>
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

	<div style="text-align:left"><a href=<?= ($flag_actif) ? '"professionnel_liste.php?actif=non">Liste des professionnels désactivés' : '"professionnel_liste.php">Liste des professionnels actifs' ?></a></div>
</div>

<?php if($check_ajout){ ?>
<div class="button">
	<input type="button" value="Ajouter un organisme" onclick="javascript:location.href='professionnel_detail.php'">
</div>
<?php } ?>
</body>
</html>