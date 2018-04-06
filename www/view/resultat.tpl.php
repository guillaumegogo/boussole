<!DOCTYPE html>
<html>
<head>
	<?php include('view/inc.head-min.php'); ?>
	<!--<script type="text/javascript" language="javascript" src="js/fix-ie.js"></script>-->
	<script>
		function afficheId(id){
			var x = document.getElementById(id);
			x.style.display = 'block';
		}
		function masqueId(id){
			var x = document.getElementById(id);
			x.style.display = 'none';
		}
		function displayId(id){
			var x = document.getElementById(id);
			if(x.style.display == 'none') {
				x.style.display = 'block';
			}else{
				x.style.display = 'none';
			}
		}
		function masqueClasse(cl){
			var tab = document.getElementsByClassName(cl);
			for(var i=0; i<tab.length; i++){
				tab[i].style.display = 'none';
			}
		}

		window.onclick = function(event) {
			if (event.target.id.substring(0, 5) == 'modal') {
				masqueClasse('modal');
			}
		}

	</script>
</head>
<body><div id="main" class="body-color">
	<?php include('view/inc.header.php'); ?>

	<div class="wrapper container">
		<div class="row bordure-bas">
			<div class="col-md-6 col-sm-6 col-xs-6">
				<div class="retour-page-wrapper">
					<a href="index.php"><img src="img/icon-retour.svg" alt="">Retour à la page d’accueil</a>
				</div>
			</div>
			<div class="col-md-6 col-sm-6 col-xs-6">
				<div class="localisation-wrapper">						
					<img src="img/localisation.svg" alt=""><span><?php xecho($_SESSION['web']['ville_habitee']) ?>, <?php xecho($_SESSION['web']['code_postal']) ?>
					<?php if($_SESSION['web']['nom_territoire']) { ?> <br/><?php xecho($_SESSION['web']['nom_territoire']); } ?></span>
				</div>
			</div>
		</div>
	</div>
	<div class="wrapper container btn-modifier-demande">
		<a href="jesouhaite.php" class="btn-block-inline">
			<img src="img/edit-pen.svg" alt="" >
			<div class="wrapper-modif-btn-texte ">
				<p class="btn-texte-1">modifier mon besoin</p>
                <!--<p class="btn-texte-2">trouver un emploi</p>-->
			</div>				
		</a>
		<a href="formulaire.php?etape=<?= (isset($_SESSION['web']['nb_pages'])) ? $_SESSION['web']['nb_pages'] : 1 ?>" class="btn-block-inline">
			<img src="img/edit-pen.svg" alt="" >
			<div class="wrapper-modif-btn-texte">
				<p class="btn-texte-1">modifier ma situation</p>
                <!--<p class="btn-texte-2">étudiant</p>-->
			</div>				
		</a>			
		<h1><?= (isset($msg)) ? $msg : '' ?></h1>
	</div>
	
<div class="joli wrapper container marge-inf">
<?php
if ($nb_offres) { 
?>
    <?php
	foreach ($sous_themes as $sous_theme_id=>$titre) {
		$nb_offres_sous_theme = count($offres[$sous_theme_id]);
		$ancre="ancre_".$sous_theme_id;
    ?>
	<div class="row">
		<div class="wrapper-titre-catgs">
			<div class="wrapper-titre-de-catg">
				<h2 class="h1resultat" onclick="displayId('<?= $ancre ?>');"><?php xecho($titre) ?></h2>
			</div>
			<div class="wrapper-nbr-rech">
				<span><?= $nb_offres_sous_theme ?></span>
			</div>
		</div>
	</div>
	<div class="wrapper-liste-details-catg">
		<?php
			$i = 0;
			foreach ($offres[$sous_theme_id] as $offre) {
		?>
        <div class="row">
            <div class="wrapper-detail-catg">
                <a href="offre.php?id=<?= (int) $offre['id'] ?>" ><?php xecho(raccourci($offre['titre'],70)) ?> <span>(<?php xecho($offre['ville']) ?>)</span></a>
            </div>
        </div>
    <?php } ?>
    </div>
<?php } 
}else{ ?>

	<div class="wrapper-liste-details-catg">
		<div class="row">
            <div class="wrapper-detail-catg">
                <a href="https://www.cidj.com/nous-rencontrer#map" target="_blank">Contacte le <b>point d'information jeunesse le plus proche de chez toi</b>, il saura certainement t'aider.</a>
            </div>
        </div>
	</div>

<?php } ?>
</div>

<?php include('view/inc.footer.php'); ?>
</body>
</html>

<?php if (DEBUG) { ?>
<!--<pre><?php print_r($offres); echo 'besoin '.$_SESSION['web']['besoin'];?></pre>-->
<?php } ?>