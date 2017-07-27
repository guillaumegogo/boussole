<?php

define('PAGE_DEMANDE_LISTE', 'demande_listing');
define('PAGE_OFFRE_LISTE', 'offre_liste');
define('PAGE_PROFESSIONNEL_LISTE', 'professionnel_liste');

function login($login, $password)
{
    global $conn;
    $logged = false;

    $sql = 'SELECT `id_utilisateur`, `nom_utilisateur`, `motdepasse`, `id_metier`, `bsl_utilisateur`.`id_statut`, `libelle_statut`, `acces_territoire`, `acces_professionnel`, `acces_offre`, `acces_theme`, `acces_utilisateur`, `acces_demande`, `acces_critere`, `nom_pro`, `nom_territoire`, `actif_utilisateur`
            FROM `bsl_utilisateur` 
            JOIN `bsl__statut` ON `bsl__statut`.`id_statut`=`bsl_utilisateur`.`id_statut`
            LEFT JOIN  `bsl_territoire` ON `bsl_utilisateur`.`id_statut` = 2 AND `id_metier`=`bsl_territoire`.`id_territoire`
            LEFT JOIN  `bsl_professionnel` ON `bsl_utilisateur`.`id_statut` = 3 AND `id_metier`=`bsl_professionnel`.`id_professionnel`
            WHERE `email` LIKE ? ';
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $login);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            mysqli_stmt_bind_result($stmt, $id_utilisateur, $nom_utilisateur, $motdepasse, $id_metier, $id_statut, $libelle_statut, $acces_territoire, $acces_professionnel, $acces_offre, $acces_theme, $acces_utilisateur, $acces_demande, $acces_critere, $nom_pro, $nom_territoire, $actif_utilisateur);
            mysqli_stmt_fetch($stmt);
            check_mysql_error($conn);

            if ($actif_utilisateur) {
                if (password_verify($password, $motdepasse)) { //verif du mot de passe saisi
                    //********* (mise en session de la gestion de droits : 1 = accès à la page listant l'objet correspondant)
                    $_SESSION['user_id'] = $id_utilisateur;
                    $_SESSION['user_statut'] = $libelle_statut;
                    $_SESSION['user_nom'] = $nom_utilisateur;
                    $_SESSION['territoire_id'] = 0;
                    if ($id_statut == 2) $_SESSION['territoire_id'] = $id_metier;
                    if ($id_statut == 3) $_SESSION['user_pro_id'] = $id_metier;
                    $_SESSION['user_droits'] = array('territoire' => $acces_territoire, 'professionnel' => $acces_professionnel, 'offre' => $acces_offre, 'theme' => $acces_theme, 'utilisateur' => $acces_utilisateur, 'demande' => $acces_demande, 'critere' => $acces_critere);

                    //********** accroche statut
                    $_SESSION['accroche'] = 'Bonjour ' . $_SESSION['user_nom'] . ', vous êtes ' . $_SESSION['user_statut'];
                    if ($id_statut == 2) $_SESSION['accroche'] .= ' (' . $nom_territoire . ')';
                    if ($id_statut == 3) $_SESSION['accroche'] .= ' (' . $nom_pro . ')';

                    $logged = true;
                }
            }
        }
    }

    //Pas de message d'erreur spécifique

    return $logged;
}

function checkLogin($page = null)
{
    if (!isset($_SESSION['user_id']))
    {
        header('Location: index.php');
        exit();
    }

    if($page !== null)
    {
        switch ($page)
        {
            case PAGE_DEMANDE_LISTE :
                if (!$_SESSION['user_droits']['demande'])
                {
                    header('Location: accueil.php');
                    exit();
                }
                break;
            case PAGE_OFFRE_LISTE :
                if (!$_SESSION['user_droits']['offre'])
                {
                    header('Location: accueil.php');
                    exit();
                }
                break;
            case PAGE_PROFESSIONNEL_LISTE :
                if (!$_SESSION['user_droits']['professionnel'])
                {
                    header('Location: accueil.php');
                    exit();
                }
                break;
            default :
                header('Location: index.php');
                exit();
        }
    }
}