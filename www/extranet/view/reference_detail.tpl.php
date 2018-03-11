<!DOCTYPE html>
<html>
<head>
	<?php include('view/inc.head.php'); ?>
</head>

<body>
<?php include('view/inc.bandeau.php'); ?>

<div class="container">

	<h2><small><a href="accueil.php">Accueil</a> > <a href="reference_liste.php">Listes de référence</a> ></small> 
		<?= ($id_ref) ? 'Détail' : 'Ajout'; ?> d'une référence</h2>

	<div class="soustitre"><?php echo $msg; ?></div>

	<form method="post" class="detail">
		<fieldset>
			<div class="une_colonne">
				<div class="lab">
					<label for="liste_reference">Liste :</label>
					<input type="text" required name="liste_reference" value="<?= (isset($row['liste'])) ? $row['liste'] : '' ?>">
				</div>
				<div class="lab">
					<label for="libelle_reference">Libellé :</label>
					<input type="text" required name="libelle_reference" value="<?= (isset($row['libelle'])) ? $row['libelle'] : '' ?>">
				</div>
			</div>
			<input type="hidden" name="maj_id_reference" value="<?php echo $id_ref; ?>">
		</fieldset>
		
		<div class="button">
			<input type="button" value="Retour" onclick="javascript:location.href='reference_liste.php'">
			<input type="submit" name="submit" value="Enregistrer">
		</div>

	</form>
</div>
</body>
</html>