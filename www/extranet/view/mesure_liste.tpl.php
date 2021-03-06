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
	<h2><small><a href="accueil.php">Accueil</a> ></small> 
		Liste des mesures <?php if (!$flag_actif) echo "désactivées"; ?></h2>

	<form method="post">
		<table class="filtres"><tr>
		<th>Filtres</th>
	<?php foreach($questions as $question) { ?>
		<td><?= ucfirst($question['libelle']) ?><br/>
		<select name="criteres[<?= $question['name'] ?>]" onChange="this.form.submit()">
			<option value=''></option>
			<?php foreach($reponses[$question['id']] as $reponse) { ?>
			<option value="<?= $reponse['valeur'] ?>" <?= (isset($_POST['criteres'][$question['name']]) && $_POST['criteres'][$question['name']]==$reponse['valeur']) ? 'selected' : '' ?>><?= $reponse['libelle'] ?></option>
			<?php } ?>
		</select></td>
	<?php } ?>
		<td style="color:#999">Code postal<br/>
		<input type="text" size="5" disabled></td>
	</tr></table></form>
	
	<?php
	if (count($mesures) > 0) {
		?>
		<table id="sortable" class="display compact">
			<thead>
			<tr>
				<th>Nom</th><!--<th>Début</th>-->
				<th nowrap>Fin de validité</th>
				<th>Thème</th>
				<th>Professionnel</th>
				<th>Zone</th>
			</tr>
			</thead>
			<tbody>

			<?php
			foreach ($mesures as $row) {
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
					<td><a href="mesure_detail.php?id=<?= $row['id_mesure'] ?>"><?= $row['nom_mesure'] ?></a></td>
					<!--<td>" . $row['date_debut']. "</td>-->
					<td><?= $row['date_fin'] ?></td>
					<td><?= $row['libelle_theme_court'] ?></td>
					<td><?= $row['nom_pro'] ?></td>
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

	<div style="text-align:left"><a href="mesure_liste.php<?= ($flag_actif) ? '?actif=non">Liste des mesures désactivées' : '">Liste des mesures' ?></a></div>
</div>

<div class="button">
	<input type="button" value="Ajouter une mesure" onclick="javascript:location.href='mesure_detail.php'">
</div>
</body>
</html>