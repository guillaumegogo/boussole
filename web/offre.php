<?php
include('secret/connect.php');
include('inc/functions.php');
include('inc/variables.php');

//********* permet de revenir sur les formulaires sans recharger
header('Cache-Control: no cache'); 
session_cache_limiter('private_no_expire'); 

//********* valeur de sessions
session_start();

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
	mysqli_stmt_bind_param($stmt, 'isss', $id_offre, $_POST["coordonnees"], $_SESSION['code_insee'], liste_criteres(','));
	$result_dmd = mysqli_stmt_execute($stmt);

	//*********** envoi de mail si demandé
	if($result_dmd){
		if(isset($_POST["envoi_mail"])){
			$to = "boussole@yopmail.fr"; //en prod il faudra mettre ici l'adresse du pro ie $row["courriel_offre"]
			$subject = "Une demande a été déposée sur la Boussole des jeunes";
			$message = "<html><p>Un jeune est intéressé par l'offre <b>".$row["nom_offre"]."</b>.</p>
			<p>Il a déposé une demande de contact le ".strftime('%d %B %Y à %H:%M')."</p>
			<p>Son profil est le suivant : ".liste_criteres('<br/>')."</p>
			<p>Merci de prévenir de la suite données à la demande dans l'<a href=\"http://www.gogo.fr/boussole/admin/demande_liste.php\">espace de gestion de la Boussole</a>.
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
?>

<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="css/style.css" />
	<link rel="icon" type="image/png" href="img/compass-icon.png" />
	<title><?php echo $titredusite; ?></title>
</head>

<body><div id="main">
<div class="bandeau"><div class="titrebandeau"><a href="index.php"><?php echo $titredusite; ?></a></div></div>

<?php
if(isset($id_offre)) {
	if (!$id_offre) {
?>

<p style="text-align:center; margin-top:10%;">Aucune offre n'est sélectionnée. <a href="index.php">Recommencez</a>.</p>

<?php
	} else {
?>

<div class="soustitre"  style="margin-top:3%">Je suis intéressé par l'offre de service &laquo;&nbsp;<b><?php echo $row["nom_offre"]; ?></b>&nbsp;&raquo;.</div>

<form class="joli resultat" style="margin-top:2%" action="offre.php" method="post">
<fieldset>
	<legend>Détail de l'offre de service</legend>

	<table class="offre">
		<tr>
			<td>Description de l'offre</td>
			<td colspan=2><?php echo $row["description_offre"]; ?></td>
		</tr>
		<tr>
			<td>Dates de validité:</td>
			<td><?php echo $row["date_debut"]; ?> au <?php echo $row["date_fin"]; ?></td>
            <td rowspan=3>
            <div style=" text-align:center; padding:0.5em;">
	<p style="margin:0.5em"><i>Partage-cette offre :</i></p>
	<a target="_blank" title="Twitter" href="https://twitter.com/share?url=<?php echo $fullurl; ?>&text=<?php echo "La Boussole des jeunes : ".$row["nom_offre"]; ?>&via=la Boussole des jeunes" rel="nofollow" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=400,width=700');return false;"><img src="img/ci_twitter.png" width="32px" alt="Twitter" /></a>
	<a target="_blank" title="Facebook" href="https://www.facebook.com/sharer.php?u=<?php echo $fullurl; ?>&t=<?php echo "La Boussole des jeunes : ".$row["nom_offre"]; ?>" rel="nofollow" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=500,width=700');return false;"><img src="img/ci_facebook.png" width="32px" alt="Facebook" /></a>
	<a target="_blank" title="Linkedin" href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $fullurl; ?>&title=<?php echo "La Boussole des jeunes : ".$row["nom_offre"]; ?>" rel="nofollow" onclick="javascript:window.open(this.href, '','menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=450,width=650');return false;"><img src="img/ci_linkedin.png" width="32px" alt="Linkedin" /></a>
	<a target="_blank" title="Envoyer par mail" href="mailto:?subject=<?php echo "La Boussole des jeunes : ".$row["nom_offre"]; ?>&body=<?php echo $fullurl; ?>" rel="nofollow"><img src="img/ci_mail.png" width="32px" alt="email" /></a>
	</div></td>
		</tr>
		<tr>
			<td style="padding:0.5em;">Thème</td>
			<td style="padding:0.5em;"><?php echo $row["theme_offre"]; ?></td>
		</tr>
		<tr>
			<td style="padding:0.5em;">Sous-thème</td>
			<td style="padding:0.5em;"><?php echo $row["sous_theme_offre"]; ?></td>
		</tr>
	</table>
</fieldset>

<fieldset>
	<legend>Demande de contact</legend>
	<div class="pluspetit">
<?php
		if (isset($result_dmd)){
			echo $resultat;
		} else { 
?>
		<p>Si je suis intéressé.e par cette offre, je laisse mon adresse de courriel ou mon numéro de téléphone portable pour être contacté·e par un conseiller d'ici <b><?php echo $row["delai_offre"]; ?> jours</b> maximum.</p>
		
		<div style="text-align:center; margin:1em auto;">
			<input type="hidden" name="id_offre" value="<?php echo $id_offre;?>">
			<input type="text" name="coordonnees" placeholder="Mon adresse courriel ou n° de téléphone"/> 
			<button type="submit">Je demande à être contacté·e</button>
			<br/> 
			<!--en vue de tests--> 
			<div style="font-size:small; color:red;">(<input type="checkbox" name="envoi_mail" value="1" > test : envoyer effectivement le mail prévu pour le professionnel, <a href="http://www.yopmail.fr?boussole" target="_blank">consultable ici</a>)</div>
			</select> 
		</div>

<?php
		}
?>
	</div>
</fieldset>

<fieldset class="demande_offre">
	<legend>Organisme</legend>
	<div class="pluspetit">

		<p>Cette offre de service est proposée par l'organisme suivant :</p>

		<div class="map"><iframe src="https://maps.google.it/maps?q=<?php echo $adresse;?>&output=embed"></iframe></div>

		<table class="offre" style="width:50%;">
			<tr>
				<td style="width:15em;">Professionnel</td>
				<td><b><?php echo $row["nom_pro"]; ?></b></td>
			</tr>

			<tr>
				<td>Adresse</td>
				<td><?php echo $adresse; ?></td>
			</tr>
			<tr>
				<td>Site internet</td>
				<td><?php echo $url; ?></td>
			</tr>
			<tr>
				<td>Courriel</td>
				<td><?php echo $courriel_offre; ?></td>
			</tr>
			<tr>
				<td>Téléphone</td>
				<td><?php if ($id_offre) { echo $row["telephone_offre"]; } ?></td>
			</tr>
			<tr>
				<td>Zone concernée</td>
				<td><?php echo $zone; ?></td>
			</tr>
		</table>
	</div>
</fieldset>
</form>

<p class="lienenbas"><a href="resultat.php">Revenir à la liste des offres</a></p>

<?php 
	}
}
?>

<div style="height:2em;">&nbsp;</div> <!--tweak css-->

<!--
<?php print_r($_POST); echo "<br/>"; print_r($_SESSION);echo "\r\n".$sql;
if (isset($sql_dmd)) { echo "\r\n".$sql_dmd; } ?>
-->
<?php include('inc/footer.inc.php'); ?>
</div>
</body>
</html>