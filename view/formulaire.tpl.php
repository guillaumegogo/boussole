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
	<p>J'habite à <b><?=$_SESSION['ville_habitee'];?></b> et je souhaite <b><?=strtolower ($_SESSION['besoin']);?></b>.</p>
</div>

<?php
switch ($_SESSION['besoin']) {
	case 'trouver un emploi':
		require 'view/formulaire_emploi.tpl.php';
		break;
	/*case "me loger":
		require 'formulaire_logement.tpl.php';
		break;*/
	default:
		echo "<p>Ce formulaire n'est pas actif. <a href=\"index.php\">Recommence</a></p>";
}
?>

<div class="lienenbas">&nbsp;</div>

<div style="height:2em;">&nbsp;</div>  <!--tweak css-->

<!--
<?php print_r($_POST); echo "<br/>"; print_r($_SESSION); ?>
-->

<?php include('inc/footer.inc.php'); ?>
</div>
</body>
</html>