<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" href="css/style_backoffice.css"/>
	<link rel="stylesheet" href="css/jquery.dataTables.min.css"/>
	<link rel="icon" type="image/png" href="img/compass-icon.png"/>
	<script type="text/javascript" language="javascript" src="js/jquery-1.12.0.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
	<title>Boussole des jeunes</title>
</head>

<body>
<h1 class="bandeau"><img src="../web/img/marianne.png" width="93px"> Administration de la boussole</h1>
<div class="statut"><?php xecho($_SESSION['accroche']); ?> (<a href="index.php">déconnexion</a>)</div>

<div class="container">
<?php
if ($meta !== null) {
?>

	<h2><small><a href="accueil.php">Accueil</a> > <a href="formulaire_liste.php">Liste des formulaires</a> ></small>
		Détail d'une page</h2> 

	<?php echo $msg; ?>

	<?php
	if (count($pages) > 0) {
	?>
	
	<div>
	<div style="float:left; width:25%; min-width:30em;">
	<h3>Meta</h3>
	<pre><?php print_r($page) ?></pre>
	
	<?php
	if (count($questions) > 0) {
	?>
	<div style="float:left; width:auto;">
	<h3>Questions</h3>
		<table class="dataTable display compact">
			<thead>
			<tr>
				<th>Ordre</th>
				<th>Identifiant</th>
				<th>Libellé</th>
			</tr>
			</thead>
			<tbody>
			<?php
			foreach ($questions[$page['id']] as $question) {
			?>
			<tr>
				<td><?php xecho($question['ordre']) ?></td>
				<td><a href="formulaire_question.php?id=<?= (int) $question['id'] ?>"><?php xecho($question['name']) ?></a></td>
				<td><?php xecho($question['libelle']) ?></td>
			</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
	</div>

	<?php
	} else {
	?>
	<div style="margin:1em;text-align:center">Aucun résultat</div>
	<?php
	}
	?>
	
<?php
} else {
	echo "N° de demande non valide.";
}
?>
		
	<!--<div class="button">
		<input type="button" value="Ajouter une page" onclick="javascript:location.href='formulaire_xxx.php?f=<?= $meta['id'] ?>'">
		<input type="button" value="Ajouter une question" onclick="javascript:location.href='formulaire_question.php?f=<?= $meta['id'] ?>'">
	</div>-->

	<div style="clear:both" class="button"><a href="formulaire_liste.php">Retour à la liste des formulaires</a></div>
</div>
</body>
</html>