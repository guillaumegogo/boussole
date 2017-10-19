<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" href="css/style_backoffice.css"/>
	<link rel="icon" type="image/png" href="img/compass-icon.png"/>
	<link rel="stylesheet" href="css/jquery.dataTables.min.css"/>
	<script type="text/javascript" language="javascript" src="js/jquery-1.12.0.js"></script>
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
<div class="statut"><?php echo $_SESSION['accroche']; ?> (<a href="index.php">déconnexion</a>)</div>

<div class="container">

	<h2><small><a href="accueil.php">Accueil</a> ></small> 
		Liste des territoires <?php if (!$flag_actif) echo "inactifs"; ?></h2>

	<?php
	if (count($territoires) > 0) {
		?>
		<table id="sortable" class="display compact">
			<thead>
			<tr>
				<th>Nom</th>
				<th>Département(s)</th>
				<th>Nombre de villes</th>
			</tr>
			</thead>
			<tbody>

			<?php
			foreach ($territoires as $row) {
				?>
				<tr>
					<td><a href="territoire_detail.php?id=<?= $row['id_territoire'] ?>"><?= $row['nom_territoire'] ?></a></td>
					<td><?= $row['dep'] ?></td>
					<td><?= $row['c'] ?></td>
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

	<div style="text-align:left"><a href=<?= ($flag_actif) ? '"?actif=non">Liste des territoires désactivés' : '"?actif=oui">Liste des territoires actifs' ?></a></div>
</div>

<?php if($check_ajout){ ?>
<div class="button">
	<input type="button" value="Créer un nouveau territoire" onclick="javascript:location.href='territoire_detail.php'">
</div>
<?php } ?>
</body>
</html>