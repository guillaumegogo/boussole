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

	$sql = 'SELECT `id_utilisateur`, `nom_utilisateur`, `motdepasse`, `'.DB_PREFIX.'bsl_utilisateur`.`id_statut`, `libelle_statut`, `date_inscription`, `id_metier`, `nom_pro`, `t_u`.`nom_territoire`, `competence_geo`, `id_competence_geo`, `t_p`.`nom_territoire` AS `nom_territoire_pro` 
			FROM `'.DB_PREFIX.'bsl_utilisateur` 
			JOIN `'.DB_PREFIX.'bsl__droits` ON `'.DB_PREFIX.'bsl__droits`.`id_statut`=`'.DB_PREFIX.'bsl_utilisateur`.`id_statut`
			LEFT JOIN `'.DB_PREFIX.'bsl_territoire` AS `t_u` ON `'.DB_PREFIX.'bsl_utilisateur`.`id_statut` = 2 AND `id_metier`=`t_u`.`id_territoire`
			LEFT JOIN `'.DB_PREFIX.'bsl_professionnel` ON `'.DB_PREFIX.'bsl_utilisateur`.`id_statut` = 3 AND `id_metier`=`'.DB_PREFIX.'bsl_professionnel`.`id_professionnel`
			LEFT JOIN `'.DB_PREFIX.'bsl_territoire` AS `t_p` ON `'.DB_PREFIX.'bsl_professionnel`.`competence_geo`="territoire" AND `'.DB_PREFIX.'bsl_professionnel`.`id_competence_geo`=`t_p`.`id_territoire`
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
				$_SESSION['user_id'] = $id_utilisateur;
				$_SESSION['user_checksum'] = secu_user_checksum($id_utilisateur, $email, $date_inscription);
				
				//accroche statut
				$_SESSION['accroche'] = 'Bonjour ' . $nom_utilisateur . ', vous êtes ' . $libelle_statut;
				
				//récup des id pro et territoire
				$_SESSION['territoire_id'] = null;
				secu_set_user_pro_id(null);
				
				switch($id_statut){
					case(ROLE_ADMIN):
						break;
					case(ROLE_ANIMATEUR):
					case(ROLE_CONSULTANT):
						$_SESSION['territoire_id'] = $id_metier;
						$_SESSION['accroche'] .= ' (' . $nom_territoire . ')';
						break;
					case(ROLE_PRO):
						secu_set_user_pro_id($id_metier);
						if($competence_geo=="territoire") $_SESSION['territoire_id'] = $id_competence_geo; 
						$_SESSION['accroche'] .= ' (' . $nom_pro . ')';
						$_SESSION['nom_pro'] = $nom_pro;
						$_SESSION['perimetre'] = 'PRO';
						break;
					case(ROLE_ADMIN_REGIONAL):
						/* ? */
						break;
				}
				if (!isset($_SESSION['perimetre'])) 
					$_SESSION['perimetre'] = $_SESSION['territoire_id'];

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
			FROM `'.DB_PREFIX.'bsl_utilisateur`
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
 * Verifie que l'utilisateur courant à les droits demandés
 * @param string $domaine
 * @return array
 */
