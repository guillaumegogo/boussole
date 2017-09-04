<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="icon" type="image/png" href="img/compass-icon.png" />
    <title><?php xecho(ucfirst($titredusite)); ?></title>
</head>
<body><div id="main">
    <div class="bandeau"><img src="img/2017_MEN_logo.jpg" width="113px" style="float:left;"><div class="titrebandeau"><a href="index.php"><?php xecho($titredusite); ?></a></div></div>
    <div class="soustitre">
        <p>J'habite à <b><?php xecho($_SESSION['ville_habitee']) ?></b> et je souhaite <b><?php xecho(strtolower($_SESSION['besoin'])) ?></b>.</p>
    </div>

    <?php
    if(count($meta)){
        ?>

        <form action="formulaire.php" method="post" class="joli formulaire">

            <fieldset class="formulaire">
                <legend><?php xecho($meta['titre']) ?> (<?php xecho($meta['etape']) ?>/<?php xecho($meta['nb']) ?>)</legend>
                <div class="aide"><img src="img/ci_help.png" title="<?php xecho($meta['aide']) ?>"></div>

                <div class="centreformulaire">
                    <input type="hidden" name="etape" value="<?php xecho($meta['suite']) ?>">

                    <?php
                    foreach ($questions as $question) {
                        ?>
                        <div class="lab">
                            <label class="label_long" for="<?php xecho($question['name']) ?>"><?php xecho($question['que']) ?></label>
                            <div style="display:inline-block;">
                                <?php
                                echo ouverture_ligne($question);
                                foreach ($reponses[$question['id']] as $reponse) {
                                    echo affiche_valeur($reponse, $question['type']);
                                }
                                echo cloture_ligne($question);
                                ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <div style="margin-top:2em;"><button type="submit" style="float:right">Je continue</button></div>
                </div>

            </fieldset>
        </form>

        <?php
    }else{
        ?>
        <div class="soustitre" style="margin-top:3%">Nous ne trouvons pas de formulaire. Recommence s'il te plait.</div>
        <?php
    }
    ?>

    <div class="lienenbas">&nbsp;</div>
    <div style="height:2em;">&nbsp;</div>  <!--tweak css-->
    <?php include('../src/web/footer.inc.php'); ?>
</div>
</body>
</html>