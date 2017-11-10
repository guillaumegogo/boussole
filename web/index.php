<?php

include('../src/web/bootstrap.php');

if(isset($_SESSION['erreur'])) {
    if ($_SESSION['erreur'] == 1) {
        $message = "Nous ne trouvons pas de ville correspondante. Recommence s'il te plaît.";
    } else if ($_SESSION['erreur'] == 2) {
        $message = "Plusieurs villes correspondent à ta saisie. Recommence s'il te plaît.";
    }
    unset($_SESSION['erreur']);
}

//********* l'utilisateur a choisi un thème -> il est envoyé vers le formulaire
if (isset($_POST['besoin'])) {
	$_SESSION['besoin'] = $_POST['besoin'];
	header('Location: formulaire.php');
	exit();
}

//view
require 'view/index.tpl.php';