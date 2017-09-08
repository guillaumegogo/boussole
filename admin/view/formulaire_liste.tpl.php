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
<h1 class="bandeau">Administration de la boussole des jeunes</h1>
<div class="statut"><?php xecho($_SESSION['accroche']); ?> (<a href="index.php">déconnexion</a>)</div>

<div class="container">
	<?php echo $select_territoire; ?>

	<h2><small><a href="accueil.php">Accueil</a> ></small> Liste des formulaires <?= ($flag_actif) ? '' : 'inactifs'; ?></h2>

	<p style='color:red; text-align:center;'>Ce module n'est pour le moment disponible qu'en consultation.</p>
	
	<?php
	if (count($formulaires) > 0) {
	?>
	
		<ol style="line-height: 200%;">
	<?php
	foreach ($formulaires as $formulaire) {
	?>
		<li><a href="formulaire_detail.php?id=<?= (int) $formulaire['id'] ?>"><?php xecho($formulaire['libelle']) ?> / <?php xecho($formulaire['territoire']) ?></a></li>
	<?php
	}
	?>
		</ol>

	<?php
	} else {
	?>
		<div style="margin:1em;text-align:center">Aucun résultat</div>
	<?php
	}
	?>

	<!--<div style="text-align:left"><a href="? <?= ($flag_actif) ? 'actif=non">Liste des formulaires inactifs' : 'actif=oui">Liste des formulaires actifs'; ?></a></div>-->

</div>

<div class="button">
	<input type="button" value="Créer un formulaire" disabled onclick="javascript:location.href='formulaire_detail.php'">
</div>
</body>
</html>