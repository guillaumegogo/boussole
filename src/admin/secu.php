<?php

/*---------------------------------------------------- CONSTANTES ----------------------------------------------------*/

define('ROLE_ADMIN', 1);
define('ROLE_ANIMATEUR', 2);
define('ROLE_PRO', 3);
define('ROLE_CONSULTANT', 4);

define('DROIT_DEMANDE', 'demande');
define('DROIT_OFFRE', 'offre');
define('DROIT_PROFESSIONNEL', 'professionnel');
define('DROIT_TERRITOIRE', 'territoire');
define('DROIT_THEME', 'theme');
define('DROIT_UTILISATEUR', 'utilisateur');
define('DROIT_CRITERE', 'critere');

define('PASSWD_MIN_LENGTH', 6);

define('SALT_BOUSSOLE', '@CC#B0usS0l3_');

/*------------------------------------------------------- LOGIN ------------------------------------------------------*/

/**
 * Fonction de login backoffice
 * @param string $email
 * @param string $password
 * @return bool
 */
function secu_login($email, $password)
{
    secu_logout();

    global $conn;
    $logged = false;

    $sql = 'SELECT `id_utilisateur`, `nom_utilisateur`, `motdepasse`, `id_metier`, `bsl_utilisateur`.`id_statut`, `libelle_statut`, `acces_territoire`, `acces_professionnel`, `acces_offre`, `acces_theme`, `acces_utilisateur`, `acces_demande`, `acces_critere`, `nom_pro`, `nom_territoire`, `date_inscription`
            FROM `bsl_utilisateur` 
            JOIN `bsl__statut` ON `bsl__statut`.`id_statut`=`bsl_utilisateur`.`id_statut`
            LEFT JOIN  `bsl_territoire` ON `bsl_utilisateur`.`id_statut` = 2 AND `id_metier`=`bsl_territoire`.`id_territoire`
            LEFT JOIN  `bsl_professionnel` ON `bsl_utilisateur`.`id_statut` = 3 AND `id_metier`=`bsl_professionnel`.`id_professionnel`
            WHERE `email` = ? AND `actif_utilisateur` = 1';
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $email);
    check_mysql_error($conn);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) === 1) {
            mysqli_stmt_bind_result($stmt, $id_utilisateur, $nom_utilisateur, $hash, $id_metier, $id_statut, $libelle_statut, $acces_territoire, $acces_professionnel, $acces_offre, $acces_theme, $acces_utilisateur, $acces_demande, $acces_critere, $nom_pro, $nom_territoire, $date_inscription);
            mysqli_stmt_fetch($stmt);


            //Verification du mot de passe saisi
            if (password_verify(SALT_BOUSSOLE . $password, $hash)) {
                $_SESSION['user_id'] = $id_utilisateur;
                $_SESSION['user_checksum'] = secu_user_checksum($id_utilisateur, $email, $date_inscription);

                secu_set_territoire_id(null);
                if ($id_statut == ROLE_ANIMATEUR)
                    secu_set_territoire_id($id_metier);

                if ($id_statut == ROLE_PRO)
                    secu_set_user_pro_id($id_metier);

                //accroche statut
                $_SESSION['accroche'] = 'Bonjour ' . $nom_utilisateur . ', vous êtes ' . $libelle_statut;

                if ($id_statut == ROLE_ANIMATEUR)
                    $_SESSION['accroche'] .= ' (' . $nom_territoire . ')';

                if ($id_statut == ROLE_PRO)
                    $_SESSION['accroche'] .= ' (' . $nom_pro . ')';

                $logged = true;
            }
            mysqli_stmt_close($stmt);
        }
    }
    //Pas de message d'erreur spécifique

    return $logged;
}

/**
 * Verification l'état d'authentification de l'utilisateur courant
 * @return bool
 */
function secu_is_logged()
{
    global $conn;

    $logged = false;
    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && isset($_SESSION['user_checksum']) && !empty($_SESSION['user_checksum'])) {
        $sql = 'SELECT `email`, `date_inscription`
            FROM `bsl_utilisateur`
            WHERE `id_utilisateur` = ? AND `actif_utilisateur` = 1';
        $stmt = mysqli_prepare($conn, $sql);
        $id = (int)$_SESSION['user_id'];
        mysqli_stmt_bind_param($stmt, 'i', $id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) === 1) {
                mysqli_stmt_bind_result($stmt, $login, $date_inscription);
                mysqli_stmt_fetch($stmt);
                check_mysql_error($conn);

                if (secu_user_checksum($id, $login, $date_inscription) === $_SESSION['user_checksum'])
                    $logged = true;

                mysqli_stmt_close($stmt);
            }
        }
    }

    return $logged;
}

