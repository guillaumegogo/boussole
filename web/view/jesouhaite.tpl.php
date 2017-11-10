<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="css/style.css" />
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css" media="all">
    <link rel="stylesheet" type="text/css" href="css/bootstrap-theme.css">
	<link rel="icon" type="image/png" href="img/compass-icon.png" />
	<link rel="stylesheet" href="css/jquery-ui.css" />
	<script type="text/javascript" language="javascript" src="js/jquery-1.12.0.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery-ui-1.12.0.js"></script>
	<script>$( function() {
			var listeVilles = [<?php include('../src/villes_index.inc');?>];
			//adaptation fichier insee : pas d'accent, de tiret, d'apostrophe, etc.
			var accentMap = {
				"á": "a",
				"â": "a",
				"ç": "c",
				"é": "e",
				"è": "e",
				"ê": "e",
				"ë": "e",
				"î": "i",
				"ï": "i",
				"ö": "o",
				"ô": "o",
				"ü": "u",
				"û": "u",
				"-": " ",
				"'": " "
			};
			var normalize = function( term ) {
				var ret = "";
				term = term.replace(/\bsaint/gi, "st");
				for ( var i = 0; i < term.length; i++ ) {
					ret += accentMap[ term.charAt(i) ] || term.charAt(i);
				}
				return ret;
			};
			$( "#villes" ).autocomplete({
				minLength: 2,
				source: function( request, response ) {
					//recherche sur les premiers caractères de la ville ou sur le code postal
					var matcher1 = new RegExp( "^" + $.ui.autocomplete.escapeRegex( normalize(request.term) ), "i" );
					var matcher2 = new RegExp( " " + $.ui.autocomplete.escapeRegex( request.term ) + "[0-9]*$", "i" );
					response( $.grep( listeVilles, function( item ){
						return (matcher1.test( item ) || matcher2.test( item ));
					}) );
				},
				select: function(event, ui) {
					$("#villes").val(ui.item.label);
					$("#searchForm").submit();
				}
			});
		} );</script>
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
					<img src="img/localisation.svg" alt=""><span><?php xecho($_SESSION['ville_habitee']) ?>, <?php xecho($_SESSION['code_postal']) ?></span> 			
				</div>
			</div>				
		</div>
	</div>
    <form class="joli accueil vert" method="post">
        <fieldset class="accueil_choix_besoin">
        <?php if($flag_theme){ ?>
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
        <?php }else{ ?>
            <div>La boussole n'est pas encore disponible sur ton territoire.<br/><br/>Tu peux cependant contacter <a href="http://www.crij.org/france" target="_blank">le réseau d'information jeunesse le plus proche de chez toi</a>.</div>
        <?php } ?>
        </fieldset>
    </form>

	<?php include('../src/web/footer.inc.php'); ?>
</div>
</body>
</html>