<!DOCTYPE html>
<html>
<head>
	<?php include('view/inc.head.php'); ?>
	
	<script type="text/javascript" language="javascript" src="../src/js/external/jquery/jquery.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery.filterByText.js"></script>
	<script type="text/javascript" language="javascript" src="js/selectbox.js"></script>
	<script type="text/javascript" language="javascript" src="js/fix-ie.js"></script>
	<script type="text/javascript" language="javascript">
	function ajoutLigne() {
		var table = document.getElementById("tableau");
		var row = table.insertRow(-1);
		var x = document.getElementById("tableau").rows.length-2;
		var cell1 = row.insertCell(0);
		var cell2 = row.insertCell(1);
		var cell3 = row.insertCell(2);
		cell1.innerHTML = "<input type=\"hidden\" name=\"sthemes["+x+"][]\" value=\"\"> <input name=\"sthemes["+x+"][]\" type=\"text\" class=\"input_treslong\" value=\"\">\r\n";
		cell2.innerHTML = "<input name=\"sthemes["+x+"][]\" type=\"text\" class=\"input_int\" value=\"\">\r\n";
		cell3.innerHTML = "<input type=\"radio\" name=\"sthemes["+x+"][]\" value=\"1\" checked> Oui <input type=\"radio\" name=\"sthemes["+x+"][]\" value=\"0\" > Non\r\n";
	}
	</script>
</head>

<body>
<?php include('view/inc.bandeau.php'); ?>

<div class="container">
	
	<h2><small><a href="accueil.php">Accueil</a> > <a href="theme_liste.php">Thèmes</a> ></small> 
		<?= ($flag_duplicate ? 'Déclinaison': ($id_theme_choisi ? 'Détail' : 'Création' )) ?>  du thème</h2>
		
	<div class="soustitre"><?php echo $msg; ?></div>

	<form method="post" class="detail">
		<fieldset style="margin-bottom:1em;">
			<legend>Description du thème</legend>
			<div class="une_colonne" style="width:auto; min-width:auto;">
				<div class="lab">
					<label for="theme">Thème :</label>
					<div style="display:inline-block"><select required <?= $id_theme_choisi ? ' disabled ' : ' name="theme" ' ?>>
						<option value=''></option>
					<?php foreach ($liste_themes as $row) { ?>
						<option value="<?= $row['libelle'] ?>" <?= (isset($theme['libelle_theme_court']) && $theme['libelle_theme_court'] == $row['libelle']) ? ' selected ' : '' ?> ><?= $row['libelle'] ?></option>
					<?php } ?>
					</select>
					<?php if($id_theme_choisi) { ?>
						<input type="hidden" name="theme" value="<?= $theme['libelle_theme_court'] ?>"/>
					<?php } ?>
					</div>
				</div>
				<br/>
				<div class="lab">
					<label for="territoire">Territoire :</label>
					<div style="display:inline-block"><select required <?= ($id_theme_choisi && !$flag_duplicate) ? ' disabled ' : ' name="territoire" ' ?>>
						<?php if (!$flag_duplicate) { ?><option value='0'>National</option><?php }else{ ?><option value=''></option><?php } ?>
						<?php foreach ($territoires as $row) { ?>
						<option value="<?= $row['id_territoire'] ?>" <?= (isset($theme['id_territoire']) && $theme['id_territoire'] == $row['id_territoire']) ? ' selected ' : '' ?> ><?= $row['nom_territoire'] ?></option>
						<?php } ?>
					</select>
					<?php if($id_theme_choisi && !$flag_duplicate) { ?>
						<input type="hidden" name="territoire" value="<?= $theme['id_territoire'] ?>"/>
					<?php } ?>
					</div>
				</div>
				<br/>
				<div class="lab">
					<label for="libelle_theme">Libellé du bouton web :</label>
					<input type="text" name="libelle_theme" value="<?= (isset($theme['libelle_theme'])) ? $theme['libelle_theme'] : '' ?>">
				</div>
				<br/>
				<div class="lab">
					<label for="actif">Actif :</label>
					<input type="radio" name="actif" value="1" <?php if (!isset($theme['actif_theme']) || $theme['actif_theme'] == 1) echo "checked"; ?>> Oui
					<input type="radio" name="actif" value="0" <?php if (isset($theme['actif_theme']) && $theme['actif_theme'] == 0) echo "checked"; ?>> Non

				</div>
			</div>
		</fieldset>

		<fieldset style="margin-bottom:1em;">
			<legend>Catégories d'offres de service</legend>
			
				<table id="tableau" class="dataTable display compact">
					<thead>
					<tr>
						<th>Libellé</th>
						<th>Ordre</th>
						<th>Actif</th>
					</tr>
					</thead>
					<tbody>
			<?php
			if (isset($sous_themes) && count($sous_themes) > 0) {
				$i=0;
				foreach($sous_themes as $row) {
					if ($row['id_theme_pere'] == $id_theme_choisi) {
			?>
						<tr>
							<td>
								<input type="hidden" name="sthemes[<?= $i ?>][]"
									   value="<?= ($flag_duplicate) ? '' : $row['id_theme'] ?>"/>
								<input type="text" name="sthemes[<?= $i ?>][]"
									   value="<?= $row['libelle_theme'] ?>" class="input_treslong" />
							</td>
							<td>
								<input type="text" name="sthemes[<?= $i ?>][]"
									   value="<?= $row['ordre_theme'] ?>" class="input_int"/>
							</td>
							<td>
								<input type="radio" name="sthemes[<?= $i ?>][]"
									   value="1" <?= ($row['actif_theme'] == 1) ? "checked" : "" ?>> Oui
								<input type="radio" name="sthemes[<?= $i ?>][]"
									   value="0" <?= ($row['actif_theme'] == 0) ? "checked" : "" ?>> Non
							</td>
						</tr>
			<?php
						$i++;
					}
				}
			}	
			?>
					</tbody>
				</table>
				<?php

			?>
			
			<div class="button" style="font-size:80%"><a href="#" onclick="ajoutLigne();">Ajouter une ligne au tableau</a></div>

		</fieldset>

		<div class="button">
			<?php if($id_theme_choisi && !$flag_duplicate) { ?>
				<input type="hidden" name="maj_id_theme" value="<?= xssafe($id_theme_choisi) ?>" />
			<?php } ?>
			
			<input type="button" value="Retour à la liste" onclick="javascript:location.href='theme_liste.php'"> 
			
			<?php if ($id_theme_choisi && $theme['actif_theme'] == 1 && !$flag_duplicate) { ?>
				<input type="button" value="Décliner sur un territoire" onclick="javascript:location.href='theme_detail.php?id=<?= (int) $id_theme_choisi ?>&act=dup'">
			<?php } ?>
			<!--
			<?php if($theme['actif_theme'] == 0){ ?>
				<input type="submit" name="restaurer" value="Restaurer">
			<?php }else{ ?>
				<input type="submit" name="archiver" value="Archiver">
			<?php } ?>
			-->
			<?php if($droit_ecriture) { ?>
				<input type="submit" name="enregistrer" value="Enregistrer">
			<?php } ?>
		</div>

	</form>
</div>
</body>
</html>