/**
 * Verifie si l'utilisateur courant a les droits pour accéder au BO ou plus spécifiquement à une page
 * Redirige automatiquement si ce n'est pas le cas
 * @param null|string $page
 */
function secu_check_login($page = null)
{
    if (secu_is_logged() !== true) {
        header('Location: index.php');
        exit();
    }

    if ($page !== null) {
        if (secu_is_authorized($page) !== true) {
            header('Location: accueil.php');
            exit();
        }
    }
}

/**
 * Verifie que l'utilisateur courant à les droits pour accéder à une page en particulier
 * @param string $page
 * @return bool
 */
function secu_is_authorized($page)
{
    global $conn;
    $authorized = false;

    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        $sql = 'SELECT `acces_territoire`, `acces_professionnel`, `acces_offre`, `acces_theme`, `acces_utilisateur`, `acces_demande`, `acces_critere`
            FROM `bsl_utilisateur` 
            JOIN `bsl__statut` ON `bsl__statut`.`id_statut`=`bsl_utilisateur`.`id_statut`
            WHERE `id_utilisateur` = ? AND `actif_utilisateur` = 1';
        $stmt = mysqli_prepare($conn, $sql);
        $id = (int)$_SESSION['user_id'];
        mysqli_stmt_bind_param($stmt, 'i', $id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) === 1) {
                mysqli_stmt_bind_result($stmt, $acces_territoire, $acces_professionnel, $acces_offre, $acces_theme, $acces_utilisateur, $acces_demande, $acces_critere);
                mysqli_stmt_fetch($stmt);
                check_mysql_error($conn);

                switch ($page) {
                    case DROIT_DEMANDE :
                        if ((int)$acces_demande > 0)
                            $authorized = true;
                        break;
                    case DROIT_OFFRE :
                        if ((int)$acces_offre > 0)
                            $authorized = true;
                        break;
                    case DROIT_PROFESSIONNEL :
                        if ((int)$acces_professionnel > 0)
                            $authorized = true;
                        break;
                    case DROIT_TERRITOIRE :
                        if ((int)$acces_territoire > 0)
                            $authorized = true;
                        break;
                    case DROIT_THEME :
                        if ((int)$acces_theme > 0)
                            $authorized = true;
                        break;
                    case DROIT_UTILISATEUR :
                        if ((int)$acces_utilisateur > 0)
                            $authorized = true;
                        break;
                    case DROIT_CRITERE :
                        if ((int)$acces_critere > 0)
                            $authorized = true;
                        break;
                }

                mysqli_stmt_close($stmt);
            }
        }
    }

    return $authorized;
}

/**
 * Verifie le role de l'utilisateur connecté
 * @param int $role
 * @return bool
 */
function secu_check_role($role)
{
    global $conn;
    $check = false;

    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        $sql = 'SELECT `id_statut` 
                FROM `bsl_utilisateur` 
                WHERE `id_utilisateur` = ? AND `actif_utilisateur` = 1';
        $stmt = mysqli_prepare($conn, $sql);
        $id = (int)$_SESSION['user_id'];
        mysqli_stmt_bind_param($stmt, 'i', $id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) === 1) {
                $id_statut = null;
                mysqli_stmt_bind_result($stmt, $id_statut);
                mysqli_stmt_fetch($stmt);
                check_mysql_error($conn);

                $check = ($id_statut === $role);

                mysqli_stmt_close($stmt);
            }
        }
    }

    return $check;
}

/*-------------------------------------------------- RESET PASSWORD --------------------------------------------------*/

/**
 * Envoi du mail de réinitialisation de mot de passe
 * @param string $email
 */
function secu_send_reset_email($email)
{
    global $conn;
    global $url_admin;

    $token = hash('sha256', $email . time() . rand(0, 1000000));

    $sql = 'UPDATE `bsl_utilisateur` 
            SET `reinitialisation_mdp`= ? ,`date_demande_reinitialisation`= NOW() 
            WHERE `email`= ? AND `actif_utilisateur`= 1';

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ss', $token, $_POST['login']);
    check_mysql_error($conn);
    if (mysqli_stmt_execute($stmt)) {
        if (mysqli_stmt_affected_rows($stmt) === 1) {
            $subject = 'Réinitialisation de votre mot de passe';
            $message = "<html><p>Vous avez demandé la réinitialisation de votre mot de passe.</p> "
                . "<p>Pour saisir votre nouveau mot de passe, merci de cliquer sur ce lien : <a href=\"" . $url_admin . "/motdepasseoublie.php?t=" . $token . "\">" . $url_admin . "/motdepasseoublie.php?t=" . $token . "</a></p>"
                . "<p>Merci d'utiliser le lien dans les trois jours, après quoi il ne sera plus valide.</html>";
            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=charset=utf-8' . "\r\n";
            $headers .= 'From: La Boussole des jeunes <boussole@jeunes.gouv.fr>' . "\r\n";
            //$headers .= 'Cc: guillaume.gogo@jeunesse-sports.gouv.fr' . "\r\n";

            mail($email, $subject, $message, $headers);
        }
        mysqli_stmt_close($stmt);
    }
}

