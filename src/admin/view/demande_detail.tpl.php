<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="css/style_backoffice.css"/>
    <link rel="icon" type="image/png" href="img/compass-icon.png"/>
    <title>Boussole des jeunes</title>
</head>

<body>
<h1 class="bandeau"><a href="accueil.php">Administration de la boussole des jeunes</a></h1>
<div class="statut"><?php xecho($_SESSION['accroche']); ?> (<a href="index.php">déconnexion</a>)</div>

<div class="container">
    <h2>Détail d'une demande</h2>
    <?php echo $msg; ?>

    <?php
    if ($demande !== null) {
        ?>

        <table>
            <tr>
                <th>N° demande</th>
                <td><?php xecho($demande['id_demande']); ?></td>
            </tr>
            <tr>
                <th>Date demande</th>
                <td><?php xecho(date_format(date_create($demande['date_demande']), 'd/m/Y à H\hi')); ?></td>
            </tr>
            <tr>
                <th>Coordonnées du demandeur</th>
                <td><?php
                    if (filter_var($demande['contact_jeune'], FILTER_VALIDATE_EMAIL)) {
                        echo "<a href=\"mailto:\"" . xssafe($demande['contact_jeune']) . ">" . xssafe($demande['contact_jeune']) . "</a>";
                    } else {
                        xecho($demande['contact_jeune']);
                    }
                    ?></td>
            </tr>
            <tr>
                <th>Offre de service</th>
                <td><?php xecho($demande['nom_offre']); ?></td>
            </tr>
            <tr>
                <th>Professionnel</th>
                <td><?php xecho($demande['nom_pro']); ?></td>
            </tr>
            <tr>
                <th>Profil</th>
                <td><?php echo str_replace(",", "<br/>", xssafe($demande['profil'])); ?></td>
            </tr>
            <tr>
                <th>Traité</th>
                <td>
                    <?php
                    if ($demande['date_traitement']) {
                        $traite = "Traité le " . date_format(date_create($demande['date_traitement']), 'd/m/Y à H\hi') . "<br/>Commentaire : " . xssafe($demande['commentaire']);
                    } else {
                        $traite = "<form method=\"post\" class=\"detail\"><input type=\"hidden\" name=\"id_traite\" value=\"" . xssafe($demande['id_demande']) . "\" /><textarea name=\"commentaire\"   style=\"width:100%\"  rows=\"5\" placeholder=\"Conditions et suites données à l'échange (...)\"></textarea> <input type=\"submit\" value=\"Marquer comme traité\"></form> ";
                    }
                    echo $traite;
                    ?>
            </tr>
        </table>

        <?php
    } else {
        echo "N° de demande non valide.";
    }
    ?>

    <div class="button"><a href="demande_liste.php">Retour à la liste des demandes</a></div>

</div>
</body>
</html>