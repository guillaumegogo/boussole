<?php
/************************ todo 24/5/2017 : 
- envoi du mail avec une coche 
- mettre en forme l'affichage de la liste des critères
****************************************/

include('secret/connect.php');
include('inc/functions.php');

//********* permet de revenir sur les formulaires sans recharger
header('Cache-Control: no cache'); 
session_cache_limiter('private_no_expire'); 

//********* valeur de sessions
session_start();

//********* l'id de l'offre peut arriver en GET ou en POST selon d'où on vient
$id_offre = null;
if(isset($_POST["id_offre"])) { 
	$id_offre = securite_bdd($conn, $_POST["id_offre"]); 
} else if (isset($_GET["id"])) { 
	$id_offre = securite_bdd($conn, $_GET["id"]); 
}
$adresse = null;

$fullurl = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?id=" . $id_offre;

//********* fonction de présentation des critères du jeune
function liste_criteres($separateur){
	$txt_criteres=null;
	$tab_criteres_a_afficher = array("ville_habitee", "besoin", "age", "europeen", "jesais", "situation", "etudes", "diplome", "permis", "handicap", "type_emploi", "temps_plein", "secteur", "experience", "inscription");
	
	foreach($_SESSION as $index=>$valeur){
		if(in_array($index, $tab_criteres_a_afficher)){
			$txt = str_replace("_", " ", $index)." : ";
			if(is_array($valeur)){
				foreach($valeur as $index2=>$valeur2)
					$txt .= $valeur2." /";
				$txt = substr($txt, 0, -1);
			}else{
				$txt .= $valeur;
			}
			$txt_criteres .= $txt.$separateur;
		}
	}
	return $txt_criteres;
}

//********* requête de récup de l'offre pour affichage
if(isset($id_offre)) {
	$sql = "SELECT `nom_offre`, `description_offre`, DATE_FORMAT(`debut_offre`, '%d/%m/%Y') AS date_debut, DATE_FORMAT(`fin_offre`, '%d/%m/%Y') AS date_fin, `theme_offre`, `sous_theme_offre`, `adresse_offre`, `code_postal_offre`, `ville_offre`, `code_insee_offre`, `courriel_offre`, `telephone_offre`, `site_web_offre`, `delai_offre`, `zone_selection_villes`, `actif_offre`, `nom_pro`  
	FROM `bsl_offre` JOIN `bsl_professionnel` ON `bsl_professionnel`.id_professionnel=`bsl_offre`.id_professionnel 
	WHERE `id_offre`=".$id_offre;
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_assoc($result);
    }
	$adresse = $row["adresse_offre"]." ".$row["code_postal_offre"]." ".$row["ville_offre"];
}

