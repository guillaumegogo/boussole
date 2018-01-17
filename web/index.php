<?php

include('../src/web/bootstrap.php');

if(isset($_SESSION['web']['erreur'])) {
	if ($_SESSION['web']['erreur'] == 1) {
		$message = "Nous ne trouvons pas de ville correspondante. Une faute de frappe ?";
	} else if ($_SESSION['web']['erreur'] == 2) {
		$message = "Plusieurs villes correspondent à ta saisie. Peux-tu préciser s'il te plaît ?";
	} else if ($_SESSION['web']['erreur'] == 3) {
		$message = "Aucune offre de service n'est encore répertoriée dans la Boussole pour ta ville, probablement car elle n'appartient pas à un des territoires actuellement ouverts.<br/>Nous t'invitons à <a href=\"https://www.cidj.com/nous-rencontrer\" target=\"_blank\">te rapprocher du réseau information jeunesse près de chez toi</a> qui saura certainement répondre à ton besoin.";
	}
	unset($_SESSION['web']['erreur']);
}

//view
require 'view/index.tpl.php';