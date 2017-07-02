<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="css/style.css" />
	<link rel="icon" type="image/png" href="img/compass-icon.png" />
	<title><?=ucfirst($titredusite); ?></title>
</head>

<body><div id="main">
<div class="bandeau"><div class="titrebandeau"><a href="index.php"><?=$titredusite; ?></a></div></div>

<?php
if(isset($id_offre)) {
	if (!$id_offre) {
?>

<p style="text-align:center; margin-top:10%;">Aucune offre n'est sélectionnée. <a href="index.php">Recommencez</a>.</p>

<?php
	} else {
?>

<div class="soustitre"  style="margin-top:3%">Je suis intéressé par l'offre de service &laquo;&nbsp;<b><?=$row["nom_offre"]; ?></b>&nbsp;&raquo;.</div>

<form class="joli resultat" style="margin-top:2%" action="offre.php" method="post">
<fieldset>
	<legend>Détail de l'offre de service</legend>

	<table class="offre">
		<tr>
			<td>Description de l'offre</td>
			<td colspan=2><?=$row["description_offre"]; ?></td>
		</tr>
		<tr>
			<td>Dates de validité:</td>
			<td><?=$row["date_debut"]; ?> au <?=$row["date_fin"]; ?></td>
			<td rowspan=3>
			<div style=" text-align:center; padding:0.5em;">
	<p style="margin:0.5em"><i>Partage-cette offre :</i></p>
	<a target="_blank" title="Twitter" href="https://twitter.com/share?url=<?=$fullurl; ?>&text=La Boussole des jeunes : <?=$row["nom_offre"]; ?>&via=la Boussole des jeunes" rel="nofollow" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=400,width=700');return false;"><img src="img/ci_twitter.png" width="32px" alt="Twitter" /></a>
	<a target="_blank" title="Facebook" href="https://www.facebook.com/sharer.php?u=<?=$fullurl; ?>&t=La Boussole des jeunes : <?=$row["nom_offre"]; ?>" rel="nofollow" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=500,width=700');return false;"><img src="img/ci_facebook.png" width="32px" alt="Facebook" /></a>
	<a target="_blank" title="Linkedin" href="https://www.linkedin.com/shareArticle?mini=true&url=<?=$fullurl; ?>&title=La Boussole des jeunes : <?=$row["nom_offre"]; ?>" rel="nofollow" onclick="javascript:window.open(this.href, '','menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=450,width=650');return false;"><img src="img/ci_linkedin.png" width="32px" alt="Linkedin" /></a>
	<a target="_blank" title="Envoyer par mail" href="mailto:?subject=La Boussole des jeunes : <?=$row["nom_offre"]; ?>&body=<?=$fullurl; ?>" rel="nofollow"><img src="img/ci_mail.png" width="32px" alt="email" /></a>
	</div></td>
		</tr>
		<tr>
			<td style="padding:0.5em;">Thème</td>
			<td style="padding:0.5em;"><?=$row["theme_offre"]; ?></td>
		</tr>
		<tr>
			<td style="padding:0.5em;">Sous-thème</td>
			<td style="padding:0.5em;"><?=$row["sous_theme_offre"]; ?></td>
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
		<p>Si je suis intéressé.e par cette offre, je laisse mon adresse de courriel ou mon numéro de téléphone portable pour être contacté·e par un conseiller d'ici <b><?=$row["delai_offre"]; ?> jours</b> maximum.</p>

		<div style="text-align:center; margin:1em auto;">
			<input type="hidden" name="id_offre" value="<?=$id_offre;?>">
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

		<div class="map"><iframe src="https://maps.google.it/maps?q=<?=$adresse;?>&output=embed"></iframe></div>

		<table class="offre" style="width:50%;">
			<tr>
				<td style="width:15em;">Professionnel</td>
				<td><b><?=$row["nom_pro"]; ?></b></td>
			</tr>

			<tr>
				<td>Adresse</td>
				<td><?=$adresse; ?></td>
			</tr>
			<tr>
				<td>Site internet</td>
				<td><?=$url; ?></td>
			</tr>
			<tr>
				<td>Courriel</td>
				<td><?=$courriel_offre; ?></td>
			</tr>
			<tr>
				<td>Téléphone</td>
				<td><?php if ($id_offre) { echo $row["telephone_offre"]; } ?></td>
			</tr>
			<tr>
				<td>Zone concernée</td>
				<td><?=$zone; ?></td>
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