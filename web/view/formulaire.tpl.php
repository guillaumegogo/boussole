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
		<legend><?= $elements_formulaire[0][2] ?> (<?= $elements_formulaire[0][3] ?>/<?= $elements_formulaire[0][1] ?>)</legend>
		<div class="aide"><img src="img/ci_help.png" title="<?= $elements_formulaire[0][5] ?>"></div>
		
		<div class="centreformulaire">
			<input type="hidden" name="etape" value="<?= ($elements_formulaire[0][3]<$elements_formulaire[0][1]) ? ($elements_formulaire[0][3]+1) : "fin" ?>">

<?php 
$la="";
$ty="";
foreach ($elements_formulaire as $ele) {

	if ($la!=$ele[6]){ //si première ligne de ce label
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
		$la=$ele[6]; 
		$ty=$ele[7]; //récup des valeurs utiles dans des variables temporaires
?>
			<div class="lab">
				<label class="label_long" for="<?= $la ?>"><?= $ele[5] ?></label>
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
			echo "<select name=\"".$la."\" size=\"".$ele[8]."\">";
			break;
		}
	}
	switch ($ty) { //affichage valeur par valeur en fonction du type
	case "radio":
		$t = "<input type=\"radio\" name=\"".$la."\" value=\"".$ele[11]."\" ";
		if (isset($_SESSION[$la])){ if ($_SESSION[$la]==$ele[11]) $t .= " checked "; } else if ($ele[12]==1) $t .= " checked ";
		$t .= "> ".$ele[10]."\n";
		break;
	case "checkbox":
		$t = "<input type=\"checkbox\" name=\"".$la."\" value=\"".$ele[11]."\" ";
		if (isset($_SESSION[$la])){ if (in_array($ele[11], $_SESSION[$la])) $t .= " checked "; } else if ($ele[12]==1) $t .= " checked ";
		$t .= "> ".$ele[10]."</br>\n";
		break;
	case "select":
	case "multiple":
		$t = "<option value=\"".$ele[11]."\" ";
		if (isset($_SESSION[$la])){ if ($_SESSION[$la]==$ele[11]) $t .= " selected "; } else if ($ele[12]==1) $t .= " selected ";
		$t .= "> ".$ele[10]."</option>\n";
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