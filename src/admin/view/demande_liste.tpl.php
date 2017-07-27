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
<div class="statut"><?php echo $_SESSION['accroche']; ?> (<a href="index.php">déconnexion</a>)</div>

<div class="container">
    <?php echo $select_territoire; ?>

    <h2><?php echo $titre_page; ?></h2>

    <?php
    if (mysqli_num_rows($result) > 0) {
        ?>
        <table id="sortable">
            <thead>
            <tr>
                <th>Date de la demande</th>
                <th>Coordonnées</th>
                <th>Offre de service</th>
                <th>Professionnel</th><?php echo ($flag_traite) ? "<th>Date de traitement</th>" : ""; ?></tr>
            </thead>
            <tbody>
            <?php
            while ($row = mysqli_fetch_assoc($result)) {
                //print_r($row);
                ?>
                <tr>
                    <td>
                        <a href="demande_detail.php?id=<?= $row['id_demande'] ?>"><?= date_format(date_create($row['date_demande']), 'd/m/Y à H\hi') ?>
                    </td>
                    <td><?= $row['contact_jeune'] ?></td>
                    <td><?= $row['nom_offre'] ?></td>
                    <td><?= $row['nom_pro'] ?></td>
                    <?php echo ($flag_traite) ? "<td>" . date_format(date_create($row['date_traitement']), 'd/m/Y à H\hi') . "</td>" : ""; ?>
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

    <div style="text-align:left"><?php echo $lien_traites; ?></div>

</div>
</body>
</html>