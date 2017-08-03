<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="icon" type="image/png" href="img/compass-icon.png" />
    <link rel="stylesheet" href="css/jquery-ui.css" />
    <link href="https://fonts.googleapis.com/css?family=Cabin|Questrial" rel="stylesheet"> 
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
    <title><?= ucfirst($titredusite) ?></title>
</head>
<body><div id="main">
    <div class="bandeau"><div class="titrebandeau"><a href="index.php"><?= $titredusite ?></a></div></div>
    <div class="soustitre"><strong>Rencontre un conseiller</strong> près de chez toi,<br>pour trouver un <strong>emploi</strong>, un <strong>métier</strong>, une <strong>formation</strong>, un <strong>logement</strong>... </div>
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
        <form class="joli accueil vert" method="post">
            <fieldset class="accueil_choix_besoin">
			<?php if($flag_theme){ ?>
                <div>J'habite à <b><?= $_SESSION['ville_habitee'] ?> (<?= $_SESSION['code_postal'] ?>)</b> et je souhaite... </div>
                <div class="boutonsbesoin">
				<?php foreach ($themes as $theme) { ?>
					<input type="submit" name="besoin" value="<?= $theme['libelle'] ?>" <?= ($theme['actif'])? "":"disabled alt=\"Cette thématique n'est pas encore disponible sur ce territoire\" title=\"Cette thématique n'est pas encore disponible sur ce territoire\" " ?>>
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
    <div class="div123">
        <div class="block123 pro">En 5 minutes je trouve le bon professionnel.</div>
        <div class="block123 contact">Je suis recontacté·e dans les jours qui suivent.</div>
        <div class="block123 rdv">J'obtiens un rendez-vous et une réponse à mon besoin.</div>
    </div>
    <?php include('../src/web/footer.inc.php'); ?>
</div>
</body>
</html>