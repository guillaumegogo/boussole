<?php

include('../src/web/bootstrap.php');

//********* variables
$envoi_mail = false;
$adresse = '';
$url = '';
$courriel_offre = '';
$zone = '';
$row = array();
$resultat = '';

//********* l'id de l'offre peut arriver en GET ou en POST selon d'où on vient
$id_offre = 0;
if (isset($_POST['id_offre'])) {
	$id_offre = $_POST['id_offre'];
} else if (isset($_GET['id'])) {
	$id_offre = $_GET['id'];
}

//************ si pas d'id offre valide
if (!isset($id_offre) || !is_numeric($id_offre)) {
	header('Location: index.php');
	exit();
}

//********* requête de récup de l'offre pour affichage
if (isset($id_offre)) {
	$row = get_offre($id_offre);

	//****** mise en forme des données utilisées dans l'affichage
	$url_toshare = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?id=' . $id_offre; // utilisé pour le partage de l'URL
	$adresse = $row['adresse_offre'] . ' ' . $row['code_postal_offre'] . ' ' . $row['ville_offre'];
	$url = $row['site_web_offre'];
	if (filter_var($url, FILTER_VALIDATE_URL)) {
		$url = '<a href="' . $url . '" target="_blank">' . str_replace(array("http://", "https://"), "", $url) . '</a>';
	}
	$courriel_offre = $row['courriel_offre'];
	if (filter_var($row['courriel_offre'], FILTER_VALIDATE_EMAIL)) {
		$courriel_offre = '<a href="mailto:"' . $row['courriel_offre'] . '">' . $row['courriel_offre'] . '</a>';
	}
	if (!$row['zone_selection_villes']) {
		$zone = 'Zone du professionnel';
	} else {
		$zone = 'Sélection de villes'; // todo : à détailler ?
	}
}

//********* si demande de contact
if (isset($_POST['coordonnees'])) {

	//*********** création de la demande
	$id_demande = create_demande($id_offre, $_POST['coordonnees']);

	//*********** envoi des mails
	if ($id_demande) {
		if ((ENVIRONMENT === ENV_PROD) || (ENVIRONMENT === ENV_TEST)) {
			//au professionnel
			$to = 'boussole@yopmail.fr';
			if (ENVIRONMENT === ENV_PROD) {
				$to = $row['courriel_offre'];
			}
			$subject = mb_encode_mimeheader('Une demande a été déposée sur la Boussole des droits');
			$message = "<html><p>Un jeune est intéressé par l'offre <b>" . $row['nom_offre'] . "</b>.</p>"
				. "<p>Il a déposé une demande de contact le " . utf8_encode(strftime('%d %B %Y &agrave; %H:%M')) . "</p>"
				. "<p>Son profil est le suivant : " . liste_criteres('<br/>') . "</p>"
				. "<p>Merci d'indiquer la suite donnée à la demande dans l'<a href=\"http://" . $_SERVER['SERVER_NAME'] . "/admin/\">espace de gestion de la Boussole</a></p></html>";
			$headers = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			$headers .= 'From: La Boussole des droits <noreply@boussole.jeunes.gouv.fr>' . "\r\n";
			if (ENVIRONMENT !== ENV_PROD) {
				$headers .= 'Cc: guillaume.gogo@jeunesse-sports.gouv.fr' . "\r\n";
			}
			$envoi_mail = mail($to, $subject, $message, $headers);

			//accusé d'envoi au demandeur
			if (filter_var($_POST['coordonnees'], FILTER_VALIDATE_EMAIL)) {
				
				$to = $_POST['coordonnees'];
				$subject = mb_encode_mimeheader('Vous avez déposé une demande de contact sur la Boussole des droits');
				$message = "<html><p>Nous vous confirmons qu'un message a été transmis au professionnel avec vos coordonnées et les informations suivantes :</p>"
					. "<p>Offre <b>" . $row['nom_offre'] . "</b>.</p>"
					. "<p>Profil : " . liste_criteres('<br/>') . "</p></html>";
				$headers = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
				$headers .= 'From: La Boussole des droits <noreply@boussole.jeunes.gouv.fr>' . "\r\n";
				if (ENVIRONMENT !== ENV_PROD) {
					$headers .= 'Cc: guillaume.gogo@jeunesse-sports.gouv.fr' . "\r\n";
				}
				$envoi_accuse = mail($to, $subject, $message, $headers);
			}
			
			if ($envoi_mail) {
				$resultat = "<p><img src=\"img/ok_circle.png\" width=\"24px\" style=\"margin-bottom:-0.3em;\"> <b>Ta demande a bien été enregistrée et un courriel contenant les informations suivantes à été transmis à l'organisme proposant l'offre de service.</b></p>
				<div style=\"liste_criteres\">" . liste_criteres('<br/>') . "</div>";
			} else {
				$resultat = "<p><img src=\"img/exclamation.png\" width=\"24px\"> Ta demande a bien été enregistrée mais aucun courriel complémentaire n'a été envoyé. Tu peux contacter directement " . $row['courriel_offre'] . ".</p>";
			}
		} else {
			$resultat = "<p><img src=\"img/exclamation.png\" width=\"24px\"> Ta demande a bien été enregistrée mais aucun courriel complémentaire n'a été envoyé. Tu peux contacter directement " . $row['courriel_offre'] . ".</p>";
		}
	} else {
		$resultat = "<p><img src=\"img/exclamation.png\" width=\"24px\"> L'application a rencontré un problème. Ta demande n'a pas pu être enregistrée. Merci de contacter l'administrateur du site si le problème persiste.</p>";
	}
}

//view
require 'view/offre.tpl.php';