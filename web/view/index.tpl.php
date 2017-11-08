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
	<header id="bandeau-home-page">
		<div class="wrapper">
			<div class="wrapper-bandeau-homepage">

				<img src="img/logo-ministere.svg" alt="Ministère de l'éducation nationale" class="logo-ministere-homepage">
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
	<?php
	//********* 1er affichage de la page (ou mauvaise saise)
	if ($nb_villes!=1) {
		?>
		<form class="joli accueil vert" method="post" id="searchForm">
			<fieldset class="accueil_choix_ville">
				<?php if (isset($message)) { ?>
					<p class="message"><?php xecho($message) ?></p>
				<?php } ?>
				<label for="ville_selectionnee">J'habite à</label>
				<input type="text" id="villes" name="ville_selectionnee" placeholder="ville ou code postal">
				<input type="submit" value="Démarrer">
			</fieldset>
			&nbsp;
		</form>
		<?php
//********* si une seule ville correspond
	}else{
		?>
		<form class="joli accueil vert" method="post">
			<fieldset class="accueil_choix_besoin">
			<?php if($flag_theme){ ?>
				<div>J'habite à <b><?php xecho($_SESSION['ville_habitee']) ?> (<?php xecho($_SESSION['code_postal']) ?>)</b> et je souhaite... </div>
				<div class="boutonsbesoin">
				<?php foreach ($themes as $theme) { ?>
					<input type="submit" name="besoin" value="<?php xecho($theme['libelle']) ?>" <?= ($theme['actif']*$theme['nb']) ? '':'disabled alt="Cette thématique n\'est pas encore disponible sur ce territoire" title="Cette thématique n\'est pas encore disponible sur ce territoire"' ?>>
				<?php } ?>
				</div>
			<?php }else{ ?>
				<div>La boussole n'est pas encore disponible sur ton territoire.<br/><br/>Tu peux cependant contacter <a href="http://www.crij.org/france" target="_blank">le réseau d'information jeunesse le plus proche de chez toi</a>.</div>
			<?php } ?>
			</fieldset>
		</form>
		<?php
	}
	?>

	<div class=" wrapper soustitre">
		<h1>Rencontrer des professionnel·le·s près de chez moi qui m'aident dans mes recherches.</h1>
	</div>
	<?php include('../src/web/footer.inc.php'); ?>
</div>
</body>
</html>