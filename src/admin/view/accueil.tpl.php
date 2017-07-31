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
<h1 class="bandeau"><a href="accueil.php">Administration de la boussole des jeunes</a></h1>
<div class="statut"><?php echo $_SESSION['accroche']; ?> (<a href="index.php">déconnexion</a>)</div>

<div class="container">
    <?php echo $select_territoire; ?>

    <h2>Modules disponibles</h2>
    <div style="width:100%; text-align:center;">
        <div class="colonne_accueil">
            <?php if ($liens_activite) { ?>
                <b>Activité</b>
                <ul style="line-height:2em;"><?= $liens_activite ?></ul>
            <?php } ?>
        </div>

        <div class="colonne_accueil">
            <?php if ($liens_admin) { ?>
                <b>Acteurs</b>
                <ul style="line-height:2em;"><?= $liens_admin ?></ul>
            <?php } ?>
        </div>

        <div class="derniere colonne_accueil">
            <?php if ($liens_reference) { ?>
                <b>Données de référence</b>
                <ul style="line-height:2em;"><?= $liens_reference ?></ul>
            <?php } ?>
        </div>
    </div>
</div>
</body>
</html>