<?php

include('../src/admin/bootstrap.php');
secu_check_login(DROIT_UTILISATEUR);
/* todo...
if (secu_check_auth(DROIT_UTILISATEUR)){ // si on a les droits, on fait juste un test sur le territoire (cas des animateurs territoriaux notamment)
	if($_SESSION['territoire_id']){
		$result = verif_territoire_user($_SESSION['territoire_id'], $_GET['id']);
		if (mysqli_num_rows($result) == 0) { header('Location: utilisateur_liste.php'); }
	}
}else{ //autrement, le seul cas possible est la consultation de ses propres infos
	$_GET['id'] = secu_get_current_user_id();
}*/

//********* variables
$last_id = null;
$msg = '';

if (isset($_POST['maj_id'])) { //si post du formulaire interne
	if (!$_POST["maj_id"]) { //requête d'ajout

		$maj_attache = "NULL";
		if (isset($_POST["statut"])) {
			if ($_POST["statut"] == ROLE_ANIMATEUR && isset($_POST["attache"])) 
				$maj_attache = $_POST["attache"];
			else if ($_POST["statut"] == ROLE_PRO && isset($_POST["attache_p"])) 
				$maj_attache = $_POST["attache_p"];
		}
		if ($_POST["nouveaumotdepasse"] === $_POST["nouveaumotdepasse2"] && strlen($_POST["nouveaumotdepasse"]) >= PASSWD_MIN_LENGTH) {

			$created = create_user($_POST["nom_pouet"], $_POST["courriel"], secu_password_hash($_POST["nouveaumotdepasse"]), $_POST["statut"], $maj_attache);
			if ($created) {
				$last_id = mysqli_insert_id($conn);
				$msg = 'Utilisateur bien créé.';
			} else {
				$msg = $message_erreur_bd;
			}
		} else {
			$msg = 'Les mots de passe saisis doivent correspondre et faire au moins '.PASSWD_MIN_LENGTH.' caractères.';
		}

	} else { //requête de modification
		if (!isset($_POST["nouveaumotdepasse"])) { //modif normale
			$updated = update_user((int)$_POST['maj_id'], $_POST["nom_pouet"], $_POST["courriel"], $_POST["actif"]);

			if ($updated) {
				$msg = 'Modification bien enregistrée.';
			} else {
				$msg = $message_erreur_bd;
			}
		} else { //modif mot de passe
			if ($_POST["nouveaumotdepasse"] === $_POST["nouveaumotdepasse2"] && strlen($_POST["nouveaumotdepasse"]) >= PASSWD_MIN_LENGTH) {
				$updated_mp = update_motdepasse((int)$_POST['maj_id'], $_POST['motdepasseactuel'], $_POST["nouveaumotdepasse"] );

				if ($updated_mp[0]) {
					$msg = $updated_mp[1];
				} else {
					$msg = $message_erreur_bd;
				}
			} else {
				$msg = 'Les mots de passe saisis doivent correspondre et faire au moins '.PASSWD_MIN_LENGTH.' caractères.';
			}
		}
		$last_id = $_POST["maj_id"];
	}
}

//*********** affichage de l'utilisateur demandé ou nouvellement créé
$attache = '';
$id_utilisateur = $last_id;
if (isset($_GET['id'])) {
	$id_utilisateur = $_GET['id'];
}
if (isset($id_utilisateur)) {
	$row = get_user_by_id((int)$id_utilisateur);

	if (count($row) > 0) {
		if ($row['id_statut'] == ROLE_ANIMATEUR) {
			$attache = $row['nom_territoire'];
		} else if ($row['id_statut'] == ROLE_PRO) {
			$attache = $row['nom_pro'];
		}

	} else {
		if (!$msg) $msg = '<div class="soustitre">Cet utilisateur est inconnu.</div>';
	}
}

//********************* listes
//si création, liste = liste du/des territoire(s) et des pros du/des territoire(s), avec tout en display none
//si modif = affichage en disabled du territoire ou de la liste des pros, en fonction de la liste

$param_territoire = null;
if (secu_check_role(ROLE_ANIMATEUR)) {
	$param_territoire = $_SESSION['territoire_id'];
}

$select_territoire = '<option value="" >A choisir</option>';
$liste_territoires = get_territoires($param_territoire);
foreach($liste_territoires as $row2) {
	$select_territoire .= '<option value="' . $row2['id_territoire'] . '" ';
	if (isset($row['id_territoire']) && ($row2['id_territoire'] == $row['id_territoire'])) {
		$select_territoire .= 'selected';
	}
	$select_territoire .= '>' . $row2['nom_territoire'] . '</option>';
}

$select_professionnel = '<option value="" >A choisir</option>';
$liste_pro = get_liste_pros_select($param_territoire);
foreach($liste_pro as $row3) {
	$select_professionnel .= '<option value="' . $row3['id_professionnel'] . '" ';
	if (isset($row['id_professionnel']) && ($row3['id_professionnel'] == $row['id_professionnel'])) {
		$select_professionnel .= 'selected';
	}
	$select_professionnel .= '>' . $row3['nom_pro'] . '</option>';
}

//type de formulaire à afficher
if (isset($_GET["do"]) && $_GET["do"] == "mdp") {
	$vue = "motdepasse";
} else if ($id_utilisateur) {
	$vue = "modif";
} else {
	$vue = "creation";
}

//view
require 'view/utilisateur_detail.tpl.php';