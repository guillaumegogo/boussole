<?php

include('../src/web/bootstrap.php');

?>
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width"/>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" href="css/style.css"/>
	<link rel="icon" type="image/png" href="img/compass-icon.png"/>
	<title><?php xecho(ucfirst($titredusite)); ?></title>
</head>
<body>
<div id="main">
	<div class="bandeau">
		<div class="titrebandeau"><a href="index.php"><?php xecho($titredusite); ?></a></div>
	</div>

	<div class="mentions">
		<h2>Mentions légales</h2>
		<h3>Informations éditoriales</h3>
		<p><strong>Éditeur</strong></p>
		<p>Ministère de l’éducation nationale<br>Direction de la jeunesse, de l’éducation populaire et de la vie
			associative<br>95, avenue de France<br>75650 PARIS Cedex<br>Tél&nbsp;: 01 40 45 90 00</p>
		<p>Administrateur national : Philippe Heurtaux (Division SI de la DJEPVA)</p>
		<p>Ce site a été développé par Guillaume Gogo (Division SI de la DJEPVA).</p>
		<p><strong>Accès au site</strong></p>
		<p>Le site web a fait l’objet d’une déclaration auprès de la Commission Nationale de l’Informatique et des
			Libertés (CNIL).</p>
		<p>Les utilisateurs du site web sont tenus de respecter les dispositions de la loi du 6 janvier 1978 relative à
			l’informatique, aux fichiers et aux libertés, dont la violation est passible de sanctions pénales. Ils
			doivent notamment s’abstenir, d’une manière générale, de porter atteinte à la vie privée ou à la réputation
			des personnes.</p>
		<p><strong>Contenu du site</strong></p>
		<p>Le Ministère en charge de la Jeunesse met à disposition des utilisateurs de ce site web des informations et
			outils disponibles et vérifiés. Les informations du présent site renvoient parfois à des sites extérieurs
			(liens hypertextes) sur lesquels le ministère n’a aucun contrôle et pour lesquels il décline toute
			responsabilité.</p>
		<p>Il s’efforcera de corriger autant que faire se peut les erreurs ou omissions qui lui seront signalées par les
			utilisateurs (en adressant un courriel aux webmestre du site).</p>
		<p><strong>Propriété</strong></p>
		<p>La structure générale et le logiciel composant ce site web sont de l’utilisation exclusive du Ministère
			chargé de la Jeunesse.</p>
		<p>La mise en place de liens vers le site, y compris "profonds", n’est conditionnée à aucun accord préalable.
			Seule la mention explicite du site du ministère dans l’intitulé du lien est souhaitée.</p>
		<p>L’autorisation de mise en place d’un lien est valable pour tout support, à l’exception de ceux diffusant des
			informations à caractère polémique, pornographique, xénophobe ou pouvant, dans une plus large mesure porter
			atteinte à la sensibilité du plus grand nombre.</p>
		<p>Les contenus ne sauraient être reproduits librement sans demande préalable et sans l’indication de la source.
			Les demandes d’autorisation de reproduction d’un contenu doivent être adressées à la rédaction du site (en
			adressant un courriel aux webmestre du site). La demande devra préciser le contenu visé ainsi que le site
			sur lequel ce dernier figurera. En outre, les informations utilisées ne doivent l’être qu’à des fins
			personnelles, associatives ou professionnelles, toute diffusion ou utilisation à des fins commerciales ou
			publicitaires étant exclues.</p>

		<h3>Données personnelles</h3>
		<p>En conformité avec les dispositions de la loi du 6 janvier 1978 susmentionnée, le traitement automatisé des
			données nominatives réalisé à partir de ce site web a fait l’objet d’une déclaration auprès de la Commission
			Nationale de l’Informatique et des Libertés.
			<!--Ce site ne collecte aucune autre donnée que des adresses IP destinées à un usage purement technique, nécessaire à la production de statistiques de consultation.--></p>

		<h3>Protection et traitement de données à caractère personnel</h3>
		<p>L’équipe du site est particulièrement attentive au respect des obligations légales de tout éditeur de site
			internet et suit les recommandations de la commission nationale de l’informatique et des libertés (CNIL) et
			celles de l’agence pour le développement de l’administration électronique (ADAE).<br></p>

		<h3>Respect des lois en vigueur</h3>
		<p>Le site respecte la vie privée de l’internaute et se conforme strictement aux lois en vigueur sur la
			protection de la vie privée et des libertés individuelles. Aucune information personnelle n’est collectée à
			votre insu. Aucune information personnelle n’est cédée à des tiers. Les courriels, les adresses
			électroniques ou autres informations nominatives dont ce site est destinataire ne font l’objet d’aucune
			exploitation et ne sont conservés que pour la durée nécessaire à leur traitement.<br></p>

		<h3>Droit des internautes&nbsp;: droit d’accès et de rectification</h3>
		<p>Conformément aux dispositions de la loi n&#176;78-17 du 6 janvier 1978 relative à l’informatique, aux
			fichiers et aux libertés, les internautes disposent d’un droit d’accès, de modification, de rectification et
			de suppression des données qui les concernent. Ce droit s’exerce par voie postale, en justifiant de son
			identité, à l’adresse suivante&nbsp;:<br>Ministère de l’éducation nationale<br>Direction de la jeunesse, de
			l’éducation populaire et de la vie associative<br>Division des Systèmes d'information<br>95, avenue de
			France<br>75013 Paris 13e</p>
			
	</div>

	<?php include('../src/web/footer.inc.php'); ?>
</div>
</body>
</html>