//********* si demande de contact
if(isset($_POST["coordonnees"])){
	
	//*********** requête de création de la demande
	$sql_dmd = "INSERT INTO `bsl_demande`(`date_demande`, `id_offre`, `contact_jeune`, `code_insee_jeune`, `profil`) VALUES (NOW(), ".$id_offre.",\"".securite_bdd($conn, $_POST["coordonnees"])."\",\"".$_SESSION['code_insee']."\",\"".liste_criteres(',')."\")";
	$result_dmd = mysqli_query($conn, $sql_dmd);

	//*********** envoi de mail si demandé
	$envoi_mail = false;
	if(isset($_POST["envoi_mail"])){
		$to = "guillaume.gogo@jeunesse-sports.gouv.fr"; //en prod il faudra mettre ici l'adresse du pro ie $row["courriel"]
		$subject = "Une demande a été déposée sur la Boussole des jeunes";
		$message = "<html><p>Un jeune est intéressé par l'offre <b>".$row["nom_offre"]."</b>.</p>
		<p>Il a déposé une demande de contact le ".strftime('%d %B %Y à %H:%M')."</p>
		<p>Son profil est le suivant : ".liste_criteres('<br/>')."</p>
		<p>Merci de prévenir de la suite données à la demande dans l'<a href=\"http://www.gogo.fr/boussole/admin/demande_liste.php\">espace de gestion de la Boussole</a>.
        <p>Ce mail aurait du être envoyé à ".$row["courriel_offre"]."</p></html>";
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=charset=utf-8' . "\r\n";
		$headers .= 'To: boussole@yopmail.fr' . "\r\n";
		$headers .= 'From: La Boussole des jeunes <boussole@gogo.fr>' . "\r\n";
		$headers .= 'Cc: guillaume.gogo@jeunesse-sports.gouv.fr' . "\r\n";
		$envoi_mail = mail($to, $subject, $message, $headers);
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
	<title>Boussole des jeunes</title>
</head>

<body><div id="main">
<div class="bandeau"><a href="index.php">La boussole des jeunes</a></div>

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
			<td style="padding:0.5em;">Sous-thème(s)</td>
			<td style="padding:0.5em;"><?php //**************** todo : lier à la base de données
if ($row["sous_theme_offre"]=="techniques") { echo "Rendre ma recherche d'emploi plus efficace par la maitrise des techniques"; }
if ($row["sous_theme_offre"]=="information") { echo "Être informé sur les salons, forums, évènements et actualités utiles à ma recherche d'emploi"; }
			?></td>
		</tr>
	</table>
</fieldset>

<fieldset>
	<legend>Demande de contact</legend>
	<div class="pluspetit">
<?php
		if (isset($envoi_mail)) {
			if ($envoi_mail) {
?>
		<p><img src="img/ok_circle.png" width="24px" style="margin-bottom:-0.3em;"> <b>Ta demande a bien été enregistrée et un courriel contenant les informations suivantes à été transmis à l'organisme proposant l'offre de service.</b></p>
		
		<div style="width:90%; margin:auto; -webkit-column-count: 5; -moz-column-count: 5; column-count: 5; font-size:0.8em;">
			<?php echo liste_criteres('<br/>'); ?><abbr title="A mettre en forme...">&#9888;</abbr> 
		</div>
		
<?php
			}else{
?>
		<p><img src="img/exclamation.png" width="24px"> Ta demande a bien été enregistrée mais aucun email complémentaire n'a été envoyé. <?php echo "Tu peux contacter directement ".$row["courriel_offre"]."."; ?></p>
		
<?php
			}
		} else { 
?>
		<p>Si je suis intéressé.e par cette offre, je laisse mon email ou mon numéro de téléphone portable pour être contacté·e par un conseiller d'ici <b><?php echo $row["delai_offre"]; ?> jours</b> maximum.</p>
		
		<div style="text-align:center; margin:1em auto;">
			<input type="hidden" name="id_offre" value="<?php echo $id_offre;?>">
			<input type="text" name="coordonnees" value="Mes coordonnées"/> 
			<button type="submit">Je demande à être contacté·e</button>
			<br/> 
			<!--en vue de tests--> 
			<div style="font-size:small; color:red;">(le temps des tests : <input type="checkbox" name="envoi_mail" value="1" > envoyer effectivement un mail qui sera <a href="http://www.yopmail.fr?boussole" target="_blank">consultable ici</a>)</div>
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
				<td><?php 
		$url=$row["site_web_offre"];
		if (substr($url, 0, 3)=="www") $url = "http://".$url;
		if (filter_var($url, FILTER_VALIDATE_URL)) {
			echo "<a href=\"".$url."\" target=\"_blank\">".$url."</a>";
		} else {
			echo $url;
		}
				?></td>
			</tr>
			<tr>
				<td>Courriel</td>
				<td><?php 
		if (filter_var($row["courriel_offre"], FILTER_VALIDATE_EMAIL)) {
			echo "<a href=\"mailto:".$row["courriel_offre"]."\">".$row["courriel_offre"]."</a>";
		} else {
			echo $row["courriel_offre"];
		}
				?></td>
			</tr>
			<tr>
				<td>Téléphone</td>
				<td><?php if ($id_offre) { echo $row["telephone_offre"]; } ?></td>
			</tr>
			<tr>
				<td>Zone concernée</td>
				<td><?php 
if (!$row["zone_selection_villes"]) { 
	echo "Territoire"; 
} else { 
	echo "Villes <abbr title=\"à lister\">&#9888;</abbr>"; 
}
				?></td>
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
<?php include('inc/footer.inc'); ?>
</div>
</body>
</html>