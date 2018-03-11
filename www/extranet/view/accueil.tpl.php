<!DOCTYPE html>
<html>
<head>
	<?php include('view/inc.head.php'); ?>
</head>

<body>
<?php include('view/inc.bandeau.php'); ?>

<div class="container">

	<h2>Accueil</h2>
	
	<table class="accueil">
		<tr>
			<td colspan="2">
			<?php if (isset($activites) && count($activites)) { ?>
				<b>Tableau de bord</b>
				<ul><?php foreach ($activites as $row){ ?>
					<li><a href="<?= $row[0] ?>"><?= $row[1] ?></a> <?= (isset($row[2])) ? $row[2] : '' ?></li>
				<?php } ?></ul>
			<?php } ?>
			</td>
			<td>
			<?php if (isset($animation) && count($animation)) { ?>
				<b>Animation réseau</b>
				<ul><?php foreach ($animation as $row){ ?>
					<li><a href="<?= $row[0] ?>" <?= (substr($row[0],0,4)=="http") ? 'target="_blank"' : ''?> ><?= $row[1] ?></a> <?= (isset($row[2])) ? $row[2] : '' ?></li>
				<?php } ?></ul>
			<?php } ?>
			</td>
		</tr>
		<tr>
			<td>
			<?php if (isset($offres) && count($offres)) { ?>
				<b>Offres</b>
				<ul><?php foreach ($offres as $row){ ?>
					<li><a href="<?= $row[0] ?>"><?= $row[1] ?></a> <?= (isset($row[2])) ? $row[2] : '' ?></li>
				<?php } ?></ul>
			<?php } ?>
			</td>
			<td>
			<?php if (isset($acteurs) && count($acteurs)) { ?>
				<b>Acteurs</b>
				<ul><?php foreach ($acteurs as $row){ ?>
					<li><a href="<?= $row[0] ?>"><?= $row[1] ?></a> <?= (isset($row[2])) ? $row[2] : '' ?></li>
				<?php } ?></ul>
			<?php } ?>
			</td>
			<td>
			<?php if (isset($references) && count($references)) { ?>
				<b>Données de référence</b>
				<ul><?php foreach ($references as $row){ ?>
					<li><a href="<?= $row[0] ?>"><?= $row[1] ?></a> <?= (isset($row[2])) ? $row[2] : '' ?></li>
				<?php } ?></ul>
			<?php } ?>
		</tr>
	</table>
	
</div>
</body>
</html>