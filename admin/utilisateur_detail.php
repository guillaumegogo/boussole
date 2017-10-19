<?php

include('../src/admin/bootstrap.php');
$droit_ecriture = (isset($_GET['id'])) ? secu_check_level(DROIT_UTILISATEUR, $_GET['id']) : true;

//********* variables
$last_id = null;
$msg = '';

if (isset($_POST['restaurer']) && isset($_POST["maj_id"])) {

	$restored = archive('utilisateur', (int)$_POST["maj_id"], 1);
 
} elseif (isset($_POST['archiver']) && isset($_POST["maj_id"])) {

	$archived = archive('utilisateur', (int)$_POST["maj_id"]);
 
} elseif (isset($_POST['enregistrer']) && isset($_POST["maj_id"])) {
	
	if (!$_POST["maj_id"]) { //requête d'ajout

		$maj_attache = NULL;
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
			$updated = update_user((int)$_POST['maj_id'], $_POST["nom_pouet"], $_POST["courriel"]);

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
	$user = get_user_by_id((int)$id_utilisateur);

	if (count($user) > 0) {
		if ($user['id_statut'] == ROLE_ANIMATEUR) {
			$attache = $user['nom_territoire'];
		} else if ($user['id_statut'] == ROLE_PRO) {
			$attache = $user['nom_pro'];
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

$liste_statuts = null;
if (secu_check_role(ROLE_ADMIN)) $liste_statuts = array('1' => 'Administrateur national', '2' => 'Animateur territorial', '3' => 'Professionnel', '4' => 'Consultant'); //+ '5' => 'Administrateur régional'
if (secu_check_role(ROLE_ANIMATEUR)) $liste_statuts = array('2' => 'Animateur territorial', '3' => 'Professionnel', '4' => 'Consultant');
if (secu_check_role(ROLE_PRO)) $liste_statuts = array('3' => 'Professionnel');

if ($id_utilisateur) {
	$liste_territoires = get_territoires();
}else{
	$liste_territoires = get_territoires($param_territoire,1);
}

$liste_pro = get_liste_pros_select('pro', 'territoire',$param_territoire);

//type de formulaire à afficher
if (isset($_GET['do']) && $_GET['do'] == 'mdp') {
	$vue = 'motdepasse';
} else if ($id_utilisateur) {
	$vue = 'modif';
} else {
	$vue = 'creation';
}

//view
require 'view/utilisateur_detail.tpl.php';