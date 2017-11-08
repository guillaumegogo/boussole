<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="css/style.css" />
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css" media="all">
    <link rel="stylesheet" type="text/css" href="css/bootstrap-theme.css">
	<link rel="icon" type="image/png" href="img/compass-icon.png" />
	<title><?php xecho(ucfirst($titredusite)); ?></title>
	<script type="text/javascript" language="javascript" src="js/fix-ie.js"></script>
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
		function afficheSuite(id){
			var x = document.getElementById('suite'+id);
			var y = document.getElementById('lien'+id);
			if(x.style.display === 'none') {
				x.style.display = 'block';
				y.style.display = 'none'; //y.innerHTML = 'Masquer les autres offres';
			}/* else {
				x.style.display = 'none';
				y.innerHTML = 'Afficher les autres offres';
			}*/
		}
		window.onclick = function(event) {
			if (event.target.id.substring(0, 5) == 'modal') {
				masqueClasse('modal');
			}
		}
		/*function masqueCriteres(){
			var x = document.getElementById('criteres');
			var y = document.getElementById('fleche_criteres');
			if(x.style.display === 'none') {
				x.style.display = 'block';
				y.innerHTML = "&#9652;"; //flèche vers le haut
			} else {
				x.style.display = 'none';
				y.innerHTML = "&#9662;"; //flèche vers le bas
			}
		}*/
	</script>
</head>
<body><div id="main" class="body-color">
	<?php include('../src/web/header.inc.php'); ?>

	<div class="wrapper container">
		<div class="row bordure-bas">
			<div class="col-md-6 col-sm-6 col-xs-6">
				<div class="retour-page-wrapper">
					<a href="etape3.html"><img src="img/icon-retour.svg" alt="">Retour à la page d’accueil</a>
				</div>
			</div>
			<div class="col-md-6 col-sm-6 col-xs-6">
				<div class="localisation-wrapper">						
					<img src="img/localisation.svg" alt=""><span>Grenoble, 38000</span> 			
				</div>
			</div>				
		</div>
	</div>
	<div class="wrapper container btn-modifier-demande">
		<a href="#" class="btn-block-inline">
			<img src="img/edit-pen.svg" alt="" >
			<div class="wrapper-modif-btn-texte ">
				<p class="btn-texte-1">modifier ma demande</p>
				<p class="btn-texte-2">trouver un emploi</p>
			</div>				
		</a>
		<a href="#" class="btn-block-inline">
			<img src="img/edit-pen.svg" alt="" >
			<div class="wrapper-modif-btn-texte">
				<p class="btn-texte-1">modifier ma situation</p>
				<p class="btn-texte-2">étudiant</p>
			</div>				
		</a>			
		<h1>33 offres correspondent à ma recherche.</h1>
	</div>
	
<?php
if ($nb_offres) { 
?>
<div class="joli wrapper container marge-inf">
		
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
				// découpage des titres trop longs
				$titre = ((strlen($offre["titre"]) > 80 ) && (strpos($offre["titre"]," ",80))) ? 
					substr($offre["titre"],0,strpos($offre["titre"]," ",80))."…" : $offre["titre"];
				$description_courte = preg_replace(array('/\[br\]\[br\]/is','/\[img\](.*?)\[\/img\]/is'),array('[br]',''),$offre["description"]);
				$description_courte = ((strlen($description_courte) > 1500 ) && (strpos($description_courte," ",1500))) ? 
					substr($description_courte,0,strpos($description_courte," ",1500))."…" : $description_courte;;
				
				if (++$i == $nb_offres_a_afficher+1){ 
		?>
	</div>
	<div id="suite<?= $sous_theme_id?>" style="display:none">
	<?php 		} ?> 
		<div class="row">
			<div class="wrapper-detail-catg">
				<a href="offre.php?id=<?= (int) $offre['id'] ?>" ><?php xecho($titre) ?></a>
			</div>
		</div>
				<!-- <div id="modal<?= (int) $offre['id'] ?>" class="modal" >
					<div class="modal-content" style="display:table;">
						<div style="display:table-cell;height:100%; width:2em; vertical-align:middle;">
						<?php if(isset($offres[$sous_theme_id][$i-2]['id'])){ ?>
							<img src="img/left.png" alt="Offre précédente" onclick="masqueId('modal<?= (int) $offre['id'] ?>');afficheId('modal<?= (int) $offres[$sous_theme_id][$i-2]['id'] ?>');">
						<?php } ?>
						</div>
						<span class="close" onclick="masqueId('modal<?= (int) $offre['id'] ?>');">&times;</span>
						
						<p style="margin-top:0;"><b><?php xecho($offre["titre"]) ?></b><br/><?= xecho($offre['nom_pro']) ?> - <?= xecho($offre['ville']) ?></p>
						<p style="font-size:90%;"><?php xbbecho($description_courte) ?> (<a href="offre.php?id=<?= (int) $offre['id'] ?>">en savoir plus</a>)</p>
						
						<div style="align:center; border:1px solid red; padding:1em; margin-top:2em;">Si cette offre t'intéresse, demande à être contacté·e par un conseiller d'ici <b><?php xecho($offre['delai']) ?> jours</b> maximum.
						<form method="post" style="text-align:center; margin:1em auto;">
							<input type="hidden" name="id_offre" value="<?php xecho($offre['id']) ?>">
							<input type="text" required name="coordonnees" placeholder="Mon adresse courriel ou n° de téléphone" 
								<?= (isset($_SESSION['coordonnees'])) ? 'value="'.$_SESSION['coordonnees'].'"':'' ?> />
							<button type="submit" style="background-color:red">Je demande à être contacté·e</button>
							<br/>
						<?php 
						if (ENVIRONMENT !== ENV_PROD) { 
							if (ENVIRONMENT === ENV_TEST) { 
						?>
							<div style="font-size:small; color:red;">En environnement de test, le mail censé être adressé au professionnel est <a href="http://www.yopmail.fr?boussole" target="_blank">consultable ici</a>.</div>
						<?php }else{ ?>
							<div style="font-size:small; color:red;">Aucun mail n'est envoyé depuis cet environnement.</div>
						<?php }
						} ?>
						</form>
						</div>
						
						<div style="display:table-cell;height:100%; vertical-align:middle; width:2em; text-align:right;">
						<?php if(isset($offres[$sous_theme_id][$i]['id'])){ ?>
						<img src="img/right.png" alt="Offre suivante" onclick="masqueId('modal<?= (int) $offre['id'] ?>');afficheId('modal<?= (int) $offres[$sous_theme_id][$i]['id'] ?>');">
						<?php } ?>
						</div>
					</div>
				</div> -->

<?php 		if($i==$nb_offres_a_afficher && $nb_offres_sous_theme > $nb_offres_a_afficher) { ?>
				<div class="center">
					<span id="lien<?= $sous_theme_id ?>" class="small" onclick="afficheSuite('<?= $sous_theme_id ?>');">
						Afficher les autres offres <img src="img/sort_desc.png"></span>
				</div>
<?php 		}
			if ($i > $nb_offres_a_afficher && $i==$nb_offres_sous_theme){ ?>
			</div>
<?php		}
		} ?>
		</div>
<?php } ?>
	</form>
<?php } ?>

<?php include('../src/web/footer.inc.php'); ?>
</div>
</body>
</html>

<!--<pre><?php print_r($offres); echo 'besoin '.$_SESSION['besoin'];?></pre>-->