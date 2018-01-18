<!DOCTYPE html>
<html>
<head>
	<?php include('view/inc.head.php'); ?>
	
	<script type="text/javascript" language="javascript" src="../src/js/external/jquery/jquery.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
	<script type="text/javascript" language="javascript">
	function ajoutLigne() {
		var table = document.getElementById("tableau");
		var row = table.insertRow(-1);
		var x = document.getElementById("tableau").rows.length-2;
		var cell1 = row.insertCell(0);
		var cell2 = row.insertCell(1);
		var cell3 = row.insertCell(2);
		var cell4 = row.insertCell(3);
		cell1.innerHTML = "<input name=\"libelle_v["+x+"]\" type=\"text\" class=\"input_treslong\" value=\"\">\r\n"; //<input name=\"id_v["+x+"]\" type=\"hidden\" value=\"\">
		cell2.innerHTML = "<input name=\"valeur_v["+x+"]\" type=\"text\" value=\"\">\r\n";
		cell3.innerHTML = "<input name=\"ordre_v["+x+"]\" type=\"text\" class=\"input_int\" value=\"\">\r\n";
		cell4.innerHTML = "<input type=\"checkbox\" name=\"actif[]\" value=\"\" checked >\r\n";
	}
	</script>
</head>

<body>
<?php include('view/inc.bandeau.php'); ?>

<div class="container">

	<form method="get" class="liste_territoire">
		<label for="id">Liste des réponses :</label>
		<select name="id" onchange="this.form.submit()">
			<?php foreach ($liste_reponses as $row) { ?>
			<option value="<?= $row['id_reponse'] ?>"
				<?= ($id_reponse == $row['id_reponse']) ? 'selected' : '' ?>>
				<?= $row['libelle'] ?></option>
			<?php } ?>
		</select>
	</form>

	<h2><small><a href="accueil.php">Accueil</a> > <a href="formulaire_liste.php">Liste des formulaires</a> > <?php if(isset($_SESSION['admin']['dernier_formulaire'])){ ?><a href="formulaire_detail.php?id=<?=$_SESSION['admin']['dernier_formulaire'] ?>">Détail du formulaire</a> ><?php } ?> </small>
		Détail de la réponse</h2> 

	<div class="soustitre"><?php echo $msg; ?></div>
	
	<form method="post" class="detail">
	<fieldset>
		<legend>Détails</legend>
		Identifiant de la réponse : <input type="text" name="libelle" required placeholder="Libellé unique du groupe de valeurs" value="<?= $libelle_reponse ?>">
	</fieldset>
	
	<fieldset>
		<legend>Valeurs</legend>
		
		<table id="tableau" class="dataTable display compact">
			<thead>
			<tr>
				<th>Libellé</th>
				<th>Identifiant <img src="img/help.png" height="16px" title="Cet identifiant doit être différent à chaque ligne du tableau. Il est à usage interne et n'est pas affiché dans le formulaire présenté au jeune."></th>
				<th>Ordre</th>
				<!--<th>Choix par défaut</th>-->
				<th>Actif</th>
			</thead>
			<tbody>
			<?php
			for ($i = 0; $i < $nb_lignes_a_afficher; $i++) {
				$vid= (isset($valeurs[$i]['id_valeur'])) ? $valeurs[$i]['id_valeur'] : null;
			?>
				<tr>
					<td><input name="id_v[<?= $i ?>]" type="hidden" value="<?= $vid ?>">
						<input name="libelle_v[<?= $i ?>]" type="text" class="input_treslong"
						value="<?php if(isset($valeurs[$i]['libelle_valeur'])) { xecho($valeurs[$i]['libelle_valeur']); } ?>"></td>
					<td><input name="valeur_v[<?= $i ?>]" type="text" 
						value="<?php if(isset($valeurs[$i]['valeur'])) { xecho($valeurs[$i]['valeur']); } ?>" <?= (isset($valeurs[$i]['valeur'])) ? 'readonly':'' ?>></td>
					<td><input name="ordre_v[<?= $i ?>]" type="text"  class="input_int" 
						value="<?php if(isset($valeurs[$i]['ordre'])) { xecho($valeurs[$i]['ordre']); } ?>"></td>
					<!--<td><input type="radio" name="defaut" value="<?= $vid ?>" <?= (isset($valeurs[$i]['defaut']) && $valeurs[$i]['defaut']==1) ? 'checked' : '' ?>></td>-->
					<td><input type="checkbox" name="actif[]" value="<?= $vid ?>" <?= (isset($valeurs[$i]['actif']) && $valeurs[$i]['actif']==1) ? 'checked' : '' ?>></td>
				</tr>

			<?php
			}
			?>
			</tbody>
		</table>
		
		<div class="button" style="font-size:80%"><a href="#" onclick="ajoutLigne();">Ajouter une ligne au tableau</a></div>
	</fieldset>
	
	<div class="button">
		<input type="hidden" name="maj_id" value="<?= xssafe($id_reponse) ?>" />
		<input type="button" value="Retour" onclick="history.go(-1)">
		<input type="submit" name="enregistrer-sous" value="Enregistrer sous">
<?php if (secu_check_role(ROLE_ADMIN)) { ?>
		<input type="submit" name="enregistrer" value="Enregistrer">
<?php } ?>
	</div>
	
	</form>
</div>
</body>
</html>