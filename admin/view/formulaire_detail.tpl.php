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
<?php
if ($meta !== null) {
?>

    <h2>Détail du formulaire : <?php xecho($meta['theme'].' / '.$meta['territoire']) ?></h2> 
	<p style='color:red; text-align:center;'>Ce module n'est pour le moment disponible qu'en consultation.</p>
    <?php echo $msg; ?>

    <?php
    if (count($pages) > 0) {
    ?>
	
	<div><div style="float:left; min-width:30%; border:1px solid white;">
	<h3>Pages</h3>
		<form method="post">
        <table class="sortable">
            <thead>
            <tr>
                <th>Ordre</th>
                <th>Titre</th>
            </thead>
            <tbody>
            <?php
            foreach ($pages as $page) {
			?>
                <tr>
					<td><input name="ordre_maj_<?= $page['id'] ?>" type="text" value="<?php xecho($page['ordre']); ?>" class="input_int"></td>
					<td><input name="titre_maj_<?= $page['id'] ?>" type="text" value="<?php xecho($page['titre']); ?>"></td>
                </tr>
                <?php
            }
            ?>
                <tr>
					<td><input name="ordre_maj_nv" type="text" value="" class="input_int"></td>
					<td><input name="titre_maj_nv" type="text" value="" placeholder="Nouvelle page"></td>
                </tr>
            </tbody>
        </table>
		
		<div class="button"><input type="submit" value="Enregistrer" disabled></div>
		</form>
	</div>
	
    <?php
    } else {
    ?>
    <div style="margin:1em;text-align:center">Aucun résultat</div>
    <?php
    }
	
    if (count($questions) > 0) {
    ?>
	<div style="width:auto; border:1px solid white;">
	<h3>Questions</h3>
        <table class="sortable">
            <thead>
            <tr>
                <th>Page</th>
                <th>Ordre</th>
                <th>Identifiant</th>
                <th>Libellé</th>
            </thead>
            <tbody>
            <?php
            foreach ($pages as $page) {
				foreach ($questions[$page['id']] as $question) {
			?>
                <tr>
                    <td><?php xecho($page['ordre']) ?></td>
                    <td><?php xecho($question['ordre']) ?></td>
                    <td><a href="formulaire_question.php?id=<?= (int) $question['id'] ?>"><?php xecho($question['name']) ?></a></td>
                    <td><?php xecho($question['libelle']) ?></td>
                </tr>
                <?php
				}
            }
            ?>
            </tbody>
        </table>
	</div>
	</div>

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
		
	<!--<div class="button">
		<input type="button" value="Ajouter une page" onclick="javascript:location.href='formulaire_page.php?f=<?= $meta['id'] ?>'">
		<input type="button" value="Ajouter une question" onclick="javascript:location.href='formulaire_question.php?f=<?= $meta['id'] ?>'">
	</div>-->

	<div class="button"><a href="formulaire_liste.php">Retour à la liste des formulaires</a></div>
</div>
</body>
</html>