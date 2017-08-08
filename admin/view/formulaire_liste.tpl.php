<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="css/style_backoffice.css"/>
    <link rel="icon" type="image/png" href="img/compass-icon.png"/>
    <link rel="stylesheet" href="css/jquery.dataTables.min.css"/>
    <script type="text/javascript" language="javascript" src="js/jquery-1.12.0.js"></script>
    <script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
    <script type="text/javascript" charset="utf-8">
        $(document).ready(function () {
            $('#sortable').dataTable();
        });
    </script>

    <title>Boussole des jeunes</title>
</head>

<body>
<h1 class="bandeau"><a href="accueil.php">Administration de la boussole des jeunes</a></h1>
<div class="statut"><?php xecho($_SESSION['accroche']); ?> (<a href="index.php">déconnexion</a>)</div>

<div class="container">
    <?php echo $select_territoire; ?>

    <h2><?php xecho($titre_page); ?></h2>

    <?php
    if (count($formulaires) > 0) {
        ?>
        <table id="sortable">
            <thead>
            <tr>
                <th>Thème</th>
                <th>Territoire</th>
                <th></th>
            </thead>
            <tbody>
            <?php
            foreach ($formulaires as $formulaire) {
                ?>
                <tr>
                    <td><?php xecho($formulaire['libelle']) ?></td>
                    <td><?php xecho($formulaire['territoire']) ?></td>
                    <td>
                        <a href="formulaire_detail.php?id=<?= (int) $formulaire['id'] ?>">Accéder</a>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>

        <?php
    } else {
        ?>
        <div style="margin:1em;text-align:center">Aucun résultat</div>
        <?php
    }
    ?>

    <div style="text-align:left"><?php echo $lien_desactives; ?></div>

</div>

<div class="button">
    <input type="button" value="Créer un formulaire" onclick="javascript:location.href='formulaire_detail.php'">
</div>
</body>
</html>