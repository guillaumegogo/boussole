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
	<header id="bandeau-home-page">
		<div class="wrapper">
			<div class="wrapper-bandeau-homepage">

				<img src="img/logo-ministere-2.jpg" alt="Ministère de l'éducation nationale" class="logo-ministere-homepage">
				<a  href="index.php"><img src="img/logo-boussole.svg" alt="logo la boussole des jeunes" class="logo-boussole-homepage"></a>

				<div class="container bonhomme-section-header">
					<div class="row">
						<div class="col-md-4 col-sm-4 col-xs-4">
							<img src="img/bonhomme1.svg" alt="">
						</div>

						<div class="col-md-4 col-sm-4 col-xs-4">
							<img src="img/bonhomme2.svg" alt="">
						</div>

						<div class="col-md-4 col-sm-4 col-xs-4">
							<img src="img/bonhomme3.svg" alt="">
						</div>
					</div>
				</div>

			</div>
		</div>
	</header>
		<!-- new form not working -->
		<div class="wrapper container accueil-recherche">
			<div class="row">
				<form action="jesouhaite.php" class="joli accueil" method="post" id="searchForm">
					<div class="col-md-3 col-sm-3 col-xs-12">
						<div class="wrapper-jhabite">
							<img src="img/localisation-gris.svg" alt="">
							<label for="ville_selectionnee">J'habite à</label>
						</div>						
					</div>
					<div class="col-md-6 col-sm-5 col-xs-12">
						<div class="wrapper-input-ville">
							<input type="text" id="villes" name="ville_selectionnee" class="input-villes" placeholder="ville ou code postal">
						</div>
                        <?php if (isset($message)) { ?>
                            <p class="message"><?php xecho($message) ?></p>
                        <?php } ?>
					</div>
					<div class="col-md-3 col-sm-4 col-xs-12">
						<div class="wrapper-submit-ville">
							<input type="submit" value="Rechercher" class="submit-ville">
						</div>
					</div>
					&nbsp;
				</form>
			</div>
		</div>

	<div class=" wrapper soustitre">
		<h1>Rencontrer des professionnel·le·s près de chez moi qui m'aident dans mes recherches.</h1>
	</div>
	<?php include('../src/web/footer.inc.php'); ?>
</div>
</body>
</html>