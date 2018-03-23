<!DOCTYPE html>
<html>
<head>
	<?php include('view/inc.head-min.php'); ?>
	<link rel="stylesheet" href="src/js/jquery-ui.min.css">
</head>
<body><div id="main">
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
    <form class="joli accueil vert" method="post">
        <fieldset class="accueil_choix_besoin">
        <?php 
		if($nb && count($themes) > 0){ 
			if(!$_SESSION['web']['nom_territoire']) { ?>
			
			<div style="font-weight: bold; margin-bottom:-2em;">La Boussole ne couvre pas encore ta ville, les services proposés sont par conséquent en nombre limité.</div>
			<?php } ?>
			
            <div class="wrapper container">
                <div class="wrapper-options">
                    <h1>Je souhaite</h1>
                </div>
            </div>
            <div class="boutonsbesoin container">
                <div class="row">
                    <?php foreach ($themes as $theme) { ?>
                        <div class="col-md-4 col-sm-4 col-xs-12 spacing-besoins">
                            <div class="wrapper-submit-besoins <?php xecho(sans_accent($theme['libelle_theme_court'])) ?> <?= ($theme['actif']*$theme['nb']) ? '':'disabled' ?>">
                                <input type="submit" name="besoin" value="<?php xecho(isset($theme['libelle']) ? $theme['libelle']:$theme['libelle_national']) ?>" class="submit-besoins" <?= ($theme['actif']*$theme['nb']) ? '':'disabled alt="Cette thématique n\'est pas encore disponible sur ce territoire" title="Cette thématique n\'est pas encore disponible sur ce territoire"' ?>>
                            </div>
                        </div>
					<?php } ?>
                </div>
            </div>
        <?php } ?>
        </fieldset>
    </form>

	<?php include('view/inc.footer.php'); ?>
</div>
</body>
</html>