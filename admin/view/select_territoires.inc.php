<?php
//si on est admin, on a accès à tous les territoires. sinon, juste son territoire + le national.
if (secu_check_role(ROLE_ADMIN)) {
	$ld_territoires = get_territoires();
}else {
	$ld_territoires = get_territoires($_SESSION['territoire_id']);
}
array_unshift($ld_territoires, array('id_territoire'=>0, 'nom_territoire'=>'National', 'code_territoire'=>''));

if (count($ld_territoires) > 0) {
?>

<form method="post" class="liste_territoire">
	<label for="choix_territoire">Territoire :</label>
	<select name="choix_territoire" onchange="this.form.submit()">

		<?php foreach ($ld_territoires as $row) { ?>
		<option value="<?= $row['id_territoire'] ?>"
			<?= (isset($_SESSION['territoire_id'])) && ($_SESSION['territoire_id'] == $row['id_territoire']) ? 'selected' : '' ?>>
			<?= $row['nom_territoire'] ?></option>
		<?php } ?>
	</select>
</form>
<?php } ?>