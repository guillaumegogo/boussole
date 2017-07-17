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
window.onclick = function(event) {
	/*alert(event.target.id);*/
	if (event.target.id.substring(0, 5) == 'modal') {
		var tabModal = document.getElementsByClassName("modal");
		for(var i=0; i<tabModal.length; i++){
			tabModal[i].style.display = "none";
		}
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
				<?php echo liste_criteres('<br/>'); ?>  <abbr title="A mettre en forme...">&#9888;</abbr>
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
$sous_offre_precedente='';
$compteur_offres=0;
foreach ($offres as $offre) {
	
	//******* affichage des sous thèmes
	if($offre['sous_theme']!=$sous_offre_precedente){
		if($sous_offre_precedente){ 
			//if($compteur_offres>4) echo '</div>';
			echo "</div>\n</fieldset>"; //en cas de changement de sous-thème on ferme le fieldset précédent
		}
		$sous_offre_precedente=$offre['sous_theme'];
		$compteur_offres=0;
?>

	<fieldset class="resultat"><legend><?= $sous_themes[$offre['sous_theme']]['titre'] ?> (<?= $sous_themes[$offre['sous_theme']]['nb'] ?> offre<?= ($sous_themes[$offre['sous_theme']]['nb']>1) ? 's':''; ?>)</legend>
		<div style="width:100%; margin:auto;">
<?php
	}
	
	//******** découpage des titres trop longs
	$titre_court = '';
	if (strlen($offre["titre"]) > 80 ) { 
		if (strpos($offre["titre"]," ",80)) { 
			$titre_court = substr($offre["titre"],0,strpos($offre["titre"]," ",80))."..."; 
		}
	} 
	//if($compteur_offres++==4) echo '<div style="display:none">';
?>
		<!-- affichage des offres -->
		<div class="resultat_offre"><!--
			<div class="coeur">&#9825;</div>-->
			<a href="#" onclick="javascript:document.getElementById('modal<?= $offre["id"] ?>').style.display = 'block';"><b><?= ($titre_court) ? $titre_court : $offre["titre"] ?></b></a>
		</div>
		<!-- fenêtre modale de l'offre -->
		<div id="modal<?= $offre["id"] ?>" class="modal" ><div class="modal-content">
			<span class="close" onclick="javascript:document.getElementById('modal<?= $offre["id"] ?>').style.display = 'none';">&times;</span>
			<b><?= $offre["titre"] ?></b><br/>
			<?= $offre["nom_pro"] ?><br/><br/>
			<?= $offre["description"] ?><br/>
			<div class="button"><a href="offre.php?id=<?= $offre["id"] ?>">En savoir +</a></div>
		</div></div>
<?php 
}
?> 
		</div>
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