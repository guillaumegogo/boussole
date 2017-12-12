<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="css/style.css" />
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css" media="all">
    <link rel="stylesheet" type="text/css" href="css/bootstrap-theme.css">
	<link rel="icon" type="image/png" href="img/compass-icon.png" />
	<link rel="stylesheet" href="css/jquery-ui.css" />
	<title><?php xecho(ucfirst($titredusite)) ?></title>
</head>
<body><div id="main">
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
					<img src="img/localisation.svg" alt=""><span><?php xecho($_SESSION['ville_habitee']) ?>, <?php xecho($_SESSION['code_postal']) ?>
					<?php if($_SESSION['nom_territoire']) { ?> <br/><?php xecho($_SESSION['nom_territoire']); } ?></span>
				</div>
			</div>
		</div>
	</div>
    <form class="joli accueil vert" method="post">
        <fieldset class="accueil_choix_besoin">
        <?php if($nb && count($themes) > 0){ ?>
			<?php if(!$_SESSION['nom_territoire']) { ?>
			<div style="font-weight: bold;"><span style="color:red">Ta ville n'appartient pour le moment à aucun territoire de la Boussole des jeunes.</span><br/><br/>Si aucune offre de service ne te convient,<br/>contacte le <a href="https://www.cidj.com/nous-rencontrer">point d'information jeunesse le plus proche de chez toi</a>,<br/>il saura certainement trouver une réponse à ton besoin.
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
                            <div class="wrapper-submit-besoins <?php xecho($theme['libelle']) ?> <?= ($theme['actif']*$theme['nb']) ? '':'disabled' ?>">
                                <input type="submit" name="besoin" value="<?php xecho($theme['libelle']) ?>" class="submit-besoins" <?= ($theme['actif']*$theme['nb']) ? '':'disabled alt="Cette thématique n\'est pas encore disponible sur ce territoire" title="Cette thématique n\'est pas encore disponible sur ce territoire"' ?>>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
        </fieldset>
    </form>

	<?php include('../src/web/footer.inc.php'); ?>
</div>
</body>
</html>