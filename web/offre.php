<?php

include('../src/web/bootstrap.php');

//********* variables
$envoi_mail = false;
$adresse = '';
$url = '';
$courriel_offre = '';
$zone = '';
$coordonnees = '';
$row = array();
$msg = null;

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
if (isset($_POST['coordonnees']) && $_POST['coordonnees']) {

    $_SESSION['coordonnees'] = $_POST['coordonnees'];

	//*********** création de la demande
	$recherche_associee = (isset($_SESSION['recherche_id']) && (int)$_SESSION['recherche_id']>0 ) ? $_SESSION['recherche_id'] : null;
	$demande = create_demande($id_offre, $_POST['coordonnees'], $recherche_associee);
	$id_demande = $demande[0];
	$token = $demande[1];

	//*********** envoi des mails
	if ($id_demande) {
		$resultat = envoi_mails_demande( $row['courriel_offre'], $row['nom_offre'], $_POST['coordonnees'], $token);

		if ($resultat){
			$msg_depot ="<p class='msg'>Ta demande de contact pour l'offre <strong>&nbsp;".$row['nom_offre']."&nbsp;</strong> a bien été enregistrée et un courriel contenant ta recherche à été transmis à l'organisme proposant l'offre de service.</p>";
		}else{
			$msg_depot = "<p class='msg'>Ta demande de contact pour l'offre <strong>&nbsp;".$row['nom_offre']."&nbsp;</strong> a bien été enregistrée mais aucun courriel complémentaire n'a été envoyé. Tu peux contacter directement le professionnel.</p>";
		}
	} else {
		$msg_depot = "<p class='msg'>L'application a rencontré un problème. Ta demande n'a pas pu être enregistrée. Merci de contacter l'administrateur du site si le problème persiste.</p>";
	}
}

//view
require 'view/offre.tpl.php';