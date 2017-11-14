<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="css/style.css" />
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css" media="all">
    <link rel="stylesheet" type="text/css" href="css/bootstrap-theme.css">
	<link rel="icon" type="image/png" href="img/compass-icon.png" />
	<title><?php xecho(ucfirst($titredusite)) ?></title>
	<script type="text/javascript" language="javascript" src="js/fix-ie.js"></script>
</head>
<body><div id="main" class="body-color">
	<?php include('../src/web/header.inc.php'); ?>

	<div class="wrapper container">
		<div class="row bordure-bas">
			<div class="col-md-6 col-sm-6 col-xs-6">
				<div class="retour-page-wrapper">
					<a href="index.php"><img src="img/icon-retour.svg" alt="">Retour à la page d’accueil</a>
				</div>
			</div>
			<div class="col-md-6 col-sm-6 col-xs-6">
				<div class="localisation-wrapper">						
					<img src="img/localisation.svg" alt=""><span><?php xecho($_SESSION['ville_habitee']) ?>, <?php xecho($_SESSION['code_postal']) ?></span> 			
				</div>
			</div>				
		</div>
	</div>
	<div class="wrapper container btn-modifier-demande">
		<a href="jesouhaite.php" class="btn-block-inline">
			<img src="img/edit-pen.svg" alt="" >
			<div class="wrapper-modif-btn-texte ">
				<p class="btn-texte-1">modifier ma demande</p>
                <!--<p class="btn-texte-2">trouver un emploi</p>-->
			</div>				
		</a>
		<a href="formulaire.php?etape=3" class="btn-block-inline">
			<img src="img/edit-pen.svg" alt="" >
			<div class="wrapper-modif-btn-texte">
				<p class="btn-texte-1">modifier ma situation</p>
                <!--<p class="btn-texte-2">étudiant</p>-->
			</div>				
		</a>
		<a href="resultat.php" class="btn-block-inline margin-left-btn btn-jaune">
			<img src="img/icon-fleche-black.svg" alt="" class="padding-icon-noir" >
			<div class="wrapper-modif-btn-texte">
				<p class="btn-texte-1">retour aux résultats</p>
                <!--<p class="btn-texte-2">XX résultats</p>-->
			</div>				
		</a>			
	</div>


	<div class="wrapper container btns-navigation nav-desktop">
		<div class="row">
			<div class="col-md-6 col-sm-6 col-xs-6 border-milieu">
				<div class="wrapper-img-fleche">
					<a href="#">
						<img src="img/path.png" alt="" class="img-fleche-gauche">
						<span>offre précédente</span>
					</a>
				</div>
			</div>
			<div class="col-md-6 col-sm-6 col-xs-6 text-right">		
				<div class="wrapper-img-fleche float-droite">			
					<a href="#">
						<img src="img/path.png" alt="" class="img-fleche-droite">
						<span>offre suivante</span>
					</a>
				</div>
			</div>
		</div>
	</div>

	<?php
	if($row['nom_offre']) { //si on a une offre
		?>

		<div class="wrapper container detail-contenu bg-white bg-shadow">
			<div class="row">
				<div class="col-md-8 col-sm-8 col-xs-12 wrapper-titre-offre">
					<div class="row">
						<div class="col-md-9 col-sm-8 col-xs-12">
							<p class="titre-offre"><?php xecho($row['nom_offre']) ?></p>
						</div>
						<div class="col-md-3 col-sm-4 col-xs-12 texte-validite-align">
							<div class="bloc-validite">
								<span class="bloc-validite-titre">Validité jusqu’au</span>
								<span class="bloc-validite-date"><?php xecho($row['date_fin']) ?></span>
							</div>					
						</div>
					</div>
					<div class="row">
						<div class="wrapper-offre-desc">
							<p class="offre-desc"><?php xbbecho($row['description_offre']) ?></p>
						</div>
					</div>
					<div class="row">
						<p class="offre-service-titre">Je suis intéressé.e par ce service</p>
					</div>
					<div class="row">
						<div class="wrapper-offre-service-from">
								<?php
								if(isset($_POST['coordonnees'])){
									echo $msg_depot;
								} else {
									?>
									<form method="post">
										<div class="col-md-6 col-sm-12 col-xs-12">
											<input type="hidden" name="id_offre" value="<?php xecho($id_offre) ?>">
											<input type="text" required name="coordonnees" class="input-adresse-mail" placeholder="Mon adresse courriel ou n° de téléphone"
												<?= (isset($_SESSION['coordonnees'])) ? 'value="'.$_SESSION['coordonnees'].'"':'' ?> />
										</div>
										<div class="col-md-6 col-sm-12 col-xs-12 submit-connexion-align">
											<button type="submit" class="submit-connexion-offre">Je demande à être contacté·e</button>
										</div>
									</form>
									<?php
								}
								?>
						</div>
					</div>
					<div class="row">
						<div class="offre-service-bloc-desc">
							<p>Si je suis intéressé.e par ce service, je laisse mon adresse de courriel ou mon numéro de téléphone portable pour être contacté·e par un professionnel <mark>d'ici <?php xecho($row['delai_offre']); ?> jours maximum.</mark></p>
							<em class="legend">Les informations recueillies à partir de ce formulaire sont nécessaires au traitement de votre demande. Elles seront enregistrées et transmises au(x) professionnel(s) auprès du(es)quel(s) vous prendrez un rendez-vous. Vous disposez d'un droit d'accès, de rectification et d'opposition aux données vous concernant, que vous pouvez exercer en adressant une demande par ce formulaire (à créer). En cas d’abandon de la recherche, les données personnelles ne sont pas conservées.</em>
						</div>
					</div>
				</div>

				<div class="col-md-4 col-sm-4 col-xs-12">
					<div class="row">
						<div class="adresse-offre">
							<p class="adresse-offre-titre">Proposée par l'organisme</p>
							<h3><?php xecho($row['nom_pro']) ?></h3>
							<p class="adresse-offre-adresse"><?php xecho($adresse) ?></p>
						</div>
					</div>
					<div class="row">
						<div class="wrapper-map">
							<iframe src="https://maps.google.it/maps?q=<?= $adresse ?>&output=embed" width="100%" height="213" frameborder="0" style="border:0" allowfullscreen></iframe>
						</div>
					</div>
					<div class="row google-bloc">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<a href="https://maps.google.it/maps?q=<?= $adresse ?>" class="lien-google" target="_blank">Ouvrir dans Google Map</a>
						</div>
                        <?php if($url && $url != ''){ ?>
						<div class="col-md-12 col-sm-12 col-xs-12">
							<div class="lien-google-http">Voir le site internet <br>
							<?php echo($url) ?>
							</div>
						</div>
                        <?php } ?>
					</div>
				</div>
			</div>
		</div>
		
		<?php
	}else{ //pas d'offre
		?>
		<p style="text-align:center; margin-top:10%;">Il n'y a pas (plus ?) d'offre correspondante disponible. <a href="index.php">Recommencez</a>.</p>
		<?php
	}
	?>


		

	<div class="wrapper container">
		<div class="reseau-sociaux">
			<span>Je partage ce service sur </span>
			<div class="reseau-sociaux-liens">
				<a target="_blank" title="Twitter" href="https://twitter.com/share?url=<?= $url_toshare ?>&text=La Boussole des jeunes : <?= $row['nom_offre'] ?>&via=la Boussole des jeunes" rel="nofollow" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=400,width=700');return false;" class="icon-twitter"></a>
				<a target="_blank" title="Google plus" href="https://plus.google.com/share?url={<?php echo $url_toshare; ?>}" onclick="javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" class="icon-googleplus"></a>
				<a target="_blank" title="Facebook" href="https://www.facebook.com/sharer.php?u=<?= $url_toshare ?>&t=La Boussole des jeunes : <?= $row['nom_offre'] ?>" rel="nofollow" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=500,width=700');return false;" class="icon-facebook"></a>
				<a target="_blank" title="Viadeo" href="#" rel="nofollow" onclick="window.open('https://www.viadeo.com/fr/widgets/share/preview?url=' + encodeURIComponent(window.location.href) + '&language=en', '_blank', 'toolbar=no, scrollbars=yes, resizable=yes, top=300, left=300, width=540, height=420'); return false;" class="icon-viadeo"></a>
				<a target="_blank" title="Linkedin" href="https://www.linkedin.com/shareArticle?mini=true&url=<?= $url_toshare ?>&title=La Boussole des jeunes : <?= $row['nom_offre'] ?>" rel="nofollow" onclick="javascript:window.open(this.href, '','menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=450,width=650');return false;" class="icon-linkedin"></a>
				<a target="_blank" title="Email" href="mailto:?body=<?php echo $url_toshare; ?>" rel="nofollow" class="icon-email"></a>
			</div>
		</div>			
	</div>

	<?php include('../src/web/footer.inc.php'); ?>
</div>
</body>
</html>