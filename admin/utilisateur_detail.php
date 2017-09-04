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
            if ($_POST["statut"] == ROLE_ANIMATEUR && isset($_POST["attache"])) $maj_attache = "\"" . $_POST["attache"] . "\"";
            else if ($_POST["statut"] == ROLE_PRO && isset($_POST["attache_p"])) $maj_attache = "\"" . $_POST["attache_p"] . "\"";
        }
        if ($_POST["nouveaumotdepasse"] === $_POST["nouveaumotdepasse2"] && strlen($_POST["nouveaumotdepasse"]) >= PASSWD_MIN_LENGTH) {

            $req = 'INSERT INTO `'.DB_PREFIX.'bsl_utilisateur`(`nom_utilisateur`, `email`, `motdepasse`, `date_inscription`, `id_statut`, `id_metier`) VALUES ("' . $_POST["nom_utilisateur"] . '","' . $_POST["courriel"] . '","' . secu_password_hash($_POST["nouveaumotdepasse"]) . '",NOW(),"' . $_POST["statut"] . '",' . $maj_attache . ')';

            if ($result = mysqli_query($conn, $req)) {
                $msg = 'Utilisateur bien créé.';
                $last_id = mysqli_insert_id($conn);
            } else {
                $msg = $message_erreur_bd;
            }
        } else {
            $msg = 'Les mots de passe saisis doivent correspondre et faire au moins '.PASSWD_MIN_LENGTH.' caractères.';
        }

    } else { //requête de modification
        if (!isset($_POST["nouveaumotdepasse"])) { //modif normale
            $req = 'UPDATE `'.DB_PREFIX.'bsl_utilisateur` SET `nom_utilisateur` = "' . $_POST["nom_utilisateur"] . '", `email` = "' . $_POST["courriel"] . '", `actif_utilisateur` = "' . $_POST["actif"] . '" WHERE `id_utilisateur` = ' . $_POST["maj_id"];
            if ($result = mysqli_query($conn, $req)) {
                $msg = 'Utilisateur modifié.';
                $last_id = $_POST["maj_id"];
            } else {
                $msg = $message_erreur_bd;
            }
        } else { //modif mot de passe
            //TODO ajouter longueur minimale pour password
            if ($_POST["nouveaumotdepasse"] === $_POST["nouveaumotdepasse2"] && strlen($_POST["nouveaumotdepasse"]) >= PASSWD_MIN_LENGTH) {

                $sql = 'SELECT `motdepasse` FROM `'.DB_PREFIX.'bsl_utilisateur` WHERE `id_utilisateur`=' . $_POST["maj_id"];
                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result)) {
                    $row = mysqli_fetch_assoc($result);
                    if (password_verify(SALT_BOUSSOLE . $_POST['motdepasseactuel'], $row['motdepasse'])) {
                        $req = 'UPDATE `'.DB_PREFIX.'bsl_utilisateur` SET `motdepasse` = "' . secu_password_hash($_POST["nouveaumotdepasse"]) . '" WHERE `id_utilisateur` = ' . $_POST["maj_id"];
                        //pas de modif du statut autorisée. sinon il faudrait ajouter : `id_statut` = \"".$_POST["statut"]."\"
                        if ($result = mysqli_query($conn, $req)) {
                            $msg = 'Mot de passe modifié.';
                            $last_id = $_POST["maj_id"];
                        } else {
                            $msg = $message_erreur_bd;
                        }
                    } else {//mdp actuel correct
                        $msg = 'Le mot de passe indiqué n\'est pas le bon.';
                    }
                } else {
                    $msg = 'Pas d\'utilisateur connu.';
                }
            } else {
                $msg = 'Les mots de passe saisis doivent correspondre et faire au moins '.PASSWD_MIN_LENGTH.' caractères.';
            }
        }
    }
    if ($result) {
        if (!$msg) $msg = "Modification bien enregistrée.";
    } else {
        if (!$msg) $msg = "Il y a eu un problème à l'enregistrement . Contactez l'administration centrale si le problème perdure.";
    }
}

//*********** affichage de l'utilisateur demandé ou nouvellement créé
$attache = '';
$id_utilisateur = $last_id;
if (isset($_GET['id'])) {
    $id_utilisateur = $_GET['id'];
}
if (isset($id_utilisateur)) {
    $sql = 'SELECT `'.DB_PREFIX.'bsl_utilisateur`.`id_statut`, `nom_utilisateur`, `email`, `date_inscription`, `actif_utilisateur`, `id_professionnel`, `nom_pro`, `id_territoire` , `nom_territoire`
	FROM `'.DB_PREFIX.'bsl_utilisateur` 
	JOIN `'.DB_PREFIX.'bsl__statut` ON `'.DB_PREFIX.'bsl__statut`.`id_statut`=`'.DB_PREFIX.'bsl_utilisateur`.`id_statut`
	LEFT JOIN `'.DB_PREFIX.'bsl_territoire` ON `'.DB_PREFIX.'bsl_territoire`.`id_territoire`=`'.DB_PREFIX.'bsl_utilisateur`.`id_metier`
	LEFT JOIN `'.DB_PREFIX.'bsl_professionnel` ON `'.DB_PREFIX.'bsl_professionnel`.`id_professionnel`=`'.DB_PREFIX.'bsl_utilisateur`.`id_metier`
	WHERE `id_utilisateur`=' . $id_utilisateur;
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        if ($row['id_statut'] == ROLE_ANIMATEUR) {
            $attache = $row['nom_territoire'];
        } else if ($row['id_statut'] == ROLE_PRO) {
            $attache = $row['nom_pro'];
        }

    } else {
        if (!$msg) $msg = '<div class="soustitre">Cet utilisateur est inconnu.</div>';
    }
}

$soustitre = ($id_utilisateur) ? "Modification d'un utilisateur" : "Ajout d'un utilisateur";

//********************* listes
$select_territoire = '<option value="" >A choisir</option>';
$select_professionnel = '<option value="" >A choisir</option>';
//si création, liste = liste du/des territoire(s) et des pros du/des territoire(s), avec tout en display none
//si modif = affichage en disabled du territoire ou de la liste des pros, en fonction de la liste

$sql2 = 'SELECT `id_territoire`, `nom_territoire` FROM `'.DB_PREFIX.'bsl_territoire` WHERE 1 ';
if (secu_check_role(ROLE_ANIMATEUR)) {
    $sql2 .= ' AND `id_territoire`=' . $_SESSION['territoire_id'];
}
$result = mysqli_query($conn, $sql2);
while ($row2 = mysqli_fetch_assoc($result)) {
    $select_territoire .= '<option value="' . $row2['id_territoire'] . '" ';
    if (isset($row['id_territoire'])) {
        if ($row2['id_territoire'] == $row['id_territoire']) {
            $select_territoire .= 'selected';
        }
    }
    $select_territoire .= '>' . $row2['nom_territoire'] . '</option>';
}

$sql3 = 'SELECT `id_professionnel`, `nom_pro` FROM `'.DB_PREFIX.'bsl_professionnel` WHERE 1 ';
if (secu_check_role(ROLE_ANIMATEUR)) {
    $sql3 .= ' AND `competence_geo`="territoire" AND `id_competence_geo`=' . $_SESSION['territoire_id'];
}
$result = mysqli_query($conn, $sql3);
while ($row3 = mysqli_fetch_assoc($result)) {
    $select_professionnel .= '<option value="' . $row3['id_professionnel'] . '" ';
    if (isset($row['id_professionnel'])) {
        if ($row3['id_professionnel'] == $row['id_professionnel']) {
            $select_professionnel .= 'selected';
        }
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