<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="css/style.css" />
	<link rel="icon" type="image/png" href="img/compass-icon.png" />
	<title><?php echo ucfirst($titredusite); ?></title>
</head>
<body><div id="main">
<div class="bandeau"><div class="titrebandeau"><a href="index.php"><?php echo $titredusite; ?></a></div></div>
<div class="soustitre">
	<p>J'habite à <b><?= $_SESSION['ville_habitee'] ?></b> et je souhaite <b><?= strtolower ($_SESSION['besoin']) ?></b>.</p>
</div>
<div class="soustitre" style="margin-top:3%"><?php echo $msg; ?></div>

<form action="formulaire.php" method="post" class="joli formulaire">

	<fieldset class="formulaire">
		<legend><?= $meta['titre'] ?> (<?= $meta['etape'] ?>/<?= $meta['nb'] ?>)</legend>
		<div class="aide"><img src="img/ci_help.png" title="<?= $meta['aide'] ?>"></div>
		
		<div class="centreformulaire">
			<input type="hidden" name="etape" value="<?= $meta['suite'] ?>">

<?php
$label_precedent="";
$type_precedent="";
foreach ($elements as $element) {

	if ($label_precedent!=$element['name']){ //si première ligne de ce label
		if ($type_precedent) echo cloture_ligne_precedente($type_precedent);
		$label_precedent=$element['name']; //récup des valeurs utiles dans des variables temporaires 
		$type_precedent=$element['type'];
?>
			<div class="lab">
				<label class="label_long" for="<?= $element['name'] ?>"><?= $element['que'] ?></label>
				<div style="display:inline-block;">
<?php
		echo ouverture_ligne($element);
	}
	
	echo affiche_valeur($element);
} 
echo cloture_ligne_precedente($type_precedent);
?>
			<div style="margin-top:2em;"><button type="submit" style="float:right">Je continue</button></div>
		</div>
		
	</fieldset>
</form>

<div class="lienenbas">&nbsp;</div>
<div style="height:2em;">&nbsp;</div>  <!--tweak css-->

<?php 
if ($ENVIRONNEMENT=="LOCAL") {
	echo "<pre>";print_r($_POST); echo "<br/>"; print_r($_SESSION); print_r($elements);echo "</pre>";
}
?>
<?php include('inc/footer.inc.php'); ?>
</div>
</body>
</html>