<!DOCTYPE html>
<html>
<head>
	<?php include('view/inc.head-min.php'); ?>
	<script type="text/javascript" language="javascript" src="js/fix-ie.js"></script>
</head>
<body><div id="main">
	<?php include('view/inc.header.php'); ?>

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
						<img src="img/localisation.svg" alt=""><span><?php xecho($_SESSION['web']['ville_habitee']) ?>, <?php xecho($_SESSION['web']['code_postal']) ?>
						<?php if($_SESSION['web']['nom_territoire']) { ?> <br/><?php xecho($_SESSION['web']['nom_territoire']); } ?></span>
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
					<a href="formulaire.php?etape=1" <?php echo $etape == 1 ? 'class="active"' : '' ?>>
						<span class="nom-etape"><?= (isset($liste_pages[0])) ? $liste_pages[0]['titre'] : ''; ?></span>
					</a>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12 wrapper-etape-deux">
					<a href="formulaire.php?etape=2" <?php echo $etape == 2 ? 'class="active"' : '' ?>>
						<span class="nom-etape"><?= (isset($liste_pages[1])) ? $liste_pages[1]['titre'] : ''; ?></span>
					</a>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12 wrapper-etape-trois">
					<a href="formulaire.php?etape=3" <?php echo $etape == 3 ? 'class="active"' : '' ?>>
						<span class="nom-etape"><?= (isset($liste_pages[2])) ? $liste_pages[2]['titre'] : ''; ?></span>
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
												<label class="label_long" for="<?php xecho($question['name']) ?>"><?php xecho($question['que']) ?> <?= ($question['obl']) ? '*':'' ?></label>
												<div style="display:block;">
													<?php
													echo ouverture_ligne($question);
													foreach ($reponses[$question['id']] as $reponse) {
														echo affiche_valeur($reponse, $question['type'], $question['obl']);
													}
													echo cloture_ligne($question);
													?>
												</div>
											</div>
										</div>
										<?php
									}
									?>
									<div style="clear:both; text-align:center">
									* Champs obligatoires
									</div>
									<div style="margin-top:2em;">
										<button type="submit" style="float:right">Je continue</button>
									</div>
                                    <?php if($etape == 1) { ?>
                                        <a href="jesouhaite.php" style="float:left">Précédent</a>
                                    <?php }else if($etape == 2){ ?>
                                        <a href="formulaire.php?etape=1" style="float:left">Précédent</a>
                                    <?php }else if($etape == 3){ ?>
                                        <a href="formulaire.php?etape=2" style="float:left">Précédent</a>
                                    <?php } ?>
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
	<?php include('view/inc.footer.php'); ?>
</div>
</body>
</html>