function secu_is_authorized($domaine)
{
	global $conn;
	$authorized = null;

	if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
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
			case DROIT_CRITERE :
				$champs = '`formulaire_r` as `lecture`, `formulaire_w` as `ecriture`'; break;
			case 'accueil' :
				$champs = '`demande_r` as `'.DROIT_DEMANDE.'`, `offre_r` as `'.DROIT_OFFRE.'`, `mesure_r` as `'.DROIT_MESURE.'`, `professionnel_r` as `'.DROIT_PROFESSIONNEL.'`, `utilisateur_r` as `'.DROIT_UTILISATEUR.'`, `formulaire_r` as `'.DROIT_CRITERE.'`, `theme_r` as `'.DROIT_THEME.'`, `territoire_r` as `'.DROIT_TERRITOIRE.'`'; break;
		}
		$sql = 'SELECT '.$champs.' FROM `'.DB_PREFIX.'bsl__droits` 
			JOIN `'.DB_PREFIX.'bsl_utilisateur` ON `'.DB_PREFIX.'bsl__droits`.`id_statut`=`'.DB_PREFIX.'bsl_utilisateur`.`id_statut`
			WHERE `id_utilisateur` = ? ';
		$stmt = mysqli_prepare($conn, $sql);
		$id = (int)$_SESSION['user_id'];
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
			return $perimetre['lecture'];
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
			//on checke les territoires de user_id et $domaine/$id. si c'est les mêmes return true
			if($domaine=='territoire' && $zone_id == $id) {
				if($check['ecriture']==PERIMETRE_ZONE) {
					$droit_ecriture = true;
				}else{
					$droit_ecriture += false;
				}
			
			}else{
				if($domaine=='demande') {
					$query='SELECT id_demande as `id` FROM `bsl_demande`
						JOIN bsl_offre ON bsl_offre.id_offre=bsl_demande.id_offre
						JOIN bsl_professionnel ON bsl_professionnel.id_professionnel=bsl_offre.id_professionnel AND competence_geo="territoire"
						WHERE bsl_professionnel.id_competence_geo=? AND id_demande=? ';
					$stmt = mysqli_prepare($conn, $query);
					mysqli_stmt_bind_param($stmt, 'ii', $zone_id, $id);
				
				} else if($domaine=='offre') {
					$query='SELECT id_offre as `id` FROM `bsl_offre`
						JOIN bsl_professionnel ON bsl_professionnel.id_professionnel=bsl_offre.id_professionnel AND competence_geo="territoire"
						WHERE bsl_professionnel.id_competence_geo=? AND id_offre=? ';
					$stmt = mysqli_prepare($conn, $query);
					mysqli_stmt_bind_param($stmt, 'ii', $zone_id, $id);
					
				} else if($domaine=='mesure') {
					$query='SELECT id_mesure as `id` FROM `bsl_mesure`
						JOIN bsl_professionnel ON bsl_professionnel.id_professionnel=bsl_mesure.id_professionnel AND bsl_professionnel.competence_geo="territoire"
						WHERE bsl_professionnel.id_competence_geo=? AND id_mesure=? ';
					$stmt = mysqli_prepare($conn, $query);
					mysqli_stmt_bind_param($stmt, 'ii', $zone_id, $id);
					
				} else if($domaine=='professionnel') {
					$query='SELECT id_professionnel as `id` FROM `bsl_professionnel`
						WHERE competence_geo="territoire" AND id_competence_geo=? AND id_professionnel=? ';
					$stmt = mysqli_prepare($conn, $query);
					mysqli_stmt_bind_param($stmt, 'ii', $zone_id, $id);
					
				} else if($domaine=='utilisateur') {
					$query='SELECT id_utilisateur as `id` FROM `bsl_utilisateur`
						WHERE id_statut IN (2,4) AND id_metier=? AND id_professionnel=? 
						UNION
						SELECT id_utilisateur as `id` FROM `bsl_utilisateur`
						LEFT JOIN `bsl_professionnel` ON bsl_utilisateur.id_metier=bsl_professionnel.id_professionnel 
						WHERE id_statut =3 AND competence_geo="territoire" AND id_competence_geo=? AND id_professionnel=? ';
					$stmt = mysqli_prepare($conn, $query);
					mysqli_stmt_bind_param($stmt, 'iiii', $zone_id, $id, $zone_id, $id);
					
				} else if($domaine=='formulaire') {
					$query='SELECT `id_formulaire` FROM `bsl_formulaire`
						WHERE `id_territoire`=? AND `id_formulaire`= ?';
					$stmt = mysqli_prepare($conn, $query);
					mysqli_stmt_bind_param($stmt, 'ii', $zone_id, $id);
					
				} else if($domaine=='theme') {
				//***inusité***
					
				} else 
				check_mysql_error($conn);
				
				if (isset($stmt) && mysqli_stmt_execute($stmt)) {
					$result = mysqli_stmt_get_result($stmt);
					if (mysqli_num_rows($result) === 1) {
						if($check['ecriture']==PERIMETRE_ZONE) {
							$droit_ecriture = true;
						}else if($check['lecture']==PERIMETRE_ZONE) {
							$droit_ecriture += false;
						}
					}
				}
				mysqli_stmt_close($stmt);
			}
		} 
		if(($check['ecriture']==PERIMETRE_PRO || $check['lecture']==PERIMETRE_PRO) && $pro_id=secu_get_user_pro_id()){
			//on checke les pro_id de user_id et $domaine+$id. si c'est les mêmes return true
			
			if($domaine=='professionnel' && $pro_id == $id) {
				if($check['ecriture']==PERIMETRE_PRO) {
					$droit_ecriture = true;
				}else{
					$droit_ecriture += false;
				}
			
			}else {
				if($domaine=='demande') {
					$query='SELECT id_demande as `id` FROM `bsl_demande`
						JOIN bsl_offre ON bsl_offre.id_offre=bsl_demande.id_offre
						JOIN bsl_professionnel ON bsl_professionnel.id_professionnel=bsl_offre.id_professionnel AND competence_geo="territoire"
						WHERE bsl_professionnel.id_professionnel=? AND id_demande=? ';
					$stmt = mysqli_prepare($conn, $query);
					mysqli_stmt_bind_param($stmt, 'ii', $pro_id, $id);
				
				} else if($domaine=='offre') {
					$query='SELECT id_offre as `id` FROM `bsl_offre`
						JOIN bsl_professionnel ON bsl_professionnel.id_professionnel=bsl_offre.id_professionnel AND competence_geo="territoire"
						WHERE bsl_professionnel.id_professionnel=? AND id_offre=? ';
					$stmt = mysqli_prepare($conn, $query);
					mysqli_stmt_bind_param($stmt, 'ii', $pro_id, $id);
					
				} else if($domaine=='mesure') {
					$query='SELECT id_mesure as `id` FROM `bsl_mesure`
						JOIN bsl_professionnel ON bsl_professionnel.id_professionnel=bsl_mesure.id_professionnel AND bsl_professionnel.competence_geo="territoire"
						WHERE bsl_professionnel.id_professionnel=? AND id_mesure=? ';
					$stmt = mysqli_prepare($conn, $query);
					mysqli_stmt_bind_param($stmt, 'ii', $pro_id, $id);
				
				} else if($domaine=='utilisateur') {
					$query='SELECT id_utilisateur as `id` FROM `bsl_utilisateur`
						LEFT JOIN `bsl_professionnel` ON bsl_utilisateur.id_metier=bsl_professionnel.id_professionnel 
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
		if($domaine=='utilisateur' && $id==$_SESSION['user_id']) {
			$droit_ecriture += true;
		}
	}
	
	// si on a pas les droits d'accès à cette page - on retourne à l'accueil directement
	if ($droit_ecriture===null) {
		/*header('Location: accueil.php');
		exit();*/
	}
	
	echo 'check '.$check['lecture'].' / '.$check['ecriture'].' → ecriture : '.(($droit_ecriture)?'oui':'non');
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

	if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
		$sql = 'SELECT `id_statut` 
				FROM `'.DB_PREFIX.'bsl_utilisateur` 
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

	$token = hash('sha256', $email . time() . rand(0, 1000000));

	$sql = 'UPDATE `'.DB_PREFIX.'bsl_utilisateur` 
			SET `reinitialisation_mdp`= ? ,`date_demande_reinitialisation`= NOW() 
			WHERE `email`= ? AND `actif_utilisateur`= 1';

	$stmt = mysqli_prepare($conn, $sql);
	mysqli_stmt_bind_param($stmt, 'ss', $token, $_POST['login']);
	check_mysql_error($conn);
	if (mysqli_stmt_execute($stmt)) {
		if (mysqli_stmt_affected_rows($stmt) === 1) {
			$subject = mb_encode_mimeheader('Réinitialisation de votre mot de passe', 'UTF-8');
			$message = "<html><p>Vous avez demandé la réinitialisation de votre mot de passe.</p> "
				. "<p>Pour saisir votre nouveau mot de passe, merci de cliquer sur ce lien : <a href=\"http://" . $_SERVER['SERVER_NAME'] . "/admin/motdepasseoublie.php?t=" . $token . "\">" . $_SERVER['SERVER_NAME'] . "/admin/motdepasseoublie.php?t=" . $token . "</a></p>"
				. "<p>Ce lien est valide trois jours, après quoi il vous faudra refaire une demande.</html>";
			$headers = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			$headers .= 'From: La Boussole des jeunes <boussole@jeunes.gouv.fr>' . "\r\n";

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
			FROM `'.DB_PREFIX.'bsl_utilisateur` 
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

	$sql = 'UPDATE `'.DB_PREFIX.'bsl_utilisateur` 
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
	if ((int)$id > 0) {
		$_SESSION['territoire_id'] = (int)$id;
	} else {
		$_SESSION['territoire_id'] = null;
	}
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