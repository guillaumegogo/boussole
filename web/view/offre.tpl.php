<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="css/style.css" />
	<link rel="icon" type="image/png" href="img/compass-icon.png" />
	<title><?php xecho(ucfirst($titredusite)) ?></title>
</head>
<body><div id="main">
	<div class="bandeau"><img src="img/marianne.png" width="93px" style="float:left;"><div class="titrebandeau"><a href="index.php"><?php xecho($titredusite) ?></a></div></div>

	<?php
	if($row['nom_offre']) { //si on a une offre
		?>
		<div class="soustitre"  style="margin-top:3%">Je suis intéressé par l'offre de service &laquo;&nbsp;<b><?php xecho($row['nom_offre']) ?></b>&nbsp;&raquo;.</div>
		<form class="joli resultat" style="margin-top:2%" action="offre.php" method="post">
			<fieldset>
				<legend>Détail de l'offre</legend>
				<table class="offre">
					<tr>
						<td>Description</td>
						<td colspan=2><?php xbbecho($row['description_offre']) ?></td>
					</tr>
					<tr>
						<td>Validité</td>
						<td><?php xecho($row['date_debut']) ?> au <?php xecho($row['date_fin']) ?></td>
						<td rowspan=2>
							<div style=" text-align:center; padding:0.5em;">
								<p style="margin:0.5em"><i>Partage-cette offre :</i></p>
								<a target="_blank" title="Twitter" href="https://twitter.com/share?url=<?= $url_toshare ?>&text=La Boussole des jeunes : <?= $row['nom_offre'] ?>&via=la Boussole des jeunes" rel="nofollow" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=400,width=700');return false;"><img src="img/ci_twitter.png" width="32px" alt="Twitter" /></a>
								<a target="_blank" title="Facebook" href="https://www.facebook.com/sharer.php?u=<?= $url_toshare ?>&t=La Boussole des jeunes : <?= $row['nom_offre'] ?>" rel="nofollow" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=500,width=700');return false;"><img src="img/ci_facebook.png" width="32px" alt="Facebook" /></a>
								<a target="_blank" title="Linkedin" href="https://www.linkedin.com/shareArticle?mini=true&url=<?= $url_toshare ?>&title=La Boussole des jeunes : <?= $row['nom_offre'] ?>" rel="nofollow" onclick="javascript:window.open(this.href, '','menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=450,width=650');return false;"><img src="img/ci_linkedin.png" width="32px" alt="Linkedin" /></a>
								<a target="_blank" title="Envoyer par mail" href="mailto:?subject=La Boussole des jeunes : <?= $row['nom_offre'] ?>&body=<?= $url_toshare ?>" rel="nofollow"><img src="img/ci_mail.png" width="32px" alt="email" /></a>
							</div></td>
					</tr>
					<!--<tr>
			<td style="padding:0.5em;">Thème</td>
			<td style="padding:0.5em;"><?php xecho($row['theme_offre']) ?></td>
		</tr>-->
					<tr>
						<td style="padding:0.5em;">Thèmatique</td>
						<td style="padding:0.5em;"><?php xecho($row['sous_theme_offre']) ?></td>
					</tr>
				</table>
			</fieldset>
			<fieldset>
				<legend>Demande de contact</legend>
				<div class="cadre">
					<?php
					if(isset($_POST['coordonnees'])){
						echo $resultat;
					} else {
						?>
						<p>Si je suis intéressé.e par cette offre, je laisse mon adresse de courriel ou mon numéro de téléphone portable pour être contacté·e par un conseiller d'ici <b><?php xecho($row['delai_offre']) ?> jours</b> maximum.</p>
						<div style="text-align:center; margin:1em auto;">
							<input type="hidden" name="id_offre" value="<?php xecho($id_offre) ?>">
							<input type="text" name="coordonnees" placeholder="Mon adresse courriel ou n° de téléphone"/>
							<button type="submit">Je demande à être contacté·e</button>
							<br/>
						<?php 
						if (ENVIRONMENT !== ENV_PROD) { 
							if (ENVIRONMENT === ENV_TEST) { 
						?>
							<div style="font-size:small; color:red;">En environnement de test, le mail censé être adressé au professionnel est <a href="http://www.yopmail.fr?boussole" target="_blank">consultable ici</a>.</div>
						<?php }else{ ?>
							<div style="font-size:small; color:red;">Aucun mail n'est envoyé depuis cet environnement.</div>
						<?php }
						} ?>
							</select>
						</div>
						<?php
					}
					?>
				</div>
			</fieldset>
			<fieldset class="demande_offre">
				<legend>Organisme</legend>
				<div class="cadre">
					<p>Cette offre de service est proposée par l'organisme suivant :</p>
					<div class="map"><iframe src="https://maps.google.it/maps?q=<?= $adresse ?>&output=embed"></iframe></div>
					<table class="offre" style="width:auto;"> <!--style="width:50%;"-->
						<tr>
							<td style="width:15em;">Professionnel</td>
							<td><b><?php xecho($row['nom_pro']) ?></b></td>
						</tr>
						<tr>
							<td>Adresse</td>
							<td><?php xecho($adresse) ?></td>
						</tr>
						<tr>
							<td>Site internet</td>
							<td><?php echo($url) ?></td>
						</tr>
						<?php if ($row['visibilite_coordonnees']) { ?>
							<tr>
								<td>Courriel</td>
								<td><?php echo($courriel_offre) ?></td>
							</tr>
							<tr>
								<td>Téléphone</td>
								<td><?php if ($id_offre) { xecho($row['telephone_offre']); } ?></td>
							</tr>
						<?php } ?>
						<!--<tr>
				<td>Zone concernée</td>
				<td><?php xecho($zone) ?></td>
			</tr>-->
					</table>
				</div>
			</fieldset>
		</form>
		<p class="lienenbas"><a href="resultat.php" class="button">Revenir à la liste des offres</a></p>
		<?php
	}else{ //pas d'offre
		?>
		<p style="text-align:center; margin-top:10%;">Il n'y a pas (plus ?) d'offre correspondante disponible. <a href="index.php">Recommencez</a>.</p>
		<?php
	}
	?>
	<div style="height:2em;">&nbsp;</div> <!--tweak css-->
	<?php include('../src/web/footer.inc.php'); ?>
</div>
</body>
</html>