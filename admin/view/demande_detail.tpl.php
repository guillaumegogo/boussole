<!DOCTYPE html>
<html>
<head>
	<?php include('../src/admin/header.inc.php'); ?>
	<script type="text/javascript" language="javascript" src="js/fix-ie.js"></script>
</head>

<body>
<?php include('../src/admin/bandeau.inc.php'); ?>

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