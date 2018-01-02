<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Boussole des jeunes</title>
	<link rel="icon" type="image/png" href="img/compass-icon.png"/>
	<link rel="stylesheet" href="css/style_backoffice.css"/>
	<link rel="stylesheet" href="css/jquery.dataTables.min.css"/>
	<script type="text/javascript" language="javascript" src="js/fix-ie.js"></script>
</head>

<body>
<h1 class="bandeau"><img src="../web/img/marianne.png" width="93px"> Administration de la boussole</h1>
<div class="statut"><?php if( isset($_SESSION['admin']['accroche']) ) { xecho($_SESSION['admin']['accroche']); ?> (<a href="index.php">déconnexion</a>) 
		<?php } else { // cas de l'acces direct à la demande depuis le mail ?><a href="index.php">Connexion</a><?php } ?></div>

<div class="container">
	<h2><small><a href="accueil.php">Accueil</a> > <a href="demande_liste.php">Liste de demandes</a> ></small> 
		Détail d'une demande</h2>
	<?php echo $msg; ?>

	<?php
	if ($demande !== null) {
		?>

		<table class="detail" >
			<tr>
				<th>N° demande</th>
				<td><?php xecho($demande['id_demande']); ?></td>
			</tr>
			<tr>
				<th>Date demande</th>
				<td><?php xecho(date_format(date_create($demande['date_demande']), 'd/m/Y à H\hi')); ?></td>
			</tr>
			<tr>
				<th>Coordonnées du demandeur</th>
				<td><?php
					if (filter_var($demande['contact_jeune'], FILTER_VALIDATE_EMAIL)) {
						echo "<a href=\"mailto:\"" . xssafe($demande['contact_jeune']) . ">" . xssafe($demande['contact_jeune']) . "</a>";
					} else {
						xecho($demande['contact_jeune']);
					}
					?></td>
			</tr>
			<tr>
				<th>Offre de service</th>
				<td><?php xecho($demande['nom_offre']); ?></td>
			</tr>
			<tr>
				<th>Professionnel</th>
				<td><?php xecho($demande['nom_pro']); ?></td>
			</tr>
			<tr>
				<th>Critères (profil)</th>
				<td><?php pretty_json_print($demande['profil']); ?></td>
			</tr>
			<tr>
				<th>Traité</th>
				<td>
					<?php if ($demande['date_traitement']) { ?>
					Traité le <?= date_format(date_create($demande['date_traitement']), 'd/m/Y à H\hi')?><br/>
					Commentaire : <?= xssafe($demande['commentaire']) ?>
					
					<?php } else { ?>
					<form method="post" class="detail">
					<input type="hidden" name="id_traite" value=<?= xssafe($demande['id_demande']) ?> />
					<textarea name="commentaire" <?= (!$droit_ecriture) ? 'disabled':'' ?> required style="width:100%" rows="5" placeholder="Conditions et suites données à l'échange (...)"></textarea> 
					<input <?= (!$droit_ecriture) ? 'disabled':'' ?> type="submit" value="Marquer comme traité">
					</form>
					<?php } ?>
			</tr>
		</table>

		<?php
	} else {
		echo "N° de demande non valide.";
	}
	?>

	<div class="button"><a href="demande_liste.php">Retour à la liste des demandes</a></div>

</div>
</body>
</html>