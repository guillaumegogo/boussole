<?php

include('../src/web/bootstrap.php');

?>
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width"/>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" href="css/style.css"/>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css" media="all">
    <link rel="stylesheet" type="text/css" href="css/bootstrap-theme.css">
	<link rel="icon" type="image/png" href="img/compass-icon.png"/>
	<title><?php xecho(ucfirst($titredusite)); ?></title>
</head>
<body>
<div id="main">
	<?php include('../src/web/header.inc.php'); ?>

	<div class="mentions container">
		<div class="row">
			<div class="col-sm-12">
    <h1>Présentation du service</h1>
<p>
    En 2016, le projet de service web destiné aux jeunes visant à améliorer
    leur accès à l’information est entré dans son étape de construction.
</p>
<p>
    Après l’exploitation de plusieurs études du ministère chargé de la jeunesse
    et un diagnostic croisé approfondi mené dans deux régions partenaires du
    projet (Champagne-Ardenne et Bretagne) auprès de jeunes en recherche
    d’information et de professionnels de l’information jeunesse (locaux,
    régionaux et nationaux), une équipe dédiée du ministère appuyée par le
    secrétariat général à la modernisation de l’action publique est passée aux
    travaux pratiques.
</p>
<p>
    4 phases de conception-réalisation en mode Fab lab avec des professionnels
    et des jeunes sur 2 territoires d’expérimentation Grand-Reims et Cœur
    d’Essonne ont permis d’aboutir à la première version de la plateforme de
    services présentée ici en version bêta.
</p>
<p>
    Celle-ci vise dans un premier temps à rendre accessibles des services pour
    les jeunes dans le domaine de l’emploi et du logement.
    <br/>
    <br/>
    L’ambition du service « la Boussole des jeunes » est à la fois de
    personnaliser l’offre d’information grâce à des questionnaires courts,
    d’apporter des réponses opérationnelles et de garantir aux jeunes une
    réponse dans des délais que chaque professionnel détermine et s’engage à
    respecter.
</p>
<p>
    Ce service vise également à améliorer les coopérations d’acteurs autour des
    besoins des usagers dans le partage d’information en favorisant une
    coordination optimisée des parcours des jeunes.
</p>
<p>
    Cette version sera stabilisée sur ces 2 territoires d’ici la fin de
    l’année. La phase de déploiement sur d’autres territoires volontaires
    débutera dès janvier.
</p>
<p>
    <h2>Vos données </h2>
</p>
<p>
    <em>
        Les informations recueillies à partir des formulaires sont nécessaires
        au traitement des demandes. Ces informations sont enregistrées et
        transmises au(x) professionnel(s) auprès du(es)quel(s) vous souhaitez
        obtenir un rendez-vous. Vous disposez d'un droit d'accès, de
        rectification et d'opposition aux données vous concernant, que vous
        pouvez exercer en adressant une demande par courriel à
    </em>
    <a href="mailto:boussoledesdroits@jeunesse-sports.gouv.fr">
        boussoledesdroits@jeunesse-sports.gouv.fr
    </a>
    <em>
        . En cas d’abandon de la recherche, les données personnelles ne sont
        pas conservées.
    </em>
</p>
<p>
    <h2>Mentions légales</h2>
</p>
<p>
    <strong>Éditeur</strong>
</p>
<p>
    Ministère de l’éducation nationale
    <br/>
    Direction de la jeunesse, de l’éducation populaire et de la vie associative
    <br/>
    95, avenue de France
    <br/>
    75650 PARIS Cedex
    <br/>
    Tél : 01 40 45 90 00
</p>
<p>
    Directeur de la publication : Jean-Benoit Dujol, délégué interministériel à
    jeunesse
</p>
<p>
    Administrateur national :
    <a href="mailto:philippe.heurtaux@jeunesse-sports.gouv.fr">
        Philippe Heurtaux
    </a>
    , Division des systèmes d’information de la direction de la jeunesse, de
    l’éducation populaire et de la vie associative
</p>
<p>
Animateur Grand-Reims :    <a href="mailto:boussoledesdroits@crij-ca.fr">Alexis Louis</a>, Centre
    Régional Information Jeunesse Champagne-Ardenne
</p>
<p>
Animateur Cœur d’Essonne :    <a href="mailto:MatthiasGUENEAU@cidj.com">Matthias GUENEAU</a>,Centre
    d’information et de documentation Jeunesse
</p>
<p>
    Participants à l’expérimentation sur les territoires : à paraître
</p>
<p>
    <strong>Développements </strong>
</p>
<p>
    Ce site est développé par la Division des systèmes d’information de la
    direction de la jeunesse, de l’éducation populaire et de la vie associative
    : Guillaume Gogo
</p>
<p>
    Code source : pas encore ouvert
</p>
<p>
    <strong>Conception graphique </strong>
</p>
<p>
    La Bonne Agence | Grenoble
    <br/>
    <a href="http://www.labonneagence.com">www.labonneagence.com</a>
    <br/>
    8 rue Jean Prévost 38000 Grenoble
</p>
<p>
    <strong>Hébergement du site </strong>
</p>
<p>
    Smile, prestataire des ministères sociaux
    <br/>
    www.
    <a
        href="http://openwide.fr/"
        target="_blank"
        title="Lien externe vers OpenWide"
    >
        smile.fr
    </a>
    <br/>
    151 boulevard Stalingrad 69100 Villeurbanne
    <br/>
    <br/>
    <br/>
</p>

			</div>
		</div>
	</div>

	<?php include('../src/web/footer.inc.php'); ?>
</div>
</body>
</html>