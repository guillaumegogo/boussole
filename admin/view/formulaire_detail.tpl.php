<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" href="css/style_backoffice.css"/>
	<link rel="stylesheet" href="css/jquery.dataTables.min.css"/>
	<link rel="icon" type="image/png" href="img/compass-icon.png"/>
	<script type="text/javascript" language="javascript" src="js/jquery-1.12.0.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
	<title>Boussole des jeunes</title>
</head>

<body>
<h1 class="bandeau"><img src="../web/img/marianne.png" width="93px"> Administration de la boussole</h1>
<div class="statut"><?php xecho($_SESSION['accroche']); ?> (<a href="index.php">déconnexion</a>)</div>

<div class="container">

	<h2><small><a href="accueil.php">Accueil</a> > <a href="formulaire_liste.php">Liste des formulaires</a> ></small>
		Détail du formulaire</h2> 

	<div class="soustitre"><?php echo $msg; ?></div>
	
	<form method="post" class="detail">
	<fieldset>
		<legend>Détails</legend> <!--(<?= $meta['theme'].' '.$meta['territoire'] ?>)-->
		Thème : <select name="theme" <?= (isset($meta['theme'])) ? 'disabled':'' ?>>
		<?php foreach($themes as $row) { ?>
			<option required value="<?= $row['id_theme'] ?>" <?= (isset($meta['theme']) && $row['libelle_theme_court']==$meta['theme']) ? ' selected ':'' ?>> <?= $row['libelle_theme_court'] ?></option>
		<?php } ?>
			</select>
		&#8231; Territoire : <select name="territoire" <?= (isset($meta['territoire'])) ? 'disabled':'' ?>>
		<?php foreach($territoires as $row) { ?>
			<option required value="<?= $row['id_territoire'] ?>" <?= (isset($meta['territoire']) && $row['nom_territoire']==$meta['territoire']) ? ' selected ':'' ?>> <?= $row['nom_territoire'] ?></option>
		<?php } ?>
			</select>
	</fieldset>
	
	<fieldset>
		<legend>Pages et questions</legend>
		
		<table class="dataTable display compact">
			<thead>
			<tr>
				<th>Type</th>
				<th>Ordre</th>
				<th>Libellé</th>
				<th>Identifiant</th>
				<th>Réponses</th>
				<th>Affichage <img src="img/help.png" height="16px" title="Conseils : option pour choix unique avec nombre limité de choix | liste déroulante pour choix unique avec de nombreux choix | coche pour choix multiple avec nombre limité de choix..."></th>
			</thead>
			<tbody>
			<?php
			//foreach ($pages as $page) {
			for ($i = 0; $i < $max_pages; $i++) {
				$pid= (isset($pages[$i]['id'])) ? $pages[$i]['id'] : null;
			?>
				<tr>
					<td class="page">page <input name="id_p[<?= $i ?>]" type="hidden" value="<?= $pid ?>"></td>
					<td class="page"><input name="ordre_p[<?= $i ?>]" type="text" style="width:1em" 
						value="<?php if(isset($pages[$i]['ordre'])) { xecho($pages[$i]['ordre']); } else { echo $i+1; } ?>"></td>
					<td class="page"><input name="titre_p[<?= $i ?>]" type="text" class="input_long" 
						value="<?php if(isset($pages[$i]['titre'])) { xecho($pages[$i]['titre']); } ?>"></td>
					<td class="page"></td>
					<td colspan=2 class="page"></td>
				</tr>
				
				<?php
				//foreach ($questions[$pid] as $question) {
				for ($j = 0; $j < $max_questions_par_page; $j++) {
				?>
				<tr>
					<td>&#8735; question <input name="id_q[<?= $i ?>][<?= $j ?>]" type="hidden" value="<?php if(isset($questions[$pid][$j]['id'])) { xecho($questions[$pid][$j]['id']); } ?>"></td>
					<td><input name="page_q[<?= $i ?>][<?= $j ?>]" type="text" style="width:1em" 
						value="<?php if(isset($pages[$i]['ordre'])) { xecho($pages[$i]['ordre']); } ?>" >. 
						<input name="ordre_q[<?= $i ?>][<?= $j ?>]" type="text" style="width:1em" 
						value="<?php if(isset($questions[$pid][$j]['ordre'])) { xecho($questions[$pid][$j]['ordre']); } else { echo $j+1;} ?>" ></td>
					<td><input name="titre_q[<?= $i ?>][<?= $j ?>]" type="text" class="input_long"
						 value="<?php if(isset($questions[$pid][$j]['libelle'])) xecho($questions[$pid][$j]['libelle']); ?>"></td>
					<td><input <?= (isset($questions[$pid][$j]['name'])) ? 'readonly' : '' ?> name="name_q[<?= $i ?>][<?= $j ?>]" type="text" style="width:10em" placeholder="identifiant unique du champ"
						 value="<?php if(isset($questions[$pid][$j]['name'])) xecho($questions[$pid][$j]['name']); ?>"></td>
					<td><select name="reponse_q[<?= $i ?>][<?= $j ?>]" style="width:12em" >
						<?php foreach($reponses as $row) { ?>
							<option value="<?= $row['id_reponse'] ?>" <?= (isset($questions[$pid][$j]['id_reponse']) && $row['id_reponse']==$questions[$pid][$j]['id_reponse']) ? ' selected ':'' ?>> <?= $row['libelle'] ?></option>
						<?php } ?>
						</select>
						<?php if(isset($questions[$pid][$j]['id_reponse'])){?>
							<a href="formulaire_reponse.php?id=<?= $questions[$pid][$j]['id_reponse'] ?>"><img src="img/find.png"></a>
						<?php } ?>
					</td>
					<td><select name="type_q[<?= $i ?>][<?= $j ?>]" style="width:12em" >
					<?php foreach($types as $key=>$val){ ?>
						<option value="<?= $key ?>" <?php if (isset($questions[$pid][$j]['type']) && $questions[$pid][$j]['type']==$key) echo 'selected'; ?>><?= $val ?></option>
					<?php } ?>
					</select></td>
				</tr>
				<?php
				}
			}
			?>
			</tbody>
		</table>
	</fieldset>
	
	<div class="button">
		<input type="hidden" name="maj_id" value=<?= xssafe($id_formulaire) ?> />
		<input type="button" value="Retour à la liste" onclick="javascript:location.href='formulaire_liste.php'"> 
		<input type="button" disabled value="Créer de nouvelles réponses" onclick="javascript:location.href='formulaire_reponse.php'"> 
		
	<?php if (!$id_formulaire) {	?>
		<input type="reset" value="Reset">
	<?php }else{ if($meta['actif'] == 0){ ?>
		<input type="submit" name="restaurer" value="Restaurer">
	<?php }else{ ?>
		<input type="submit" name="archiver" value="Archiver">
	<?php } } ?>
		<input type="submit" name="enregistrer" value="Enregistrer">
	</div>
	
	</form>
</div>
</body>
</html>