/**
 * Verification du token de réinitialisation de mot de passe
 * @param $token
 * @return bool
 */
function secu_check_reset_token($token)
{
    global $conn;
    $check = false;

    $sql = 'SELECT `date_demande_reinitialisation` 
            FROM `bsl_utilisateur` 
            WHERE `reinitialisation_mdp`= ? AND `actif_utilisateur`= 1';

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $token);
    check_mysql_error($conn);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) === 1) {
            mysqli_stmt_bind_result($stmt, $date_demande_reinitialisation);
            mysqli_stmt_fetch($stmt);
            if (strtotime($date_demande_reinitialisation) > time() - (3600 * 24 * 3)) {  //3 jours
                $check = true;
            }
        }
        mysqli_stmt_close($stmt);
    }

    return $check;
}

/**
 * Réinitialisation de mot de passe (en base)
 * @param string $password
 * @param string $token
 */
function secu_reset_password($password, $token)
{
    global $conn;

    $sql = 'UPDATE `bsl_utilisateur` 
            SET `motdepasse` = ?, reinitialisation_mdp = NULL 
            WHERE `reinitialisation_mdp` = ? AND `actif_utilisateur`= 1';

    $stmt = mysqli_prepare($conn, $sql);
    $hash = secu_password_hash($password);
    mysqli_stmt_bind_param($stmt, 'ss', $hash, $token);
    check_mysql_error($conn);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

/*------------------------------------------------ SESSION MANAGEMENT ------------------------------------------------*/

/**
 * Récupération de l'id de l'utilisateur courant
 * @return int|null
 */
function secu_get_current_user_id()
{
    $user_id = null;
    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id']))
        $user_id = (int)$_SESSION['user_id'];

    return $user_id;
}

/**
 * Affectation de l'id de territoire
 * @param int|null $id
 */
function secu_set_territoire_id($id)
{
    if ((int)$id > 0)
        $_SESSION['territoire_id'] = (int)$id;
    else
        $_SESSION['territoire_id'] = null;
}

/**
 * Recuperation de l'id de territoire
 * @return int|null
 */
function secu_get_territoire_id()
{
    $territoire_id = null;
    if (isset($_SESSION['territoire_id']) && !empty($_SESSION['territoire_id']))
        $territoire_id = (int)$_SESSION['territoire_id'];

    return $territoire_id;
}

/**
 * Affectation de l'id de user pro
 * @param int|null $id
 */
function secu_set_user_pro_id($id)
{
    if ((int)$id > 0)
        $_SESSION['user_pro_id'] = (int)$id;
    else
        $_SESSION['user_pro_id'] = null;
}

/**
 * Recuperation de l'id de user pro
 * @return int|null
 */
function secu_get_user_pro_id()
{
    $user_pro_id = null;
    if (isset($_SESSION['user_pro_id']) && !empty($_SESSION['user_pro_id']))
        $user_pro_id = (int)$_SESSION['user_pro_id'];

    return $user_pro_id;
}

/*------------------------------------------------------ UTILS -------------------------------------------------------*/

/**
 * Generation d'un checksum
 * @param int $id
 * @param string $email
 * @param string $date_inscription
 * @return string
 */
function secu_user_checksum($id, $email, $date_inscription)
{
    $ip = secu_get_ip();
    return hash('sha256', SALT_BOUSSOLE . '%' . $ip . '*' . $id . '/' . $email . '!' . $_SERVER['HTTP_USER_AGENT'] . '¤' . $date_inscription);
}

/**
 * Récupération de l'adresse ip d'un utilisateur
 * @return array|string
 */
function secu_get_ip()
{
    $address = $_SERVER['REMOTE_ADDR'];
    if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $address = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (array_key_exists('HTTP_CLIENT_IP', $_SERVER) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
        $address = $_SERVER['HTTP_CLIENT_IP'];
    }

    if (strpos($address, ",") > 0) {
        $ips = explode(",", $address);
        $address = trim($ips[0]);
    }

    return $address;
}

/**
 * Hash du password
 * @param $password
 * @return bool|string
 */
function secu_password_hash($password)
{
    return password_hash(SALT_BOUSSOLE . $password, PASSWORD_DEFAULT);
}

/**
 * Nettoyage de la session
 */
function secu_logout()
{
    session_unset();
}