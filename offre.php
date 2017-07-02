<?php
session_start();

include('secret/connect.php');
include('inc/functions.php');
include('inc/variables.php');

//********* censé permettre de revenir sur les formulaires sans recharger
header('Cache-Control: no cache'); 
session_cache_limiter('private_no_expire');

//********* variables
$resultat="";
$envoi_mail = false;
$adresse = "";
$url = "";
$courriel_offre = "";
$zone = "";
$row[] = array();

//********* l'id de l'offre peut arriver en GET ou en POST selon d'où on vient
$id_offre = null;
if(isset($_POST["id_offre"])) { 
	$id_offre = $_POST["id_offre"]; 
} else if (isset($_GET["id"])) { 
	$id_offre = $_GET["id"]; 
}
$fullurl = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?id=" . $id_offre;

//********* requête de récup de l'offre pour affichage
if(isset($id_offre)) {
	$sql = "SELECT `nom_offre`, `description_offre`, DATE_FORMAT(`debut_offre`, '%d/%m/%Y') AS date_debut, DATE_FORMAT(`fin_offre`, '%d/%m/%Y') AS date_fin, `theme_pere`.libelle_theme AS `theme_offre`, `theme_fils`.libelle_theme AS `sous_theme_offre`, `adresse_offre`, `code_postal_offre`, `ville_offre`, `code_insee_offre`, `courriel_offre`, `telephone_offre`, `site_web_offre`, `delai_offre`, `zone_selection_villes`, `nom_pro`  
	FROM `bsl_offre` 
	JOIN `bsl_professionnel` ON `bsl_professionnel`.id_professionnel=`bsl_offre`.id_professionnel 
	JOIN `bsl_theme` AS `theme_fils` ON `theme_fils`.id_theme=`bsl_offre`.id_sous_theme
	JOIN `bsl_theme` AS `theme_pere` ON `theme_pere`.id_theme=`theme_fils`.id_theme_pere
	WHERE `actif_offre` = 1 AND `id_offre`= ? ";
	$stmt = mysqli_prepare($conn, $sql);
	mysqli_stmt_bind_param($stmt, 'i', $id_offre);

	if (mysqli_stmt_execute($stmt)) {
		mysqli_stmt_store_result($stmt);
		if (mysqli_stmt_num_rows($stmt) > 0) {
			mysqli_stmt_bind_result($stmt, $row["nom_offre"], $row['description_offre'], $row['date_debut'], $row['date_fin'], $row['theme_offre'], $row['sous_theme_offre'], $row['adresse_offre'], $row['code_postal_offre'], $row['ville_offre'], $row['code_insee_offre'], $row['courriel_offre'], $row['telephone_offre'], $row['site_web_offre'], $row['delai_offre'], $row['zone_selection_villes'], $row['nom_pro']);
			mysqli_stmt_fetch($stmt);

			//mise en forme des données :
			$adresse = $row["adresse_offre"]." ".$row["code_postal_offre"]." ".$row["ville_offre"];

			$url=$row["site_web_offre"];
			if (substr($url, 0, 3)=="www") {
				$url = "http://".$url;
			}
			if (filter_var($url, FILTER_VALIDATE_URL)) {
				$url .= "<a href=\"".$url."\" target=\"_blank\">".$url."</a>";
			}

			$courriel_offre = $row["courriel_offre"];
			if (filter_var($row["courriel_offre"], FILTER_VALIDATE_EMAIL)) {
				$courriel_offre = "<a href=\"mailto:".$row["courriel_offre"]."\">".$row["courriel_offre"]."</a>";
			}

			if (!$row["zone_selection_villes"]) { 
				$zone = "Territoire"; 
			} else { 
				$zone = "Sélection de villes"; // TODO : pas fini !
			}
		}
	}
	mysqli_stmt_close($stmt);
}

//********* si demande de contact
if(isset($_POST["coordonnees"])){

	//*********** requête de création de la demande
	$sql_dmd = "INSERT INTO `bsl_demande`(`date_demande`, `id_offre`, `contact_jeune`, `code_insee_jeune`, `profil`) VALUES (NOW(), ?, ?, ?, ?)";
	$stmt = mysqli_prepare($conn, $sql_dmd);
	$liste=liste_criteres(',');
	mysqli_stmt_bind_param($stmt, 'isss', $id_offre, $_POST["coordonnees"], $_SESSION['code_insee'], $liste);
	$result_dmd = mysqli_stmt_execute($stmt);

	//*********** envoi de mail si demandé
	if($result_dmd){
		if(isset($_POST["envoi_mail"])){
			$to = "boussole@yopmail.fr"; //en prod il faudra mettre ici l'adresse du pro ie $row["courriel_offre"]
			$subject = "Une demande a été déposée sur la Boussole des jeunes";
			$message = "<html><p>Un jeune est intéressé par l'offre <b>".$row["nom_offre"]."</b>.</p>
			<p>Il a déposé une demande de contact le ".strftime('%d %B %Y à %H:%M')."</p>
			<p>Son profil est le suivant : ".liste_criteres('<br/>')."</p>
			<p>Merci d'indiquer la suite donnée à la demande dans l'<a href=\"http://www.gogo.fr/boussole/admin/demande_liste.php\">espace de gestion de la Boussole</a>.
			<p>Ce mail aurait du être envoyé à ".$row["courriel_offre"]."</p></html>";
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=charset=utf-8' . "\r\n";
			$headers .= 'From: La Boussole des jeunes <boussole@gogo.fr>' . "\r\n";
			$headers .= 'Cc: guillaume.gogo@jeunesse-sports.gouv.fr' . "\r\n";
			$envoi_mail = mail($to, $subject, $message, $headers);

			if ($envoi_mail){
				//todo : critères à mettre mieux en forme
				$resultat = "<p><img src=\"img/ok_circle.png\" width=\"24px\" style=\"margin-bottom:-0.3em;\"> <b>Ta demande a bien été enregistrée et un courriel contenant les informations suivantes à été transmis à l'organisme proposant l'offre de service.</b></p>
				<div style=\"width:90%; margin:auto; -webkit-column-count: 5; -moz-column-count: 5; column-count: 5; font-size:0.8em;\">".liste_criteres('<br/>')."</div>";
			}else{
				$resultat="<p><img src=\"img/exclamation.png\" width=\"24px\"> Ta demande a bien été enregistrée mais aucun courriel complémentaire n'a été envoyé. Tu peux contacter directement ".$row["courriel_offre"].".</p>";
			}
		}else{
			$resultat="<p><img src=\"img/exclamation.png\" width=\"24px\"> Ta demande a bien été enregistrée mais aucun courriel complémentaire n'a été envoyé. Tu peux contacter directement ".$row["courriel_offre"].".</p>";
		}
	}else{
		$resultat="<p><img src=\"img/exclamation.png\" width=\"24px\"> L'application a rencontré un problème. Ta demande n'a pas pu être enregistrée. Merci de contacter l'administrateur du site si le problème persiste.</p>";
	}
}

//view
require 'view/offre.tpl.php';