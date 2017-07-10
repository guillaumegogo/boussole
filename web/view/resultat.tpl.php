<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="css/style.css" />
	<link rel="icon" type="image/png" href="img/compass-icon.png" />
	<title><?php echo ucfirst($titredusite); ?></title>
	<script>
function masqueCriteres(){
	var x = document.getElementById('criteres');
	var y = document.getElementById('fleche_criteres');
	if(x.style.display === 'none') {
		x.style.display = 'block';
		y.innerHTML = "&#9651;"; 
	} else {
		x.style.display = 'none';
		y.innerHTML = "&#9661;"; 
	}
}
	</script>
</head>
<body>
<div id="main">
<div class="bandeau"><div class="titrebandeau"><a href="index.php"><?php echo $titredusite; ?></a></div></div>
<div class="soustitre" style="margin-top:3%"><?php echo $msg; ?></div>
<form class="joli resultat">
<fieldset class="resultat">
	<legend>Rappel de mes informations</legend>
	<div>
		<?php echo $titre_criteres; ?>
		<div id="criteres" style="display:<?php echo ($nb_offres) ? "none":"block"; ?>">
			<div class="colonnes">
				<?php echo $txt_criteres; ?>  <abbr title="A mettre en forme...">&#9888;</abbr>
			</div>
			<div class="enbasadroite">
				<a href="javascript:location.href='formulaire.php'">Revenir au formulaire</a>
			</div>
		</div>
	</div>
</fieldset>
</form>
<form class="joli resultat" style="margin-top:1%;">
<?php 
$soustheme_encours = "";
foreach ($offres as $offre) {
	//*********** découpage des titres trop longs
	if (strlen($offre["titre"]) > 80 ) { 
		if (strpos($offre["titre"]," ",80)) { 
			$offre["titre"] = substr($offre["titre"],0,strpos($offre["titre"]," ",80))."..."; 
		}
	} 
	//*********** séparation par sous thèmes
	if ($offre["sous_theme"]!=$soustheme_encours){
		if ($soustheme_encours) echo "</fieldset>"; /*tweak */
		$soustheme_encours=$offre["sous_theme"];
?>
	<fieldset class="resultat"><legend><?= $soustheme_encours ?></legend>
		<div style="width:100%; margin:auto;">
<?php } ?>
	<!-- affichage des offres -->
	<div class="resultat_offre">
		<!--<div class="coeur">&#9825;</div>-->
		<a href="offre.php?id=<?= $offre["id"] ?>"><b><?= $offre["titre"] ?></b><!--<br/>
		<small><?php
		$desc=strip_tags($offre["description"]);
		echo (strlen($desc) > 80 ) ? substr($desc,0,strpos($desc," ",80))."..." : $offre["description"] ; 
		?></small>--></a>
	</div>
<?php } ?>
	</fieldset>
</form>
<div class="lienenbas">
<?php
	echo $aucune_offre;
?>
</div>
<div style="height:2em;">&nbsp;</div>  <!--tweak css-->
<!--
<?php echo $print_sql."\n"; print_r($_POST); echo "<br/>"; print_r($_SESSION); ?>
-->
<?php include('inc/footer.inc.php'); ?>
</div>
</body>
</html>