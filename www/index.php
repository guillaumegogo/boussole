<?php
include('src/web/bootstrap.php');

if(isset($_SESSION['web']['erreur'])) {
	
	if ($_SESSION['web']['erreur'] == 1) {
		$message_bas = "Nous ne trouvons pas de ville correspondante. Une faute de frappe ?";
	} else if ($_SESSION['web']['erreur'] == 2) {
		$message_bas = "Plusieurs villes correspondent à ta saisie. Peux-tu préciser s'il te plaît ?";
		
	} else if ($_SESSION['web']['erreur'] == 3) {
		$message_haut = "Oups, ta ville n'appartient pas encore à un territoire de la Boussole !<br/>En attendant que ce soit le cas, contacte le CRIJ le plus près de chez toi :";
	} else if ($_SESSION['web']['erreur'] == 4) {
		$message_haut = "Oups, aucune offre de service n'est encore répertoriée dans la Boussole sur ta ville !<br/>En attendant que ce soit le cas, contacte le CRIJ le plus près de chez toi :";
	}
	
	$liste_crij = (in_array($_SESSION['web']['erreur'], array(3,4))) ? get_pro_by_ville($_SESSION['web']['code_insee'], "CRIJ") : null;
	foreach($liste_crij as &$crij){ 
		$crij['url'] = null;
		if (substr($crij['site_web_pro'],0,4)!="http") {
			$crij['site_web_pro'] = "http://".$crij['site_web_pro'];
		}
		if($crij['site_web_pro'] && filter_var($crij['site_web_pro'], FILTER_VALIDATE_URL)){ 
			$crij['url'] = '<a href="'.$crij['site_web_pro'].'" target="_blank">' ;
		}
		unset($crij);
	}

	unset($_SESSION['web']['erreur']);
}

//si on arrive sur la beta et qu'on ne met pas un "?beta" en URL, alors on est redirigé vers la prod
$redirection_prod = (ENVIRONMENT === ENV_BETA) ? ((isset($_GET['beta'])) ? false : true) : false;

//view
require 'view/index.tpl.php';