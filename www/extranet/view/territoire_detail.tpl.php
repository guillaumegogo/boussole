<!DOCTYPE html>
<html>
<head>
	<?php include('view/inc.head.php'); ?>
	
	<?php if($droit_ecriture) { ?>
	<script type="text/javascript" language="javascript" src="../src/js/external/jquery/jquery.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery.filterByText.js"></script>
	<script type="text/javascript" language="javascript" src="js/selectbox.js"></script>
	<script type="text/javascript" language="javascript" src="js/fix-ie.js"></script>
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
	<?php } else { ?>
	<link rel="stylesheet" type="text/css" href="css/readonlyform.css" media="screen" />
	<script type="text/javascript" language="javascript" src="js/readonlyform.js"></script>
	<?php } ?>
</head>

<body>
<?php include('view/inc.bandeau.php'); ?>

<div class="container">

	<h2><small><a href="accueil.php">Accueil</a> > <a href="territoire_liste.php">Liste des territoires</a> ></small> 
		<?= ($id_territoire) ? 'Détail' : 'Création'; ?> d'un territoire</h2>

	<div class="soustitre"><?php echo $msg; ?></div>

	<form method="post" class="detail" onsubmit='checkall()'>
		<fieldset class="centre" <?= (!$droit_ecriture) ? 'disabled="disabled"':'' ?>>
			<legend>Description du territoire  <?= ($id_territoire && $territoire['actif'] == 0) ? '<span style="color:red">(désactivé)</span>':'' ?> </legend>
			<label for="libelle_territoire" class="court">Libellé :</label>
			<input type="text" required name="libelle_territoire" value="<?= (isset($territoire)) ? $territoire['nom_territoire']:'' ?>">
			<input type="hidden" name="maj_id_territoire" value="<?php echo $id_territoire; ?>">
				<?php if($droit_ecriture) { ?>
			<input type="submit" name="submit_meta" value="Valider">
				<?php } ?>
		</fieldset>

		<?php if ($id_territoire) { ?>

		<fieldset class="centre" <?= (!$droit_ecriture) ? 'disabled="disabled"':'' ?>>
			<legend>Sélection des villes du territoire</legend>

			<div style="width:auto; text-align:left; clear:both; display: inline-block; vertical-align: middle; /*height: 100%;*/">
				<?php if($droit_ecriture) { ?>
				<div style="margin-bottom:1em;">Filtre : 
					<input id="textbox" placeholder="nom de ville, code postal ou département..."
						type="text" style="width:20em;">
					</div>

				<div style="display:inline-block; vertical-align:top;">
					<select id="list1" MULTIPLE SIZE="20" style=" min-width:20em;">
						<?php include($path_from_extranet_to_web.'/src/admin/villes_options_insee.inc'); //la liste des villes de France... todo : à remplacer par $("#villes").autocomplete ?>
					</select>
				</div>

				<div style="display:inline-block; margin-top:1em; vertical-align: top;">
					<INPUT TYPE="button" class="margin1" style="display:block;" NAME="right" VALUE="&gt;&gt;"
						   ONCLICK="moveSelectedOptions(this.form['list1'],this.form['list2'],true)">

					<INPUT TYPE="button" class="margin1" style="display:block;" NAME="left" VALUE="&lt;&lt;"
						   ONCLICK="moveSelectedOptions(this.form['list2'],this.form['list1'],true)">
				</div>

				<?php } ?>
				<div style="display:inline-block;  vertical-align:top;">
					<select name="list2[]" id="list2" MULTIPLE SIZE="20"
							style=" min-width:20em;">
				<?php 
				if (isset($villes)){
					foreach($villes as $row){ ?>
						<option value="<?= $row['code_insee'] ?>"><?= $row['nom_ville'].' '. $row['code_postal'] ?></option>
				<?php
					}
				} ?>
					</select>
				</div>

				<?php if($droit_ecriture) { ?>
				<input style="display:block; margin:2em auto 0 auto;" type="submit" name="submit_villes"
					   value="Enregistrer le périmètre du territoire">
				<?php } ?>
			</div>
		</fieldset>

		<?php } ?>

	</form>
</div>
</body>
</html>

<?php
if (DEBUG) { 
	$timestamp_fin = microtime(true);
	$difference_ms = $timestamp_fin - $timestamp_debut;
	echo '<pre>Exécution du script : ' . substr($difference_ms,0,6) . ' secondes.</pre>'; 
}
?>