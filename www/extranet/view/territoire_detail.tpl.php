<!--<?php @print_r($_POST); ?>-->
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

	/*editeur de texte*/
	function commande(nom, argument)
	{
		if (typeof argument === 'undefined')
		{
			argument = '';
		}
		switch (nom)
		{
			case "createLink":
				argument = prompt("Quelle est l'adresse du lien ?", "http://");
				break;
			case "insertImage":
				argument = prompt("Quelle est l'adresse de l'image ?");
				break;
		}
		// Exécuter la commande
		document.execCommand(nom, false, argument);
		if (nom == "createLink")
		{
			var selection = document.getSelection();
			selection.anchorNode.parentElement.target = '_blank';
		}
	}
	function htmleditor()
	{
		document.getElementById("resultat").value = document.getElementById("editeur").innerHTML;
	}
		
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

	<h2><small><a href="accueil.php">Accueil</a> > <a href="territoire_liste.php">Territoires</a> ></small> 
		<?= ($id_territoire) ? 'Détail' : 'Création'; ?> d'un territoire 
		<?= (isset($territoire['actif']) && $territoire['actif'] == 0) ? '<span style="color:red">(archivé)</span>':'' ?></h2>

	<div class="soustitre"><?php echo $msg; ?></div>

	<form method="post" class="detail" onsubmit='htmleditor(); checkall();'>
		<fieldset <?= (!$droit_ecriture) ? 'disabled="disabled"':'' ?>> <!--class="centre"--> 
			<div class="une_colonne">
				<div class="lab">
					<label for="libelle_territoire">Libellé :</label>
					<input type="text" required name="libelle_territoire" value="<?= (isset($territoire['nom_territoire'])) ? $territoire['nom_territoire']:'' ?>">
				</div>
				<div class="lab">
					<label for="desc">Description du territoire :</label>
					<div style="display:inline-block;" id="div-editeur">
						<input type="button" value="G" style="font-weight: bold;" onclick="commande('bold');"/>
						<input type="button" value="I" style="font-style: italic;" onclick="commande('italic');"/>
						<input type="button" value="S" style="text-decoration: underline;" onclick="commande('underline');"/>
						<input type="button" value="Lien" onclick="commande('createLink');"/>
						<input type="button" value="Image" onclick="commande('insertImage');"/>
						<div id="editeur" contentEditable ><?= (isset($territoire['description_territoire'])) ? bbcode2html($territoire['description_territoire']) : '' ?></div>
						<input id="resultat" type="hidden" name="desc"/>
					</div>
				</div>
			</div>
			<input type="hidden" name="maj_id_territoire" value="<?php echo $id_territoire; ?>">
		</fieldset>

		<?php //if ($id_territoire) { ?>

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

			</div>
		</fieldset>

		<?php //} ?>
		
		<div class="button">
			<input type="button" value="Retour" onclick="javascript:location.href='territoire_liste.php'">
		<?php if(isset($territoire['actif'])) {
			if ($territoire['actif'] == 0){ ?>
			<input type="submit" name="restaurer" value="Restaurer">
		<?php }else{ ?>
			<input type="submit" name="archiver" value="Archiver">
		<?php } } ?>
		<?php if($droit_ecriture) { ?>
			<input type="submit" name="submit" value="Enregistrer">
		<?php } ?>
		</div>

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