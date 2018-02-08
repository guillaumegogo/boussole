<?php

/*---------------------------------------------------- CONSTANTES ----------------------------------------------------*/

define('ROLE_ADMIN', 1);
define('ROLE_ANIMATEUR', 2);
define('ROLE_PRO', 3);
define('ROLE_CONSULTANT', 4);
define('ROLE_ADMIN_REGIONAL', 5);

define('PERIMETRE_NATIONAL', 3);
define('PERIMETRE_ZONE', 2);
define('PERIMETRE_PRO', 1);

define('DROIT_DEMANDE', 'demande');
define('DROIT_OFFRE', 'offre');
define('DROIT_MESURE', 'mesure');
define('DROIT_PROFESSIONNEL', 'professionnel');
define('DROIT_TERRITOIRE', 'territoire');
define('DROIT_THEME', 'theme');
define('DROIT_UTILISATEUR', 'utilisateur');
define('DROIT_FORMULAIRE', 'formulaire');

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

	$sql = 'SELECT `id_utilisateur`, `nom_utilisateur`, `motdepasse`, `u`.`id_statut`, `libelle_statut`, `date_inscription`, `id_metier`, `nom_pro`, `t_u`.`nom_territoire`, `competence_geo`, `id_competence_geo`, `t_p`.`nom_territoire` AS `nom_territoire_pro` 
			FROM `'.DB_PREFIX.'utilisateur` AS `u`
			JOIN `'.DB_PREFIX.'_droits` AS `d` ON `d`.`id_statut`=`u`.`id_statut`
			LEFT JOIN `'.DB_PREFIX.'territoire` AS `t_u` ON `u`.`id_statut` = 2 AND `id_metier`=`t_u`.`id_territoire`
			LEFT JOIN `'.DB_PREFIX.'professionnel` AS `p` ON `u`.`id_statut` = 3 AND `id_metier`=`p`.`id_professionnel`
			LEFT JOIN `'.DB_PREFIX.'territoire` AS `t_p` ON `p`.`competence_geo`="territoire" AND `p`.`id_competence_geo`=`t_p`.`id_territoire`
			WHERE `email` = ? AND `actif_utilisateur` = 1';
	$stmt = mysqli_prepare($conn, $sql);
	mysqli_stmt_bind_param($stmt, 's', $email);
	check_mysql_error($conn);

	if (mysqli_stmt_execute($stmt)) {
		mysqli_stmt_store_result($stmt);
		if (mysqli_stmt_num_rows($stmt) === 1) {
			mysqli_stmt_bind_result($stmt, $id_utilisateur, $nom_utilisateur, $hash, $id_statut, $libelle_statut, $date_inscription, $id_metier, $nom_pro, $nom_territoire, $competence_geo, $id_competence_geo, $nom_territoire_pro);
			mysqli_stmt_fetch($stmt);

			//Verification du mot de passe saisi
			if (password_verify(SALT_BOUSSOLE . $password, $hash)) {
				$_SESSION['admin']['user_id'] = $id_utilisateur;
				$_SESSION['admin']['user_checksum'] = secu_user_checksum($id_utilisateur, $email, $date_inscription);
				
				//accroche statut
				$_SESSION['admin']['accroche'] = 'Bonjour <a href="utilisateur_detail.php?id=' . $id_utilisateur . '">'. $nom_utilisateur . '</a>, vous êtes ' . $libelle_statut;
				
				//récup des id pro et territoire
				$_SESSION['admin']['territoire_id'] = null;
				secu_set_user_pro_id(null);
				
				switch($id_statut){
					case(ROLE_ADMIN):
						break;
					case(ROLE_ANIMATEUR):
					case(ROLE_CONSULTANT):
						$_SESSION['admin']['territoire_id'] = $id_metier;
						$_SESSION['admin']['accroche'] .= ' (<a href="territoire_detail.php?id=' . $id_metier . '">' . $nom_territoire . '</a>)';
						break;
					case(ROLE_PRO):
						secu_set_user_pro_id($id_metier);
						$accroche_territoire = '';
						if($competence_geo=="territoire") {
							$_SESSION['admin']['territoire_id'] = $id_competence_geo;
							$accroche_territoire = ' / <a href="territoire_detail.php?id=' . $id_competence_geo . '">' . $nom_territoire_pro . '</a>';
						}
						$_SESSION['admin']['accroche'] .= ' (<a href="professionnel_detail.php?id=' . $id_metier . '">' . $nom_pro . '</a>' . $accroche_territoire.')';
						$_SESSION['admin']['nom_pro'] = $nom_pro;
						$_SESSION['admin']['perimetre'] = 'PRO';
						break;
					case(ROLE_ADMIN_REGIONAL):
						/* ? */
						break;
				}
				if (!isset($_SESSION['admin']['perimetre'])) 
					$_SESSION['admin']['perimetre'] = $_SESSION['admin']['territoire_id'];

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
	if (isset($_SESSION['admin']['user_id']) && !empty($_SESSION['admin']['user_id']) && isset($_SESSION['admin']['user_checksum']) && !empty($_SESSION['admin']['user_checksum'])) {
		$sql = 'SELECT `email`, `date_inscription`
			FROM `'.DB_PREFIX.'utilisateur`
			WHERE `id_utilisateur` = ? AND `actif_utilisateur` = 1';
		$stmt = mysqli_prepare($conn, $sql);
		$id = (int)$_SESSION['admin']['user_id'];
		mysqli_stmt_bind_param($stmt, 'i', $id);

		if (mysqli_stmt_execute($stmt)) {
			mysqli_stmt_store_result($stmt);
			if (mysqli_stmt_num_rows($stmt) === 1) {
				mysqli_stmt_bind_result($stmt, $login, $date_inscription);
				mysqli_stmt_fetch($stmt);
				check_mysql_error($conn);

				if (secu_user_checksum($id, $login, $date_inscription) === $_SESSION['admin']['user_checksum'])
					$logged = true;

				mysqli_stmt_close($stmt);
			}
		}
	}

	return $logged;
}

/**
 * Verifie que l'utilisateur courant à les droits demandés
 * @param string $domaine
 * @return array
 */
function secu_is_authorized($domaine)
{
	global $conn;
	$authorized = null;

	if (isset($_SESSION['admin']['user_id']) && !empty($_SESSION['admin']['user_id'])) {
		switch ($domaine) {
			case DROIT_DEMANDE :
				$champs = '`demande_r` as `lecture`, `demande_w` as `ecriture`'; break;
			case DROIT_OFFRE :
				$champs = '`offre_r` as `lecture`, `offre_w` as `ecriture`'; break;
			case DROIT_MESURE :
				$champs = '`mesure_r` as `lecture`, `mesure_w` as `ecriture`'; break;
			case DROIT_PROFESSIONNEL :
				$champs = '`professionnel_r` as `lecture`, `professionnel_w` as `ecriture`'; break;
			case DROIT_TERRITOIRE :
				$champs = '`territoire_r` as `lecture`, `territoire_w` as `ecriture`'; break;
			case DROIT_THEME :
				$champs = '`theme_r` as `lecture`, `theme_w` as `ecriture`'; break;
			case DROIT_UTILISATEUR :
				$champs = '`utilisateur_r` as `lecture`, `utilisateur_w` as `ecriture`'; break;
			case DROIT_FORMULAIRE :
				$champs = '`formulaire_r` as `lecture`, `formulaire_w` as `ecriture`'; break;
			case 'accueil' :
				$champs = '`demande_r` as `'.DROIT_DEMANDE.'`, `offre_r` as `'.DROIT_OFFRE.'`, `mesure_r` as `'.DROIT_MESURE.'`, `professionnel_r` as `'.DROIT_PROFESSIONNEL.'`, `utilisateur_r` as `'.DROIT_UTILISATEUR.'`, `formulaire_r` as `'.DROIT_FORMULAIRE.'`, `theme_r` as `'.DROIT_THEME.'`, `territoire_r` as `'.DROIT_TERRITOIRE.'`'; break;
		}
		$sql = 'SELECT '.$champs.' FROM `'.DB_PREFIX.'_droits` 
			JOIN `'.DB_PREFIX.'utilisateur` ON `'.DB_PREFIX.'_droits`.`id_statut`=`'.DB_PREFIX.'utilisateur`.`id_statut`
			WHERE `id_utilisateur` = ? ';
		$stmt = mysqli_prepare($conn, $sql);
		$id = (int)$_SESSION['admin']['user_id'];
		mysqli_stmt_bind_param($stmt, 'i', $id);
		check_mysql_error($conn);
		
		if (mysqli_stmt_execute($stmt)) {
			$result = mysqli_stmt_get_result($stmt);
			if (mysqli_num_rows($result) === 1) {
				$authorized = mysqli_fetch_assoc($result);
			}
		}
		mysqli_stmt_close($stmt);
	}

	return $authorized;
}

/**
 * Verifie si l'utilisateur courant a les droits pour accéder au BO ou plus spécifiquement à un domaine
 * Redirige automatiquement si ce n'est pas le cas
 * @param null|string $domaine
 * @return int $perimetre
 */
function secu_check_login($domaine = null)
{
	if (secu_is_logged() !== true) {
		header('Location: index.php');
		exit();
	}

	if ($domaine !== null) {
		$perimetre = secu_is_authorized($domaine);
		if (!isset($perimetre['lecture']) || $perimetre['lecture']==0) {
			header('Location: accueil.php');
			exit();
		}else{
			return $perimetre;
		}
	}
}

/**
 * Verifie les droits de lecture/écriture de l'utilisateur courant sur un item particulier
 * @param string $domaine
 * @param int $id
 * @return bool
 */
function secu_check_level($domaine, $id)
{
	global $conn;
	
	$droit_ecriture = null;	
	$check = secu_is_authorized($domaine);
	
	if(isset($check)){
		if($check['ecriture']==PERIMETRE_NATIONAL){
			$droit_ecriture = true;
		
		} 
		if(($check['ecriture']==PERIMETRE_ZONE || $check['lecture']==PERIMETRE_ZONE) && $zone_id=secu_get_territoire_id()){
			
			if($domaine===DROIT_TERRITOIRE && $zone_id == $id) {
				if($check['ecriture']==PERIMETRE_ZONE) {
					$droit_ecriture = true;
				}else{
					$droit_ecriture += false;
				}
			
			}else{
				if($domaine===DROIT_DEMANDE) {
					$query='SELECT id_demande as `id` FROM `'.DB_PREFIX.'demande`
						JOIN `'.DB_PREFIX.'offre` ON `'.DB_PREFIX.'offre`.id_offre=`'.DB_PREFIX.'demande`.id_offre
						JOIN `'.DB_PREFIX.'professionnel` ON `'.DB_PREFIX.'professionnel`.id_professionnel=`'.DB_PREFIX.'offre`.id_professionnel AND competence_geo="territoire"
						WHERE `'.DB_PREFIX.'professionnel`.id_competence_geo=? AND id_demande=? ';
					$stmt = mysqli_prepare($conn, $query);
					mysqli_stmt_bind_param($stmt, 'ii', $zone_id, $id);
				
				} else if($domaine===DROIT_OFFRE) {
					$query='SELECT id_offre as `id` FROM `'.DB_PREFIX.'offre`
						JOIN `'.DB_PREFIX.'professionnel` ON `'.DB_PREFIX.'professionnel`.id_professionnel=`'.DB_PREFIX.'offre`.id_professionnel AND competence_geo="territoire"
						WHERE `'.DB_PREFIX.'professionnel`.id_competence_geo=? AND id_offre=? ';
					$stmt = mysqli_prepare($conn, $query);
					mysqli_stmt_bind_param($stmt, 'ii', $zone_id, $id);
					
				} else if($domaine===DROIT_MESURE) {
					$query='SELECT id_mesure as `id` FROM `'.DB_PREFIX.'mesure`
						JOIN `'.DB_PREFIX.'professionnel` ON `'.DB_PREFIX.'professionnel`.id_professionnel=`'.DB_PREFIX.'mesure`.id_professionnel AND `'.DB_PREFIX.'professionnel`.competence_geo="territoire"
						WHERE `'.DB_PREFIX.'professionnel`.id_competence_geo=? AND id_mesure=? ';
					$stmt = mysqli_prepare($conn, $query);
					mysqli_stmt_bind_param($stmt, 'ii', $zone_id, $id);
					
				} else if($domaine===DROIT_PROFESSIONNEL) {
					$query='SELECT id_professionnel as `id` FROM `'.DB_PREFIX.'professionnel`
						WHERE competence_geo="territoire" AND id_competence_geo=? AND id_professionnel=? ';
					$stmt = mysqli_prepare($conn, $query);
					mysqli_stmt_bind_param($stmt, 'ii', $zone_id, $id);
					
				} else if($domaine===DROIT_UTILISATEUR) {
					$query='SELECT id_utilisateur as `id` FROM `'.DB_PREFIX.'utilisateur`
						WHERE id_statut IN (2,4) AND id_metier=? AND id_utilisateur=? 
						UNION
						SELECT id_utilisateur as `id` FROM `'.DB_PREFIX.'utilisateur`
						LEFT JOIN `'.DB_PREFIX.'professionnel` ON `'.DB_PREFIX.'utilisateur`.id_metier=`'.DB_PREFIX.'professionnel`.id_professionnel 
						WHERE id_statut =3 AND competence_geo="territoire" AND id_competence_geo=? AND id_utilisateur=? ';
					$stmt = mysqli_prepare($conn, $query);
					mysqli_stmt_bind_param($stmt, 'iiii', $zone_id, $id, $zone_id, $id);
					
				} else if($domaine===DROIT_FORMULAIRE) {
					$query='SELECT `id_formulaire` FROM `'.DB_PREFIX.'formulaire`
						WHERE `id_territoire`=? AND `id_formulaire`= ?';
					$stmt = mysqli_prepare($conn, $query);
					mysqli_stmt_bind_param($stmt, 'ii', $zone_id, $id);
					
				} else if($domaine===DROIT_THEME) {
					$query='SELECT `id_theme` FROM `'.DB_PREFIX.'theme`
						WHERE `id_territoire`=? AND `id_theme`= ?';
					$stmt = mysqli_prepare($conn, $query);
					mysqli_stmt_bind_param($stmt, 'ii', $zone_id, $id);
					
				} else check_mysql_error($conn);
				
				if (isset($stmt) && mysqli_stmt_execute($stmt)) {
					$result = mysqli_stmt_get_result($stmt);
					if (mysqli_num_rows($result) === 1) {
						if($check['ecriture']==PERIMETRE_ZONE) {
							$droit_ecriture = true;
						}else if($check['lecture']==PERIMETRE_ZONE) {
							$droit_ecriture += false;
						}
					}
					mysqli_stmt_close($stmt);
				}
			}
		} 
		if(($check['ecriture']==PERIMETRE_PRO || $check['lecture']==PERIMETRE_PRO) && $pro_id=secu_get_user_pro_id()){
			//on checke les pro_id de user_id et $domaine+$id. si c'est les mêmes return true
			
			if($domaine===DROIT_PROFESSIONNEL) {
				if($pro_id == $id && $check['ecriture']==PERIMETRE_PRO) {
					$droit_ecriture = true;
				}else{
					$droit_ecriture += false;
				}
			
			}else {
				if($domaine===DROIT_DEMANDE) {
					$query='SELECT id_demande as `id` FROM `'.DB_PREFIX.'demande`
						JOIN `'.DB_PREFIX.'offre` ON `'.DB_PREFIX.'offre`.id_offre=`'.DB_PREFIX.'demande`.id_offre
						JOIN `'.DB_PREFIX.'professionnel` ON `'.DB_PREFIX.'professionnel`.id_professionnel=`'.DB_PREFIX.'offre`.id_professionnel AND competence_geo="territoire"
						WHERE `'.DB_PREFIX.'professionnel`.id_professionnel=? AND id_demande=? ';
					$stmt = mysqli_prepare($conn, $query);
					mysqli_stmt_bind_param($stmt, 'ii', $pro_id, $id);
					//echo $query.' '.$pro_id.' '.$id;
				
				} else if($domaine===DROIT_OFFRE) {
					$query='SELECT id_offre as `id` FROM `'.DB_PREFIX.'offre`
						JOIN `'.DB_PREFIX.'professionnel` ON `'.DB_PREFIX.'professionnel`.id_professionnel=`'.DB_PREFIX.'offre`.id_professionnel AND competence_geo="territoire"
						WHERE `'.DB_PREFIX.'professionnel`.id_professionnel=? AND id_offre=? ';
					$stmt = mysqli_prepare($conn, $query);
					mysqli_stmt_bind_param($stmt, 'ii', $pro_id, $id);
					
				} else if($domaine===DROIT_MESURE) {
					$query='SELECT id_mesure as `id` FROM `'.DB_PREFIX.'mesure`
						JOIN `'.DB_PREFIX.'professionnel` ON `'.DB_PREFIX.'professionnel`.id_professionnel=`'.DB_PREFIX.'mesure`.id_professionnel AND `'.DB_PREFIX.'professionnel`.competence_geo="territoire"
						WHERE `'.DB_PREFIX.'professionnel`.id_professionnel=? AND id_mesure=? ';
					$stmt = mysqli_prepare($conn, $query);
					mysqli_stmt_bind_param($stmt, 'ii', $pro_id, $id);
				
				} else if($domaine===DROIT_UTILISATEUR) {
					$query='SELECT id_utilisateur as `id` FROM `'.DB_PREFIX.'utilisateur`
						LEFT JOIN `'.DB_PREFIX.'professionnel` ON `'.DB_PREFIX.'utilisateur`.id_metier=`'.DB_PREFIX.'professionnel`.id_professionnel 
						WHERE id_statut =3 AND id_professionnel=? AND id_utilisateur=? ';
					$stmt = mysqli_prepare($conn, $query);
					mysqli_stmt_bind_param($stmt, 'ii', $pro_id, $id);
					
				}
				check_mysql_error($conn);
				
				if (isset($stmt) && mysqli_stmt_execute($stmt)) {
					$result = mysqli_stmt_get_result($stmt);
					if (mysqli_num_rows($result) === 1) {
						if($check['ecriture']==PERIMETRE_PRO) {
							$droit_ecriture = true;
						}else if($check['lecture']==PERIMETRE_PRO) {
							$droit_ecriture += false;
						}
					}
				}
				mysqli_stmt_close($stmt);
			}		
		} 
		if($check['lecture']==PERIMETRE_NATIONAL){
			$droit_ecriture += false;
		}
		
		//exception pour toujours pouvoir accéder à son propre compte
		if($domaine=='utilisateur' && $id==$_SESSION['admin']['user_id']) {
			$droit_ecriture += true;
		}
	}
	
	// si on a pas les droits d'accès à cette page - on retourne à l'accueil directement
	if ($droit_ecriture===null) {
		header('Location: accueil.php');
		exit();
	}
	
	//echo 'check '.$check['lecture'].' / '.$check['ecriture'].' → ecriture : '.(($droit_ecriture)?'oui':'non');
	return $droit_ecriture;
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

	if (isset($_SESSION['admin']['user_id']) && !empty($_SESSION['admin']['user_id'])) {
		$sql = 'SELECT `id_statut` 
				FROM `'.DB_PREFIX.'utilisateur` 
				WHERE `id_utilisateur` = ? AND `actif_utilisateur` = 1';
		$stmt = mysqli_prepare($conn, $sql);
		$id = (int)$_SESSION['admin']['user_id'];
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
function secu_send_pass_email($email, $origine='reset')
{
	global $conn;
	global $path_extranet;
	$sent = false;
	$token = hash('sha256', $email . time() . rand(0, 1000000));

	$sql = 'UPDATE `'.DB_PREFIX.'utilisateur` 
			SET `reinitialisation_mdp`= ? ,`date_demande_reinitialisation`= NOW() 
			WHERE `email`= ? AND `actif_utilisateur`= 1';

	$stmt = mysqli_prepare($conn, $sql);
	mysqli_stmt_bind_param($stmt, 'ss', $token, $email);
	check_mysql_error($conn);
	if (mysqli_stmt_execute($stmt)) {
		if (mysqli_stmt_affected_rows($stmt) === 1) {
			if ($origine=='reset') {
				$subject = mb_encode_mimeheader('Réinitialisation de votre mot de passe', 'UTF-8');
				$message = "<html><p>Vous avez demandé la réinitialisation de votre mot de passe.</p> "
				. "<p>Pour saisir votre nouveau mot de passe, merci de cliquer sur le lien suivant : <a href=\"http://" . $_SERVER['SERVER_NAME'] . $path_extranet . "/motdepasseoublie.php?t=" . $token . "\">" . $_SERVER['SERVER_NAME'] . $path_extranet . "/motdepasseoublie.php?t=" . $token . "</a></p>"
				. "<p>Ce lien est valide trois jours, après quoi il vous faudra refaire une demande.</p>"
				. "<p>Cordialement. L'équipe de la Boussole des jeunes.</p></html>";
			}else if ($origine=='init') {
				$subject = mb_encode_mimeheader('Création de votre compte Boussole des jeunes et initialisation de votre mot de passe', 'UTF-8');
				$message = "<html><p>Un compte a été créé à votre nom sur la Boussole des jeunes.</p> "
				. "<p>Avant de pouvoir vous connecter, vous devez initialiser votre mot de passe en cliquant sur le lien suivant : <a href=\"http://" . $_SERVER['SERVER_NAME'] . $path_extranet . "/motdepasseoublie.php?t=" . $token . "\">" . $_SERVER['SERVER_NAME'] . $path_extranet . "/motdepasseoublie.php?t=" . $token . "</a></p>"
				. "<p>Ce lien est valide trois jours, après quoi il faudra demander une réinitialisation du mot de passe sur <a href=\"http://" . $_SERVER['SERVER_NAME'] . $path_extranet . "/motdepasseoublie.php\">" . $_SERVER['SERVER_NAME'] . $path_extranet . "/motdepasseoublie.php</a>.</p>"
				. "<p>Cordialement. L'équipe de la Boussole des jeunes.</p></html>";
			}
			$headers = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			$headers .= 'From: La Boussole des jeunes <noreply@boussole.jeunes.gouv.fr>' . "\r\n";

			$sent = mail($email, $subject, $message, $headers);
		}
	}
	mysqli_stmt_close($stmt);
	return $sent;
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
			FROM `'.DB_PREFIX.'utilisateur` 
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

	$sql = 'UPDATE `'.DB_PREFIX.'utilisateur` 
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
	if (isset($_SESSION['admin']['user_id']) && !empty($_SESSION['admin']['user_id']))
		$user_id = (int)$_SESSION['admin']['user_id'];

	return $user_id;
}

/**
 * Affectation de l'id de territoire
 * @param int|null $id
 */
function secu_set_territoire_id($id)
{
	if ((int)$id > 0) {
		$_SESSION['admin']['territoire_id'] = (int)$id;
	} else {
		$_SESSION['admin']['territoire_id'] = null;
	}
}

/**
 * Recuperation de l'id de territoire
 * @return int|null
 */
function secu_get_territoire_id()
{
	$territoire_id = null;
	if (isset($_SESSION['admin']['territoire_id']) && !empty($_SESSION['admin']['territoire_id']))
		$territoire_id = (int)$_SESSION['admin']['territoire_id'];

	return $territoire_id;
}

/**
 * Affectation de l'id de user pro
 * @param int|null $id
 */
function secu_set_user_pro_id($id)
{
	if ((int)$id > 0)
		$_SESSION['admin']['user_pro_id'] = (int)$id;
	else
		$_SESSION['admin']['user_pro_id'] = null;
}

/**
 * Recuperation de l'id de user pro
 * @return int|null
 */
function secu_get_user_pro_id()
{
	$user_pro_id = null;
	if (isset($_SESSION['admin']['user_pro_id']) && !empty($_SESSION['admin']['user_pro_id']))
		$user_pro_id = (int)$_SESSION['admin']['user_pro_id'];

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