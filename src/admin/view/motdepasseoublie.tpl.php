<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width"/>
    <link rel="stylesheet" href="css/style_backoffice.css"/>
    <link rel="icon" type="image/png" href="img/compass-icon.png"/>
    <title>Boussole des jeunes</title>
</head>

<body>
<a href="../web/" target="_blank"><img src="img/external-link.png" class="retour_boussole"></a>
<h1 class="bandeau">Administration de la boussole des jeunes</h1>

<div class="container">

    <h2>Réinitialisation du mot de passe</h2>

    <div class="soustitre"><?= $msg ?></div>

    <form method="post" class="detail">
        <?php if ($vue == 'normal') { ?>
            <div class="une_colonne" style="border:1px solid grey; padding:1em; text-align:center;">
                <div class="lab">
                    <label for="login">Adresse de courriel :</label>
                    <input type="text" name="login"/>
                </div>
                <input type="submit" value="Réinitialiser le mot de passe">
            </div>

        <?php } else if ($vue == 'reinit') { ?>
            <div class="une_colonne" style="border:1px solid grey; padding:1em; text-align:center;">
                <input type="hidden" name="maj_id" value="<?= $id_utilisateur ?>">

                <div class="lab">
                    <label for="nouveaumotdepasse">Nouveau mot de passe :</label>
                    <input type="password" name="nouveaumotdepasse"/>
                </div>
                <div class="lab">
                    <label for="nouveaumotdepasse2">Confirmez le mot de passe :</label>
                    <input type="password" name="nouveaumotdepasse2"/>
                </div>
                <input type="submit" value="Valider">
            </div>

        <?php } ?>
    </form>
</div>
</body>
</html>