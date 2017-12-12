<?php
//accès par défaut à national + tous les territoires
$ld_territoires = [];
echo '<!--'.$perimetre_lecture.'-->';
if (isset($perimetre_lecture)){
	switch($perimetre_lecture){
		case PERIMETRE_NATIONAL :
			$ld_territoires[] = array('id_territoire'=>0, 'nom_territoire'=>'National');
			$ld_territoires = array_merge($ld_territoires, get_territoires(null, 1));
			if (secu_check_role(ROLE_PRO)) {
				$ld_territoires[] = array('id_territoire'=>'PRO', 'nom_territoire'=>$_SESSION['nom_pro']);
			}
			break;
		case PERIMETRE_ZONE :
			$ld_territoires = get_territoires($_SESSION['territoire_id'], 1);
			if (secu_check_role(ROLE_PRO)) {
				$ld_territoires[] = array('id_territoire'=>'PRO', 'nom_territoire'=>$_SESSION['nom_pro']);
			}
			break;
		case PERIMETRE_PRO :
			$ld_territoires[] = array('id_territoire'=>'PRO', 'nom_territoire'=>$_SESSION['nom_pro']);
			break;
	}
}else{
	$ld_territoires[] = array('id_territoire'=>0, 'nom_territoire'=>'! National');
	$ld_territoires = array_merge($ld_territoires, get_territoires(null, 1));
}
?>

<form method="post" class="liste_territoire">
	<label for="choix_territoire">Périmètre :</label>
	<select name="choix_territoire" onchange="this.form.submit()">

		<?php foreach ($ld_territoires as $row) { ?>
		<option value="<?= $row['id_territoire'] ?>"
			<?= (isset($_SESSION['perimetre'])) && ($_SESSION['perimetre'] == $row['id_territoire']) ? 'selected' : '' ?>>
			<?= $row['nom_territoire'] ?></option>
		<?php } ?>
	</select>
</form>