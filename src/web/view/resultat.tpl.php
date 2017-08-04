<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="icon" type="image/png" href="img/compass-icon.png" />
    <title><?php xecho(ucfirst($titredusite)); ?></title>
    <script>
        function masqueCriteres(){
            var x = document.getElementById('criteres');
            var y = document.getElementById('fleche_criteres');
            if(x.style.display === 'none') {
                x.style.display = 'block';
                y.innerHTML = "&#9652;"; //flèche vers le haut
            } else {
                x.style.display = 'none';
                y.innerHTML = "&#9662;"; //flèche vers le bas
            }
        }
        function afficheAutres(id){
            var x = document.getElementById('suite'+id);
            var y = document.getElementById('lien'+id);
            if(x.style.display === 'none') {
                x.style.display = 'block';
                y.innerHTML = 'Masquer les autres offres';
            } else {
                x.style.display = 'none';
                y.innerHTML = 'Afficher les autres offres';
            }
        }
        function afficheModal(id){
            var x = document.getElementById('modal'+id);
            x.style.display = 'block';
        }
        function cacheModal(id){
            var x = document.getElementById('modal'+id);
            x.style.display = 'none';
        }
        window.onclick = function(event) {
            /*alert(event.target.id);*/
            if (event.target.id.substring(0, 5) == 'modal') {
                var tabModal = document.getElementsByClassName("modal");
                for(var i=0; i<tabModal.length; i++){
                    tabModal[i].style.display = "none";
                }
            }
        }
    </script>
</head>
<body>
<div id="main">
    <div class="bandeau"><div class="titrebandeau"><a href="index.php"><?php xecho($titredusite); ?></a></div></div>
    <div class="soustitre" style="margin-top:3%"><?php xecho($msg); ?></div>
    <form class="joli resultat">
        <fieldset class="resultat">
            <legend>Rappel de mes informations</legend>
            <div>
                <p onclick='masqueCriteres()'>J'habite à <b><?php xecho($_SESSION['ville_habitee']) ?></b> et je souhaite <b><?php xecho(strtolower($_SESSION['besoin'])) ?></b> <span id="fleche_criteres">&#9662;</span></p>
                <div id="criteres" style="display:<?php echo ($nb_offres) ? "none":"block"; ?>">
                    <div class="colonnes">
                        <?php echo liste_criteres('<br/>'); ?> <!--todo : à mettre en forme-->
                    </div>
                    <div class="enbasadroite">
                        <a href="javascript:location.href='formulaire.php'">Revenir au formulaire</a>
                    </div>
                </div>
            </div>
        </fieldset>
    </form>
    <?php
    if ($nb_offres) {
        ?>
        <form class="joli resultat" style="margin-top:1%;">
            <?php
            $sous_offre_precedente='';
            $compteur_offres=0;
            foreach ($offres as $offre) {

            //******* affichage des sous thèmes
            if($offre['sous_theme']!=$sous_offre_precedente){
            if($sous_offre_precedente){
                if($compteur_offres>4) echo '</div><div class="center"><a href="#'.$anchor.'" id="lien'.$sous_offre_precedente.'" class="small" onclick="afficheAutres(\''.$sous_offre_precedente.'\');">Afficher les autres offres</a></div>';
                echo "</div>\n</fieldset>"; //en cas de changement de sous-thème on ferme le fieldset précédent
            }
            $sous_offre_precedente=$offre['sous_theme'];
            $compteur_offres=0;
            $anchor="a".$offre['sous_theme'];
            ?>

            <fieldset class="resultat" id="<?= $anchor ?>"><legend><?php xecho($sous_themes[$offre['sous_theme']]['titre']) ?> (<?php xecho($sous_themes[$offre['sous_theme']]['nb']) ?> offre<?= ($sous_themes[$offre['sous_theme']]['nb']>1) ? 's':''; ?>)</legend>
                <div style="width:100%; margin:auto;">
                    <?php
                    }

                    //******** découpage des titres trop longs
                    $titre_court = '';
                    if (strlen($offre["titre"]) > 80 ) {
                        if (strpos($offre["titre"]," ",80)) {
                            $titre_court = substr($offre["titre"],0,strpos($offre["titre"]," ",80))."...";
                        }
                    }
                    if($compteur_offres++==4) echo '<div id="suite'.$offre['sous_theme'].'" style="display:none">';
                    ?>
                    <!-- affichage des offres -->
                    <div class="resultat_offre"><!--
			<div class="coeur">&#9825;</div>-->
                        <a href="#<?= $anchor ?>" onclick="afficheModal('<?= (int) $offre["id"] ?>');"><b><?php xecho(($titre_court) ? $titre_court : $offre["titre"]) ?></b></a>
                    </div>
                    <!-- fenêtre modale de l'offre -->
                    <div id="modal<?= (int) $offre["id"] ?>" class="modal" ><div class="modal-content">
                            <span class="close" onclick="cacheModal('<?= (int) $offre["id"] ?>');">&times;</span>
                            <p><b><?php xecho($offre["titre"]) ?></b><br/><?= (int) $offre["nom_pro"] ?></p>
                            <p><?php xecho($offre["description"]) ?></p>
                            <div class="center"><a href="offre.php?id=<?= (int) $offre["id"] ?>" class="button">En savoir +</a></div>
                        </div></div>
                    <?php
                    }
                    ?>
                </div>
            </fieldset>
        </form>
        <?php
    }
    ?>
    <div class="lienenbas">
        <?php
        echo $aucune_offre;
        ?>
    </div>
    <div style="height:2em;">&nbsp;</div>  <!--tweak css-->
    <?php include('../src/web/footer.inc.php'); ?>
</div>
</body>
</html>