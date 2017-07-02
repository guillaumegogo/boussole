<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="css/style.css" />
	<link rel="icon" type="image/png" href="img/compass-icon.png" />
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
	<script type="text/javascript" language="javascript" src="js/jquery-1.12.0.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery-ui-1.12.0.js"></script>
	<script>$( function() {
	var listeVilles = [<?php include('inc/villes_index.inc.php');?>]; 
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
	<title><?=ucfirst($titredusite); ?></title>
</head>

<body><div id="main">
<div class="bandeau"><div class="titrebandeau"><a href="index.php"><?=$titredusite; ?></a></div></div>

<div class="soustitre">Rencontrer des professionnel·le·s <b>près de chez moi</b> qui <b>m'aident</b> dans mes recherches.</div>

<?php
//********* 1er affichage de la page (ou mauvaise saise)
if ($nb_villes!=1) {
?>

<form class="joli accueil vert" method="post" id="searchForm">
<fieldset class="accueil_choix_ville">
<?php
if (isset($message)) { echo "<p class=\"message\">".$message."</p>"; }
?>
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

<form action="formulaire.php" class="joli accueil vert" method="post">
<fieldset class="accueil_choix_besoin">
	<div>J'habite à <b><?=$_SESSION['ville_habitee']; ?> (<?=$_SESSION['code_postal']; ?>)</b> et je souhaite... </div>
	<div class="boutonsbesoin">
<?php foreach ($themes as $theme) { ?>
		<input type="submit" value="<?=$theme['libelle']; ?>" name="besoin" <?= ($theme['actif'])? "":"disabled"; ?> >
<?php } ?>
	</div>
</fieldset>
</form>
<?php
}
?>

<div class="div123">
	<div class="block123"><img src="img/ci_search.png">1. En 5 minutes je trouve le bon professionnel.</div>
	<div class="block123"><img src="img/message.png">2. Je suis recontacté·e dans les jours qui suivent.</div>
	<div class="block123"><img src="img/calendar.png">3. J'obtiens un rendez-vous et une réponse à mon besoin.</div>
</div>

<!--
<?php print_r($_SESSION); echo "<br/>".$sql; ?>
-->
<?php include('inc/footer.inc.php'); ?>
</div>
</body>
</html>