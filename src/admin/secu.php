<?php

define('DROIT_DEMANDE', 'demande');
define('DROIT_OFFRE', 'offre');
define('DROIT_PROFESSIONNEL', 'professionnel');
define('DROIT_TERRITOIRE', 'territoire');
define('DROIT_THEME', 'theme');
define('DROIT_UTILISATEUR', 'utilisateur');
define('DROIT_CRITERE', 'critere');

define('SALT_BOUSSOLE', '@CC#B0usS0l3_');

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

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) === 1) {
            mysqli_stmt_bind_result($stmt, $id_utilisateur, $nom_utilisateur, $hash, $id_metier, $id_statut, $libelle_statut, $acces_territoire, $acces_professionnel, $acces_offre, $acces_theme, $acces_utilisateur, $acces_demande, $acces_critere, $nom_pro, $nom_territoire, $date_inscription);
            mysqli_stmt_fetch($stmt);
            check_mysql_error($conn);

            //Verification du mot de passe saisi
            if (password_verify(SALT_BOUSSOLE . $password, $hash)) {

                //(mise en session de la gestion de droits : 1 = accès à la page listant l'objet correspondant)
                $_SESSION['user_id'] = $id_utilisateur;
                $_SESSION['user_checksum'] = secu_user_checksum($id_utilisateur, $email, $date_inscription);

                $_SESSION['user_statut'] = $libelle_statut;
                $_SESSION['user_nom'] = $nom_utilisateur;

                $_SESSION['territoire_id'] = 0;
                if ($id_statut == 2)
                    $_SESSION['territoire_id'] = $id_metier;

                if ($id_statut == 3)
                    $_SESSION['user_pro_id'] = $id_metier;

                $_SESSION['user_droits'] = array(
                    'territoire' => $acces_territoire,
                    'professionnel' => $acces_professionnel,
                    'offre' => $acces_offre,
                    'theme' => $acces_theme,
                    'utilisateur' => $acces_utilisateur,
                    'demande' => $acces_demande,
                    'critere' => $acces_critere
                );

                //accroche statut
                $_SESSION['accroche'] = 'Bonjour ' . $_SESSION['user_nom'] . ', vous êtes ' . $_SESSION['user_statut'];

                if ($id_statut == 2)
                    $_SESSION['accroche'] .= ' (' . $nom_territoire . ')';

                if ($id_statut == 3)
                    $_SESSION['accroche'] .= ' (' . $nom_pro . ')';

                $logged = true;
            }
        }
    }

    //Pas de message d'erreur spécifique

    return $logged;
}

function secu_check_login($page = null)
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
            }
        }
    }

    if ($logged !== true) {
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

function secu_is_authorized($page)
{
    $authorized = false;
    switch ($page) {
        case DROIT_DEMANDE :
            if ($_SESSION['user_droits']['demande'])
                $authorized = true;
            break;
        case DROIT_OFFRE :
            if ($_SESSION['user_droits']['offre'])
                $authorized = true;
            break;
        case DROIT_PROFESSIONNEL :
            if ($_SESSION['user_droits']['professionnel'])
                $authorized = true;
            break;
        case DROIT_TERRITOIRE :
            if ($_SESSION['user_droits']['territoire'])
                $authorized = true;
            break;
        case DROIT_THEME :
            if ($_SESSION['user_droits']['theme'])
                $authorized = true;
            break;
        case DROIT_UTILISATEUR :
            if ($_SESSION['user_droits']['utilisateur'])
                $authorized = true;
            break;
        case DROIT_CRITERE :
            if ($_SESSION['user_droits']['critere'])
                $authorized = true;
            break;
            break;
    }

    return $authorized;
}

function secu_user_checksum($id, $email, $date_inscription)
{
    return hash('sha256', SALT_BOUSSOLE . $id . '/' . $email . '!' . $_SERVER['HTTP_USER_AGENT'] . '¤' . $date_inscription);
}

function secu_password_hash($password)
{
    return password_hash(SALT_BOUSSOLE . $password, PASSWORD_DEFAULT);
}

function secu_logout()
{
    session_unset();
}