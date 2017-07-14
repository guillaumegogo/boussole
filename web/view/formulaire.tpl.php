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
$la="";
$ty="";
foreach ($elements as $ele) {

	if ($la!=$ele['name']){ //si première ligne de ce label
		if($ty){ //cloture de la ligne précédente
			switch ($ty) {
			case "checkbox":
				echo "</div>";
				break;
			case "select":
			case "multiple":
				echo "</select>";
				break;
			}
			echo "</div>";
		}
		$la=$ele['name']; 
		$ty=$ele['type']; //récup des valeurs utiles dans des variables temporaires
?>
			<div class="lab">
				<label class="label_long" for="<?= $la ?>"><?= $ele['que'] ?></label>
				<div style="display:inline-block;">
<?php 
		switch ($ty) { //affichage ligne préalable si le type le demande
		case "checkbox":
			echo "<div style=\"display:inline-table;\">";
			break;
		case "select":
			echo "<select name=\"".$la."\">"; 
			break;
		case "multiple":
			echo "<select name=\"".$la."\" size=\"".$ele['tai']."\">";
			break;
		}
	}
	switch ($ty) { //affichage valeur par valeur en fonction du type
	case "radio":
		$t = "<input type=\"radio\" name=\"".$la."\" value=\"".$ele['val']."\" ";
		if (isset($_SESSION[$la])){ if ($_SESSION[$la]==$ele['val']) $t .= " checked "; } else if ($ele['def']==1) $t .= " checked ";
		$t .= "> ".$ele['lib']."\n";
		break;
	case "checkbox":
		$t = "<input type=\"checkbox\" name=\"".$la."\" value=\"".$ele['val']."\" ";
		if (isset($_SESSION[$la])){ if (in_array($ele['val'], $_SESSION[$la])) $t .= " checked "; } else if ($ele['def']==1) $t .= " checked ";
		$t .= "> ".$ele['lib']."</br>\n";
		break;
	case "select":
	case "multiple":
		$t = "<option value=\"".$ele['val']."\" ";
		if (isset($_SESSION[$la])){ if ($_SESSION[$la]==$ele['val']) $t .= " selected "; } else if ($ele['def']==1) $t .= " selected ";
		$t .= "> ".$ele['lib']."</option>\n";
		break;
	}
	echo $t;
} ?>
		<!--</div>
	</div>-->

			<div style="margin-top:2em;"><button type="submit" style="float:right">Je continue</button></div>
		</div>
		
	</fieldset>
</form>

<div class="lienenbas">&nbsp;</div>
<div style="height:2em;">&nbsp;</div>  <!--tweak css-->

<?php 
if ($ENVIRONNEMENT=="LOCAL") {
	echo "<pre>";print_r($_POST); echo "<br/>"; print_r($_SESSION);
print_r($elements_formulaire);echo "</pre>";
}
?>
<?php include('inc/footer.inc.php'); ?>
</div>
</body>
</html>