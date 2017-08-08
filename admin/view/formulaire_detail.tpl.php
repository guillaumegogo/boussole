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
    <h2>Détail d'un formulaire</h2>
    <?php echo $msg; ?>

    <?php
    if ($form !== null) {
        ?>
	
    <?php
    if (count($form[0]) > 0) {
        ?>
        <table id="sortable">
            <thead>
            <tr>
                <th>Question</th>
                <th>Identifiant</th>
                <th>Page</th>
                <th>Type</th>
                <th>Taille</th>
                <th>Obligatoire</th>
                <th>Valeurs proposées</th>
            </thead>
            <tbody>
            <?php
            foreach ($form[0] as $question) {
            ?>
                <tr>
                    <td><a href="#"><!--formulaire_question.php?id=<?= (int) $question['id'] ?>--><?php xecho($question['que']) ?></a></td>
                    <td><?php xecho($question['name']) ?></td>
                    <td><?php xecho($question['ordre_page']) ?></td>
                    <td><?php xecho($question['type']) ?></td>
                    <td><?php xecho($question['tai']) ?></td>
                    <td><?= ($question['obl']) ? "oui":"non" ?></td>
					<td>
				<?php
				foreach ($form[1][$question['id']] as $reponse) {
                ?>
					<?php xecho($reponse['lib']." ;") ?>
				<?php
				}
                ?>
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
        <?php
    } else {
        echo "N° de demande non valide.";
    }
    ?>

    <div class="button"><a href="formulaire_liste.php">Retour à la liste des formulaires</a></div>
	
	<pre><?php print_r($form); ?></pre>
</div>
</body>
</html>