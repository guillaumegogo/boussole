<!DOCTYPE html>
<html>
<head>
	<?php include('view/inc.head.php'); ?>
	
	<script type="text/javascript" language="javascript" src="../src/js/external/jquery/jquery.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
</head>

<body>
<?php include('view/inc.bandeau.php'); ?>

<div class="container">

	<h2><small><a href="accueil.php">Accueil</a> > <a href="formulaire_liste.php">Formulaires</a> ></small>
		<?= ($flag_duplicate ? 'Déclinaison': ($id_formulaire ? 'Détail' : 'Création' )) ?>  du formulaire</h2>

	<div class="soustitre"><?php echo $msg; ?></div>
	
	<form method="post" class="detail">
	<fieldset <?= (!$droit_ecriture ) ? 'disabled="disabled"':'' ?> > 
		<legend>Description du formulaire</legend> 
		<div class="lab">
			<label for="theme">Thème / territoire :</label>
		<?php if(!count($themes)){ ?>
			<span class="notice">Tous les thèmes déclarés ont déjà un formulaire associé.</span>
		<?php } else { ?>
			<select <?= ($id_formulaire && !$flag_duplicate) ? ' disabled ' : ' name="theme" ' ?> required>
		<?php foreach($themes as $row) { ?>
			<option value="<?= $row['id_theme'] ?>" <?= (isset($meta['id_theme']) && $row['id_theme']==$meta['id_theme']) ? ' selected ':'' ?>> <?= $row['libelle_theme_court'].' / '.($row['nom_territoire'] ? $row['nom_territoire'] : 'national') ?></option>
		<?php } ?>
			</select>
		<?php if($id_formulaire && !$flag_duplicate) { ?>
			<input type="hidden" name="theme" value="<?= $meta['id_theme'] ?>"/>
		<?php } ?>
		</div>
		
		<div class="lab" style="display:block;">
		<label for="" style="width:auto; margin-bottom:1em">Titre des pages et liste des questions :</label>
		</div>
		<table class="dataTable display compact">
			<thead>
			<tr>
				<th><abbr title="Numéro de page / numéro de question">Ordre</abbr></th>
				<th>Libellé</th>
				<th>Identifiant</th>
				<th>Réponses</th>
				<th>Affichage</th>
				<th>Requis ?</th>
				<th>&nbsp;</th>
			</thead>
			<tbody>
			<?php
			//foreach ($pages as $page) {
			for ($i = 0; $i < $max_pages; $i++) {
				$pid= (isset($pages[$i]['id'])) ? $pages[$i]['id'] : null;
			?>
				<tr>
					<td class="page"><input name="id_p[<?= $i ?>]" type="hidden" value="<?= (!$flag_duplicate) ? $pid:'' ?>"> <input name="ordre_p[<?= $i ?>]" type="text" style="width:1em" 
						value="<?php if(isset($pages[$i]['ordre'])) { xecho($pages[$i]['ordre']); } else { echo $i+1; } ?>"></td>
					<td class="page"><input name="titre_p[<?= $i ?>]" type="text" class="input_long" 
						value="<?php if(isset($pages[$i]['titre'])) { xecho($pages[$i]['titre']); } ?>"></td>
					<td class="page"></td>
					<td colspan="3" class="page"></td>
					<td class="page"><?php if($pid && empty(array_filter_recursive($questions[$pid]))) { //on ne peut supprimer une page que si elle existe (pid) et qu'elle ne contient plus de question ?><a href="?id=<?= $id_formulaire ?>&act=dp&i=<?= $pid ?>" onclick="return confirm('Voulez-vous supprimer cette page ?')"><img src="img/cancel.png" width="16px"></a><?php } ?></td>
				</tr>
				
				<?php
				$nb_questions = (isset($questions[$pid])) ? count($questions[$pid]) : 0;
				for ($j = 0; $j < max($max_questions_par_page, $nb_questions); $j++) {
					$qid= (isset($questions[$pid][$j]['id'])) ? $questions[$pid][$j]['id'] : null;
				?>
				<tr>
					<td><input name="id_q[<?= $i ?>][<?= $j ?>]" type="hidden" value="<?php if(!$flag_duplicate) { xecho($qid); } ?>"> <input name="page_q[<?= $i ?>][<?= $j ?>]" type="text" style="width:1em" 
						value="<?php if(isset($pages[$i]['ordre'])) { xecho($pages[$i]['ordre']); } else { echo $i+1; } ?>" >.
						<input name="ordre_q[<?= $i ?>][<?= $j ?>]" type="text" style="width:1em" 
						value="<?php if(isset($questions[$pid][$j]['ordre'])) { xecho($questions[$pid][$j]['ordre']); } else { echo $j+1;} ?>" ></td>
					<td><input name="titre_q[<?= $i ?>][<?= $j ?>]" type="text" class="input_long"
						 value="<?php if(isset($questions[$pid][$j]['libelle'])) xecho($questions[$pid][$j]['libelle']); ?>"></td>
					<td><input <?= (isset($questions[$pid][$j]['name'])) ? 'readonly' : '' ?> name="name_q[<?= $i ?>][<?= $j ?>]" type="text" style="width:10em" placeholder="identifiant unique du champ"
						 value="<?php if(isset($questions[$pid][$j]['name'])) xecho($questions[$pid][$j]['name']); ?>"></td>
					<td><select name="reponse_q[<?= $i ?>][<?= $j ?>]" style="width:12em" >
						<?php 
						foreach($reponses as $row) { ?>
							<option value="<?= $row['id_reponse'] ?>" <?= (isset($questions[$pid][$j]['id_reponse']) && $row['id_reponse']==$questions[$pid][$j]['id_reponse']) ? ' selected ':'' ?>> <?= $row['libelle'] ?></option>
						<?php } ?>
						</select>
						<?php if($droit_ecriture && isset($questions[$pid][$j]['id_reponse'])){?>
							<a href="formulaire_reponse.php?id=<?= $questions[$pid][$j]['id_reponse'] ?>"><img src="img/find.png"></a>
						<?php } ?>
					</td>
					<td><select name="type_q[<?= $i ?>][<?= $j ?>]" style="width:12em" >
					<?php foreach($types as $key=>$val){ ?>
						<option value="<?= $key ?>" <?php if (isset($questions[$pid][$j]['type']) && $questions[$pid][$j]['type']==$key) echo 'selected'; ?>><?= $val ?></option>
					<?php } ?>
					</select></td>
					<td><input type="checkbox" name="requis[<?= $i ?>][<?= $j ?>]" value="1" <?= (isset($questions[$pid][$j]['obligatoire']) && $questions[$pid][$j]['obligatoire']) ? ' checked ':'' ?>></td>
					
					<td><?php if($qid) { ?><a href="?id=<?= $id_formulaire ?>&act=dq&i=<?= $qid ?>" onclick="return confirm('Voulez-vous supprimer cette question ?')"><img src="img/cancel.png" width="16px"></a><?php } ?></td>
				</tr>
				<?php
				}
			}
			?>
			</tbody>
		</table>
		
		<div class="notice centre" style="margin-bottom:1em">Conseils sur le choix de la colonne affichage : choisir "option" pour un choix unique avec nombre limité de choix (<4), "liste déroulante" pour un choix unique avec de nombreux choix, "coche" pour choix multiple avec nombre limité de choix.</div>
		<?php } ?>
	</fieldset>
	
	<div class="button">
		<input type="hidden" name="maj_id" value="<?= (!$flag_duplicate) ? xssafe($id_formulaire) : '' ?>" />
		<input type="button" value="Retour à la liste" onclick="javascript:location.href='formulaire_liste.php'"> 
	
	<?php if($droit_ecriture) { 
		if ($id_formulaire && !$flag_duplicate) {?>
		<input type="submit" name="decliner" value="Décliner sur un territoire">
		<!--<input type="button" value="Décliner sur un territoire" onclick="javascript:location.href='formulaire_detail.php?id=<?= (int) $id_formulaire ?>&act=dup'">-->
	<?php 
		if($meta['actif'] == 0){ 
	?>
		<input type="submit" name="restaurer" value="Restaurer">
	<?php }else{ ?>
		<input type="submit" name="archiver" value="Archiver">
	<?php } } ?>
		<input type="submit" name="enregistrer" value="Enregistrer">
	<?php } ?>
	</div>
	
	</form>
</div>
</body>
</html>