<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="css/style.css" />
	<link rel="icon" type="image/png" href="img/compass-icon.png" />
	<title><?php xecho(ucfirst($titredusite)); ?></title>
	<script>
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
		function afficheAutres(id){
			var x = document.getElementById('suite'+id);
			var y = document.getElementById('lien'+id);
			if(x.style.display === 'none') {
				x.style.display = 'block';
				y.style.display = 'none';
				//y.innerHTML = 'Masquer les autres offres';
			}/* else {
				x.style.display = 'none';
				y.innerHTML = 'Afficher les autres offres';
			}*/
		}
		function afficheModal(id){
			var x = document.getElementById('modal'+id);
			x.style.display = 'block';
		}
		function cacheModal(id){
			var x = document.getElementById('modal'+id);
			x.style.display = 'none';
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
<body><div id="main">
	<div class="bandeau"><img src="img/marianne.png" width="93px" style="float:left;"><div class="titrebandeau"><a href="index.php"><?php xecho($titredusite); ?></a></div></div>
	<div class="soustitre" style="margin:3em 0 2em 0;"><?php if (isset($resultat)) echo '<span style="color:red">'.$resultat.'</span><br/><br/>'; xecho($msg); ?></div>
	
<?php
if ($nb_offres) { 
?>
	<div class="joli">
		
<?php
	foreach ($sous_themes as $sous_theme_id=>$titre) {
		$nb_offres_sous_theme = count($offres[$sous_theme_id]);
		$ancre="ancre_".$sous_theme_id;
?>
		<div class="resultat" id="<?= $ancre ?>">
			<h1><?php xecho($titre) ?> (<?= $nb_offres_sous_theme ?>)</h1>
	
<?php
		$i = 0;
		foreach ($offres[$sous_theme_id] as $offre) {
			// découpage des titres trop longs
			$titre = ((strlen($offre["titre"]) > 80 ) && (strpos($offre["titre"]," ",80))) ? 
				substr($offre["titre"],0,strpos($offre["titre"]," ",80))."…" : $offre["titre"];
			$description_courte = ((strlen($offre["description"]) > 500 ) && (strpos($offre["description"]," ",500))) ? 
				substr($offre["description"],0,strpos($offre["description"]," ",500))."…" : $offre["description"];
			$description_courte = str_replace('[br][br]','[br]',$description_courte);
			
			if (++$i == $nb_offres_a_afficher+1){ 
?>
			<div id="suite<?= $sous_theme_id?>" style="display:none">
<?php 		} ?> 

				<div class="resultat_offre">
					<!--<div class="coeur">&#9825;</div>-->
					<a href="#<?= $ancre ?>" onclick="afficheModal('<?= (int) $offre["id"] ?>');">
						<b><?php xecho($titre) ?></b></a>
				</div>
				<!-- fenêtre modale de l'offre -->
				<div id="modal<?= (int) $offre["id"] ?>" class="modal" >
					<div class="modal-content">
						<span class="close" onclick="cacheModal('<?= (int) $offre["id"] ?>');">&times;</span>
						<p><b><?php xecho($offre["titre"]) ?></b><br/><?= xecho($offre['nom_pro']) ?> - <?= xecho($offre['ville']) ?></p>
						<p style="font-size:90%;"><?php xbbecho($description_courte) ?> &rarr; <a href="offre.php?id=<?= (int) $offre["id"] ?>">en savoir plus</a></p>
						
						<div style="align:center; border:1px solid red; padding:1em; margin-top:2em;">Si cette offre t'intéresses, demande à être contacté·e par un conseiller d'ici <b><?php xecho($offre['delai']) ?> jours</b> maximum.
						<form method="post" style="text-align:center; margin:1em auto;">
							<input type="hidden" name="id_offre" value="<?php xecho($offre["id"]) ?>">
							<input type="text" name="coordonnees" placeholder="Mon adresse courriel ou n° de téléphone"/>
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
					</div>
				</div>

<?php 		if($i==$nb_offres_a_afficher && $nb_offres_sous_theme > $nb_offres_a_afficher) { ?>
				<div class="center">
					<span id="lien<?= $sous_theme_id ?>" class="small" onclick="afficheAutres('<?= $sous_theme_id ?>');">
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

<div id="criteres" style="border:1px solid #29B297; text-align:center; margin:1em; padding:1em;">Rappel de mes informations : j'habite à <b><?php xecho($_SESSION['ville_habitee']) ?></b> et je souhaite <b><?php xecho(strtolower($_SESSION['besoin'])) ?></b>.<br/>Mes critères sont les suivants : <?php echo liste_criteres(', '); ?>.
<div class="enbasadroite">
	<a href="javascript:location.href='formulaire.php'">Revenir au formulaire</a>
</div></div>

<?php if ($nb_offres) { ?>
<div style="font-size:small; text-align:center; margin:1em;"><a href="contact.php" target="_blank">Aucune offre ne m'intéresse</a></div>
<?php } ?>
	



<div style="height:2em;">&nbsp;</div>  <!--tweak css-->

<?php include('../src/web/footer.inc.php'); ?>
</div>
</body>
</html>

<!--<pre><?php print_r($offres); ?></pre>-->