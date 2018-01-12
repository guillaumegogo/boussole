<?php

include('../src/web/bootstrap.php');

if(isset($_SESSION['web']['erreur'])) {
	if ($_SESSION['web']['erreur'] == 1) {
		$message = "Nous ne trouvons pas de ville correspondante. Une faute de frappe ?";
	} else if ($_SESSION['web']['erreur'] == 2) {
		$message = "Plusieurs villes correspondent à ta saisie. Peux-tu préciser s'il te plaît ?";
	} else if ($_SESSION['web']['erreur'] == 3) {
		$message = "La Boussole ne semble pas encore active sur ta ville. Nous t'invitons à <a href=\"https://www.cidj.com/nous-rencontrer\" target=\"_blank\">te rapprocher du réseau information jeunesse près de chez toi</a>.";
	}
	unset($_SESSION['web']['erreur']);
}

//view
require 'view/index.tpl.php';