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
			<td>
				<b>Ma Boussole</b>
				<ul>
					<?= (isset($liens['territoires'])) ? '<li>'.lien($liens['territoires']).'</li>' : '' ?>
					<?= (isset($liens['formulaires'])) ? '<li>'.lien($liens['formulaires']).'</li>' : '' ?>
					<?= (isset($liens['themes'])) ? '<li>'.lien($liens['themes']).'</li>' : '' ?>
					<?= (isset($liens['organismes'])) ? '<li>'.lien($liens['organismes']).'</li>' : '' ?>
					<?= (isset($liens['utilisateurs'])) ? '<li>'.lien($liens['utilisateurs']).'</li>' : '' ?>
					<?= (isset($liens['offres'])) ? '<li>'.lien($liens['offres']).'</li>' : '' ?>
				</ul>
			</td>
			<td>
				<b>Tableau de bord</b>
				<ul>
					<?= (isset($liens['demandes'])) ? '<li>'.lien($liens['demandes']).'</li>' : '' ?>
					<?= (isset($liens['demandes_traitees'])) ? '<li>'.lien($liens['demandes_traitees']).'</li>' : '' ?>
					<?= (isset($liens['recherches'])) ? '<li>'.lien($liens['recherches']).'</li>' : '' ?>
					<?= (isset($liens['stats'])) ? '<li>'.lien($liens['stats']).'</li>' : '' ?>
					<?= (isset($liens['mesures'])) ? '<li>'.lien($liens['mesures']).'</li>' : '' ?>
					<?= (isset($liens['droits'])) ? '<li>'.lien($liens['droits']).'</li>' : '' ?>
				</ul>
			</td>
		</tr>
	</table>
	
	<div class="centre" style="margin-top:3em"><a href="accueil.php">Retour à l'ancien accueil</a></div>
</div>
</body>
</html>