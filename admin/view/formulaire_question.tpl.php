<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" href="css/style_backoffice.css"/>
	<link rel="icon" type="image/png" href="img/compass-icon.png"/>
	<title>Boussole des jeunes</title>
</head>

<body>
<h1 class="bandeau">Administration de la boussole des jeunes</h1>
<div class="statut"><?php xecho($_SESSION['accroche']); ?> (<a href="index.php">déconnexion</a>)</div>

<div class="container">
	<h2><small><a href="accueil.php">Accueil</a> ></small> Détails de la question</h2>
	<?php echo $msg; ?>

	<?php
	if ($question !== null) {
	?>
		
		<form method="post">
		<input type="hidden" name="id_maj" value="<?php xecho($question['id']); ?>" />
		
		<table class="dataTable display compact">
			<thead>
			<tr>
				<th>Nom</th>
				<th>Libellé</th>
				<th>Type</th>
				<th <?php if ($question['type']!="multiple") echo "style='display:none;'"; ?>>Taille</th>
				<th>Obligatoire</th>
			</tr>
			<tr>
				<td><input name="name_maj" type="text" value="<?php xecho($question['name']); ?>"> <abbr title="doit être unique pour le formulaire">&#9888;</abbr></td>
				<td><input name="libelle_maj" type="text" value="<?php xecho($question['libelle']); ?>"></td>
				<td>
					<select name="type_maj">
					<?php foreach($liste_types as $key=>$val){ ?>
						<option value="<?= $key ?>" <?php if ($question['type']==$key) echo 'selected'; ?>><?= $val ?></option>
					<?php } ?>
					</select>
				</td>
				<td <?php if ($question['type']!="multiple") echo "style='display:none;'"; ?> >
					<select name="taille_maj" >
					<?php for($i = $liste_taille['min']; $i < $liste_taille['max'] ;$i++) {?>
						<option value="<?= $i ?>" <?php if ($question['taille']==$i) echo 'selected'; ?>><?= $i ?></option>
					<?php } ?>
					</select>
				</td>
				<td>
					<select name="obligatoire_maj">
					<?php foreach($liste_obligatoire as $key=>$val){ ?>
						<option value="<?= $key ?>" <?php if ($question['obligatoire']==$key) echo 'selected'; ?>><?= $val ?></option>
					<?php } ?>
					</select>
				</td>
			</tr>
		</table>
		
	<h3>Réponses proposées</h3>
		
		<table class="dataTable display compact">
			<thead>
			<tr>
				<th>Ordre</th>
				<th>Libellé</th>
				<th>Valeur</th>
				<th>Choix par défaut</th>
			</tr>
			<?php foreach($reponses as $reponse){ ?>
			<tr>
				<td><input name="ordre_maj_<?php xecho($reponse['ordre']) ?>" type="text" value="<?php xecho($reponse['ordre']) ?>" class="input_int"> </td>
				<td><input name="libelle_maj_<?php xecho($reponse['ordre']) ?>" type="text" value="<?php xecho($reponse['libelle']) ?>"> </td>
				<td><input name="valeur_maj_<?php xecho($reponse['ordre']) ?>" type="text" value="<?php xecho($reponse['valeur']) ?>" disabled> </td>
				<td><input name="defaut_maj_<?php xecho($reponse['ordre']) ?>" type="text" value="<?= ($reponse['defaut']) ? '*' : '' ?>" class="input_int"> </td>
			</tr>
			<?php } ?>
			<tr>
				<td><input name="ordre_maj_nv" type="text" value="" class="input_int"> </td>
				<td><input name="libelle_maj_nv" type="text" value=""> </td>
				<td><input name="valeur_maj_nv" type="text" value=""> </td>
				<td><input name="defaut_maj_nv" type="text" value="" class="input_int"> </td>
			</tr>
		</table>
		
		<div class="button"><input type="submit" value="Enregistrer" disabled></div>
		
		<?php
	} else {
		echo "N° de demande non valide.";
	}
	?>

	<div class="button"><a href="formulaire_detail.php?id=<?php xecho($question['id_formulaire']); ?>">Retour au détail du formulaire</a></div>

</div>
</body>
</html>