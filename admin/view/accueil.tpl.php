<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta name="viewport" content="width=device-width"/>
	<link rel="stylesheet" href="css/style_backoffice.css"/>
	<link rel="icon" type="image/png" href="img/compass-icon.png"/>
	<title>Boussole des jeunes</title>
</head>

<body>
<a href="../web/" target="_blank"><img src="img/external-link.png" class="retour_boussole"></a>
<h1 class="bandeau">Administration de la boussole des jeunes</h1>
<div class="statut"><?php echo $_SESSION['accroche']; ?> (<a href="index.php">déconnexion</a>)</div>

<div class="container">
	<?php echo $select_territoire; ?>

	<h2>Accueil</h2>
	
	<table class="accueil">
		<tr>
			<td colspan="3">
			<?php if (count($activites)) { ?>
				<b>Tableau de bord</b>
				<ul><?php foreach ($activites as $row){ ?>
					<li><a href="<?= $row[0] ?>"><?= $row[1] ?></a> <?= $row[2] ?></li>
				<?php } ?></ul>
			<?php } ?>
			</td>
		</tr>
		<tr>
			<td>
			<?php if (count($offres)) { ?>
				<b>Offres</b>
				<ul><?php foreach ($offres as $row){ ?>
					<li><a href="<?= $row[0] ?>"><?= $row[1] ?></a> <?= $row[2] ?></li>
				<?php } ?></ul>
			<?php } ?>
			</td>
			<td>
			<?php if (count($acteurs)) { ?>
				<b>Acteurs</b>
				<ul><?php foreach ($acteurs as $row){ ?>
					<li><a href="<?= $row[0] ?>"><?= $row[1] ?></a> <?= $row[2] ?></li>
				<?php } ?></ul>
			<?php } ?>
			</td>
			<td>
			<?php if (count($references)) { ?>
				<b>Données de référence</b>
				<ul><?php foreach ($references as $row){ ?>
					<li><a href="<?= $row[0] ?>"><?= $row[1] ?></a> <?= $row[2] ?></li>
				<?php } ?></ul>
			<?php } ?>
		</tr>
	</table>
</div>
</body>
</html>