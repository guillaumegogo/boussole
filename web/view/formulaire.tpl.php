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
</head>
<body><div id="main">
	<?php include('../src/web/header.inc.php'); ?>

	<main>
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
			<a href="jesouhaite.php">
				<img src="img/edit-pen.svg" alt="" >
				<div class="wrapper-modif-btn-texte">
					<p class="btn-texte-1">modifier ma demande</p>
                    <!--<p class="btn-texte-2">trouver un emploi</p>-->
				</div>				
			</a>			
		</div>

		<div id="formulaire-etapes-recherche" class="wrapper container">
			<div class=" row wrapper-etapes">
				<div class="col-md-4 col-sm-4 col-xs-12 wrapper-etape-un">
					<a href="formulaire.php?etape=1">
						<span class="nom-etape">étape #1</span>
					</a>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12 wrapper-etape-deux">
					<a href="formulaire.php?etape=2">
						<span class="nom-etape">étape #2</span>
					</a>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12 wrapper-etape-trois">
					<a href="formulaire.php?etape=3">
						<span class="nom-etape">étape #3</span>
					</a>
				</div>				
			</div>
			<?php
			if(count($meta)){
			?>
			<div id="formulaire-etape-contenu" class="wrapper container">
				<form action="formulaire.php" method="post" class="joli formulaire">
					<div class="row">
						
							<fieldset class="formulaire">
								<div class="centreformulaire">
									<input type="hidden" name="etape" value="<?php xecho($meta['suite']) ?>">
									<?php
									foreach ($questions as $question) {
										?>
										<div class="col-md-6 col-sm-6 col-xs-12 spacing">
											<div class="lab">
												<label class="label_long" for="<?php xecho($question['name']) ?>"><?php xecho($question['que']) ?></label>
												<div style="display:block;">
													<?php
													echo ouverture_ligne($question);
													foreach ($reponses[$question['id']] as $reponse) {
														echo affiche_valeur($reponse, $question['type']);
													}
													echo cloture_ligne($question);
													?>
												</div>
											</div>
										</div>
										<?php
									}
									?>
									<div style="margin-top:2em;"><button type="submit" style="float:right">Je continue</button></div>
								</div>
							</fieldset>						
					</div>
				</form>

				<?php
			}else{
				?>
				<div class="soustitre" style="margin-top:3%">Nous ne trouvons pas de formulaire. Recommence s'il te plait.</div>
				<?php
			}
			?>
			</div>
	</main>
	<?php include('../src/web/footer.inc.php'); ?>
</div>
</body>
</html>