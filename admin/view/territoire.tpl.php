<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="css/style_backoffice.css"/>
    <link rel="icon" type="image/png" href="img/compass-icon.png"/>
    <script type="text/javascript" language="javascript" src="js/jquery-1.12.0.js"></script>
    <script type="text/javascript" language="javascript" src="js/jquery.filterByText.js"></script>
    <script type="text/javascript" language="javascript" src="js/selectbox.js"></script>
    <script type="text/javascript">
        $(function () {
            $('#list1').filterByText($('#textbox'));
        });

        function checkall()
        {
            var sel = document.getElementById('list2')
            for (i = 0; i < sel.options.length; i++)
            {
                sel.options[i].selected = true;
            }
        }
    </script>
    <title>Boussole des jeunes</title>
</head>

<body>
<h1 class="bandeau"><a href="accueil.php">Administration de la boussole des jeunes</a></h1>
<div class="statut"><?php echo $_SESSION['accroche']; ?> (<a href="index.php">déconnexion</a>)</div>

<div class="container">
    <?php echo $select_territoire; ?>

    <h2>Gestion des territoires</h2>

    <div class="soustitre"><?php echo $msg; ?></div>

    <form method="post" class="detail" onsubmit='checkall()'>
        <fieldset class="centre">
            <legend><?php echo ($_SESSION['territoire_id']) ? "Modification du" : "Création d'un nouveau"; ?>
                territoire
            </legend>
            <div class="une_colonne">
                <label for="libelle_territoire" class="court">Libellé :</label>
                <input type="text" name="libelle_territoire" value="<?php echo $nom_territoire_choisi; ?>">
                <input type="hidden" name="maj_id_territoire" value="<?php echo $_SESSION['territoire_id']; ?>">
            </div>
            <input type="submit" name="submit_meta" value="Valider">
        </fieldset>

        <?php if ($_SESSION['territoire_id']) { ?>

            <fieldset class="centre">
                <legend>Sélection des villes du territoire</legend>

                <div style="width:auto; text-align:left; clear:both; display: inline-block; vertical-align: middle; height: 100%;">
                    <div style="margin-bottom:1em;">Filtre : <input id="textbox"
                                                                    placeholder="nom de ville, code postal ou département..."
                                                                    type="text" style="width:20em;"></div>

                    <div style="display:inline-block; vertical-align:top;">
                        <select id="list1" MULTIPLE SIZE="20"
                                style=" min-width:20em;"><?php include('../src/admin/villes_options_insee.inc'); ?></select>
                    </div>

                    <div style="display:inline-block; margin-top:1em; vertical-align: top;">
                        <INPUT TYPE="button" style="display:block; margin:1em;" NAME="right" VALUE="&gt;&gt;"
                               ONCLICK="moveSelectedOptions(this.form['list1'],this.form['list2'],true)">

                        <INPUT TYPE="button" style="display:block; margin:1em;" NAME="left" VALUE="&lt;&lt;"
                               ONCLICK="moveSelectedOptions(this.form['list2'],this.form['list1'],true)">
                    </div>

                    <div style="display:inline-block;  vertical-align:top;">
                        <select name="list2[]" id="list2" MULTIPLE SIZE="20"
                                style=" min-width:20em;"><?php echo $liste2; ?></select>
                    </div>

                    <input style="display:block; margin:2em auto 0 auto;" type="submit" name="submit_villes"
                           value="Enregistrer le périmètre du territoire">
                </div>
            </fieldset>

        <?php } ?>

    </form>
</div>
</body>
</html>