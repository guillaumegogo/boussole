<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" href="css/style_backoffice.css"/>
	<link rel="stylesheet" href="css/jquery.dataTables.min.css"/>
	<link rel="icon" type="image/png" href="img/compass-icon.png"/>
	<script type="text/javascript" language="javascript" src="js/jquery-1.12.0.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
	<script type="text/javascript" language="javascript">
	function ajoutLigne() {
		var table = document.getElementById("tableau");
		var row = table.insertRow(-1);
		var x = document.getElementById("tableau").rows.length;
		var cell1 = row.insertCell(0);
		var cell2 = row.insertCell(1);
		var cell3 = row.insertCell(2);
		var cell4 = row.insertCell(3);
		var cell5 = row.insertCell(4);
		cell1.innerHTML = "<input name=\"id_v["+x+"]\" type=\"hidden\" value=\"\"><input name=\"libelle_v["+x+"]\" type=\"text\" class=\"input_long\" value=\"\">";
		cell2.innerHTML = "<input name=\"valeur_v["+x+"]\" type=\"text\" class=\"input_long\" value=\"\">";
		cell3.innerHTML = "<input name=\"ordre_v["+x+"]\" type=\"text\" style=\"width:2em\" value=\"\">";
		cell4.innerHTML = "<input type=\"radio\" name=\"defaut\" value=\"\" >";
		cell5.innerHTML = "<input type=\"checkbox\" name=\"actif[]\" value=\"\" >";
	}
	</script>
	<title>Boussole des jeunes</title>
</head>

<body>
<h1 class="bandeau"><img src="../web/img/marianne.png" width="93px"> Administration de la boussole</h1>
<div class="statut"><?php xecho($_SESSION['accroche']); ?> (<a href="index.php">déconnexion</a>)</div>

<div class="container">

	<h2><small><a href="accueil.php">Accueil</a> > <a href="formulaire_liste.php">Liste des formulaires</a> ></small>
		Détail de la réponse</h2> 

	<div class="soustitre"><?php echo $msg; ?></div>
	
	<form method="post" class="detail">
	<fieldset>
		<legend>Détails</legend>
		Identifiant de la réponse : <input type="text" required placeholder="Libellé unique du groupe de valeurs" value="<?= $libelle_reponse ?>">
	</fieldset>
	
	<fieldset>
		<legend>Valeurs</legend>
		
		<table id="tableau" class="dataTable display compact">
			<thead>
			<tr>
				<th>Libellé</th>
				<th>Valeur</th>
				<th>Ordre</th>
				<th>Choix par défaut</th>
				<th>Actif</th>
			</thead>
			<tbody>
			<?php
			for ($i = 0; $i < $nb_lignes_a_afficher; $i++) {
				$vid= (isset($valeurs[$i]['id_valeur'])) ? $valeurs[$i]['id_valeur'] : null;
			?>
				<tr>
					<td><input name="id_v[<?= $i ?>]" type="hidden" value="<?= $vid ?>">
						<input name="libelle_v[<?= $i ?>]" type="text" class="input_long"
						value="<?php if(isset($valeurs[$i]['libelle_valeur'])) { xecho($valeurs[$i]['libelle_valeur']); } ?>"></td>
					<td><input name="valeur_v[<?= $i ?>]" type="text" class="input_long"
						value="<?php if(isset($valeurs[$i]['valeur'])) { xecho($valeurs[$i]['valeur']); } ?>" <?= (isset($valeurs[$i]['valeur'])) ? 'readonly':'' ?>></td>
					<td><input name="ordre_v[<?= $i ?>]" type="text"  style="width:2em" 
						value="<?php if(isset($valeurs[$i]['ordre'])) { xecho($valeurs[$i]['ordre']); } ?>"></td>
					<td><input type="radio" name="defaut" value="<?= $vid ?>" <?= (isset($valeurs[$i]['defaut']) && $valeurs[$i]['defaut']==1) ? 'checked' : '' ?>></td>
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
		<input type="hidden" name="maj_id" value=<?= xssafe($id_reponse) ?> />
		<input type="button" value="Retour" onclick="history.go(-1)">
		<input type="submit" disabled name="enregistrer" value="Enregistrer">
	</div>
	
	</form>
</div>
</body>
</html>