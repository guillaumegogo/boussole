<?php
include('src/web/bootstrap.php');

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

//********* requête de récup de l'offre pour affichage (avant la gestion de la demande de contact pour récupérer $row)
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

	if (preg_match('/^(([-\w\d]+)(\.[-\w\d]+)*@([-\w\d]+)(\.[-\w\d]+)*(\.([a-zA-Z]{2,5}|[\d]{1,3})){1,2}|(0[67]([[\d]){8}))$/', $_POST['coordonnees'])) {

		//*********** création de la demande
		$_SESSION['web']['coordonnees'] = $_POST['coordonnees'];
		$recherche_associee = (isset($_SESSION['web']['recherche_id']) && (int)$_SESSION['web']['recherche_id']>0 ) ? $_SESSION['web']['recherche_id'] : null;
		$demande = create_demande($id_offre, $_POST['coordonnees'], $recherche_associee);
		$id_demande = $demande[0];
		$token = $demande[1];

		//*********** envoi des mails
		if ($id_demande){
			$msg = "Ta demande de contact pour l'offre <strong>&nbsp;".$row['nom_offre']."&nbsp;</strong> a bien été enregistrée.";
			
			$criteres = isset($_SESSION['web']['critere']) ? $_SESSION['web']['critere'] : null;
			$resultat = envoi_mails_demande( $row['courriel_offre'], $row['nom_offre'], $_POST['coordonnees'], $criteres, $token);
			if ($resultat){
				$msg .= " Un courriel contenant ta recherche à été transmis à l'organisme proposant l'offre de service.";
			}
			
		} else {
			$msg = "L'application a rencontré un problème, ta demande n'a pas pu être enregistrée. Merci de contacter l'administrateur du site si le problème persiste.";
		}
	}
}
//view
require 'view/offre.tpl.php';